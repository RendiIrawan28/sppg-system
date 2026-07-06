<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AslapAppApiController extends Controller
{
    public function handle(Request $request)
    {
        $action = $request->input('action');

        return match ($action) {
            'login' => $this->login($request),

            'asistenDashboard' => $this->asistenDashboard($request),
            'getAslapDashboardAllDivisi' => $this->getAslapDashboardAllDivisi($request),
            'getDetailMonitoringDivisi' => $this->getDetailMonitoringDivisi($request),

            'asistenSaveDistribusi' => $this->saveDistribusi($request),
            'asistenGetDistribusi' => $this->getDistribusi($request),
            'asistenUpdateDistribusi' => $this->updateDistribusi($request),
            'asistenDeleteDistribusi' => $this->deleteDistribusi($request),

            'asistenSavePlanning' => $this->savePlanning($request),
            'asistenGetPlanning' => $this->getPlanning($request),
            'asistenUpdatePlanning' => $this->updatePlanning($request),
            'asistenDeletePlanning' => $this->deletePlanning($request),

            default => response()->json([
                'success' => false,
                'message' => 'Action tidak dikenali.',
                'data' => null,
            ], 400),
        };
    }

    protected function login(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = trim((string) $request->input('password'));

        $validUsername = env('ASLAP_USERNAME', 'aslap');
        $validPassword = env('ASLAP_PASSWORD', '123456');

        if ($username !== $validUsername || $password !== $validPassword) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
                'data' => null,
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'id' => '1',
                'username' => $username,
                'nama' => 'Asisten Lapangan',
                'role' => 'admin',
                'divisi' => 'asisten lapangan',
                'status' => 'aktif',
            ],
        ]);
    }

    protected function asistenDashboard(Request $request)
    {
        $tanggalHariIni = now('Asia/Jakarta')->format('Y-m-d');
        $tanggalBesok = now('Asia/Jakarta')->addDay()->format('Y-m-d');

        return response()->json([
            'success' => true,
            'message' => 'Dashboard berhasil dimuat.',
            'data' => [
                'tanggalHariIni' => $tanggalHariIni,
                'tanggalBesok' => $tanggalBesok,
                'totalDistribusiHariIni' => $this->sumColumnByDate(
                    'aslap_distribusi_reports',
                    'tanggal',
                    $tanggalHariIni,
                    'total'
                ),
                'totalPlanningBesok' => $this->sumColumnByDate(
                    'aslap_planning_reports',
                    'tanggal',
                    $tanggalBesok,
                    'total'
                ),
            ],
        ]);
    }

    protected function getAslapDashboardAllDivisi(Request $request)
    {
        $tanggal = $this->dateOrToday($request->input('tanggal'));

        $persiapan = $this->countTables($tanggal, [
            'persiapan_bahan_reports',
            'persiapan_limbah_reports',
        ]);

        $pengolahanSuhu = $this->countTable($tanggal, 'pengolahan_suhu_reports');
        $pengolahanProduksi = $this->countTable($tanggal, 'pengolahan_produksi_reports');
        $totalPengolahan = $pengolahanSuhu + $pengolahanProduksi;

        $pemorsian = $this->countTables($tanggal, [
            'pemorsian_ompreng_reports',
            'pemorsian_sisa_reports',
        ]);

        $distribusi = $this->countTable($tanggal, 'distribusi_reports');
        $pencucian = $this->countTable($tanggal, 'pencucian_limbah_reports');
        $kebersihan = $this->countTable($tanggal, 'kebersihan_limbah_reports');

        $aslapDistribusi = $this->sumColumnByDate(
            'aslap_distribusi_reports',
            'tanggal',
            $tanggal,
            'total'
        );

        $aslapPlanning = $this->sumColumnByDate(
            'aslap_planning_reports',
            'tanggal',
            $tanggal,
            'total'
        );

        return response()->json([
            'success' => true,
            'message' => 'Dashboard berhasil dimuat.',
            'data' => [
                'tanggal' => $tanggal,
                'tanggalPlanning' => $tanggal,

                'persiapan' => $persiapan,
                'pengolahanSuhu' => $pengolahanSuhu,
                'pengolahanProduksi' => $pengolahanProduksi,
                'totalPengolahan' => $totalPengolahan,
                'pemorsian' => $pemorsian,
                'distribusi' => $distribusi,
                'pencucian' => $pencucian,
                'kebersihan' => $kebersihan,

                'aslapDistribusi' => $aslapDistribusi,
                'aslapPlanning' => $aslapPlanning,

                'statusPersiapan' => $this->statusFromCount($persiapan),
                'statusPengolahan' => $this->statusFromCount($totalPengolahan),
                'statusPemorsian' => $this->statusFromCount($pemorsian),
                'statusDistribusi' => $this->statusFromCount($distribusi),
                'statusPencucian' => $this->statusFromCount($pencucian),
                'statusKebersihan' => $this->statusFromCount($kebersihan),
            ],
        ]);
    }

    protected function getDetailMonitoringDivisi(Request $request)
    {
        $divisi = strtolower(trim((string) $request->input('divisi')));
        $tanggal = $this->dateOrToday($request->input('tanggal'));

        $data = [];

        if ($divisi === 'persiapan') {
            $bahan = DB::table('persiapan_bahan_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($bahan as $row) {
                $data[] = [
                    'title' => $row->nama_bahan ?: '-',
                    'subtitle' => 'Pemeriksaan Bahan',
                    'value1' => 'Baik: ' . ($row->baik ?? 0),
                    'value2' => 'Sedang: ' . ($row->sedang ?? 0),
                    'value3' => 'Rusak: ' . ($row->rusak ?? 0),
                ];
            }

            $limbah = DB::table('persiapan_limbah_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($limbah as $row) {
                $data[] = [
                    'title' => $row->jenis_limbah ?: 'Limbah',
                    'subtitle' => 'Limbah Persiapan',
                    'value1' => 'Berat: ' . ($row->berat_limbah_kg ?? 0) . ' Kg',
                    'value2' => 'BA: ' . ($row->no_ba ?: '-'),
                    'value3' => $row->nama_petugas ?: '-',
                ];
            }
        }

        if ($divisi === 'pengolahan') {
            $suhu = DB::table('pengolahan_suhu_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($suhu as $row) {
                $data[] = [
                    'title' => $row->nama_produk ?: '-',
                    'subtitle' => 'Pemantauan Suhu',
                    'value1' => 'Suhu: ' . ($row->suhu_produk ?? 0) . '°C',
                    'value2' => 'Waktu: ' . ($row->waktu ?: '-'),
                    'value3' => $row->nama_petugas ?: '-',
                ];
            }

            $produksi = DB::table('pengolahan_produksi_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($produksi as $row) {
                $data[] = [
                    'title' => $row->menu ?: '-',
                    'subtitle' => 'Monitoring Produksi',
                    'value1' => 'Bahan: ' . ($row->bahan_baku ?: '-'),
                    'value2' => 'Hasil: ' . ($row->hasil_akhir ?? 0) . ' ' . ($row->satuan_hasil ?: ''),
                    'value3' => $row->nama_petugas ?: '-',
                ];
            }
        }

        if ($divisi === 'pemorsian') {
            $ompreng = DB::table('pemorsian_ompreng_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($ompreng as $row) {
                $data[] = [
                    'title' => $row->rute ?: '-',
                    'subtitle' => 'Pemorsian Ompreng',
                    'value1' => 'Besar: ' . ($row->qty_ompreng_besar ?? 0),
                    'value2' => 'Kecil: ' . ($row->qty_ompreng_kecil ?? 0),
                    'value3' => 'Waktu: ' . ($row->waktu_pemorsian ?: '-'),
                ];
            }

            $sisa = DB::table('pemorsian_sisa_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($sisa as $row) {
                $data[] = [
                    'title' => $row->jenis_makanan ?: '-',
                    'subtitle' => 'Sisa Makanan',
                    'value1' => 'Rute: ' . ($row->rute ?: '-'),
                    'value2' => 'Berat: ' . ($row->berat_sisa_kg ?? 0) . ' Kg',
                    'value3' => $row->keterangan ?: '-',
                ];
            }
        }

        if ($divisi === 'distribusi') {
            $rows = DB::table('distribusi_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($rows as $row) {
                $data[] = [
                    'title' => $row->lokasi_tujuan ?: '-',
                    'subtitle' => 'Distribusi Makanan',
                    'value1' => 'Porsi: ' . ($row->jumlah_porsi ?? 0),
                    'value2' => 'Status: ' . ($row->status ?: '-'),
                    'value3' => $row->nama_petugas ?: '-',
                ];
            }
        }

        if ($divisi === 'pencucian') {
            $rows = DB::table('pencucian_limbah_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($rows as $row) {
                $data[] = [
                    'title' => $row->jenis_limbah ?: 'Limbah Pencucian',
                    'subtitle' => 'Limbah Pencucian',
                    'value1' => 'Berat: ' . ($row->berat_limbah_kg ?? 0) . ' Kg',
                    'value2' => 'BA: ' . ($row->no_ba ?: '-'),
                    'value3' => $row->nama_petugas ?: '-',
                ];
            }
        }

        if ($divisi === 'kebersihan') {
            $rows = DB::table('kebersihan_limbah_reports')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('id')
                ->get();

            foreach ($rows as $row) {
                $data[] = [
                    'title' => $row->jenis_limbah ?: 'Limbah Kebersihan',
                    'subtitle' => 'Limbah Kebersihan',
                    'value1' => 'Berat: ' . ($row->berat_limbah_kg ?? 0) . ' Kg',
                    'value2' => 'BA: ' . ($row->no_ba ?: '-'),
                    'value3' => $row->nama_petugas ?: '-',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail berhasil dimuat.',
            'data' => $data,
        ]);
    }

    protected function saveDistribusi(Request $request)
    {
        $tanggal = $this->dateOrToday($request->input('tanggal'));
        $nama = trim((string) $request->input('nama'));

        if ($nama === '') {
            return $this->fail('Nama penerima wajib diisi.');
        }

        $tendik = $this->toInt($request->input('tendik'));
        $besar = $this->toInt($request->input('porsiBesar'));
        $kecil = $this->toInt($request->input('porsiKecil'));
        $total = $tendik + $besar + $kecil;

        $id = DB::table('aslap_distribusi_reports')->insertGetId([
            'tanggal' => $tanggal,
            'nomor' => $this->nextNomor('aslap_distribusi_reports', $tanggal),
            'nama_penerima' => $nama,
            'tendik' => $tendik,
            'porsi_besar' => $besar,
            'porsi_kecil' => $kecil,
            'total' => $total,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->successSaved($id, $tanggal, $total);
    }

    protected function getDistribusi(Request $request)
    {
        $tanggal = $this->dateOrToday($request->input('tanggal'));

        $rows = DB::table('aslap_distribusi_reports')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('id')
            ->get();

        $data = [];

        foreach ($rows as $index => $row) {
            $data[] = [
                'id' => (string) $row->id,
                'no' => (string) ($row->nomor ?: ($index + 1)),
                'nama' => (string) ($row->nama_penerima ?: ''),
                'tendik' => (string) ($row->tendik ?? 0),
                'porsiBesar' => (string) ($row->porsi_besar ?? 0),
                'porsiKecil' => (string) ($row->porsi_kecil ?? 0),
                'total' => (string) ($row->total ?? 0),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Data distribusi berhasil dimuat.',
            'data' => $data,
        ]);
    }

    protected function updateDistribusi(Request $request)
    {
        $id = $request->input('id');
        $tanggal = $this->dateOrToday($request->input('tanggal'));
        $nama = trim((string) $request->input('nama'));

        if (!$id) {
            return $this->fail('ID data tidak valid.');
        }

        if ($nama === '') {
            return $this->fail('Nama penerima wajib diisi.');
        }

        $tendik = $this->toInt($request->input('tendik'));
        $besar = $this->toInt($request->input('porsiBesar'));
        $kecil = $this->toInt($request->input('porsiKecil'));
        $total = $tendik + $besar + $kecil;

        DB::table('aslap_distribusi_reports')
            ->where('id', $id)
            ->update([
                'tanggal' => $tanggal,
                'nama_penerima' => $nama,
                'tendik' => $tendik,
                'porsi_besar' => $besar,
                'porsi_kecil' => $kecil,
                'total' => $total,
                'updated_at' => now(),
            ]);

        return $this->successSaved($id, $tanggal, $total, 'Data distribusi berhasil diperbarui.');
    }

    protected function deleteDistribusi(Request $request)
    {
        $id = $request->input('id');

        if (!$id) {
            return $this->fail('ID data tidak valid.');
        }

        DB::table('aslap_distribusi_reports')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data distribusi berhasil dihapus.',
            'data' => null,
        ]);
    }

    protected function savePlanning(Request $request)
    {
        $tanggal = $this->dateOrToday($request->input('tanggal'));
        $nama = trim((string) $request->input('nama'));

        if ($nama === '') {
            return $this->fail('Nama penerima wajib diisi.');
        }

        $besar = $this->toInt($request->input('porsiBesar'));
        $kecil = $this->toInt($request->input('porsiKecil'));
        $total = $besar + $kecil;

        $id = DB::table('aslap_planning_reports')->insertGetId([
            'tanggal' => $tanggal,
            'nomor' => $this->nextNomor('aslap_planning_reports', $tanggal),
            'nama_penerima' => $nama,
            'porsi_besar' => $besar,
            'porsi_kecil' => $kecil,
            'total' => $total,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->successSaved($id, $tanggal, $total);
    }

    protected function getPlanning(Request $request)
    {
        $tanggal = $this->dateOrToday($request->input('tanggal'));

        $rows = DB::table('aslap_planning_reports')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('id')
            ->get();

        $data = [];

        foreach ($rows as $index => $row) {
            $data[] = [
                'id' => (string) $row->id,
                'no' => (string) ($row->nomor ?: ($index + 1)),
                'nama' => (string) ($row->nama_penerima ?: ''),
                'porsiBesar' => (string) ($row->porsi_besar ?? 0),
                'porsiKecil' => (string) ($row->porsi_kecil ?? 0),
                'total' => (string) ($row->total ?? 0),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Data planning berhasil dimuat.',
            'data' => $data,
        ]);
    }

    protected function updatePlanning(Request $request)
    {
        $id = $request->input('id');
        $tanggal = $this->dateOrToday($request->input('tanggal'));
        $nama = trim((string) $request->input('nama'));

        if (!$id) {
            return $this->fail('ID data tidak valid.');
        }

        if ($nama === '') {
            return $this->fail('Nama penerima wajib diisi.');
        }

        $besar = $this->toInt($request->input('porsiBesar'));
        $kecil = $this->toInt($request->input('porsiKecil'));
        $total = $besar + $kecil;

        DB::table('aslap_planning_reports')
            ->where('id', $id)
            ->update([
                'tanggal' => $tanggal,
                'nama_penerima' => $nama,
                'porsi_besar' => $besar,
                'porsi_kecil' => $kecil,
                'total' => $total,
                'updated_at' => now(),
            ]);

        return $this->successSaved($id, $tanggal, $total, 'Data planning berhasil diperbarui.');
    }

    protected function deletePlanning(Request $request)
    {
        $id = $request->input('id');

        if (!$id) {
            return $this->fail('ID data tidak valid.');
        }

        DB::table('aslap_planning_reports')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data planning berhasil dihapus.',
            'data' => null,
        ]);
    }

    protected function dateOrToday(?string $tanggal): string
    {
        if ($tanggal && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return $tanggal;
        }

        return now('Asia/Jakarta')->format('Y-m-d');
    }

    protected function nextNomor(string $table, string $tanggal): int
    {
        return DB::table($table)
            ->whereDate('tanggal', $tanggal)
            ->count() + 1;
    }

    protected function countTable(string $tanggal, string $table): int
    {
        return DB::table($table)
            ->whereDate('tanggal', $tanggal)
            ->count();
    }

    protected function countTables(string $tanggal, array $tables): int
    {
        $total = 0;

        foreach ($tables as $table) {
            $total += $this->countTable($tanggal, $table);
        }

        return $total;
    }

    protected function sumColumnByDate(
        string $table,
        string $dateColumn,
        string $tanggal,
        string $sumColumn
    ): int {
        return (int) DB::table($table)
            ->whereDate($dateColumn, $tanggal)
            ->sum($sumColumn);
    }

    protected function statusFromCount(int $count): string
    {
        return $count > 0 ? 'Sudah Input' : 'Belum Input';
    }

    protected function toInt(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $cleaned = preg_replace('/[^\d-]/', '', (string) $value);

        if ($cleaned === '' || !is_numeric($cleaned)) {
            return 0;
        }

        return (int) $cleaned;
    }

    protected function successSaved(
        int|string $id,
        string $tanggal,
        int $total,
        string $message = 'Data berhasil disimpan.'
    ) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'id' => (string) $id,
                'tanggal' => $tanggal,
                'total' => $total,
            ],
        ]);
    }

    protected function fail(string $message, int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $status);
    }
}