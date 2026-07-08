@extends('layouts.main')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Divisi</h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola divisi pegawai SPPG. Jam default bersifat opsional karena presensi memakai sistem shift dinamis.
            </p>
        </div>

        <button type="button"
            onclick="openTambahModal()"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm transition">
            Tambah Divisi
        </button>
    </div>

    @if (session('success'))
    <div class="p-4 rounded-xl bg-green-50 text-green-700 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="p-4 rounded-xl bg-red-50 text-red-700 border border-red-200">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="p-4 rounded-xl bg-red-50 text-red-700 border border-red-200">
        <div class="font-semibold mb-2">Periksa kembali input berikut:</div>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Nama Divisi
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jumlah Pegawai
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jam Masuk Default
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jam Keluar Default
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($divisis as $divisi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $divisi->nama }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $divisi->pegawais_count }} pegawai
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $divisi->jam_masuk ? substr($divisi->jam_masuk, 0, 5) : '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $divisi->jam_keluar ? substr($divisi->jam_keluar, 0, 5) : '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <button type="button"
                                onclick="openEditModal(this)"
                                data-id="{{ $divisi->id }}"
                                data-nama="{{ $divisi->nama }}"
                                data-jam-masuk="{{ $divisi->jam_masuk ? substr($divisi->jam_masuk, 0, 5) : '' }}"
                                data-jam-keluar="{{ $divisi->jam_keluar ? substr($divisi->jam_keluar, 0, 5) : '' }}"
                                class="text-indigo-600 hover:text-indigo-900 font-semibold mr-4">
                                Edit
                            </button>

                            <form action="{{ route('presensi.divisi.destroy', $divisi) }}"
                                method="POST"
                                class="inline"
                                onsubmit="return confirm('Yakin menghapus divisi ini?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="text-red-600 hover:text-red-900 font-semibold"
                                    @disabled($divisi->pegawais_count > 0)
                                    title="{{ $divisi->pegawais_count > 0 ? 'Divisi masih digunakan pegawai' : 'Hapus divisi' }}">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                            Belum ada data divisi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modalTambah" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-lg shadow-xl rounded-2xl bg-white">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Tambah Divisi</h3>
                <p class="text-sm text-gray-500 mt-1">Jam default boleh dikosongkan.</p>
            </div>

            <button type="button" onclick="closeTambahModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                &times;
            </button>
        </div>

        <form action="{{ route('presensi.divisi.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="nama_tambah" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Divisi
                </label>
                <input type="text"
                    name="nama"
                    id="nama_tambah"
                    required
                    value="{{ old('nama') }}"
                    class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="jam_masuk_tambah" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Masuk Default
                    </label>
                    <input type="time"
                        name="jam_masuk"
                        id="jam_masuk_tambah"
                        value="{{ old('jam_masuk') }}"
                        class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="jam_keluar_tambah" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Keluar Default
                    </label>
                    <input type="time"
                        name="jam_keluar"
                        id="jam_keluar_tambah"
                        value="{{ old('jam_keluar') }}"
                        class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button"
                    onclick="closeTambahModal()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2.5 px-5 rounded-xl">
                    Batal
                </button>

                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-lg shadow-xl rounded-2xl bg-white">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Edit Divisi</h3>
                <p class="text-sm text-gray-500 mt-1">Perubahan divisi akan dipakai oleh master pegawai.</p>
            </div>

            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                &times;
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit_nama" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Divisi
                </label>
                <input type="text"
                    name="nama"
                    id="edit_nama"
                    required
                    class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_jam_masuk" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Masuk Default
                    </label>
                    <input type="time"
                        name="jam_masuk"
                        id="edit_jam_masuk"
                        class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="edit_jam_keluar" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Keluar Default
                    </label>
                    <input type="time"
                        name="jam_keluar"
                        id="edit_jam_keluar"
                        class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button"
                    onclick="closeEditModal()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2.5 px-5 rounded-xl">
                    Batal
                </button>

                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTambahModal() {
        document.getElementById('modalTambah').classList.remove('hidden');
    }

    function closeTambahModal() {
        document.getElementById('modalTambah').classList.add('hidden');
    }

    function openEditModal(button) {
        const form = document.getElementById('editForm');

        const id = button.dataset.id;
        const nama = button.dataset.nama || '';
        const jamMasuk = button.dataset.jamMasuk || '';
        const jamKeluar = button.dataset.jamKeluar || '';

        const templateUrl = "{{ route('presensi.divisi.update', ['divisi' => '__ID__']) }}";

        form.action = templateUrl.replace('__ID__', id);

        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_jam_masuk').value = jamMasuk;
        document.getElementById('edit_jam_keluar').value = jamKeluar;

        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEdit').classList.add('hidden');
    }
</script>
@endsection