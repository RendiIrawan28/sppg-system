@extends('layouts.main')

@section('content')
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-2">Manajemen Data Divisi</h1>

    <div class="flex justify-end mb-6">
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg transition duration-150 ease-in-out flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Divisi
        </button>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Divisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                    {{-- TOLERANSI TELAT DIHAPUS --}}
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($divisis as $divisi)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $divisi->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $divisi->jam_masuk }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $divisi->jam_keluar }}</td>
                        {{-- TOLERANSI TELAT DIHAPUS --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <button onclick="openEditModal({{ $divisi->id }}, '{{ $divisi->nama }}', '{{ $divisi->jam_masuk }}', '{{ $divisi->jam_keluar }}')"
                                    class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                            
                            <form action="{{ route('presensi.divisi.destroy', $divisi) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi {{ $divisi->nama }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data Divisi yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="modalTambah" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Divisi Baru</h3>
            <form action="{{ route('presensi.divisi.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="nama_tambah" class="block text-sm font-medium text-gray-700">Nama Divisi</label>
                        <input type="text" name="nama" id="nama_tambah" required 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('nama') border-red-500 @enderror"
                               value="{{ old('nama') }}">
                        @error('nama')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="jam_masuk_tambah" class="block text-sm font-medium text-gray-700">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="jam_masuk_tambah" required 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('jam_masuk') border-red-500 @enderror"
                               value="{{ old('jam_masuk') }}">
                        @error('jam_masuk')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="jam_keluar_tambah" class="block text-sm font-medium text-gray-700">Jam Keluar</label>
                        <input type="time" name="jam_keluar" id="jam_keluar_tambah" required 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('jam_keluar') border-red-500 @enderror"
                               value="{{ old('jam_keluar') }}">
                        @error('jam_keluar')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- TOLERANSI TELAT DIHAPUS --}}

                    <div class="flex justify-end pt-4 space-x-3">
                        <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                                class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            Batal
                        </button>
                        <button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-700 font-semibold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEdit" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Divisi</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <input type="hidden" name="id" id="edit_id"> 
                    
                    <div>
                        <label for="edit_nama" class="block text-sm font-medium text-gray-700">Nama Divisi</label>
                        <input type="text" name="nama" id="edit_nama" required 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('nama') border-red-500 @enderror">
                        @error('nama')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="edit_jam_masuk" class="block text-sm font-medium text-gray-700">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="edit_jam_masuk" required 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('jam_masuk') border-red-500 @enderror">
                        @error('jam_masuk')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="edit_jam_keluar" class="block text-sm font-medium text-gray-700">Jam Keluar</label>
                        <input type="time" name="jam_keluar" id="edit_jam_keluar" required 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('jam_keluar') border-red-500 @enderror">
                        @error('jam_keluar')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- TOLERANSI TELAT DIHAPUS --}}

                    <div class="flex justify-end pt-4 space-x-3">
                        <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                                class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            Batal
                        </button>
                        <button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-700 font-semibold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openEditModal(id, nama, jam_masuk, jam_keluar) { // HAPUS 'toleransi_telat' dari parameter
            const form = document.getElementById('editForm');
            
            form.action = '/divisi/' + id; 
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_jam_masuk').value = jam_masuk;
            document.getElementById('edit_jam_keluar').value = jam_keluar;
            // HAPUS: document.getElementById('edit_toleransi_telat').value = toleransi_telat;
            
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        // Logika untuk menampilkan modal Tambah jika ada error validasi
        @if ($errors->any() && old('nama') && !isset($divisi->id))
            document.getElementById('modalTambah').classList.remove('hidden');
        @endif
        
    </script>
@endsection
