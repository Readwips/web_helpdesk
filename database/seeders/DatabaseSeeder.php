<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\AssetRepair;
use App\Models\Department;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = collect(['Teknologi Informasi', 'Keuangan', 'Sumber Daya Manusia', 'Operasional', 'Pemasaran'])->map(fn ($n) => Department::firstOrCreate(['name' => $n], ['description' => 'Departemen '.$n]));
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], ['name' => 'Administrator Sistem', 'password' => Hash::make('password'), 'role' => 'admin', 'status' => 'active', 'department_id' => $departments[0]->id, 'phone' => '081234567890', 'email_verified_at' => now()]);
        $technicians = collect(['teknisi@example.com' => 'Budi Santoso', 'teknisi2@example.com' => 'Rina Pratama', 'teknisi3@example.com' => 'Dedi Kurniawan'])->map(fn ($name, $email) => User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => Hash::make('password'), 'role' => 'technician', 'status' => 'active', 'department_id' => $departments[0]->id, 'email_verified_at' => now()]))->values();
        $names = ['Andi Wijaya', 'Siti Rahma', 'Fajar Nugroho', 'Maya Lestari', 'Rizky Hidayat', 'Dewi Anggraini', 'Agus Setiawan', 'Nadia Putri'];
        $users = collect($names)->map(fn ($name, $i) => User::firstOrCreate(['email' => $i === 0 ? 'user@example.com' : 'user'.($i + 1).'@example.com'], ['name' => $name, 'password' => Hash::make('password'), 'role' => 'user', 'status' => 'active', 'department_id' => $departments[($i % 4) + 1]->id, 'phone' => '0812'.str_pad((string) $i, 8, '0'), 'email_verified_at' => now()]));
        $ticketCategories = collect(['Hardware', 'Software', 'Network', 'Printer', 'Account', 'Security', 'Other'])->map(fn ($n) => TicketCategory::firstOrCreate(['name' => $n], ['description' => 'Masalah terkait '.$n]));
        $assetDefinitions = [['Laptop', 'LPT'], ['Desktop PC', 'PC'], ['Monitor', 'MON'], ['Printer', 'PRN'], ['Router', 'RTR'], ['Access Point', 'AP'], ['Switch', 'SWT'], ['Server', 'SRV']];
        $assetCategories = collect($assetDefinitions)->map(fn ($x) => AssetCategory::firstOrCreate(['code' => $x[1]], ['name' => $x[0], 'description' => 'Kategori '.$x[0]]));
        $knowledgeNames = ['Hardware', 'Windows', 'Printer', 'Jaringan', 'Aplikasi', 'Email', 'Keamanan', 'Akun Pengguna'];
        $knowledgeCategories = collect($knowledgeNames)->map(fn ($n) => KnowledgeCategory::firstOrCreate(['slug' => Str::slug($n)], ['name' => $n, 'description' => 'Panduan '.$n]));
        if (Asset::count() === 0) {
            for ($i = 1; $i <= 25; $i++) {
                $cat = $assetCategories[($i - 1) % $assetCategories->count()];
                Asset::create(['asset_code' => 'AST-'.$cat->code.'-'.str_pad((string) (intdiv($i - 1, $assetCategories->count()) + 1), 4, '0', STR_PAD_LEFT), 'asset_category_id' => $cat->id, 'name' => $cat->name.' Kantor '.$i, 'brand' => ['Lenovo', 'HP', 'Dell', 'Asus', 'Cisco'][$i % 5], 'model' => 'Model-'.$i, 'serial_number' => 'SN-DEMO-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT), 'specifications' => 'Spesifikasi standar operasional kantor.', 'purchase_date' => now()->subMonths($i), 'purchase_price' => 3500000 + ($i * 175000), 'warranty_end_date' => now()->addDays(($i - 8) * 30), 'location' => 'Lantai '.(($i % 3) + 1), 'condition' => $i % 7 === 0 ? 'perlu_perawatan' : 'baik', 'status' => 'tersedia', 'notes' => 'Data demonstrasi lokal.']);
            }
        }
        $assets = Asset::get();
        if (AssetAssignment::count() === 0) {
            for ($i = 0; $i < 15; $i++) {
                $asset = $assets[$i];
                $active = $i < 8;
                $assignment = AssetAssignment::create(['asset_id' => $asset->id, 'user_id' => $users[$i % $users->count()]->id, 'assigned_by' => $admin->id, 'assigned_at' => now()->subDays(90 - $i), 'returned_at' => $active ? null : now()->subDays(20 - $i), 'condition_when_assigned' => 'baik', 'condition_when_returned' => $active ? null : ($i % 3 === 0 ? 'perlu_perawatan' : 'baik'), 'notes' => 'Penugasan perangkat kerja demo.']);
                if ($active) {
                    $asset->update(['assigned_user_id' => $assignment->user_id, 'status' => $i % 3 === 0 ? 'dipinjamkan' : 'digunakan']);
                }
            }
        }
        $issues = ['Laptop tidak dapat terhubung ke Wi-Fi', 'Printer tidak dapat mencetak', 'Komputer berjalan lambat', 'Monitor tidak menampilkan gambar', 'Akun pengguna terkunci', 'Tidak dapat mengakses folder sharing', 'Kabel jaringan tidak terdeteksi', 'Aplikasi administrasi tidak dapat dibuka'];
        $statuses = ['baru', 'ditugaskan', 'diproses', 'menunggu_konfirmasi', 'selesai', 'dibatalkan'];
        $priorities = ['rendah', 'sedang', 'tinggi', 'kritis'];
        if (Ticket::count() === 0) {
            for ($i = 1; $i <= 30; $i++) {
                $status = $statuses[($i - 1) % count($statuses)];
                $user = $users[($i - 1) % $users->count()];
                $tech = $status === 'baru' ? null : $technicians[($i - 1) % $technicians->count()];
                $started = in_array($status, ['diproses', 'menunggu_konfirmasi', 'selesai'], true) ? now()->subDays(31 - $i)->addHours(2) : null;
                $resolved = in_array($status, ['menunggu_konfirmasi', 'selesai'], true) ? $started?->copy()->addHours(($i % 8) + 1) : null;
                $ticket = Ticket::create(['ticket_number' => 'TKT-'.now()->format('Ym').'-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT), 'user_id' => $user->id, 'technician_id' => $tech?->id, 'asset_id' => $assets[($i - 1) % $assets->count()]->id, 'ticket_category_id' => $ticketCategories[($i - 1) % $ticketCategories->count()]->id, 'title' => $issues[($i - 1) % count($issues)], 'description' => 'Masalah muncul saat aktivitas kerja dan menghambat proses operasional. Pengguna sudah mencoba memulai ulang perangkat.', 'location' => 'Lantai '.(($i % 3) + 1), 'priority' => $priorities[($i - 1) % count($priorities)], 'status' => $status, 'diagnosis' => $started ? 'Ditemukan gangguan pada konfigurasi atau komponen terkait.' : null, 'solution' => $resolved ? 'Konfigurasi diperbaiki, layanan diuji ulang, dan berfungsi normal.' : null, 'started_at' => $started, 'resolved_at' => $resolved, 'confirmed_at' => $status === 'selesai' ? $resolved?->copy()->addHour() : null, 'created_at' => now()->subDays(31 - $i), 'updated_at' => now()->subDays(max(0, 25 - $i))]);
                TicketHistory::create(['ticket_id' => $ticket->id, 'changed_by' => $user->id, 'action' => 'Tiket dibuat', 'new_status' => 'baru', 'created_at' => $ticket->created_at]);
                if ($tech) {
                    TicketHistory::create(['ticket_id' => $ticket->id, 'changed_by' => $admin->id, 'action' => 'Teknisi ditugaskan', 'old_status' => 'baru', 'new_status' => 'ditugaskan', 'metadata' => ['technician' => $tech->name]]);
                }TicketComment::create(['ticket_id' => $ticket->id, 'user_id' => $user->id, 'comment' => 'Mohon bantuan karena masalah ini menghambat pekerjaan.', 'is_internal' => false]);
                if ($tech) {
                    TicketComment::create(['ticket_id' => $ticket->id, 'user_id' => $tech->id, 'comment' => 'Pemeriksaan awal telah dilakukan dan tindak lanjut sedang diproses.', 'is_internal' => false]);
                }if ($started) {
                    TicketComment::create(['ticket_id' => $ticket->id, 'user_id' => $tech->id, 'comment' => 'Catatan teknis: verifikasi konfigurasi dan log perangkat.', 'is_internal' => true]);
                }
            }
        }
        $tickets = Ticket::get();
        if (AssetRepair::count() === 0) {
            for ($i = 0; $i < 12; $i++) {
                AssetRepair::create(['asset_id' => $assets[$i]->id, 'ticket_id' => $tickets[$i]->id, 'technician_id' => $technicians[$i % $technicians->count()]->id, 'repair_date' => now()->subDays(60 - $i * 4), 'complaint' => $issues[$i % count($issues)], 'diagnosis' => 'Komponen atau konfigurasi memerlukan penyesuaian.', 'repair_action' => 'Pembersihan, konfigurasi ulang, dan pengujian fungsi.', 'replaced_components' => $i % 4 === 0 ? 'Kabel dan konektor' : null, 'repair_cost' => $i % 4 === 0 ? 250000 : 0, 'result' => $i % 5 === 0 ? 'berhasil_sebagian' : 'berhasil', 'notes' => 'Perangkat sudah diuji setelah perbaikan.', 'next_maintenance_date' => now()->addMonths(($i % 3) + 1)]);
            }
        }
        $articleTitles = ['Komputer tidak dapat terhubung ke Wi-Fi', 'Printer terdeteksi tetapi tidak mencetak', 'Windows berjalan lambat', 'Komputer tidak menyala', 'Tidak dapat membuka folder sharing', 'Aplikasi tidak dapat dibuka', 'Cara membersihkan temporary files', 'Cara memeriksa koneksi jaringan dasar', 'Mengatasi akun pengguna terkunci', 'Mengenali email phishing'];
        foreach ($articleTitles as $i => $title) {
            KnowledgeArticle::firstOrCreate(['slug' => Str::slug($title)], ['knowledge_category_id' => $knowledgeCategories[$i % $knowledgeCategories->count()]->id, 'author_id' => $i % 2 ? $technicians[$i % $technicians->count()]->id : $admin->id, 'title' => $title, 'summary' => 'Panduan praktis untuk mendiagnosis dan menyelesaikan masalah '.$title.'.', 'cause' => 'Periksa perubahan terbaru, koneksi, konfigurasi, pesan kesalahan, serta kondisi perangkat.', 'solution_steps' => "1. Catat gejala dan pesan kesalahan.\n2. Periksa koneksi fisik dan konfigurasi.\n3. Mulai ulang layanan atau perangkat dengan aman.\n4. Uji kembali fungsi utama.\n5. Dokumentasikan hasil atau eskalasi ke teknisi.", 'additional_notes' => 'Jangan mengubah konfigurasi sensitif tanpa izin administrator.', 'status' => 'published', 'view_count' => ($i + 1) * 7, 'published_at' => now()->subDays(10 - $i)]);
        }
    }
}
