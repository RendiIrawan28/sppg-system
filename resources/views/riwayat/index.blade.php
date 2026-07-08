@extends('layouts.main')

@section('content')

@if (session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">
        {{ session('success') }}
    </div>
@endif
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Riwayat Presensi</h1>

    {{-- Filter Section (tetap sama) --}}
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
        <form method="GET" action="{{ route('presensi.riwayat.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tgl Mulai</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-2 border">
            </div>
            <div>
                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tgl Akhir</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-2 border">
            </div>
            <div>
                <label for="pegawai_id" class="block text-sm font-medium text-gray-700">Pilih Pegawai</label>
                <select name="pegawai_id" id="pegawai_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-2 border">
                    <option value="">Semua Pegawai</option>
                    @foreach ($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}" @selected(request('pegawai_id') == $pegawai->id)>{{ $pegawai->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150">Filter</button>
                <a href="{{ route('presensi.riwayat.index') }}" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg text-center transition duration-150">Reset</a>
            </div>
            <div class="md:col-span-1">
                <a href="{{ route('presensi.riwayat.export', request()->query()) }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export CSV
                </a>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($riwayats as $riwayat)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ Carbon\Carbon::parse($riwayat->jam_masuk)->format('d F Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $riwayat->pegawai->nama ?? 'Pegawai Dihapus' }}</td>
                            {{-- FIX KRITIS: Ganti nama_divisi menjadi nama --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $riwayat->pegawai->divisi->nama ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Carbon\Carbon::parse($riwayat->jam_masuk)->format('H:i:s') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($riwayat->jam_keluar)
                                    {{ Carbon\Carbon::parse($riwayat->jam_keluar)->format('H:i:s') }}
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $riwayat->total_jam ? $riwayat->total_jam . ' jam' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($riwayat->status_telat == 'Telat')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Telat</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tepat Waktu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data riwayat presensi yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{-- Pastikan pagination links --}}
            {{ $riwayats->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
