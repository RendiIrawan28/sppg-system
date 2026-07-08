<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $pegawais = Pegawai::orderBy('nama')->get();

        $query = Presensi::with(['pegawai.divisi']);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        $riwayats = $query->orderBy('jam_masuk', 'desc')->paginate(20);

        return view('riwayat.index', compact('riwayats', 'pegawais'));
    }

    public function export(Request $request)
    {
        $riwayats = Presensi::with(['pegawai.divisi'])
            ->when($request->tanggal_mulai, fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
            ->when($request->tanggal_akhir, fn ($q, $date) => $q->whereDate('tanggal', '<=', $date))
            ->when($request->pegawai_id, fn ($q, $id) => $q->where('pegawai_id', $id))
            ->orderBy('jam_masuk', 'desc')
            ->get();

        $filename = 'riwayat_presensi_' . Carbon::now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($riwayats) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Nama Pegawai',
                'Divisi',
                'Tanggal Kerja',
                'Jam Masuk',
                'Jam Keluar',
                'Durasi Menit',
                'Durasi',
                'Status',
                'Jenis Checkout',
                'Catatan',
            ]);

            foreach ($riwayats as $row) {
                $totalMenit = (int) ($row->total_jam ?? 0);

                fputcsv($file, [
                    $row->id,
                    $row->pegawai->nama ?? '-',
                    $row->pegawai->divisi->nama ?? '-',
                    $row->tanggal ? Carbon::parse($row->tanggal)->format('Y-m-d') : '-',
                    $row->jam_masuk ? Carbon::parse($row->jam_masuk)->format('Y-m-d H:i:s') : '-',
                    $row->jam_keluar ? Carbon::parse($row->jam_keluar)->format('Y-m-d H:i:s') : '-',
                    $totalMenit,
                    $this->formatDurasi($totalMenit),
                    $row->status_label,
                    $row->checkout_type ?? '-',
                    $row->catatan ?? '-',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
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
