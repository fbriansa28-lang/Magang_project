<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\QuestionBank;
use App\Models\User;
use App\Services\ChallengeQuestionService;
use Illuminate\Database\Seeder;

/**
 * Bank soal VARIATIF: setiap soal punya konten, kunci, dan hint yang
 * berbeda-beda (8 soal per kombinasi tipe x tingkat = 48 soal), sehingga
 * mudah dicek saat sistem mengambil soal acak ke set challenge:
 *
 * - Kunci PG bervariasi (a/b/c/d), tidak selalu 'a'.
 * - Kunci uraian (flag) berupa kalimat referensi yang berbeda.
 * - Sebagian soal punya hint, sebagian tidak.
 *
 * Seeder juga menyusun ulang set soal challenge contoh "Simulasi Keamanan
 * Dasar" (selama belum ada attempt) agar langsung berisi soal variatif.
 */
class QuestionBankVariedSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@mail.com')->first() ?? User::first();
        if (! $admin) return;

        $category = Category::firstOrCreate(
            ['name' => 'Dasar Keamanan Siber'],
            ['weight_score' => 10, 'point_ambang_batas' => 60, 'is_active' => true]
        );

        $inserted = 0;

        foreach ($this->questions() as $typeValue => $byDifficulty) {
            foreach ($byDifficulty as $difficultyValue => $items) {
                foreach ($items as $item) {
                    $isPg = $typeValue === 'pilihan_ganda';

                    $bank = QuestionBank::updateOrCreate(
                        [
                            'kategori_id' => $category->id,
                            'content' => $item['content'],
                        ],
                        [
                            'question_type' => $typeValue,
                            'difficulty' => $difficultyValue,
                            'flag' => $isPg ? null : $item['flag'],
                            'correct_option' => $isPg ? $item['correct'] : null,
                            'hint' => $item['hint'] ?? null,
                            'explanation' => $item['explanation'] ?? null,
                            'is_active' => true,
                            'created_by' => $admin->id,
                        ]
                    );

                    if ($isPg) {
                        $bank->syncOptions($item['options']);
                    }

                    $inserted++;
                }
            }
        }

        $this->command?->info(sprintf(
            'QuestionBankVariedSeeder: %d soal variatif siap di kategori "%s".',
            $inserted,
            $category->name
        ));

        $this->rebuildExampleChallengeSet();
    }

    /**
     * Susun ulang set soal challenge contoh agar memakai pool variatif.
     * Dilewati bila sudah ada attempt (agar hasil peserta tidak terganggu).
     */
    private function rebuildExampleChallengeSet(): void
    {
        $challenge = Challenge::where('title', 'Simulasi Keamanan Dasar')->first();

        if (! $challenge) {
            return;
        }

        if ($challenge->attempts()->exists()) {
            $this->command?->warn('Challenge contoh dilewati: sudah ada attempt peserta.');

            return;
        }

        $service = app(ChallengeQuestionService::class);

        $service->buildSet($challenge, [
            'easy' => (int) $challenge->questions_easy,
            'medium' => (int) $challenge->questions_medium,
            'hard' => (int) $challenge->questions_hard,
        ]);

        $summary = $service->setSummary($challenge);

        $this->command?->info(sprintf(
            'Set challenge contoh disusun ulang: %d soal, total bobot %s (= 100%%).',
            $summary['total_questions'],
            number_format($summary['total_weight'], 2)
        ));

        // Tampilkan isi set supaya langsung bisa dicek.
        foreach ($challenge->challengeQuestions()->with('question')->get() as $row) {
            $this->command?->line(sprintf(
                '  #%02d [%s/%s, bobot %s] %s',
                $row->order_no,
                $row->type,
                $row->difficulty,
                number_format((float) $row->weight, 2),
                $row->question?->content
            ));
        }
    }

    /**
     * 8 soal per kombinasi (48 total), semuanya berbeda.
     *
     * @return array<string, array<string, array<int, array<string, mixed>>>>
     */
    private function questions(): array
    {
        return [
            'pilihan_ganda' => [
                'easy' => [
                    [
                        'content' => 'Apa kepanjangan dari SQL?',
                        'options' => ['a' => 'Structured Query Language', 'b' => 'Simple Question List', 'c' => 'Secure Query Layer', 'd' => 'System Quality Logic'],
                        'correct' => 'a',
                        'hint' => 'Bahasa query standar untuk database relasional.',
                    ],
                    [
                        'content' => 'Apa fungsi utama firewall pada jaringan?',
                        'options' => ['a' => 'Mempercepat koneksi internet', 'b' => 'Menyaring lalu lintas jaringan berdasarkan aturan', 'c' => 'Menyimpan password pengguna', 'd' => 'Mengompres file yang dikirim'],
                        'correct' => 'b',
                        'hint' => null,
                    ],
                    [
                        'content' => 'Apa yang dimaksud dengan phishing?',
                        'options' => ['a' => 'Teknik menipu korban untuk menyerahkan data rahasia', 'b' => 'Bahasa pemrograman web', 'c' => 'Algoritma enkripsi simetris', 'd' => 'Protokol transfer file'],
                        'correct' => 'a',
                        'hint' => 'Berkaitan dengan penipuan melalui pesan atau email.',
                    ],
                    [
                        'content' => 'Apa kepanjangan HTTPS?',
                        'options' => ['a' => 'High Transfer Text Protocol System', 'b' => 'HyperText Transmission Privacy Suite', 'c' => 'HyperText Transfer Protocol Secure', 'd' => 'Host Transfer Protocol Secure'],
                        'correct' => 'c',
                        'hint' => 'Versi aman dari HTTP.',
                    ],
                    [
                        'content' => 'Tool apa yang populer untuk menangkap paket jaringan?',
                        'options' => ['a' => 'GIMP', 'b' => 'Wireshark', 'c' => 'Notepad', 'd' => 'Excel'],
                        'correct' => 'b',
                        'hint' => 'Namanya berawalan W.',
                    ],
                    [
                        'content' => 'Manakah yang termasuk ciri password kuat?',
                        'options' => ['a' => 'Tanggal lahir', 'b' => 'Nama hewan peliharaan', 'c' => 'Kombinasi huruf besar, kecil, angka, dan simbol', 'd' => '12345678'],
                        'correct' => 'c',
                        'hint' => null,
                    ],
                    [
                        'content' => 'Apa itu malware?',
                        'options' => ['a' => 'Perangkat lunak berbahaya', 'b' => 'Perangkat keras jaringan', 'c' => 'Bahasa pemrograman', 'd' => 'Sistem operasi'],
                        'correct' => 'a',
                        'hint' => null,
                    ],
                    [
                        'content' => 'Apa kepanjangan VPN?',
                        'options' => ['a' => 'Virtual Public Node', 'b' => 'Very Private Network', 'c' => 'Verified Personal Network', 'd' => 'Virtual Private Network'],
                        'correct' => 'd',
                        'hint' => 'Berhubungan dengan jaringan privat.',
                    ],
                ],
                'medium' => [
                    [
                        'content' => 'Serangan SQL Injection menargetkan kelemahan pada bagian mana?',
                        'options' => ['a' => 'Validasi input yang digunakan dalam query database', 'b' => 'Kabel jaringan', 'c' => 'Kapasitas hard disk', 'd' => 'Kecepatan CPU'],
                        'correct' => 'a',
                        'hint' => 'Perhatikan bagaimana input pengguna diproses.',
                    ],
                    [
                        'content' => 'Mengapa password disimpan sebagai hash, bukan enkripsi dua arah?',
                        'options' => ['a' => 'Agar ukuran file lebih kecil', 'b' => 'Karena hashing satu arah sehingga tidak bisa dibalik', 'c' => 'Agar bisa dikirim lewat email', 'd' => 'Supaya mudah dibaca admin'],
                        'correct' => 'b',
                        'hint' => 'Sifat utama hashing.',
                    ],
                    [
                        'content' => 'Apa itu serangan XSS?',
                        'options' => ['a' => 'Menyisipkan skrip berbahaya ke halaman web yang dilihat korban', 'b' => 'Membanjiri server dengan trafik', 'c' => 'Mencuri kabel jaringan', 'd' => 'Mematikan listrik server'],
                        'correct' => 'a',
                        'hint' => 'Targetnya browser korban.',
                    ],
                    [
                        'content' => 'Berapa port default HTTPS?',
                        'options' => ['a' => '21', 'b' => '80', 'c' => '443', 'd' => '3306'],
                        'correct' => 'c',
                        'hint' => null,
                    ],
                    [
                        'content' => 'Dalam CIA triad, aspek Integrity berarti?',
                        'options' => ['a' => 'Data selalu tersedia', 'b' => 'Data tidak diubah tanpa izin', 'c' => 'Data selalu terenkripsi', 'd' => 'Data selalu di-backup'],
                        'correct' => 'b',
                        'hint' => null,
                    ],
                    [
                        'content' => 'Apa itu brute force attack?',
                        'options' => ['a' => 'Mencoba semua kemungkinan kombinasi untuk menebak kredensial', 'b' => 'Mencuri perangkat fisik', 'c' => 'Menipu lewat telepon', 'd' => 'Menyadap kabel jaringan'],
                        'correct' => 'a',
                        'hint' => 'Coba-coba sampai berhasil.',
                    ],
                    [
                        'content' => 'Apa fungsi salt pada hashing password?',
                        'options' => ['a' => 'Menambah nilai acak agar hash unik walau password sama', 'b' => 'Mempercepat proses login', 'c' => 'Mengompres database', 'd' => 'Menghapus password lama'],
                        'correct' => 'a',
                        'hint' => 'Membuat hash tiap pengguna berbeda.',
                    ],
                    [
                        'content' => 'Apa kepanjangan CSRF?',
                        'options' => ['a' => 'Central Secure Random Function', 'b' => 'Cross-Site Request Forgery', 'c' => 'Client Side Rendering Format', 'd' => 'Cyber Security Response Force'],
                        'correct' => 'b',
                        'hint' => null,
                    ],
                ],
                'hard' => [
                    [
                        'content' => 'Kombinasi proteksi apa yang paling efektif memitigasi buffer overflow?',
                        'options' => ['a' => 'ASLR dan stack canary', 'b' => 'Menambah RAM', 'c' => 'Mengganti kabel LAN', 'd' => 'Memperbesar layar'],
                        'correct' => 'a',
                        'hint' => 'Ada pengacakan alamat memori dan penjaga stack.',
                    ],
                    [
                        'content' => 'Serangan man-in-the-middle paling tepat dicegah dengan?',
                        'options' => ['a' => 'Mengganti wallpaper', 'b' => 'TLS dengan validasi sertifikat', 'c' => 'Mematikan firewall', 'd' => 'Menghapus cookie'],
                        'correct' => 'b',
                        'hint' => 'Perhatikan validasi sertifikat server.',
                    ],
                    [
                        'content' => 'Apa itu SSRF?',
                        'options' => ['a' => 'Mencuri session cookie', 'b' => 'Menebak password pengguna', 'c' => 'Memaksa server mengakses resource internal yang seharusnya tidak dijangkau penyerang', 'd' => 'Menyadap jaringan WiFi'],
                        'correct' => 'c',
                        'hint' => 'Server menjadi perantara akses.',
                    ],
                    [
                        'content' => 'Apa kegunaan nonce pada enkripsi?',
                        'options' => ['a' => 'Mempercepat enkripsi', 'b' => 'Mencegah penggunaan ulang keystream sehingga ciphertext unik', 'c' => 'Menyimpan kunci enkripsi', 'd' => 'Mengompres plaintext'],
                        'correct' => 'b',
                        'hint' => 'Nilai yang hanya dipakai sekali.',
                    ],
                    [
                        'content' => 'Apa perbedaan utama IDS dan IPS?',
                        'options' => ['a' => 'IDS hanya mendeteksi, IPS dapat memblokir', 'b' => 'IDS lebih murah', 'c' => 'IPS tidak butuh konfigurasi', 'd' => 'Keduanya sama saja'],
                        'correct' => 'a',
                        'hint' => 'Fokus pada huruf P di IPS.',
                    ],
                    [
                        'content' => 'Apa itu race condition?',
                        'options' => ['a' => 'Perlombaan balap mobil', 'b' => 'Proses booting yang lambat', 'c' => 'Antrean cetak dokumen', 'd' => 'Kondisi saat dua proses mengakses resource bersamaan sehingga hasil tidak terduga'],
                        'correct' => 'd',
                        'hint' => 'Tentang waktu akses yang bersamaan.',
                    ],
                    [
                        'content' => 'Privilege escalation sering memanfaatkan?',
                        'options' => ['a' => 'Misconfiguration seperti file SUID yang salah', 'b' => 'Keyboard rusak', 'c' => 'Monitor mati', 'd' => 'Kabel jaringan putus'],
                        'correct' => 'a',
                        'hint' => 'Cari konfigurasi yang terlalu longgar.',
                    ],
                    [
                        'content' => 'Apa arti zero-day?',
                        'options' => ['a' => 'Serangan yang berlangsung sehari', 'b' => 'Bug yang muncul jam 00.00', 'c' => 'Kerentanan yang belum ada patch-nya dan belum diketahui vendor', 'd' => 'Fitur baru aplikasi'],
                        'correct' => 'c',
                        'hint' => 'Vendor belum mengetahuinya.',
                    ],
                ],
            ],
            'essay' => [
                'easy' => [
                    [
                        'content' => 'Jelaskan apa itu firewall dan tujuannya.',
                        'flag' => 'Firewall adalah sistem keamanan yang menyaring lalu lintas jaringan berdasarkan aturan tertentu untuk melindungi dari akses tidak sah.',
                        'hint' => 'Fokus pada penyaringan lalu lintas.',
                    ],
                    [
                        'content' => 'Jelaskan apa yang dimaksud dengan autentikasi dua faktor.',
                        'flag' => 'Autentikasi dua faktor adalah verifikasi identitas yang menggunakan dua jenis bukti berbeda seperti password dan kode OTP.',
                        'hint' => 'Dua jenis bukti berbeda.',
                    ],
                    [
                        'content' => 'Mengapa pembaruan perangkat lunak penting untuk keamanan?',
                        'flag' => 'Pembaruan perangkat lunak penting karena menambal kerentanan keamanan yang bisa dieksploitasi penyerang.',
                        'hint' => 'Pikirkan tentang penambalan celah.',
                    ],
                    [
                        'content' => 'Jelaskan apa itu enkripsi.',
                        'flag' => 'Enkripsi adalah proses mengubah data menjadi bentuk teracak agar tidak dapat dibaca tanpa kunci dekripsi.',
                        'hint' => 'Tentang menyamarkan data.',
                    ],
                    [
                        'content' => 'Apa fungsi antivirus pada komputer?',
                        'flag' => 'Antivirus berfungsi mendeteksi dan menghapus perangkat lunak berbahaya seperti virus dan malware.',
                        'hint' => null,
                    ],
                    [
                        'content' => 'Jelaskan apa itu backup data.',
                        'flag' => 'Backup data adalah proses menyalin data ke lokasi lain agar dapat dipulihkan jika data asli hilang atau rusak.',
                        'hint' => 'Tentang salinan cadangan.',
                    ],
                    [
                        'content' => 'Apa yang dimaksud dengan cookie pada browser?',
                        'flag' => 'Cookie adalah data kecil yang disimpan browser untuk mengingat informasi sesi pengguna di sebuah situs.',
                        'hint' => null,
                    ],
                    [
                        'content' => 'Mengapa berbagi password dengan orang lain berbahaya?',
                        'flag' => 'Berbagi password berbahaya karena menghilangkan akuntabilitas dan meningkatkan risiko penyalahgunaan akun.',
                        'hint' => null,
                    ],
                ],
                'medium' => [
                    [
                        'content' => 'Jelaskan secara singkat bagaimana SQL Injection terjadi.',
                        'flag' => 'SQL Injection terjadi ketika input pengguna tidak divalidasi sehingga penyerang dapat menyisipkan perintah SQL ke dalam query database.',
                        'hint' => 'Berawal dari input yang tidak divalidasi.',
                    ],
                    [
                        'content' => 'Jelaskan perbedaan HTTP dan HTTPS.',
                        'flag' => 'HTTPS adalah versi HTTP yang menggunakan TLS untuk mengenkripsi komunikasi antara browser dan server sehingga data tidak mudah disadap.',
                        'hint' => 'Tambahan huruf S berarti aman.',
                    ],
                    [
                        'content' => 'Bagaimana serangan XSS dapat merugikan korban?',
                        'flag' => 'XSS merugikan korban karena penyerang dapat mencuri cookie sesi atau menjalankan aksi atas nama korban melalui skrip yang disisipkan.',
                        'hint' => 'Skrip berjalan di browser korban.',
                    ],
                    [
                        'content' => 'Jelaskan prinsip least privilege.',
                        'flag' => 'Prinsip least privilege berarti setiap pengguna atau proses hanya diberi hak akses seminimal mungkin yang diperlukan untuk tugasnya.',
                        'hint' => 'Akses seperlunya saja.',
                    ],
                    [
                        'content' => 'Apa itu social engineering dan berikan contohnya.',
                        'flag' => 'Social engineering adalah manipulasi psikologis untuk mendapatkan informasi rahasia, contohnya telepon yang mengaku dari bank.',
                        'hint' => 'Menipu orang, bukan sistem.',
                    ],
                    [
                        'content' => 'Jelaskan fungsi VPN bagi pengguna internet.',
                        'flag' => 'VPN mengenkripsi koneksi dan menyamarkan alamat IP pengguna sehingga komunikasi lebih aman dan privat.',
                        'hint' => null,
                    ],
                    [
                        'content' => 'Sebutkan ciri-ciri email phishing yang perlu diwaspadai.',
                        'flag' => 'Ciri email phishing antara lain pengirim tidak dikenal, permintaan mendesak, tautan mencurigakan, dan lampiran tidak wajar.',
                        'hint' => 'Perhatikan pengirim dan tautan.',
                    ],
                    [
                        'content' => 'Jelaskan apa itu hashing pada penyimpanan password.',
                        'flag' => 'Hashing mengubah password menjadi nilai unik satu arah sehingga password asli tidak dapat dibaca dari database.',
                        'hint' => 'Satu arah, tidak bisa dibalik.',
                    ],
                ],
                'hard' => [
                    [
                        'content' => 'Jelaskan mekanisme dasar buffer overflow.',
                        'flag' => 'Buffer overflow terjadi ketika data yang ditulis melebihi kapasitas buffer sehingga menimpa memori sekitar termasuk alamat kembali fungsi.',
                        'hint' => 'Tentang data yang melebihi kapasitas.',
                    ],
                    [
                        'content' => 'Jelaskan bagaimana serangan man-in-the-middle bekerja dan cara mencegahnya.',
                        'flag' => 'Penyerang menyadap komunikasi antara dua pihak; pencegahannya menggunakan TLS dan validasi sertifikat yang ketat.',
                        'hint' => 'Ada pihak ketiga di tengah komunikasi.',
                    ],
                    [
                        'content' => 'Jelaskan konsep SSRF dan dampaknya.',
                        'flag' => 'SSRF memaksa server mengakses sumber daya internal yang tidak dapat dijangkau penyerang langsung sehingga data internal bisa bocor.',
                        'hint' => 'Server menjadi perantara akses.',
                    ],
                    [
                        'content' => 'Jelaskan secara singkat cara kerja enkripsi AES.',
                        'flag' => 'AES mengenkripsi data per blok menggunakan kunci simetris melalui beberapa putaran substitusi dan permutasi.',
                        'hint' => 'Enkripsi blok dengan kunci simetris.',
                    ],
                    [
                        'content' => 'Bagaimana race condition dapat dieksploitasi penyerang?',
                        'flag' => 'Penyerang memanfaatkan celah waktu antara pemeriksaan dan penggunaan resource sehingga aksi ilegal lolos validasi.',
                        'hint' => 'Manfaatkan celah waktu.',
                    ],
                    [
                        'content' => 'Jelaskan langkah umum privilege escalation pada Linux.',
                        'flag' => 'Penyerang mencari misconfiguration seperti file SUID, sudo tanpa password, atau kernel usang lalu memanfaatkannya untuk menjadi root.',
                        'hint' => 'Cari konfigurasi yang terlalu longgar.',
                    ],
                    [
                        'content' => 'Apa itu supply chain attack?',
                        'flag' => 'Supply chain attack adalah serangan melalui pihak ketiga seperti library atau vendor terpercaya yang disusupi penyerang.',
                        'hint' => 'Serangan lewat pihak ketiga.',
                    ],
                    [
                        'content' => 'Jelaskan konsep zero trust pada keamanan jaringan.',
                        'flag' => 'Zero trust tidak pernah mempercayai pengguna atau perangkat secara default dan selalu memverifikasi setiap akses.',
                        'hint' => 'Jangan percaya, verifikasi.',
                    ],
                ],
            ],
        ];
    }
}
