@extends('layouts.main')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Riwayat Presensi</h1>
            <p class="text-sm text-gray-500 mt-1">
                Data check-in, check-out, dan durasi kerja pegawai SPPG.
            </p>
        </div>
    </div>

    {{-- Alert --}}
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

    {{-- Filter & Action --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col xl:flex-row xl:items-end gap-4">

            {{-- Filter Form --}}
            <form method="GET"
                  action="{{ route('presensi.riwayat.index') }}"
                  class="flex-1 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 items-end">

                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">
                        Tgl Mulai
                    </label>
                    <input type="date"
                           name="tanggal_mulai"
                           id="tanggal_mulai"
                           value="{{ request('tanggal_mulai') }}"
                           class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-1">
                        Tgl Akhir
                    </label>
                    <input type="date"
                           name="tanggal_akhir"
                           id="tanggal_akhir"
                           value="{{ request('tanggal_akhir') }}"
                           class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Pegawai
                    </label>
                    <select name="pegawai_id"
                            id="pegawai_id"
                            class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Pegawai</option>
                        @foreach ($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}" @selected(request('pegawai_id') == $pegawai->id)>
                                {{ $pegawai->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="h-[46px] bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 rounded-xl transition">
                    Filter
                </button>

                <a href="{{ route('presensi.riwayat.index') }}"
                   class="h-[46px] bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-5 rounded-xl transition flex items-center justify-center">
                    Reset Filter
                </a>
            </form>

            {{-- Export --}}
            <a href="{{ route('presensi.riwayat.export', request()->query()) }}"
               class="h-[46px] bg-green-600 hover:bg-green-700 text-white font-semibold px-5 rounded-xl shadow-sm transition flex items-center justify-center whitespace-nowrap">
                Export CSV
            </a>

            {{-- Reset Data --}}
            <form action="{{ route('presensi.reset.hari-ini') }}"
                  method="POST"
                  onsubmit="return confirm('Yakin reset data presensi hari ini? Data yang dihapus tidak bisa dikembalikan.');">
                @csrf

                <button type="submit"
                        class="h-[46px] bg-red-600 hover:bg-red-700 text-white font-semibold px-5 rounded-xl shadow-sm transition whitespace-nowrap">
                    Reset Data Hari Ini
                </button>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tanggal Kerja
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Nama Pegawai
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Divisi
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jam Masuk
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jam Keluar
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Durasi
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($riwayats as $riwayat)
                        @php
                            $totalMenit = (int) ($riwayat->total_jam ?? 0);
                            $jam = intdiv($totalMenit, 60);
                            $menit = $totalMenit % 60;

                            if ($riwayat->jam_keluar) {
                                if ($jam > 0 && $menit > 0) {
                                    $durasi = $jam . ' jam ' . $menit . ' menit';
                                } elseif ($jam > 0) {
                                    $durasi = $jam . ' jam';
                                } else {
                                    $durasi = $menit . ' menit';
                                }
                            } else {
                                $durasi = 'Masih bekerja';
                            }

                            $status = $riwayat->status ?? null;
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if ($riwayat->tanggal)
                                    {{ \Carbon\Carbon::parse($riwayat->tanggal)->format('d M Y') }}
                                @elseif ($riwayat->jam_masuk)
                                    {{ \Carbon\Carbon::parse($riwayat->jam_masuk)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $riwayat->pegawai->nama ?? 'Pegawai Dihapus' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $riwayat->pegawai->divisi->nama ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if ($riwayat->jam_masuk)
                                    {{ \Carbon\Carbon::parse($riwayat->jam_masuk)->format('H:i:s') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if ($riwayat->jam_keluar)
                                    {{ \Carbon\Carbon::parse($riwayat->jam_keluar)->format('H:i:s') }}
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Belum Check-Out
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $durasi }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if (! $riwayat->jam_keluar || $status === 'open')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Sedang Bekerja
                                    </span>
                                @elseif ($status === 'auto_checkout')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                        Auto Check-Out
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                Tidak ada data riwayat presensi yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $riwayats->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection