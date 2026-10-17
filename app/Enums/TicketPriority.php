<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Low = 'Low';
    case Medium = 'Medium';
    case High = 'High';
    case Urgent = 'Urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'ต่ำ (Low)',
            self::Medium => 'ปานกลาง (Medium)',
            self::High => 'สูง (High)',
            self::Urgent => 'เร่งด่วนมาก (Urgent)',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Low => 'bg-slate-100 text-slate-700 border border-slate-200',
            self::Medium => 'bg-sky-100 text-sky-800 border border-sky-200',
            self::High => 'bg-orange-100 text-orange-800 border border-orange-200',
            self::Urgent => 'bg-rose-100 text-rose-800 border border-rose-200 animate-pulse',
        };
    }
}
