<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Comment;
use App\Models\StatusLog;
use App\Models\Ticket;
use App\Models\User;
use App\Services\LineMessagingService;
use App\Services\TicketStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(
        protected TicketStatusService $statusService,
        protected LineMessagingService $lineService
    ) {}

    /**
     * Display a listing of tickets with filters.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['user', 'category', 'technician', 'rating']);

        // RBAC View Scope
        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isTechnician()) {
            if ($request->query('view') === 'mine') {
                $query->where('technician_id', $user->id);
            }
        }

        // Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $categories = Category::all();
        $statuses = TicketStatus::cases();
        $priorities = TicketPriority::cases();

        // Summary counts
        $counts = [
            'all' => $user->isUser() ? Ticket::where('user_id', $user->id)->count() : Ticket::count(),
            'open' => ($user->isUser() ? Ticket::where('user_id', $user->id) : Ticket::query())->where('status', TicketStatus::Open)->count(),
            'in_progress' => ($user->isUser() ? Ticket::where('user_id', $user->id) : Ticket::query())->whereIn('status', [TicketStatus::Assigned, TicketStatus::InProgress])->count(),
            'resolved' => ($user->isUser() ? Ticket::where('user_id', $user->id) : Ticket::query())->whereIn('status', [TicketStatus::Resolved, TicketStatus::Closed])->count(),
        ];

        return view('tickets.index', compact('tickets', 'categories', 'statuses', 'priorities', 'counts'));
    }

    /**
     * Show form for creating a new ticket.
     */
    public function create()
    {
        $categories = Category::all();
        $priorities = TicketPriority::cases();
        return view('tickets.create', compact('categories', 'priorities'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|string|in:Low,Medium,High,Urgent',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => TicketStatus::Open,
            'image_path' => $imagePath,
        ]);

        // Record Initial status log
        StatusLog::create([
            'ticket_id' => $ticket->id,
            'changed_by' => Auth::id(),
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Open,
            'note' => 'สร้างใบแจ้งซ่อมใหม่ในระบบ',
        ]);

        // LINE Notify to Admin / IT Team
        $category = Category::find($validated['category_id']);
        $msg = "\n🆕 [Smart IT Helpdesk] มีการแจ้งซ่อมใหม่ #{$ticket->id}\n";
        $msg .= "📌 หัวข้อ: {$ticket->title}\n";
        $msg .= "🏷️ หมวดหมู่: " . ($category ? $category->name : 'ทั่วไป') . "\n";
        $msg .= "⚡ ความสำคัญ: {$ticket->priority->label()}\n";
        $msg .= "👤 ผู้แจ้ง: " . Auth::user()->name . "\n";
        $this->lineService->send($msg);

        return redirect()->route('tickets.show', $ticket)->with('success', 'บันทึกการแจ้งซ่อมสำเร็จ รหัสงาน #' . $ticket->id);
    }

    /**
     * Display a specific ticket.
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        // RBAC Authorization check
        if ($user->isUser() && $ticket->user_id !== $user->id) {
            abort(403, 'คุณไม่มีสิทธิ์ดูใบแจ้งซ่อมของผู้อื่น');
        }

        $ticket->load([
            'user',
            'category',
            'technician',
            'comments.user',
            'statusLogs.changer',
            'rating',
        ]);

        $technicians = User::where('role', UserRole::Technician)->get();

        return view('tickets.show', compact('ticket', 'technicians'));
    }

    /**
     * Update ticket status through State Machine.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'to_status' => 'required|string|in:Open,Assigned,InProgress,Resolved,Closed',
            'technician_id' => 'nullable|exists:users,id',
            'note' => 'nullable|string',
            'comment' => 'nullable|string',
            'score' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('repairs', 'public');
        }

        $toStatus = TicketStatus::from($validated['to_status']);

        try {
            $payload = [
                'technician_id' => $validated['technician_id'] ?? null,
                'note' => $validated['note'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'score' => $validated['score'] ?? null,
                'feedback' => $validated['feedback'] ?? null,
                'image_path' => $imagePath,
            ];

            $this->statusService->transition($ticket, $toStatus, Auth::user(), $payload);

            return back()->with('success', "อัปเดตสถานะใบแจ้งซ่อมเป็น '{$toStatus->label()}' เรียบร้อยแล้ว");
        } catch (\Throwable $e) {
            return back()->with('error', 'ไม่สามารถเปลี่ยนสถานะได้: ' . $e->getMessage());
        }
    }

    /**
     * Add a comment to the ticket.
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'body' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
        }

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'image_path' => $imagePath,
        ]);

        return back()->with('success', 'ส่งความคิดเห็นเรียบร้อยแล้ว');
    }
}
