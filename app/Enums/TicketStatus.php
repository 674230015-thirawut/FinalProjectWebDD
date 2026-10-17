<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'Open';
    case Assigned = 'Assigned';
    case InProgress = 'InProgress';
    case Resolved = 'Resolved';
    case Closed = 'Closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'รอรับเรื่อง (Open)',
            self::Assigned => 'มอบหมายช่างแล้ว (Assigned)',
            self::InProgress => 'กำลังดำเนินการ (In Progress)',
            self::Resolved => 'ซ่อมเสร็จแล้ว (Resolved)',
            self::Closed => 'ปิดงานเรียบร้อย (Closed)',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-blue-100 text-blue-800 border border-blue-200',
            self::Assigned => 'bg-amber-100 text-amber-800 border border-amber-200',
            self::InProgress => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
            self::Resolved => 'bg-purple-100 text-purple-800 border border-purple-200',
            self::Closed => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            self::Open => 'bg-blue-500',
            self::Assigned => 'bg-amber-500',
            self::InProgress => 'bg-indigo-500',
            self::Resolved => 'bg-purple-500',
            self::Closed => 'bg-emerald-500',
        };
    }
}
