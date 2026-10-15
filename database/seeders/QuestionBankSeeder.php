<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\QuestionBank;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@mail.com')->first() ?? User::first();
        if (!$admin) return;

        $data = [
            'Web Exploitation' => [
                ['content' => 'Bypass login menggunakan SQL Injection.', 'type' => 'essay', 'flag' => "' OR '1'='1", 'difficulty' => 'easy', 'hint' => 'Perhatikan validasi input username.'],
                ['content' => 'Payload XSS untuk menjalankan JavaScript pada halaman pencarian.', 'type' => 'pilihan_ganda', 'correct' => 'a', 'options' => ['a' => '<script>alert(1)</script>', 'b' => 'SELECT * FROM users', 'c' => '127.0.0.1', 'd' => 'chmod 777'], 'difficulty' => 'easy', 'hint' => 'Gunakan tag script.'],
                ['content' => 'Operator shell untuk menyisipkan perintah tambahan.', 'type' => 'pilihan_ganda', 'correct' => 'b', 'options' => ['a' => '|', 'b' => ';', 'c' => '&&&', 'd' => '::'], 'difficulty' => 'medium', 'hint' => 'Gunakan pemisah perintah.'],
            ],
            'Cryptography' => [
                ['content' => 'Algoritma klasik yang menggunakan pergeseran alfabet.', 'type' => 'pilihan_ganda', 'correct' => 'c', 'options' => ['a' => 'RSA', 'b' => 'AES', 'c' => 'Caesar', 'd' => 'SHA-256'], 'difficulty' => 'easy', 'hint' => 'Namanya berasal dari seorang kaisar.'],
                ['content' => 'Jelaskan mengapa penggunaan nonce penting pada enkripsi modern.', 'type' => 'essay', 'flag' => 'Nonce mencegah penggunaan ulang keystream dan ciphertext yang sama.', 'difficulty' => 'medium', 'hint' => 'Pikirkan penggunaan ulang keystream.'],
            ],
            'Forensics' => [
                ['content' => 'Tool untuk membaca metadata file.', 'type' => 'pilihan_ganda', 'correct' => 'a', 'options' => ['a' => 'exiftool', 'b' => 'nmap', 'c' => 'gdb', 'd' => 'sqlmap'], 'difficulty' => 'easy', 'hint' => 'Tool ini sering digunakan pada file gambar.'],
                ['content' => 'Jelaskan fungsi strings dalam analisis binary.', 'type' => 'essay', 'flag' => 'Strings menampilkan rangkaian karakter yang dapat dibaca dari file binary.', 'difficulty' => 'medium', 'hint' => 'Cari teks yang dapat dibaca.'],
            ],
            'Reverse Engineering' => [
                ['content' => 'Tool debugger untuk menganalisis binary Linux.', 'type' => 'pilihan_ganda', 'correct' => 'd', 'options' => ['a' => 'Wireshark', 'b' => 'Burp Suite', 'c' => 'ExifTool', 'd' => 'GDB'], 'difficulty' => 'easy', 'hint' => 'Debugger GNU.'],
            ],
            'Network Security' => [
                ['content' => 'Tool untuk menangkap dan menganalisis paket jaringan.', 'type' => 'pilihan_ganda', 'correct' => 'b', 'options' => ['a' => 'GDB', 'b' => 'Wireshark', 'c' => 'ExifTool', 'd' => 'John'], 'difficulty' => 'easy', 'hint' => 'Gunakan packet analyzer.'],
            ],
            'Binary Exploitation' => [
                ['content' => 'Teknik untuk menentukan offset pada buffer overflow.', 'type' => 'essay', 'flag' => 'Gunakan cyclic pattern untuk menemukan offset return address.', 'difficulty' => 'medium', 'hint' => 'Gunakan pattern cyclic.'],
            ],
        ];

        foreach ($data as $categoryName => $questions) {
            $category = Category::where('name', $categoryName)->first();
            if (!$category) continue;

            foreach ($questions as $question) {
                $bank = QuestionBank::updateOrCreate(
                    ['kategori_id' => $category->id, 'content' => $question['content']],
                    [
                        'question_type' => $question['type'],
                        'flag' => $question['flag'] ?? null,
                        'correct_option' => $question['correct'] ?? null,
                        'difficulty' => $question['difficulty'],
                        'hint' => $question['hint'] ?? null,
                        'explanation' => null,
                        'is_active' => true,
                        'created_by' => $admin->id,
                    ]
                );

                if ($question['type'] === 'pilihan_ganda') {
                    $bank->syncOptions($question['options']);
                }
            }
        }
    }
}
