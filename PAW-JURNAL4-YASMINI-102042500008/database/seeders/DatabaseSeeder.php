<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // ===================== TODO 8 =====================

        // 6. Mahasiswa tambahan
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'siti@celoe.test'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // Mahasiswa kedua juga terdaftar di mata kuliah pertama
        $course1->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        // 7. Mata kuliah tambahan dengan semester & SKS berbeda
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF202'],
            [
                'nama_mk' => 'Basis Data',
                'deskripsi' => 'Perancangan basis data relasional, normalisasi, SQL, dan transaksi.',
                'sks' => 3,
                'semester' => 'Genap 2025/2026',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF101'],
            [
                'nama_mk' => 'Algoritma dan Pemrograman',
                'deskripsi' => 'Dasar logika pemrograman, struktur kontrol, array, dan fungsi.',
                'sks' => 2,
                'semester' => 'Ganjil 2025/2026',
                'dosen_id' => $dosen->id,
            ]
        );

        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 8. Tugas perkuliahan
        $tugas1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 1: Implementasi Middleware & Routing'],
            [
                'deskripsi' => 'Buat aplikasi Laravel sederhana yang menerapkan middleware auth dan role pada route.',
                'deadline' => now()->addDays(7),
            ]
        );

        $tugas2 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 2: Export Data ke CSV'],
            [
                'deskripsi' => 'Jelaskan cara kerja response()->streamDownload() dan fputcsv() di Laravel.',
                'deadline' => now()->addDays(14),
            ]
        );

        // 9. Contoh pengumpulan tugas (satu sudah dinilai, satu belum)
        Submission::updateOrCreate(
            ['assignment_id' => $tugas1->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Saya membuat RoleMiddleware dan mendaftarkannya sebagai alias role di bootstrap/app.php, lalu membungkus route dengan middleware auth dan role:dosen.',
                'submitted_at' => now()->subDay(),
                'nilai' => 85,
                'catatan_dosen' => 'Penjelasan sudah baik dan lengkap.',
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $tugas1->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'Middleware digunakan untuk menyaring request sebelum sampai ke controller.',
                'submitted_at' => now(),
            ]
        );
    }
}