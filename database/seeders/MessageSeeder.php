<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'name' => 'Budi Hartono',
                'email' => 'budi.hartono@email.com',
                'phone' => '+62 813 1234567',
                'company' => 'PT Maju Jaya',
                'subject' => 'Konsultasi Pembangunan Gudang',
                'message' => 'Selamat pagi, kami tertarik untuk membangun gudang dengan luas 1500 m² di area Cibitung. Bisa tolong informasikan estimasi biaya dan waktu pengerjaan? Terima kasih.',
                'type' => 'inquiry',
                'is_read' => true,
                'is_replied' => true,
                'read_at' => now()->subDays(2),
                'replied_at' => now()->subDays(1),
                'replied_by' => 3, // Junaidi
            ],
            [
                'name' => 'Junaidi',
                'email' => 'junaidi01091983@gmail.com',
                'phone' => '+62 814 5678901',
                'subject' => 'Re: Konsultasi Pembangunan Gudang',
                'message' => 'Selamat siang Pak Budi, terima kasih atas inquirynya. Untuk pembangunan gudang 1500 m² di Cibitung, estimasi biaya sekitar Rp 4.5-6 miliar dengan waktu pengerjaan 6-8 bulan. Kami bisa schedule site survey untuk memberikan quotation yang lebih akurat. Bisa koordinasi untuk jadwal yang sesuai?',
                'type' => 'reply',
                'parent_id' => 1,
                'user_id' => 3,
                'is_read' => true,
                'read_at' => now()->subDays(1),
            ],
            [
                'name' => 'Sari Dewi',
                'email' => 'sari.dewi@konstruksi.com',
                'phone' => '+62 821 2345678',
                'company' => 'CV Bangun Sejahtera',
                'subject' => 'Kerjasama Subkontraktor',
                'message' => 'Kepada Tim CV Usaha Prima Lestari, kami CV Bangun Sejahtera bergerak di bidang konstruksi dan tertarik untuk melakukan kerjasama sebagai subkontraktor. Kami memiliki spesialisasi di pekerjaan finishing dan MEP. Mohon informasi prosedur untuk menjadi partner kerja. Terima kasih.',
                'type' => 'partnership',
                'is_read' => true,
                'read_at' => now()->subDays(3),
            ],
            [
                'name' => 'Indra Kusuma',
                'email' => 'indra.kusuma@gmail.com',
                'phone' => '+62 812 3456789',
                'subject' => 'Pertanyaan Tentang Sertifikasi',
                'message' => 'Halo, saya mahasiswa teknik sipil yang sedang melakukan penelitian tentang implementasi ISO 9001 di perusahaan konstruksi. Apakah bisa saya mendapatkan informasi lebih lanjut tentang pengalaman CV UPL dalam implementasi sistem manajemen mutu? Terima kasih.',
                'type' => 'inquiry',
                'is_read' => false,
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@propertindo.com',
                'phone' => '+62 856 4567890',
                'company' => 'PT Propertindo Makmur',
                'subject' => 'Request Portofolio Proyek Apartemen',
                'message' => 'Selamat siang, kami sedang mencari kontraktor untuk proyek apartemen 15 lantai di Jakarta Selatan. Bisakah kami mendapatkan portfolio proyek apartemen yang pernah dikerjakan beserta referensi klien? Budget proyek sekitar 80 miliar.',
                'type' => 'inquiry',
                'is_read' => true,
                'is_replied' => false,
                'read_at' => now()->subHours(8),
            ],
            [
                'name' => 'Bambang Sutrisno',
                'email' => 'bambang.sutrisno@supplier.com',
                'phone' => '+62 817 5678901',
                'company' => 'PT Material Konstruksi Utama',
                'subject' => 'Penawaran Kerjasama Supply Material',
                'message' => 'Kepada Yth. Procurement Manager CV UPL, kami PT Material Konstruksi Utama ingin menawarkan kerjasama supply material konstruksi dengan harga kompetitif dan kualitas terjamin. Attached adalah company profile dan price list kami.',
                'type' => 'supplier_offer',
                'is_read' => true,
                'read_at' => now()->subDays(1),
            ],
            [
                'name' => 'Fitri Handayani',
                'email' => 'fitri.handayani@developer.co.id',
                'phone' => '+62 822 6789012',
                'company' => 'Handayani Property Developer',
                'subject' => 'Urgent: Perbaikan Struktur Bangunan',
                'message' => 'Selamat pagi, kami memiliki bangunan komersial yang mengalami masalah pada struktur lantai 3. Kondisinya cukup urgent dan perlu penanganan segera. Apakah tim UPL bisa melakukan assessment dan emergency repair? Mohon response secepatnya.',
                'type' => 'emergency',
                'is_read' => true,
                'is_replied' => true,
                'read_at' => now()->subHours(4),
                'replied_at' => now()->subHours(2),
                'replied_by' => 2, // Robinson
            ],
            [
                'name' => 'Robinson Totong',
                'email' => 'robinsonjuventino@gmail.com',
                'phone' => '+62 813 4567890',
                'subject' => 'Re: Urgent: Perbaikan Struktur Bangunan',
                'message' => 'Selamat pagi Bu Fitri, kami sudah menerima laporan urgent repair untuk struktur bangunan. Tim emergency response kami akan ke lokasi hari ini jam 14:00 untuk assessment. Mohon koordinasi dengan security untuk akses ke lokasi. Contact person: Ahmad Fauzi (0818-9012345).',
                'type' => 'reply',
                'parent_id' => 7,
                'user_id' => 2,
                'is_read' => true,
                'read_at' => now()->subHours(1),
            ],
            [
                'name' => 'Teguh Prasetyo',
                'email' => 'teguh.prasetyo@owner.com',
                'phone' => '+62 815 7890123',
                'subject' => 'Feedback Positif Proyek Renovasi',
                'message' => 'Kepada seluruh tim CV UPL, saya sangat puas dengan hasil renovasi rumah saya. Pekerjaan rapi, tepat waktu, dan sesuai budget. Tim lapangan sangat profesional dan komunikatif. Saya akan merekomendasikan CV UPL kepada rekan-rekan lainnya. Terima kasih!',
                'type' => 'feedback',
                'is_read' => true,
                'read_at' => now()->subDays(1),
            ],
            [
                'name' => 'Linda Wati',
                'email' => 'linda.wati@karir.com',
                'phone' => '+62 818 8901234',
                'subject' => 'Lamaran Kerja - Site Engineer',
                'message' => 'Selamat pagi HRD CV Usaha Prima Lestari, saya Linda Wati, lulusan Teknik Sipil dengan pengalaman 3 tahun sebagai site engineer. Saya tertarik untuk bergabung dengan tim UPL. Terlampir CV dan portofolio saya. Mohon informasi posisi yang tersedia.',
                'type' => 'job_application',
                'is_read' => false,
            ],
            [
                'name' => 'Dr. Susanto',
                'email' => 'dr.susanto@rumahsakit.com',
                'phone' => '+62 819 9012345',
                'company' => 'RS Sehat Sentosa',
                'subject' => 'Kepuasan Atas Layanan Renovasi Medical Facility',
                'message' => 'Kepada Management CV UPL, atas nama RS Sehat Sentosa, saya ingin menyampaikan apresiasi tertinggi atas profesionalisme tim dalam menangani renovasi fasilitas medis kami. Pekerjaan dilakukan dengan standar medis yang ketat tanpa mengganggu operasional rumah sakit. Excellent work!',
                'type' => 'feedback',
                'project_id' => 6, // Linked to hospital renovation project
                'is_read' => true,
                'read_at' => now()->subDays(2),
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.wijaya@media.com',
                'phone' => '+62 821 0123456',
                'company' => 'Konstruksi Media Indonesia',
                'subject' => 'Request Interview untuk Media Coverage',
                'message' => 'Selamat siang, saya Andi Wijaya dari Konstruksi Media Indonesia. Kami tertarik untuk melakukan feature story tentang perkembangan industri konstruksi dan ingin mewawancarai pimpinan CV UPL. Apakah memungkinkan untuk mengatur jadwal interview?',
                'type' => 'media_inquiry',
                'is_read' => true,
                'read_at' => now()->subHours(6),
            ],
        ];

        foreach ($messages as $message) {
            DB::table('messages')->insert(array_merge($message, [
                'created_at' => $message['parent_id'] ?? false ? 
                    now()->subDays(rand(0, 2)) : 
                    now()->subDays(rand(1, 15)),
                'updated_at' => now()->subHours(rand(1, 24)),
            ]));
        }
    }
}