<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SPPG Nogotirto IV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
        }
        .pagination .flex div:first-child a, 
        .pagination .flex div:first-child span {
            border-top: none !important;
            border-bottom: none !important;
            border-right: none !important;
        }
        .pagination .flex span.relative {
            background-color: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col">
        <nav class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-0">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        {{-- START PERUBAHAN DI SINI --}}
                        <a href="{{ route('presensi.dashboard') }}" class="flex items-center text-sm font-bold text-indigo-600 tracking-wider">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/9/96/Logo_Badan_Gizi_Nasional_%282024%29.png" alt="Logo BGN" class="w-16 h-16 mr-3">
                            <div class="flex flex-col text-left">
                                <span class="text-xs sm:text-sm font-semibold text-gray-800">BADAN GIZI NASIONAL <span class="font-normal text-gray-600">(NATIONAL NUTRITION AGENCY)</span></span>
                                <span class="text-xs sm:text-sm font-bold text-indigo-600 mt-0.5">SATUAN PELAYANAN PEMENUHAN GIZI SLEMAN GAMPING NOGOTIRTO</span>
                                
                            </div>
                        </a>
                        {{-- END PERUBAHAN DI SINI --}}
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('presensi.dashboard') }}" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out @if(request()->routeIs('presensi.dashboard')) bg-indigo-50 text-indigo-700 @endif">Dashboard</a>
                        
                        <a href="{{ route('presensi.pegawai.index') }}" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out @if(request()->routeIs('presensi.pegawai.index')) bg-indigo-50 text-indigo-700 @endif">Data Pegawai</a>
                        
                        <a href="{{ route('presensi.divisi.index') }}" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out @if(request()->routeIs('presensi.divisi.*')) bg-indigo-50 text-indigo-700 @endif">Manajemen Divisi</a>

                        <a href="{{ route('presensi.riwayat.index') }}" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out @if(request()->routeIs('presensi.riwayat.index')) bg-indigo-50 text-indigo-700 @endif">Riwayat Presensi</a>
                        
                       
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-md" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-md" role="alert">
                        <strong class="font-bold">Error Validasi!</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} SPPG Nogotirto. Dibuat dengan Laravel & Tailwind CSS.
            </div>
        </footer>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>