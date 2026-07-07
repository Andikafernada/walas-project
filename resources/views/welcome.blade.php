<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walas - Sistem Manajemen Kelas Digital</title>
    <meta name="description" content="Walas adalah platform digital untuk wali kelas dalam mengelola absensi, pelanggaran, jurnal, dan komunikasi dengan orang tua.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                        accent: { 400: '#34d399', 500: '#10b981', 600: '#059669' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="font-sans bg-white text-gray-900 antialiased">
    
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Walas</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="text-gray-600 hover:text-primary-600 transition-colors">Fitur</a>
                    <a href="#cara-pakai" class="text-gray-600 hover:text-primary-600 transition-colors">Cara Pakai</a>
                    <a href="#kontak" class="text-gray-600 hover:text-primary-600 transition-colors">Kontak</a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="/login" class="px-4 py-2 text-gray-700 hover:text-primary-600 font-medium transition-colors">Masuk</a>
                    <a href="/register" class="px-5 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25">Daftar Gratis</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 lg:pt-40 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 text-primary-700 rounded-full text-sm font-medium mb-8">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    Platform Digital untuk Wali Kelas
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                    Kelola Kelas dengan
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-500">Lebih Mudah</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                    Walas membantu wali kelas mengelola absensi, mencatat pelanggaran, membuat jurnal harian, dan mengomunikasikan kondisi siswa ke orang tua secara digital.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 transition-all shadow-xl shadow-primary-500/30">
                        <span>Mulai Gratis Sekarang</span>
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                    <a href="#fitur" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                        <i data-lucide="play-circle" class="w-5 h-5"></i>
                        <span>Lihat Demo</span>
                    </a>
                </div>
                <div class="mt-12 flex items-center justify-center gap-8 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-accent-500"></i>
                        <span>Gratis selamanya</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-accent-500"></i>
                        <span>Tanpa kartu kredit</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-accent-500"></i>
                        <span>Setup dalam 5 menit</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-bold text-primary-600 mb-2">10K+</div>
                    <div class="text-gray-600">Siswa Terdaftar</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-bold text-primary-600 mb-2">500+</div>
                    <div class="text-gray-600">Guru & Wali Kelas</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-bold text-primary-600 mb-2">50+</div>
                    <div class="text-gray-600">Sekolah</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-bold text-primary-600 mb-2">99.9%</div>
                    <div class="text-gray-600">Uptime</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Fitur Lengkap untuk Wali Kelas</h2>
                <p class="text-lg text-gray-600">Semua yang Anda butuhkan untuk mengelola kelas secara digital dalam satu platform.</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-500/10 transition-all">
                    <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="clipboard-check" class="w-7 h-7 text-primary-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Absensi Digital</h3>
                    <p class="text-gray-600">Catat absensi siswa dengan mudah.支持 QR code, link magic, dan manual input. Generate laporan otomatis.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-500/10 transition-all">
                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="alert-triangle" class="w-7 h-7 text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pelanggaran Siswa</h3>
                    <p class="text-gray-600">Catat dan lacak pelanggaran siswa. Sistem poin otomatis dengan notifikasi ke orang tua via WhatsApp.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-500/10 transition-all">
                    <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="book-open" class="w-7 h-7 text-amber-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Jurnal Kelas</h3>
                    <p class="text-gray-600">Buat dan kelola jurnal harian kelas. Catat materi, kegiatan, dan pencapaian dengan template siap pakai.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-500/10 transition-all">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="wallet" class="w-7 h-7 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Buku Kas</h3>
                    <p class="text-gray-600">Kelola keuangan kelas dengan transparan. Catat pemasukan dan pengeluaran, generate laporan keuangan.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-500/10 transition-all">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="message-square" class="w-7 h-7 text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">WhatsApp Gateway</h3>
                    <p class="text-gray-600">Kirim notifikasi ke orang tua secara otomatis.支持 pesan massal dan template pesan customizable.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-500/10 transition-all">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-lucide="bar-chart-3" class="w-7 h-7 text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Laporan & Analitik</h3>
                    <p class="text-gray-600">Generate laporan lengkap dalam format PDF/Excel. Visualisasi data attendance dan pelanggaran siswa.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="cara-pakai" class="py-20 lg:py-32 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Mulai dalam 3 Langkah</h2>
                <p class="text-lg text-gray-600">Proses setup yang simpel dan cepat, tanpa perlu keahlian IT.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Daftar & Verifikasi</h3>
                    <p class="text-gray-600">Buat akun gratis dengan email sekolah. Verifikasi dalam 1x24 jam.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tambah Data Kelas</h3>
                    <p class="text-gray-600">Input data siswa, orang tua, dan jadwal pelajaran dengan mudah.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Mulai Mengelola</h3>
                    <p class="text-gray-600">Gunakan semua fitur untuk mengelola kelas secara digital.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-3xl p-8 sm:p-12 lg:p-16 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Siap Memulai?</h2>
                <p class="text-lg text-primary-100 mb-10 max-w-2xl mx-auto">Bergabung dengan ratusan wali kelas yang sudah menggunakan Walas untuk mengelola kelas mereka.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary-700 rounded-xl font-semibold hover:bg-gray-100 transition-all shadow-xl">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        <span>Daftar Sekarang</span>
                    </a>
                    <a href="/demo" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary-500/30 text-white rounded-xl font-semibold hover:bg-primary-500/40 transition-all">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                        <span>Lihat Demo</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-white">Walas</span>
                    </div>
                    <p class="text-gray-400 max-w-md">Platform digital manajemen kelas untuk wali kelas Indonesia. Membantu mengelola absensi, pelanggaran, dan komunikasi dengan orang tua.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Link</h4>
                    <ul class="space-y-2">
                        <li><a href="#fitur" class="hover:text-white transition-colors">Fitur</a></li>
                        <li><a href="#cara-pakai" class="hover:text-white transition-colors">Cara Pakai</a></li>
                        <li><a href="/login" class="hover:text-white transition-colors">Masuk</a></li>
                        <li><a href="/register" class="hover:text-white transition-colors">Daftar</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                            <span>support@walas.my.id</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>+62 812-3456-7890</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p>&copy; 2026 Walas. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-white transition-colors">
                        <i data-lucide="github" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="hover:text-white transition-colors">
                        <i data-lucide="twitter" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="hover:text-white transition-colors">
                        <i data-lucide="instagram" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
