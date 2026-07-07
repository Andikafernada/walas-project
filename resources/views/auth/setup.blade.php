<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Organisasi - Walas</title>
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
</head>
<body class="font-sans bg-gray-50 antialiased">

    <div class="min-h-screen flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 p-12 flex-col justify-between relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-accent-400 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">Walas</span>
                </a>
            </div>

            <div class="relative z-10">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 mb-6">
                    <div class="flex items-center gap-4 mb-4">
                        @if(isset($googleUser['avatar']))
                            <img src="{{ $googleUser['avatar'] }}" alt="Avatar" class="w-16 h-16 rounded-full ring-4 ring-white/30">
                        @else
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <p class="text-white font-semibold text-lg">{{ $googleUser['name'] ?? 'User' }}</p>
                            <p class="text-primary-200">{{ $googleUser['email'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-green-300 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Akun Google terhubung</span>
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-white mb-4 leading-tight">
                    Satu Langkah Lagi!
                </h1>
                <p class="text-primary-100 text-lg">
                    Pilih atau daftarkan sekolah/institusi Anda untuk mulai menggunakan Walas.
                </p>
            </div>

            <p class="text-primary-200 text-sm relative z-10">&copy; 2026 Walas. All rights reserved.</p>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-lg">
                <div class="lg:hidden mb-8 text-center">
                    <a href="/" class="inline-flex items-center gap-2">
                        <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Walas</span>
                    </a>
                </div>

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Setup Organisasi</h2>
                    <p class="text-gray-600">Pilih sekolah/institusi Anda atau daftarkan yang baru</p>
                </div>

                <form method="POST" action="{{ route('auth.setup.complete') }}" class="space-y-6">
                    @csrf

                    <!-- Choice: New or Existing -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Bagaimana dengan sekolah Anda?</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="organization_choice" value="existing" class="peer sr-only" checked onchange="toggleOrganizationForm(this.value)">
                                <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 transition-all hover:border-gray-300">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-3 peer-checked:bg-primary-100">
                                            <svg class="w-6 h-6 text-gray-500 peer-checked:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-900">Sudah Terdaftar</span>
                                        <span class="text-sm text-gray-500">Pilih dari daftar</span>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="organization_choice" value="new" class="peer sr-only" onchange="toggleOrganizationForm(this.value)">
                                <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 transition-all hover:border-gray-300">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-3 peer-checked:bg-primary-100">
                                            <svg class="w-6 h-6 text-gray-500 peer-checked:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-900">Sekolah Baru</span>
                                        <span class="text-sm text-gray-500">Daftarkan sekarang</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Existing Organization Select -->
                    <div id="existing-organization" class="space-y-2">
                        <label for="organization_id" class="block text-sm font-medium text-gray-700">Pilih Sekolah</label>
                        <div class="relative">
                            <select id="organization_id" name="organization_id"
                                class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all appearance-none bg-white">
                                <option value="">-- Pilih Sekolah --</option>
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                        {{ $org->name }} ({{ $org->type_label }}) - {{ $org->city }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('organization_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Organization Form -->
                    <div id="new-organization" class="space-y-5 hidden">
                        <div>
                            <label for="organization_name" class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                            <input type="text" id="organization_name" name="organization_name" value="{{ old('organization_name') }}"
                                class="mt-1 w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                placeholder="Contoh: SMP Negeri 1 Jakarta">
                            @error('organization_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="organization_type" class="block text-sm font-medium text-gray-700">Jenjang Sekolah</label>
                            <div class="relative">
                                <select id="organization_type" name="organization_type"
                                    class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all appearance-none bg-white">
                                    <option value="">-- Pilih Jenjang --</option>
                                    <option value="sd" {{ old('organization_type') == 'sd' ? 'selected' : '' }}>Sekolah Dasar (SD)</option>
                                    <option value="smp" {{ old('organization_type') == 'smp' ? 'selected' : '' }}>Sekolah Menengah Pertama (SMP)</option>
                                    <option value="sma" {{ old('organization_type') == 'sma' ? 'selected' : '' }}>Sekolah Menengah Atas (SMA)</option>
                                    <option value="smk" {{ old('organization_type') == 'smk' ? 'selected' : '' }}>Sekolah Menengah Kejuruan (SMK)</option>
                                    <option value="others" {{ old('organization_type') == 'others' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('organization_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="organization_city" class="block text-sm font-medium text-gray-700">Kota</label>
                            <input type="text" id="organization_city" name="organization_city" value="{{ old('organization_city') }}"
                                class="mt-1 w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                placeholder="Contoh: Jakarta">
                            @error('organization_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full py-3.5 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 focus:ring-4 focus:ring-primary-500/30 transition-all flex items-center justify-center gap-2">
                        <span>Lanjutkan</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6">
                    Dengan mendaftar, Anda menyetujui
                    <a href="#" class="text-primary-600 hover:text-primary-700">Syarat & Ketentuan</a>
                    dan
                    <a href="#" class="text-primary-600 hover:text-primary-700">Kebijakan Privasi</a>
                    kami.
                </p>
            </div>
        </div>
    </div>

    <script>
        function toggleOrganizationForm(choice) {
            const existingDiv = document.getElementById('existing-organization');
            const newDiv = document.getElementById('new-organization');

            if (choice === 'existing') {
                existingDiv.classList.remove('hidden');
                newDiv.classList.add('hidden');
                document.getElementById('organization_id').required = true;
                document.getElementById('organization_name').required = false;
                document.getElementById('organization_type').required = false;
                document.getElementById('organization_city').required = false;
            } else {
                existingDiv.classList.add('hidden');
                newDiv.classList.remove('hidden');
                document.getElementById('organization_id').required = false;
                document.getElementById('organization_name').required = true;
                document.getElementById('organization_type').required = true;
                document.getElementById('organization_city').required = true;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedChoice = document.querySelector('input[name="organization_choice"]:checked').value;
            toggleOrganizationForm(selectedChoice);
        });
    </script>
</body>
</html>
