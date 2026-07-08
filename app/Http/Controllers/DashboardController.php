<?php

namespace App\Http\Controllers;

use App\Models\Mode;
use App\Models\Pegawai;
use App\Models\Presensi;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    protected string $timezone = 'Asia/Jakarta';

    public function index()
    {
        $mode = Mode::find(1);
        $modeStatus = $mode ? $mode->status : 'presensi';

        $today = Carbon::today($this->timezone)->toDateString();

        $presensiHariIni = Presensi::whereDate('tanggal', $today)
            ->orWhereNull('jam_keluar')
            ->get();

        $pegawai = Pegawai::orderBy('nama', 'asc')->get();

        return view('dashboard.index', compact('pegawai', 'modeStatus', 'presensiHariIni'));
    }

    public function getPresensiData()
    {
        $today = Carbon::today($this->timezone)->toDateString();

        $presensis = Presensi::where(function ($query) use ($today) {
                $query->whereDate('tanggal', $today)
                    ->orWhereNull('jam_keluar');
            })
            ->with(['pegawai.divisi'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function (Presensi $presensi) {
                $jamMasuk = $presensi->jam_masuk
                    ? Carbon::parse($presensi->jam_masuk)->format('H:i:s')
                    : '—';

                $jamKeluar = $presensi->jam_keluar
                    ? Carbon::parse($presensi->jam_keluar)->format('H:i:s')
                    : '—';

                $totalMenit = (int) ($presensi->total_jam ?? 0);

                return [
                    'id' => $presensi->id,
                    'nama' => $presensi->pegawai->nama ?? '-',
                    'divisi' => $presensi->pegawai->divisi->nama ?? '-',
                    'tanggal' => $presensi->tanggal ? Carbon::parse($presensi->tanggal)->format('Y-m-d') : '-',
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => $jamKeluar,
                    'total_jam' => $presensi->jam_keluar ? $this->formatDurasi($totalMenit) : 'Masih bekerja',
                    'status' => $presensi->status_label,
                    'checkout_type' => $presensi->checkout_type,
                ];
            });

        return response()->json($presensis);
    }

    protected function formatDurasi(int $minutes): string
    {
        $minutes = max(0, $minutes);

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return $hours . ' jam ' . $remainingMinutes . ' menit';
        }

        if ($hours > 0) {
            return $hours . ' jam';
        }

        return $remainingMinutes . ' menit';
    }
}
