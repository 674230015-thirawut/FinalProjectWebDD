<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Technician = 'technician';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'ผู้ใช้งานทั่วไป (User)',
            self::Technician => 'ช่างเทคนิค (Technician)',
            self::Admin => 'ผู้ดูแลระบบ (Admin)',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::User => 'bg-blue-100 text-blue-700',
            self::Technician => 'bg-amber-100 text-amber-800',
            self::Admin => 'bg-purple-100 text-purple-800',
        };
    }
}
