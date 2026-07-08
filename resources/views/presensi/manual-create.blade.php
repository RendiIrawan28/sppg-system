@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-3xl font-bold text-gray-800">Tambah Presensi Manual</h1>
        <p class="text-sm text-gray-500 mt-1">
            Digunakan saat pegawai tidak membawa kartu RFID atau data perlu diinput oleh admin.
        </p>
    </div>

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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('presensi.manual.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Pegawai
                </label>
                <select name="pegawai_id"
                        id="pegawai_id"
                        required
                        class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Pegawai</option>
                    @foreach ($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}" @selected(old('pegawai_id') == $pegawai->id)>
                            {{ $pegawai->nama }} — {{ $pegawai->divisi->nama ?? 'Tanpa Divisi' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Kerja
                    </label>
                    <input type="date"
                           name="tanggal"
                           id="tanggal"
                           value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}"
                           required
                           class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="jam_masuk" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Masuk
                    </label>
                    <input type="time"
                           name="jam_masuk"
                           id="jam_masuk"
                           value="{{ old('jam_masuk') }}"
                           required
                           class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="jam_keluar" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Keluar
                    </label>
                    <input type="time"
                           name="jam_keluar"
                           id="jam_keluar"
                           value="{{ old('jam_keluar') }}"
                           class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-blue-500 focus:border-blue-500">

                    <p class="text-xs text-gray-500 mt-1">
                        Kosongkan jika pegawai masih bekerja.
                    </p>
                </div>
            </div>

            <div>
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan
                </label>
                <textarea name="catatan"
                          id="catatan"
                          rows="3"
                          class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Contoh: pegawai tidak membawa kartu RFID">{{ old('catatan') }}</textarea>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
                Jika jam keluar lebih kecil dari jam masuk, sistem otomatis menganggap shift melewati tengah malam.
                Contoh: masuk 16:00 dan keluar 01:30 akan dihitung sampai hari berikutnya.
            </div>

            <div class="flex flex-col md:flex-row gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm transition">
                    Simpan Presensi Manual
                </button>

                <a href="{{ route('presensi.riwayat.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2.5 px-5 rounded-xl transition text-center">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection