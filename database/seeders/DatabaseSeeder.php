<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\StatusLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users with different roles
        $admin = User::create([
            'name' => 'ธนภัทร แอดมินใหญ่ (Admin)',
            'email' => 'admin@helpdesk.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'department' => 'IT Department',
            'phone' => '081-111-2233',
        ]);

        $tech1 = User::create([
            'name' => 'สมศักดิ์ ช่างซ่อมฮาร์ดแวร์ (Tech 1)',
            'email' => 'tech1@helpdesk.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Technician,
            'department' => 'IT Support Team A',
            'phone' => '082-222-3344',
        ]);

        $tech2 = User::create([
            'name' => 'กิตติศักดิ์ ช่างระบบเครือข่าย (Tech 2)',
            'email' => 'tech2@helpdesk.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Technician,
            'department' => 'IT Support Team B',
            'phone' => '083-333-4455',
        ]);

        $user1 = User::create([
            'name' => 'วิภาดา ฝ่ายบัญชี (User 1)',
            'email' => 'user1@helpdesk.com',
            'password' => Hash::make('password'),
            'role' => UserRole::User,
            'department' => 'แผนกบัญชีและการเงิน',
            'phone' => '084-444-5566',
        ]);

        $user2 = User::create([
            'name' => 'ณัฐพล ฝ่ายการตลาด (User 2)',
            'email' => 'user2@helpdesk.com',
            'password' => Hash::make('password'),
            'role' => UserRole::User,
            'department' => 'แผนกการตลาด',
            'phone' => '085-555-6677',
        ]);

        // 2. Create Default Categories
        $catHardware = Category::create([
            'name' => 'Hardware / ฮาร์ดแวร์และอุปกรณ์',
            'description' => 'ปัญหาเกี่ยวกับเครื่องคอมพิวเตอร์, หน้าจอ, คีย์บอร์ด, เมาส์, RAM, Harddisk',
        ]);

        $catSoftware = Category::create([
            'name' => 'Software / โปรแกรมและระบบปฏิบัติการ',
            'description' => 'Windows, MS Office, Antivirus, Adobe, โปรแกรม ERP และลิขสิทธิ์ต่างๆ',
        ]);

        $catNetwork = Category::create([
            'name' => 'Network / อินเทอร์เน็ตและเครือข่าย',
            'description' => 'LAN, Wi-Fi, สัญญาณเน็ตช้า, VPN, การเชื่อมต่อเข้าระบบเซิร์ฟเวอร์',
        ]);

        $catPrinter = Category::create([
            'name' => 'Printer / เครื่องพิมพ์และสแกนเนอร์',
            'description' => 'ปริ้นไม่ออก, กระดาษติด, หมึกหมด, หมึกเลอะ, ตั้งค่าการแชร์เครื่องพิมพ์',
        ]);

        $catAccount = Category::create([
            'name' => 'Account / บัญชีและสิทธิ์การเข้าถึง',
            'description' => 'ลืมรหัสผ่าน, ปลดล็อกบัญชี, ขอสิทธิ์เข้าใช้งานโฟลเดอร์หรือระบบงาน',
        ]);

        // 3. Create Sample Tickets across different State Machine phases

        // Ticket 1: OPEN (waiting for admin to assign)
        $t1 = Ticket::create([
            'user_id' => $user1->id,
            'category_id' => $catHardware->id,
            'technician_id' => null,
            'title' => 'คอมพิวเตอร์จอฟ้า (Blue Screen) บ่อยมาก เปิดใช้งานไม่ได้',
            'description' => "คอมพิวเตอร์ตั้งโต๊ะเปิดติดแต่พอเข้าสู่ Windows แล้วจะขึ้นจอฟ้าโค้ด MEMORY_MANAGEMENT ตลอดเวลา รบกวนช่วยตรวจสอบหน่อยค่ะ งานปิดงบต้องรีบใช้",
            'status' => TicketStatus::Open,
            'priority' => TicketPriority::Urgent,
            'created_at' => now()->subHours(2),
        ]);
        StatusLog::create([
            'ticket_id' => $t1->id,
            'changed_by' => $user1->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Open,
            'note' => 'ผู้แจ้งสร้างใบแจ้งซ่อมใหม่ในระบบ',
            'created_at' => now()->subHours(2),
        ]);

        // Ticket 2: ASSIGNED (Admin assigned to Tech 1, waiting for tech to accept)
        $t2 = Ticket::create([
            'user_id' => $user2->id,
            'category_id' => $catPrinter->id,
            'technician_id' => $tech1->id,
            'title' => 'เครื่องพิมพ์แผนกการตลาดกระดาษติดและไฟสีส้มกะพริบ',
            'description' => 'เครื่องพิมพ์ Canon ชั้น 2 พิมพ์ไม่ออก มีกระดาษติดอยู่ข้างในดึงไม่ออก และมีไฟส้มกะพริบตลอดเวลา',
            'status' => TicketStatus::Assigned,
            'priority' => TicketPriority::High,
            'created_at' => now()->subHours(5),
        ]);
        StatusLog::create([
            'ticket_id' => $t2->id,
            'changed_by' => $user2->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Open,
            'note' => 'สร้างใบแจ้งซ่อม',
            'created_at' => now()->subHours(5),
        ]);
        StatusLog::create([
            'ticket_id' => $t2->id,
            'changed_by' => $admin->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Assigned,
            'note' => 'มอบหมายงานให้ช่าง: ' . $tech1->name,
            'created_at' => now()->subHours(4),
        ]);

        // Ticket 3: IN PROGRESS (Tech 2 accepted and currently inspecting)
        $t3 = Ticket::create([
            'user_id' => $user1->id,
            'category_id' => $catNetwork->id,
            'technician_id' => $tech2->id,
            'title' => 'Wi-Fi โซนชั้น 3 หลุดบ่อยมาก เชื่อมต่อแล้วใช้งานไม่ได้',
            'description' => 'สัญญาณ Wi-Fi ติดๆ ดับๆ ตลอดทั้งบ่าย พนักงานโซนบัญชีไม่สามารถเปิดอีเมลได้เลยครับ',
            'status' => TicketStatus::InProgress,
            'priority' => TicketPriority::High,
            'created_at' => now()->subHours(8),
        ]);
        StatusLog::create([
            'ticket_id' => $t3->id,
            'changed_by' => $user1->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Open,
            'note' => 'สร้างใบแจ้งซ่อม',
            'created_at' => now()->subHours(8),
        ]);
        StatusLog::create([
            'ticket_id' => $t3->id,
            'changed_by' => $admin->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Assigned,
            'note' => 'มอบหมายงานให้ช่าง: ' . $tech2->name,
            'created_at' => now()->subHours(7),
        ]);
        StatusLog::create([
            'ticket_id' => $t3->id,
            'changed_by' => $tech2->id,
            'from_status' => TicketStatus::Assigned,
            'to_status' => TicketStatus::InProgress,
            'note' => 'ช่างรับงานและกำลังตรวจสอบ Access Point ชั้น 3',
            'created_at' => now()->subHours(6),
        ]);
        Comment::create([
            'ticket_id' => $t3->id,
            'user_id' => $tech2->id,
            'body' => 'กำลังขึ้นไปตรวจสอบตัว Access Point บริเวณเสาต้นกลางนะครับ คาดว่าต้องรีสตาร์ท Switch',
            'created_at' => now()->subHours(6),
        ]);

        // Ticket 4: RESOLVED (Tech 1 resolved, waiting for User 2 to confirm & rate)
        $t4 = Ticket::create([
            'user_id' => $user2->id,
            'category_id' => $catSoftware->id,
            'technician_id' => $tech1->id,
            'title' => 'ขอติดตั้งโปรแกรมตกแต่งภาพและต่ออายุ License',
            'description' => 'ต้องการติดตั้ง Adobe Illustrator และ Photoshop เวอร์ชันล่าสุดเพื่อใช้ออกแบบแบนเนอร์ประชาสัมพันธ์',
            'status' => TicketStatus::Resolved,
            'priority' => TicketPriority::Medium,
            'created_at' => now()->subDays(1),
            'resolved_at' => now()->subHours(1),
        ]);
        StatusLog::create([
            'ticket_id' => $t4->id,
            'changed_by' => $user2->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Open,
            'note' => 'สร้างใบแจ้งซ่อม',
            'created_at' => now()->subDays(1),
        ]);
        StatusLog::create([
            'ticket_id' => $t4->id,
            'changed_by' => $admin->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Assigned,
            'note' => 'มอบหมายงานให้ช่าง: ' . $tech1->name,
            'created_at' => now()->subDays(1)->addHour(),
        ]);
        StatusLog::create([
            'ticket_id' => $t4->id,
            'changed_by' => $tech1->id,
            'from_status' => TicketStatus::Assigned,
            'to_status' => TicketStatus::InProgress,
            'note' => 'เริ่มลงโปรแกรม',
            'created_at' => now()->subHours(3),
        ]);
        StatusLog::create([
            'ticket_id' => $t4->id,
            'changed_by' => $tech1->id,
            'from_status' => TicketStatus::InProgress,
            'to_status' => TicketStatus::Resolved,
            'note' => 'ติดตั้ง Adobe Creative Cloud และล็อกอิน License ให้เรียบร้อยแล้ว พร้อมใช้งาน',
            'created_at' => now()->subHours(1),
        ]);
        Comment::create([
            'ticket_id' => $t4->id,
            'user_id' => $tech1->id,
            'body' => 'ดำเนินการติดตั้งโปรแกรม Adobe Illustrator & Photoshop พร้อมผูกกับอีเมลบริษัทเรียบร้อยครับ ลองเปิดทดสอบใช้งานดูได้เลยครับ',
            'created_at' => now()->subHours(1),
        ]);

        // Ticket 5: CLOSED (Completed & Rated)
        $t5 = Ticket::create([
            'user_id' => $user1->id,
            'category_id' => $catHardware->id,
            'technician_id' => $tech1->id,
            'title' => 'เมาส์ไร้สายคลิกซ้ายไม่ตอบสนอง ขอเปลี่ยนตัวใหม่',
            'description' => 'เมาส์ที่ใช้งานอยู่คลิกเบิ้ลเองตลอดและบางครั้งไม่ตอบสนอง รบกวนขอเปลี่ยนเมาส์ตัวใหม่ค่ะ',
            'status' => TicketStatus::Closed,
            'priority' => TicketPriority::Low,
            'created_at' => now()->subDays(3),
            'resolved_at' => now()->subDays(2),
            'closed_at' => now()->subDays(2)->addHours(2),
        ]);
        StatusLog::create([
            'ticket_id' => $t5->id,
            'changed_by' => $user1->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Open,
            'note' => 'สร้างใบแจ้งซ่อม',
            'created_at' => now()->subDays(3),
        ]);
        StatusLog::create([
            'ticket_id' => $t5->id,
            'changed_by' => $admin->id,
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Assigned,
            'note' => 'มอบหมายงานให้ช่าง: ' . $tech1->name,
            'created_at' => now()->subDays(3)->addHours(1),
        ]);
        StatusLog::create([
            'ticket_id' => $t5->id,
            'changed_by' => $tech1->id,
            'from_status' => TicketStatus::Assigned,
            'to_status' => TicketStatus::InProgress,
            'note' => 'เบิกเมาส์ตัวใหม่จากคลัง',
            'created_at' => now()->subDays(2)->subHours(4),
        ]);
        StatusLog::create([
            'ticket_id' => $t5->id,
            'changed_by' => $tech1->id,
            'from_status' => TicketStatus::InProgress,
            'to_status' => TicketStatus::Resolved,
            'note' => 'นำเมาส์ตัวใหม่ไปเปลี่ยนให้ที่โต๊ะทำงานเรียบร้อย',
            'created_at' => now()->subDays(2),
        ]);
        StatusLog::create([
            'ticket_id' => $t5->id,
            'changed_by' => $user1->id,
            'from_status' => TicketStatus::Resolved,
            'to_status' => TicketStatus::Closed,
            'note' => 'ผู้แจ้งยืนยันผลการซ่อมและประเมินความพึงพอใจ 5 ดาว',
            'created_at' => now()->subDays(2)->addHours(2),
        ]);
        Rating::create([
            'ticket_id' => $t5->id,
            'score' => 5,
            'feedback' => 'ช่างนำเมาส์ตัวใหม่มาเปลี่ยนให้อย่างรวดเร็วมากค่ะ พูดจาสุภาพ ขอบคุณมากนะคะ',
            'created_at' => now()->subDays(2)->addHours(2),
        ]);
    }
}
