@extends('layouts.main')

@section('content')
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Manajemen Data Pegawai</h1>

    {{-- (Kode Panel Status Anda tetap di sini) --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 p-4 bg-white rounded-xl shadow-lg border-l-4 @if($modeStatus == 'registrasi') border-red-500 @else border-green-500 @endif">
        <div class="mb-4 sm:mb-0">
            <p class="text-sm text-gray-500">Mode Sistem Saat Ini:</p>
            <span class="text-xl font-bold uppercase @if($modeStatus == 'registrasi') text-red-600 @else text-green-600 @endif">
                {{ $modeStatus }}
            </span>
            <p class="text-xs text-gray-400 mt-1">Gunakan tombol di bawah untuk mengubah mode registrasi/presensi.</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="toggleModal('add-pegawai-modal')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Pegawai Baru
            </button>

            @if($modeStatus == 'presensi')
                <form action="{{ route('presensi.mode.registrasi') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-150 ease-in-out">
                        Ubah ke REGISTRASI
                    </button>
                </form>
            @else
                <form action="{{ route('presensi.mode.presensi') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-150 ease-in-out">
                        Ubah ke PRESENSI
                    </button>
                </form>
            @endif
        </div>
    </div>


    <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Nama Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Divisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">UID Kartu</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($pegawais as $pegawai)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pegawai->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $pegawai->nama }}</td>
                        {{-- FIX: Tampilkan nama Divisi melalui relasi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pegawai->divisi->nama ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono @if(!$pegawai->uid_kartu) text-red-500 @else text-green-600 @endif">
                            {{ $pegawai->uid_kartu ?? 'BELUM TERDAFTAR' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex justify-center space-x-2">
                            {{-- FIX: Kirim data Divisi ID untuk modal edit --}}
                            <button onclick="openEditModal({{ json_encode($pegawai) }})" class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out p-1 rounded hover:bg-indigo-50" title="Edit Pegawai">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-9-4l6-6m-6 6l-6 6"></path></svg>
                            </button>
                            
                            <form action="{{ route('presensi.pegawai.destroy', $pegawai->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ $pegawai->nama }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out p-1 rounded hover:bg-red-50" title="Hapus Pegawai">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data pegawai. Silakan tambahkan pegawai baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="add-pegawai-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-lg">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Tambah Pegawai Baru</h3>
            <form action="{{ route('presensi.pegawai.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="add_nama" class="block text-sm font-medium text-gray-700">Nama Pegawai</label>
                        <input type="text" name="nama" id="add_nama" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border" value="{{ old('nama') }}">
                    </div>
                    
                    {{-- FIX: Ganti input teks menjadi SELECT Divisi --}}
                    <div>
                        <label for="add_divisi_id" class="block text-sm font-medium text-gray-700">Divisi</label>
                        <select name="divisi_id" id="add_divisi_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach ($divisis as $divisi)
                                <option value="{{ $divisi->id }}" {{ old('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <p class="text-sm text-gray-500 pt-2">Kolom UID Kartu dikosongkan. Kartu akan didaftarkan saat sistem dalam mode REGISTRASI.</p>
                </div>
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="toggleModal('add-pegawai-modal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-150">Simpan Pegawai</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="edit-pegawai-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-lg">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Edit Data Pegawai</h3>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="edit_nama" class="block text-sm font-medium text-gray-700">Nama Pegawai</label>
                        <input type="text" name="nama" id="edit_nama" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
                    </div>
                    
                    {{-- FIX: Ganti input teks menjadi SELECT Divisi --}}
                    <div>
                        <label for="edit_divisi_id" class="block text-sm font-medium text-gray-700">Divisi</label>
                        <select name="divisi_id" id="edit_divisi_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach ($divisis as $divisi)
                                <option value="{{ $divisi->id }}">
                                    {{ $divisi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_uid_kartu" class="block text-sm font-medium text-gray-700">UID Kartu (Opsi)</label>
                        <input type="text" name="uid_kartu" id="edit_uid_kartu" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
                        <p class="text-xs text-gray-500 mt-1">Hati-hati saat mengubah UID secara manual. Kosongkan untuk menghapus UID kartu.</p>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="toggleModal('edit-pegawai-modal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-150">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function openEditModal(pegawai) {
            const form = document.getElementById('edit-form');
            const editDivisiSelect = document.getElementById('edit_divisi_id');

            // Menetapkan URL aksi form ke route pegawai.update
            form.action = `{{ url('pegawai') }}/${pegawai.id}`; 
            
            // Mengisi data ke dalam input field
            document.getElementById('edit_nama').value = pegawai.nama;
            document.getElementById('edit_uid_kartu').value = pegawai.uid_kartu || '';

            // FIX: Pilih Divisi yang sesuai
            // Jika pegawai.divisi_id ada, kita gunakan ID tersebut, jika tidak, kita tidak memilih apa-apa
            if (pegawai.divisi_id) {
                editDivisiSelect.value = pegawai.divisi_id;
            } else {
                editDivisiSelect.value = ''; // Reset pilihan
            }

            toggleModal('edit-pegawai-modal');
        }
    </script>
@endsection
