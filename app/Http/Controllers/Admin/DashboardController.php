<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Rating;
use App\Models\StatusLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Core KPIs
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', TicketStatus::Open)->count();
        $assignedTickets = Ticket::where('status', TicketStatus::Assigned)->count();
        $inProgressTickets = Ticket::whereIn('status', [TicketStatus::InProgress, TicketStatus::PendingFix])->count();
        $pendingFixTickets = Ticket::where('status', TicketStatus::PendingFix)->count();
        $resolvedTickets = Ticket::where('status', TicketStatus::Resolved)->count();
        $closedTickets = Ticket::where('status', TicketStatus::Closed)->count();

        // 2. Average Resolution Time (Hours)
        $avgResolutionHours = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG((julianday(resolved_at) - julianday(created_at)) * 24) as avg_hours')
            ->value('avg_hours') ?? 0;

        // 3. Average Rating
        $avgRating = Rating::avg('score') ?? 0;
        $totalRatings = Rating::count();

        // 4. Tickets by Category
        $categoriesStats = Category::withCount('tickets')->get();

        // 5. Tickets by Priority
        $priorityStats = [
            'Urgent' => Ticket::where('priority', TicketPriority::Urgent)->count(),
            'High' => Ticket::where('priority', TicketPriority::High)->count(),
            'Medium' => Ticket::where('priority', TicketPriority::Medium)->count(),
            'Low' => Ticket::where('priority', TicketPriority::Low)->count(),
        ];

        // 6. Top Technicians Performance
        $technicians = User::where('role', UserRole::Technician)
            ->withCount([
                'assignedTickets as total_assigned',
                'assignedTickets as resolved_count' => function ($q) {
                    $q->whereIn('status', [TicketStatus::Resolved, TicketStatus::Closed]);
                },
            ])
            ->get();

        // 7. Recent Status Activity Logs
        $recentLogs = StatusLog::with(['ticket', 'changer'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalTickets',
            'openTickets',
            'assignedTickets',
            'inProgressTickets',
            'pendingFixTickets',
            'resolvedTickets',
            'closedTickets',
            'avgResolutionHours',
            'avgRating',
            'totalRatings',
            'categoriesStats',
            'priorityStats',
            'technicians',
            'recentLogs'
        ));
    }
}
