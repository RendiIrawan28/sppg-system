<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GudangBahanKeluar;
use App\Models\GudangBahanMasuk;
use App\Services\SppgReportService;
use Illuminate\Http\Request;

class DashboardMonitoringController extends Controller
{
    public function __construct(
        protected SppgReportService $reports
    ) {}

    public function index(Request $request)
    {
        $tanggal = $this->reports->dateOrToday($request->input('tanggal'));

        $data = [
            // Asisten Lapangan
            'aslap_distribusi' => $this->mobileList('aslap_distribusi', $tanggal),
            'aslap_planning' => $this->mobileList('aslap_planning', $tanggal),

            // 6 Divisi Operasional
            'persiapan_bahan' => $this->mobileList('persiapan_bahan', $tanggal),
            'persiapan_limbah' => $this->mobileList('persiapan_limbah', $tanggal),

            'pengolahan_suhu' => $this->mobileList('pengolahan_suhu', $tanggal),
            'pengolahan_produksi' => $this->mobileList('pengolahan_produksi', $tanggal),

            'pemorsian_ompreng' => $this->mobileList('pemorsian_ompreng', $tanggal),
            'pemorsian_sisa' => $this->mobileList('pemorsian_sisa', $tanggal),

            'distribusi' => $this->mobileList('distribusi', $tanggal),

            'pencucian_limbah' => $this->mobileList('pencucian_limbah', $tanggal),
            'kebersihan_limbah' => $this->mobileList('kebersihan_limbah', $tanggal),

            // Gudang
            'gudang_stok' => $this->gudangStok(),
            'gudang_bahan_masuk' => $this->gudangBahanMasuk($tanggal),
            'gudang_bahan_keluar' => $this->gudangBahanKeluar($tanggal),
        ];

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Dashboard monitoring berhasil dimuat.',
            'tanggal' => $tanggal,
            'data' => $data,
        ]);
    }

    private function mobileList(string $category, string $tanggal): array
    {
        if (! $this->reports->hasCategory($category)) {
            return [];
        }

        return array_map(
            fn (array $row) => $this->reports->toMobileItem($category, $row),
            $this->reports->list($category, $tanggal)
        );
    }

    private function gudangBahanMasuk(string $tanggal)
    {
        return GudangBahanMasuk::whereDate('tanggal', $tanggal)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($item) => [
                'ID' => $item->id,
                'Tanggal' => optional($item->tanggal)->format('Y-m-d'),
                'Nama Petugas' => $item->nama_petugas,
                'Nama Supplier' => $item->nama_supplier,
                'Nama Bahan' => $item->nama_bahan,
                'Jumlah' => (float) $item->jumlah,
                'Satuan' => $item->satuan,
                'Kondisi Bahan' => $item->kondisi_bahan,
                'Tanggal Kedaluwarsa' => optional($item->tanggal_kedaluwarsa)->format('Y-m-d'),
                'Catatan' => $item->catatan,
                'Foto URL' => $item->foto_url,
            ])
            ->values();
    }

    private function gudangBahanKeluar(string $tanggal)
    {
        return GudangBahanKeluar::whereDate('tanggal', $tanggal)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($item) => [
                'ID' => $item->id,
                'Tanggal' => optional($item->tanggal)->format('Y-m-d'),
                'Nama Petugas' => $item->nama_petugas,
                'Nama Penerima' => $item->nama_penerima,
                'Divisi Penerima' => $item->divisi_penerima,
                'Nama Bahan' => $item->nama_bahan,
                'Jumlah' => (float) $item->jumlah,
                'Satuan' => $item->satuan,
                'Catatan' => $item->catatan,
                'Foto URL' => $item->foto_url,
            ])
            ->values();
    }

    private function gudangStok()
    {
        $bahanList = GudangBahanMasuk::select('nama_bahan', 'satuan')
            ->groupBy('nama_bahan', 'satuan')
            ->orderBy('nama_bahan')
            ->get();

        return $bahanList->map(function ($item) {
            $namaBahanNormal = strtolower(trim($item->nama_bahan));

            $totalMasuk = GudangBahanMasuk::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$namaBahanNormal])
                ->sum('jumlah');

            $totalKeluar = GudangBahanKeluar::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$namaBahanNormal])
                ->sum('jumlah');

            $stok = (float) $totalMasuk - (float) $totalKeluar;

            return [
                'Nama Bahan' => $item->nama_bahan,
                'Satuan' => $item->satuan,
                'Total Masuk' => (float) $totalMasuk,
                'Total Keluar' => (float) $totalKeluar,
                'Stok' => $stok,
                'Status' => $stok <= 0 ? 'Habis' : ($stok <= 5 ? 'Menipis' : 'Aman'),
            ];
        })->values();
    }
}