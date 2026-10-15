<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Materi;
use App\Models\Simulasi;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk "Manajemen Learning".
 * Setiap kategori challenge (Category) diisi TEPAT 3 materi
 * pembelajaran (LearningMaterial), masing-masing dengan 2 soal pilihan ganda.
 */
class LearningMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@mail.com')->first();

        if (! $admin) {
            $admin = User::first();
        }

        if (! $admin) {
            return;
        }

        // Kunci array = nama Category (harus sama persis dengan CategorySeeder)
        $data = [
            'Web Exploitation' => [
                [
                    'title'       => 'Dasar SQL Injection',
                    'description' => 'Memahami bagaimana celah SQL Injection terjadi dan cara mencegahnya.',
                    'level'       => 'easy',
                    'content'     => "SQL Injection adalah teknik serangan yang memanfaatkan celah pada validasi input aplikasi, dengan cara "
                        . "menyisipkan (inject) perintah SQL berbahaya ke dalam query yang dijalankan oleh aplikasi.\n\n"
                        . "Contoh input berbahaya pada form login: ' OR '1'='1. Jika query dibangun dengan menggabungkan string secara langsung "
                        . "tanpa sanitasi, input tersebut dapat mengubah logika query sehingga autentikasi bisa dilewati.\n\n"
                        . "Cara mencegah SQL Injection antara lain:\n"
                        . "1. Gunakan Prepared Statement / Parameterized Query.\n"
                        . "2. Validasi dan sanitasi seluruh input pengguna.\n"
                        . "3. Terapkan prinsip least privilege pada akun database.",
                    'questions'   => [
                        [
                            'question'       => 'Teknik untuk mencegah SQL Injection yang paling direkomendasikan adalah...',
                            'option_a'       => 'Menyembunyikan pesan error',
                            'option_b'       => 'Menggunakan prepared statement',
                            'option_c'       => 'Mengganti nama tabel database',
                            'option_d'       => 'Menonaktifkan JavaScript',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => "Payload ' OR '1'='1 biasa digunakan untuk menyerang fitur apa?",
                            'option_a'       => 'Upload file',
                            'option_b'       => 'Form login',
                            'option_c'       => 'Reset password via email',
                            'option_d'       => 'Rate limiting API',
                            'correct_option' => 'b',
                        ],
                    ],
                ],
                [
                    'title'       => 'Cross-Site Scripting (XSS) Fundamentals',
                    'description' => 'Memahami jenis-jenis XSS dan bagaimana payload JavaScript bisa dieksekusi di browser korban.',
                    'level'       => 'easy',
                    'content'     => "Cross-Site Scripting (XSS) adalah celah keamanan yang memungkinkan penyerang menyisipkan script "
                        . "(biasanya JavaScript) ke dalam halaman web yang kemudian dieksekusi di browser pengguna lain.\n\n"
                        . "Terdapat tiga jenis utama XSS:\n"
                        . "1. Reflected XSS - payload dikirim lewat request (misal parameter URL) dan langsung dipantulkan di response.\n"
                        . "2. Stored XSS - payload tersimpan di database dan dieksekusi setiap kali data tersebut ditampilkan.\n"
                        . "3. DOM-based XSS - payload dieksekusi murni lewat manipulasi DOM di sisi client.\n\n"
                        . "Cara mencegah XSS antara lain melakukan output encoding, menerapkan Content Security Policy (CSP), "
                        . "serta memvalidasi dan membersihkan input pengguna.",
                    'questions'   => [
                        [
                            'question'       => 'Jenis XSS yang payload-nya tersimpan permanen di database disebut...',
                            'option_a'       => 'Reflected XSS',
                            'option_b'       => 'Stored XSS',
                            'option_c'       => 'DOM-based XSS',
                            'option_d'       => 'Blind SQL Injection',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Mekanisme browser yang dapat membatasi sumber script yang boleh dijalankan pada sebuah halaman disebut...',
                            'option_a'       => 'CORS',
                            'option_b'       => 'CSRF Token',
                            'option_c'       => 'Content Security Policy (CSP)',
                            'option_d'       => 'Same-Origin Policy saja',
                            'correct_option' => 'c',
                        ],
                    ],
                ],
                [
                    'title'       => 'Keamanan Upload File & Command Injection',
                    'description' => 'Memahami risiko fitur upload file dan celah command injection pada aplikasi web.',
                    'level'       => 'medium',
                    'content'     => "Fitur upload file yang tidak divalidasi dengan benar dapat dimanfaatkan penyerang untuk mengunggah "
                        . "file berbahaya (misal web shell) yang kemudian dieksekusi oleh server.\n\n"
                        . "Command Injection terjadi ketika aplikasi menjalankan perintah sistem operasi menggunakan input pengguna "
                        . "tanpa validasi, sehingga penyerang bisa menyisipkan perintah tambahan menggunakan operator shell seperti "
                        . "`;`, `&&`, atau `|`.\n\n"
                        . "Praktik mitigasi yang disarankan:\n"
                        . "1. Validasi ekstensi & tipe MIME file yang diunggah, serta simpan di luar direktori yang bisa dieksekusi.\n"
                        . "2. Hindari memanggil perintah shell langsung dari input pengguna; gunakan API/library yang aman.\n"
                        . "3. Terapkan whitelist karakter yang diperbolehkan pada input.",
                    'questions'   => [
                        [
                            'question'       => 'Salah satu cara aman menangani upload file adalah...',
                            'option_a'       => 'Mengizinkan semua ekstensi file',
                            'option_b'       => 'Menyimpan file di direktori yang bisa dieksekusi web server',
                            'option_c'       => 'Memvalidasi ekstensi dan tipe MIME file',
                            'option_d'       => 'Mempercayai nama file dari client sepenuhnya',
                            'correct_option' => 'c',
                        ],
                        [
                            'question'       => 'Operator shell yang sering dimanfaatkan pada serangan command injection adalah...',
                            'option_a'       => '== dan !=',
                            'option_b'       => '; dan &&',
                            'option_c'       => '<< dan >>',
                            'option_d'       => '// dan /* */',
                            'correct_option' => 'b',
                        ],
                    ],
                ],
            ],

            'Cryptography' => [
                [
                    'title'       => 'Pengantar Kriptografi',
                    'description' => 'Konsep dasar enkripsi, dekripsi, dan penggunaannya dalam keamanan data.',
                    'level'       => 'easy',
                    'content'     => "Kriptografi adalah ilmu yang mempelajari teknik menyembunyikan makna suatu pesan agar hanya pihak yang "
                        . "berwenang yang dapat membacanya. Terdapat dua jenis utama kriptografi:\n\n"
                        . "1. Symmetric Encryption - menggunakan satu kunci yang sama untuk enkripsi dan dekripsi (contoh: AES, DES).\n"
                        . "2. Asymmetric Encryption - menggunakan sepasang kunci publik dan privat (contoh: RSA).\n\n"
                        . "Selain itu terdapat teknik encoding seperti Base64 yang bukan merupakan enkripsi, karena tidak menggunakan kunci "
                        . "rahasia dan mudah dikembalikan ke bentuk semula (reversible tanpa key).",
                    'questions'   => [
                        [
                            'question'       => 'Algoritma yang menggunakan kunci publik dan privat disebut...',
                            'option_a'       => 'Symmetric Encryption',
                            'option_b'       => 'Hashing',
                            'option_c'       => 'Asymmetric Encryption',
                            'option_d'       => 'Base64 Encoding',
                            'correct_option' => 'c',
                        ],
                        [
                            'question'       => 'Manakah pernyataan yang benar tentang Base64?',
                            'option_a'       => 'Base64 adalah metode enkripsi yang sangat aman',
                            'option_b'       => 'Base64 adalah encoding, bukan enkripsi',
                            'option_c'       => 'Base64 membutuhkan kunci privat',
                            'option_d'       => 'Base64 tidak dapat di-decode kembali',
                            'correct_option' => 'b',
                        ],
                    ],
                ],
                [
                    'title'       => 'Cipher Klasik: Caesar & Vigenere',
                    'description' => 'Mempelajari cara kerja cipher klasik serta teknik dasar untuk memecahkannya (cryptanalysis).',
                    'level'       => 'easy',
                    'content'     => "Caesar cipher adalah salah satu teknik enkripsi tertua, bekerja dengan menggeser setiap huruf pada "
                        . "pesan sejauh N posisi pada alfabet. Karena hanya ada 25 kemungkinan pergeseran, cipher ini sangat rentan "
                        . "terhadap brute force.\n\n"
                        . "Vigenere cipher merupakan pengembangan dari Caesar cipher yang menggunakan sebuah kata kunci (keyword) "
                        . "sehingga pergeseran huruf berbeda-beda di sepanjang pesan. Namun cipher ini tetap bisa dipecahkan menggunakan "
                        . "analisis frekuensi huruf dan teknik Kasiski examination untuk menemukan panjang kata kunci.",
                    'questions'   => [
                        [
                            'question'       => 'Mengapa Caesar cipher sangat mudah dipecahkan?',
                            'option_a'       => 'Karena menggunakan kunci publik',
                            'option_b'       => 'Karena hanya ada 25 kemungkinan pergeseran',
                            'option_c'       => 'Karena tidak menggunakan huruf alfabet',
                            'option_d'       => 'Karena pesan selalu berupa angka',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Teknik untuk memperkirakan panjang keyword pada Vigenere cipher disebut...',
                            'option_a'       => 'Kasiski Examination',
                            'option_b'       => 'Cube Root Attack',
                            'option_c'       => 'Man-in-the-Middle',
                            'option_d'       => 'Rainbow Table',
                            'correct_option' => 'a',
                        ],
                    ],
                ],
                [
                    'title'       => 'RSA dan Kriptografi Kunci Publik',
                    'description' => 'Memahami prinsip dasar RSA serta kesalahan implementasi umum yang membuatnya rentan diserang.',
                    'level'       => 'medium',
                    'content'     => "RSA adalah algoritma kriptografi asimetris yang keamanannya bergantung pada sulitnya memfaktorkan "
                        . "bilangan bulat besar hasil perkalian dua bilangan prima. RSA menggunakan sepasang kunci: kunci publik untuk "
                        . "enkripsi dan kunci privat untuk dekripsi.\n\n"
                        . "Beberapa kesalahan implementasi yang umum membuat RSA rentan diserang antara lain:\n"
                        . "1. Menggunakan public exponent (e) yang terlalu kecil tanpa padding yang benar.\n"
                        . "2. Menggunakan bilangan prima yang terlalu kecil atau modulus (n) yang dipakai bersama antar pengguna.\n"
                        . "3. Tidak menerapkan padding standar seperti OAEP pada proses enkripsi.",
                    'questions'   => [
                        [
                            'question'       => 'Keamanan RSA bergantung pada sulitnya...',
                            'option_a'       => 'Menebak password pengguna',
                            'option_b'       => 'Memfaktorkan bilangan bulat besar',
                            'option_c'       => 'Membalik fungsi hash',
                            'option_d'       => 'Menyusun ROP chain',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Risiko menggunakan public exponent (e) yang terlalu kecil tanpa padding yang benar adalah...',
                            'option_a'       => 'Proses enkripsi menjadi lebih lambat',
                            'option_b'       => 'Ciphertext bisa didekripsi lewat cube root attack',
                            'option_c'       => 'Kunci privat otomatis terungkap di header',
                            'option_d'       => 'Tidak ada risiko sama sekali',
                            'correct_option' => 'b',
                        ],
                    ],
                ],
            ],

            'Forensics' => [
                [
                    'title'       => 'Pengantar Digital Forensics',
                    'description' => 'Memahami konsep dasar dan tahapan investigasi digital forensics.',
                    'level'       => 'easy',
                    'content'     => "Digital forensics adalah proses mengidentifikasi, mengumpulkan, menganalisis, dan menyajikan bukti "
                        . "digital dengan cara yang dapat dipertanggungjawabkan secara hukum maupun teknis.\n\n"
                        . "Tahapan umum dalam investigasi digital forensics meliputi:\n"
                        . "1. Identification - menentukan sumber bukti digital yang relevan.\n"
                        . "2. Preservation - menjaga integritas bukti agar tidak berubah (misal dengan hashing).\n"
                        . "3. Analysis - menganalisis data untuk menemukan petunjuk atau bukti serangan.\n"
                        . "4. Reporting - menyusun laporan hasil investigasi secara sistematis.",
                    'questions'   => [
                        [
                            'question'       => 'Tahapan yang bertujuan menjaga integritas bukti digital agar tidak berubah disebut...',
                            'option_a'       => 'Identification',
                            'option_b'       => 'Preservation',
                            'option_c'       => 'Analysis',
                            'option_d'       => 'Reporting',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Teknik yang umum digunakan untuk memastikan sebuah file bukti tidak berubah adalah...',
                            'option_a'       => 'Kompresi file',
                            'option_b'       => 'Enkripsi AES',
                            'option_c'       => 'Hashing (misal MD5/SHA256)',
                            'option_d'       => 'Mengganti nama file',
                            'correct_option' => 'c',
                        ],
                    ],
                ],
                [
                    'title'       => 'Analisis Metadata & Steganografi',
                    'description' => 'Mempelajari cara menggali informasi tersembunyi dari metadata file dan teknik steganografi.',
                    'level'       => 'easy',
                    'content'     => "Metadata adalah data tentang data, misalnya informasi kamera, lokasi, dan waktu pengambilan yang "
                        . "tersimpan di dalam file gambar (EXIF). Metadata ini sering menjadi sumber petunjuk penting dalam investigasi.\n\n"
                        . "Steganografi adalah teknik menyembunyikan pesan di dalam media lain (gambar, audio, video) sehingga "
                        . "keberadaan pesan tersebut tidak terlihat secara kasat mata. Salah satu teknik populer adalah LSB "
                        . "(Least Significant Bit), yaitu menyisipkan data pada bit paling tidak signifikan dari setiap pixel gambar.",
                    'questions'   => [
                        [
                            'question'       => 'Informasi kamera dan lokasi pada file gambar biasanya tersimpan pada...',
                            'option_a'       => 'Header HTTP',
                            'option_b'       => 'Metadata EXIF',
                            'option_c'       => 'Cookie browser',
                            'option_d'       => 'Log server',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Teknik menyisipkan data pada bit paling tidak signifikan dari pixel gambar disebut...',
                            'option_a'       => 'LSB Steganography',
                            'option_b'       => 'Base64 Encoding',
                            'option_c'       => 'ARP Spoofing',
                            'option_d'       => 'ROP Chaining',
                            'correct_option' => 'a',
                        ],
                    ],
                ],
                [
                    'title'       => 'Investigasi Trafik Jaringan (PCAP)',
                    'description' => 'Mempelajari dasar analisis file capture jaringan untuk menemukan aktivitas mencurigakan.',
                    'level'       => 'medium',
                    'content'     => "File PCAP menyimpan rekaman seluruh paket data yang melewati sebuah jaringan pada rentang waktu "
                        . "tertentu. Analis forensik dapat menggunakan tool seperti Wireshark untuk membuka dan menelusuri isi file ini.\n\n"
                        . "Beberapa hal yang biasa dicari saat investigasi PCAP:\n"
                        . "1. Kredensial atau data sensitif yang dikirim lewat protokol plaintext (HTTP, FTP, Telnet).\n"
                        . "2. Pola trafik mencurigakan seperti request beruntun ke satu domain (indikasi DNS tunneling atau C2).\n"
                        . "3. Sesi TCP yang bisa direkonstruksi ulang menggunakan fitur 'Follow TCP Stream'.",
                    'questions'   => [
                        [
                            'question'       => 'Fitur pada Wireshark yang berguna untuk merekonstruksi ulang isi sebuah sesi TCP adalah...',
                            'option_a'       => 'Follow TCP Stream',
                            'option_b'       => 'Export Objects',
                            'option_c'       => 'IO Graph',
                            'option_d'       => 'Protocol Hierarchy',
                            'correct_option' => 'a',
                        ],
                        [
                            'question'       => 'Protokol berikut ini yang rentan membocorkan kredensial secara plaintext adalah...',
                            'option_a'       => 'HTTPS',
                            'option_b'       => 'SSH',
                            'option_c'       => 'FTP',
                            'option_d'       => 'SFTP',
                            'correct_option' => 'c',
                        ],
                    ],
                ],
            ],

            'Reverse Engineering' => [
                [
                    'title'       => 'Pengantar Reverse Engineering',
                    'description' => 'Memahami konsep dasar reverse engineering dan tujuan penggunaannya dalam keamanan siber.',
                    'level'       => 'easy',
                    'content'     => "Reverse engineering adalah proses menganalisis sebuah program atau sistem untuk memahami cara "
                        . "kerjanya tanpa memiliki source code aslinya. Dalam konteks keamanan siber, reverse engineering sering "
                        . "digunakan untuk menganalisis malware, menemukan celah keamanan, atau memecahkan mekanisme lisensi/proteksi.\n\n"
                        . "Dua pendekatan utama dalam reverse engineering:\n"
                        . "1. Static Analysis - menganalisis binary tanpa menjalankannya (misal lewat disassembler).\n"
                        . "2. Dynamic Analysis - menjalankan binary sambil diawasi menggunakan debugger untuk memahami perilakunya.",
                    'questions'   => [
                        [
                            'question'       => 'Analisis binary yang dilakukan tanpa menjalankan programnya disebut...',
                            'option_a'       => 'Dynamic Analysis',
                            'option_b'       => 'Static Analysis',
                            'option_c'       => 'Live Forensics',
                            'option_d'       => 'Fuzzing',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Salah satu tujuan umum reverse engineering dalam keamanan siber adalah...',
                            'option_a'       => 'Membuat website baru',
                            'option_b'       => 'Menganalisis perilaku malware',
                            'option_c'       => 'Mengelola basis data',
                            'option_d'       => 'Mendesain antarmuka pengguna',
                            'correct_option' => 'b',
                        ],
                    ],
                ],
                [
                    'title'       => 'Assembly & Disassembly Dasar',
                    'description' => 'Mengenal instruksi assembly dasar dan cara membaca hasil disassembly sebuah binary.',
                    'level'       => 'medium',
                    'content'     => "Assembly adalah bahasa tingkat rendah yang merepresentasikan instruksi yang dijalankan langsung "
                        . "oleh prosesor. Untuk membaca sebuah binary, reverse engineer biasanya menggunakan disassembler yang "
                        . "mengubah kode mesin menjadi instruksi assembly yang lebih mudah dibaca.\n\n"
                        . "Beberapa instruksi dasar yang sering ditemui:\n"
                        . "- `mov` untuk memindahkan data antar register/memori.\n"
                        . "- `cmp` untuk membandingkan dua nilai.\n"
                        . "- `je` / `jne` untuk melompat berdasarkan hasil perbandingan (conditional jump).\n"
                        . "- `call` dan `ret` untuk memanggil dan kembali dari sebuah fungsi.",
                    'questions'   => [
                        [
                            'question'       => 'Instruksi assembly yang digunakan untuk membandingkan dua nilai adalah...',
                            'option_a'       => 'mov',
                            'option_b'       => 'cmp',
                            'option_c'       => 'call',
                            'option_d'       => 'push',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Tool yang mengubah kode mesin (machine code) menjadi instruksi assembly disebut...',
                            'option_a'       => 'Compiler',
                            'option_b'       => 'Disassembler',
                            'option_c'       => 'Linker',
                            'option_d'       => 'Interpreter',
                            'correct_option' => 'b',
                        ],
                    ],
                ],
                [
                    'title'       => 'Teknik Anti-Debugging & Obfuscation',
                    'description' => 'Mempelajari teknik yang digunakan pengembang untuk mempersulit proses reverse engineering.',
                    'level'       => 'hard',
                    'content'     => "Anti-debugging adalah teknik yang digunakan untuk mendeteksi dan mencegah program dianalisis "
                        . "menggunakan debugger, misalnya dengan memeriksa flag debugger pada sistem operasi (contoh: fungsi "
                        . "`IsDebuggerPresent` pada Windows).\n\n"
                        . "Obfuscation adalah teknik menyamarkan logika kode agar sulit dipahami, misalnya dengan mengacak nama "
                        . "fungsi/variabel, menambahkan kode yang tidak berarti (junk code), atau mengenkripsi bagian dari kode "
                        . "program yang baru didekripsi saat runtime.",
                    'questions'   => [
                        [
                            'question'       => 'Fungsi Windows API yang umum digunakan untuk mendeteksi keberadaan debugger adalah...',
                            'option_a'       => 'CreateProcess',
                            'option_b'       => 'IsDebuggerPresent',
                            'option_c'       => 'ReadFile',
                            'option_d'       => 'GetModuleHandle',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Teknik menyamarkan logika kode agar sulit dipahami disebut...',
                            'option_a'       => 'Obfuscation',
                            'option_b'       => 'Hashing',
                            'option_c'       => 'Load Balancing',
                            'option_d'       => 'Serialization',
                            'correct_option' => 'a',
                        ],
                    ],
                ],
            ],

            'Network Security' => [
                [
                    'title'       => 'Dasar Keamanan Jaringan',
                    'description' => 'Memahami konsep dasar keamanan jaringan komputer dan ancaman umum yang dihadapinya.',
                    'level'       => 'easy',
                    'content'     => "Keamanan jaringan (network security) mencakup kebijakan dan praktik yang digunakan untuk "
                        . "mencegah dan memantau akses tidak sah, penyalahgunaan, modifikasi, atau penolakan layanan pada jaringan "
                        . "komputer dan sumber daya yang dapat diakses melaluinya.\n\n"
                        . "Beberapa komponen dasar keamanan jaringan:\n"
                        . "1. Firewall - menyaring trafik masuk dan keluar berdasarkan aturan tertentu.\n"
                        . "2. IDS/IPS - mendeteksi (dan mencegah) aktivitas mencurigakan pada jaringan.\n"
                        . "3. VPN - mengenkripsi trafik agar aman saat melewati jaringan publik.",
                    'questions'   => [
                        [
                            'question'       => 'Komponen yang bertugas menyaring trafik masuk dan keluar jaringan berdasarkan aturan tertentu adalah...',
                            'option_a'       => 'Firewall',
                            'option_b'       => 'Switch',
                            'option_c'       => 'Load Balancer',
                            'option_d'       => 'DNS Server',
                            'correct_option' => 'a',
                        ],
                        [
                            'question'       => 'Teknologi yang digunakan untuk mengenkripsi trafik saat melewati jaringan publik adalah...',
                            'option_a'       => 'VPN',
                            'option_b'       => 'DHCP',
                            'option_c'       => 'ARP',
                            'option_d'       => 'NAT saja',
                            'correct_option' => 'a',
                        ],
                    ],
                ],
                [
                    'title'       => 'Serangan Man-in-the-Middle (MITM)',
                    'description' => 'Mempelajari cara kerja serangan MITM seperti ARP spoofing dan dampaknya pada jaringan.',
                    'level'       => 'medium',
                    'content'     => "Man-in-the-Middle (MITM) adalah jenis serangan di mana penyerang menyisipkan diri di antara "
                        . "komunikasi dua pihak tanpa sepengetahuan mereka, sehingga penyerang dapat menyadap atau bahkan mengubah "
                        . "data yang dikirimkan.\n\n"
                        . "Salah satu teknik umum untuk melakukan MITM pada jaringan lokal adalah ARP spoofing, yaitu mengirimkan "
                        . "ARP reply palsu agar korban mengira alamat MAC penyerang adalah alamat MAC gateway, sehingga seluruh "
                        . "trafik korban dialihkan melalui perangkat penyerang terlebih dahulu.",
                    'questions'   => [
                        [
                            'question'       => 'Teknik memalsukan ARP reply agar trafik korban melewati perangkat penyerang disebut...',
                            'option_a'       => 'DNS Tunneling',
                            'option_b'       => 'ARP Spoofing',
                            'option_c'       => 'SQL Injection',
                            'option_d'       => 'Buffer Overflow',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Dampak utama dari serangan Man-in-the-Middle yang berhasil adalah...',
                            'option_a'       => 'Server menjadi lebih cepat',
                            'option_b'       => 'Penyerang dapat menyadap/mengubah trafik korban',
                            'option_c'       => 'Password otomatis terenkripsi',
                            'option_d'       => 'Jaringan menjadi lebih stabil',
                            'correct_option' => 'b',
                        ],
                    ],
                ],
                [
                    'title'       => 'Deteksi Intrusi & Analisis Trafik',
                    'description' => 'Mempelajari dasar penggunaan sistem deteksi intrusi dan cara menganalisis pola trafik mencurigakan.',
                    'level'       => 'medium',
                    'content'     => "Intrusion Detection System (IDS) adalah sistem yang memantau trafik jaringan atau aktivitas "
                        . "sistem untuk mendeteksi tanda-tanda serangan atau pelanggaran kebijakan keamanan, lalu memberikan "
                        . "peringatan (alert) kepada administrator.\n\n"
                        . "Beberapa pola yang sering dicurigai saat menganalisis trafik jaringan:\n"
                        . "1. Volume query DNS yang tinggi ke satu domain (indikasi DNS tunneling).\n"
                        . "2. Port scanning berurutan dari satu sumber IP dalam waktu singkat.\n"
                        . "3. Paket dengan payload yang di-encode/di-obfuscate secara tidak wajar.",
                    'questions'   => [
                        [
                            'question'       => 'Sistem yang bertugas memantau dan memberi peringatan atas aktivitas mencurigakan pada jaringan disebut...',
                            'option_a'       => 'IDS (Intrusion Detection System)',
                            'option_b'       => 'DHCP Server',
                            'option_c'       => 'Load Balancer',
                            'option_d'       => 'Proxy Cache',
                            'correct_option' => 'a',
                        ],
                        [
                            'question'       => 'Volume query DNS yang sangat tinggi ke satu domain bisa menjadi indikasi dari...',
                            'option_a'       => 'DNS Tunneling',
                            'option_b'       => 'SQL Injection',
                            'option_c'       => 'Buffer Overflow',
                            'option_d'       => 'Reverse Engineering',
                            'correct_option' => 'a',
                        ],
                    ],
                ],
            ],

            'Binary Exploitation' => [
                [
                    'title'       => 'Pengantar Memory Corruption',
                    'description' => 'Memahami konsep dasar kerentanan memory corruption pada program native (C/C++).',
                    'level'       => 'medium',
                    'content'     => "Memory corruption terjadi ketika sebuah program menulis data di luar batas memori yang "
                        . "seharusnya, sehingga dapat merusak data lain, mengubah alur eksekusi program, atau bahkan memberi "
                        . "kendali penuh kepada penyerang.\n\n"
                        . "Beberapa area memori yang sering menjadi target eksploitasi:\n"
                        . "1. Stack - tempat menyimpan local variable dan return address fungsi.\n"
                        . "2. Heap - tempat alokasi memori dinamis (misal lewat `malloc`).\n"
                        . "Proteksi seperti ASLR, NX (DEP), dan stack canary diterapkan untuk mempersulit eksploitasi jenis ini.",
                    'questions'   => [
                        [
                            'question'       => 'Area memori yang menyimpan local variable dan return address sebuah fungsi disebut...',
                            'option_a'       => 'Heap',
                            'option_b'       => 'Stack',
                            'option_c'       => 'Data Segment',
                            'option_d'       => 'Registry',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Proteksi yang mengacak alamat memori setiap kali program dijalankan disebut...',
                            'option_a'       => 'ASLR',
                            'option_b'       => 'CSP',
                            'option_c'       => 'CORS',
                            'option_d'       => 'WAF',
                            'correct_option' => 'a',
                        ],
                    ],
                ],
                [
                    'title'       => 'Buffer Overflow & Stack Smashing',
                    'description' => 'Mempelajari mekanisme buffer overflow pada stack dan cara mendeteksinya.',
                    'level'       => 'medium',
                    'content'     => "Buffer overflow terjadi ketika data yang ditulis ke sebuah buffer melebihi kapasitas yang "
                        . "dialokasikan, sehingga menimpa data di sekitarnya termasuk saved return address pada stack. Jika "
                        . "penyerang dapat mengontrol nilai yang menimpa return address, alur eksekusi program bisa dialihkan "
                        . "ke lokasi yang diinginkan penyerang.\n\n"
                        . "Stack canary adalah nilai acak yang diletakkan sebelum saved return address; jika nilai ini berubah "
                        . "saat fungsi selesai dieksekusi, program akan mendeteksi adanya stack smashing dan langsung dihentikan.",
                    'questions'   => [
                        [
                            'question'       => 'Nilai yang diletakkan sebelum saved return address untuk mendeteksi stack smashing disebut...',
                            'option_a'       => 'Heap Chunk',
                            'option_b'       => 'Stack Canary',
                            'option_c'       => 'GOT Entry',
                            'option_d'       => 'Session Token',
                            'correct_option' => 'b',
                        ],
                        [
                            'question'       => 'Dampak paling berbahaya dari buffer overflow pada stack adalah...',
                            'option_a'       => 'Program berjalan lebih cepat',
                            'option_b'       => 'Penyerang dapat mengontrol alur eksekusi program',
                            'option_c'       => 'Memori otomatis dienkripsi',
                            'option_d'       => 'Tidak ada dampak berarti',
                            'correct_option' => 'b',
                        ],
                    ],
                ],
                [
                    'title'       => 'Teknik Eksploitasi Lanjutan (ROP & Heap)',
                    'description' => 'Mengenal teknik eksploitasi lanjutan seperti Return Oriented Programming dan heap exploitation.',
                    'level'       => 'hard',
                    'content'     => "Ketika proteksi NX (non-executable stack) aktif, penyerang tidak bisa lagi menjalankan "
                        . "shellcode langsung di stack. Sebagai gantinya, teknik Return Oriented Programming (ROP) memanfaatkan "
                        . "potongan-potongan instruksi yang sudah ada di dalam binary (disebut 'gadget') dan menyusunnya menjadi "
                        . "rangkaian eksekusi yang diinginkan penyerang.\n\n"
                        . "Selain itu, heap exploitation memanfaatkan kesalahan pengelolaan memori dinamis (misal use-after-free "
                        . "atau overflow pada heap chunk) untuk merusak metadata alokator memori dan pada akhirnya mendapatkan "
                        . "kemampuan arbitrary read/write.",
                    'questions'   => [
                        [
                            'question'       => 'Teknik yang menyusun potongan instruksi (gadget) yang sudah ada di binary untuk melewati proteksi NX disebut...',
                            'option_a'       => 'Return Oriented Programming (ROP)',
                            'option_b'       => 'SQL Injection',
                            'option_c'       => 'ARP Spoofing',
                            'option_d'       => 'DNS Tunneling',
                            'correct_option' => 'a',
                        ],
                        [
                            'question'       => 'Kesalahan pengelolaan memori dinamis di mana memori yang sudah dibebaskan masih digunakan disebut...',
                            'option_a'       => 'Use-After-Free',
                            'option_b'       => 'Stack Canary Bypass',
                            'option_c'       => 'Format String Bug',
                            'option_d'       => 'Race Condition Web',
                            'correct_option' => 'a',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($data as $categoryName => $materials) {
            $categoryId = Category::where('name', $categoryName)->value('id');

            if (! $categoryId) {
                continue;
            }

            foreach ($materials as $order => $materialData) {
                $material = Materi::updateOrCreate(
                    ['title' => $materialData['title']],
                    [
                        'description'            => $materialData['description'],
                        'content'                => $materialData['content'],
                        'level'                  => $materialData['level'],
                        'order'                  => $order + 1,
                        'is_active'              => true,
                        'created_by'             => $admin->id,
                        'kategori_id'  => $categoryId,
                    ]
                );

                foreach ($materialData['questions'] as $qOrder => $questionData) {
                    $question = Simulasi::firstOrCreate(
                        [
                            'learning_material_id' => $material->id,
                            'order'                => $qOrder + 1,
                        ],
                        [
                            'question'       => $questionData['question'],
                            'question_type'  => Simulasi::TYPE_PILIHAN_GANDA,
                            'correct_option' => $questionData['correct_option'],
                        ]
                    );

                    // option_a..e sudah dinormalisasi ke tabel
                    // learning_question_options (bukan kolom langsung lagi).
                    $question->syncOptions([
                        'a' => $questionData['option_a'] ?? null,
                        'b' => $questionData['option_b'] ?? null,
                        'c' => $questionData['option_c'] ?? null,
                        'd' => $questionData['option_d'] ?? null,
                        'e' => $questionData['option_e'] ?? null,
                    ]);
                }
            }
        }
    }
}
