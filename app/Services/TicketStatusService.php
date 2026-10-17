<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\StatusLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TicketStatusService
{
    public function __construct(
        protected LineMessagingService $lineService
    ) {}

    /**
     * Perform a ticket status transition according to State Machine rules.
     *
     * @param Ticket $ticket
     * @param TicketStatus $newStatus
     * @param User $actor
     * @param array $payload Optional data: technician_id, note, comment, image_path, score, feedback
     * @return Ticket
     */
    public function transition(Ticket $ticket, TicketStatus $newStatus, User $actor, array $payload = []): Ticket
    {
        $currentStatus = $ticket->status;

        if ($currentStatus === $newStatus) {
            return $ticket;
        }

        // 1. Validate State Transition rules
        $this->validateTransition($currentStatus, $newStatus);

        // 2. Check Permissions
        $this->checkPermission($actor, $ticket, $currentStatus, $newStatus);

        // 3. Execute in DB Transaction
        return DB::transaction(function () use ($ticket, $currentStatus, $newStatus, $actor, $payload) {
            $updateData = [
                'status' => $newStatus,
            ];

            $logNote = $payload['note'] ?? null;

            // Scenario 1: Open -> Assigned
            if ($currentStatus === TicketStatus::Open && $newStatus === TicketStatus::Assigned) {
                if (empty($payload['technician_id'])) {
                    throw new InvalidArgumentException('กรุณาระบุช่างเทคนิคที่ต้องการมอบหมายงาน');
                }
                $updateData['technician_id'] = $payload['technician_id'];
                $tech = User::find($payload['technician_id']);
                $logNote = $logNote ?: 'มอบหมายงานให้ช่าง: ' . ($tech ? $tech->name : 'N/A');
            }

            // Scenario 2: Assigned -> InProgress
            if ($currentStatus === TicketStatus::Assigned && $newStatus === TicketStatus::InProgress) {
                $logNote = $logNote ?: 'ช่างรับงานและเริ่มตรวจสอบอุปกรณ์';
                if (!empty($payload['comment'])) {
                    Comment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $actor->id,
                        'body' => $payload['comment'],
                        'image_path' => $payload['image_path'] ?? null,
                    ]);
                }
            }

            // Scenario 3: InProgress -> Resolved
            if ($currentStatus === TicketStatus::InProgress && $newStatus === TicketStatus::Resolved) {
                $updateData['resolved_at'] = now();
                $logNote = $logNote ?: 'ช่างดำเนินการซ่อมเสร็จสิ้น';
                if (!empty($payload['comment']) || !empty($payload['image_path'])) {
                    Comment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $actor->id,
                        'body' => $payload['comment'] ?: 'ซ่อมแซมเสร็จสิ้น พร้อมส่งมอบงาน',
                        'image_path' => $payload['image_path'] ?? null,
                    ]);
                }
            }

            // Scenario 4: Resolved -> Closed
            if ($currentStatus === TicketStatus::Resolved && $newStatus === TicketStatus::Closed) {
                $updateData['closed_at'] = now();
                $logNote = $logNote ?: 'ผู้แจ้งยืนยันผลการซ่อมและประเมินความพึงพอใจ';

                if (isset($payload['score'])) {
                    Rating::updateOrCreate(
                        ['ticket_id' => $ticket->id],
                        [
                            'score' => (int) $payload['score'],
                            'feedback' => $payload['feedback'] ?? null,
                        ]
                    );
                }
            }

            // Scenario 5: Resolved -> InProgress (Rejection)
            if ($currentStatus === TicketStatus::Resolved && $newStatus === TicketStatus::InProgress) {
                $updateData['resolved_at'] = null;
                $reason = $payload['note'] ?? $payload['comment'] ?? 'ผู้แจ้งขอส่งกลับให้ตรวจสอบใหม่';
                $logNote = 'ส่งงานกลับให้แก้ไข: ' . $reason;

                Comment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $actor->id,
                    'body' => '❌ ส่งงานกลับเพื่อแก้ไข: ' . $reason,
                ]);
            }

            $ticket->update($updateData);

            // Create Audit Log in status_logs
            StatusLog::create([
                'ticket_id' => $ticket->id,
                'changed_by' => $actor->id,
                'from_status' => $currentStatus,
                'to_status' => $newStatus,
                'note' => $logNote,
            ]);

            // Dispatch LINE notification
            $this->notifyStatusChange($ticket->fresh(), $currentStatus, $newStatus, $actor);

            return $ticket->fresh();
        });
    }

    /**
     * Validate whether transition is legal under the State Machine.
     */
    protected function validateTransition(TicketStatus $from, TicketStatus $to): void
    {
        $allowedTransitions = [
            TicketStatus::Open->value => [TicketStatus::Assigned->value],
            TicketStatus::Assigned->value => [TicketStatus::InProgress->value],
            TicketStatus::InProgress->value => [TicketStatus::Resolved->value],
            TicketStatus::Resolved->value => [TicketStatus::Closed->value, TicketStatus::InProgress->value],
            TicketStatus::Closed->value => [],
        ];

        if (!in_array($to->value, $allowedTransitions[$from->value] ?? [])) {
            throw new InvalidArgumentException("ไม่สามารถเปลี่ยนสถานะจาก {$from->label()} ไปยัง {$to->label()} ได้");
        }
    }

    /**
     * Check authorization for the given state transition.
     */
    protected function checkPermission(User $actor, Ticket $ticket, TicketStatus $from, TicketStatus $to): void
    {
        if ($actor->isAdmin()) {
            return; // Admin can execute all actions
        }

        // Open -> Assigned requires Admin
        if ($from === TicketStatus::Open && $to === TicketStatus::Assigned) {
            throw new InvalidArgumentException('เฉพาะผู้ดูแลระบบ (Admin) เท่านั้นที่สามารถมอบหมายงานได้');
        }

        // Assigned -> InProgress requires Assigned Technician
        if ($from === TicketStatus::Assigned && $to === TicketStatus::InProgress) {
            if (!$actor->isTechnician() || $ticket->technician_id !== $actor->id) {
                throw new InvalidArgumentException('เฉพาะช่างที่ได้รับมอบหมายเท่านั้นที่สามารถกดรับงานได้');
            }
        }

        // InProgress -> Resolved requires Assigned Technician
        if ($from === TicketStatus::InProgress && $to === TicketStatus::Resolved) {
            if (!$actor->isTechnician() || $ticket->technician_id !== $actor->id) {
                throw new InvalidArgumentException('เฉพาะช่างที่รับผิดชอบเท่านั้นที่สามารถปิดงานซ่อมได้');
            }
        }

        // Resolved -> Closed or Resolved -> InProgress requires Ticket Owner
        if ($from === TicketStatus::Resolved) {
            if ($ticket->user_id !== $actor->id) {
                throw new InvalidArgumentException('เฉพาะเจ้าของใบแจ้งซ่อมเท่านั้นที่สามารถยืนยันหรือส่งงานกลับได้');
            }
        }
    }

    /**
     * Send notification through LINE Messaging Service
     */
    protected function notifyStatusChange(Ticket $ticket, TicketStatus $from, TicketStatus $to, User $actor): void
    {
        $msg = "\n🔔 [Smart IT Helpdesk] อัปเดตสถานะใบแจ้งซ่อม #{$ticket->id}\n";
        $msg .= "📌 หัวข้อ: {$ticket->title}\n";
        $msg .= "🔄 สถานะ: {$from->label()} ➔ {$to->label()}\n";
        $msg .= "👤 ดำเนินการโดย: {$actor->name} ({$actor->role->label()})\n";

        if ($to === TicketStatus::Assigned && $ticket->technician) {
            $msg .= "🛠️ ช่างผู้ดูแล: {$ticket->technician->name}\n";
        }

        $this->lineService->send($msg);
    }
}
