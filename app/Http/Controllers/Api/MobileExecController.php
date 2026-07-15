<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SppgReportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MobileUser;
use Illuminate\Support\Facades\Hash;
use App\Models\GudangBahanMasuk;
use App\Models\GudangBahanKeluar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MobileExecController extends Controller
{
    public function __construct(
        protected SppgReportService $reports
    ) {}

    public function handle(Request $request)
    {
        $action = $this->normalizeAction((string) $request->input('action', ''));

        if ($action === 'login') {
            return $this->login($request);
        }

        if ($action === 'asistendashboard') {
            return $this->asistenDashboard();
        }

        if ($action === 'getaslapdashboardalldivisi') {
            return $this->getAslapDashboardAllDivisi($request);
        }

        if ($action === 'getdetailmonitoringdivisi') {
            return $this->getDetailMonitoringDivisi($request);
        }

        if ($action === 'getpemorsianexport') {
            return $this->getPemorsianExport($request);
        }
        if ($action === 'getpemorsianexport') {
            return $this->getPemorsianExport($request);
        }

        // ===================== GUDANG =====================
        if ($action === 'getgudangdashboard') {
            return $this->getGudangDashboard($request);
        }

        if ($action === 'addgudangbahanmasuk') {
            return $this->addGudangBahanMasuk($request);
        }

        if ($action === 'updategudangbahanmasuk') {
            return $this->updateGudangBahanMasuk($request);
        }

        if ($action === 'deletegudangbahanmasuk') {
            return $this->deleteGudangBahanMasuk($request);
        }

        if ($action === 'getgudangbahanmasuk') {
            return $this->getGudangBahanMasuk($request);
        }

        if ($action === 'addgudangbahankeluar') {
            return $this->addGudangBahanKeluar($request);
        }

        if ($action === 'updategudangbahankeluar') {
            return $this->updateGudangBahanKeluar($request);
        }

        if ($action === 'deletegudangbahankeluar') {
            return $this->deleteGudangBahanKeluar($request);
        }

        if ($action === 'getgudangbahankeluar') {
            return $this->getGudangBahanKeluar($request);
        }

        if ($action === 'getgudangstok') {
            return $this->getGudangStok($request);
        }

        if ($action === 'getgudangexport') {
            return $this->getGudangExport($request);
        }
        // =================== END GUDANG ===================

        if ($category = $this->categoryFromSaveAction($action)) {
            return $this->saveByCategory($request, $category, true);
        }

        if ($category = $this->categoryFromSaveAction($action)) {
            return $this->saveByCategory($request, $category, true);
        }

        if ($category = $this->categoryFromGetAction($action)) {
            return $this->getByCategory($request, $category);
        }

        if ($category = $this->categoryFromUpdateAction($action)) {
            return $this->updateByCategory($request, $category);
        }

        if ($category = $this->categoryFromDeleteAction($action)) {
            return $this->deleteByCategory($request, $category);
        }

        $category = $request->input('category') ?: $request->input('kategori');

        if ($category && $this->reports->hasCategory($category)) {
            return $this->saveByCategory($request, $category, false);
        }

        return response()->json([
            'status' => 'error',
            'success' => false,
            'message' => 'Action atau category tidak dikenali.',
            'data' => null,
        ], 400);
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string', Rule::in($this->reports->categories())],
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'rows' => ['required', 'array'],
            'rows.*' => ['array'],
        ]);

        $saved = [];

        foreach ($validated['rows'] as $row) {
            $saved[] = $this->reports->store(
                $validated['category'],
                $validated['tanggal'],
                $row
            );
        }

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'count' => count($saved),
            'data' => $saved,
        ], 201);
    }

    public function destroyByCategory(string $category, int|string $id)
    {
        if (!$this->reports->hasCategory($category)) {
            return $this->fail('Category tidak dikenal.', 404);
        }

        $deleted = $this->reports->delete($category, $id);

        return response()->json([
            'status' => $deleted ? 'success' : 'error',
            'success' => $deleted,
            'message' => $deleted ? 'Data berhasil dihapus.' : 'Data tidak ditemukan.',
        ], $deleted ? 200 : 404);
    }

    protected function saveByCategory(Request $request, string $category, bool $legacyResponse)
    {
        $tanggal = $this->reports->dateOrToday(
            $request->input('tanggal') ?: $request->input('report_date')
        );

        if (!$request->input('tanggal') && !$request->input('report_date')) {
            return $this->fail('Tanggal wajib diisi.');
        }

        $payload = $this->reports->payloadFromRequest($request);
        $row = $this->reports->store($category, $tanggal, $payload);

        if ($legacyResponse) {
            return $this->mobileSavedResponse($category, $row, 'Data berhasil disimpan.');
        }

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Laporan berhasil disimpan.',
            'data' => $row,
        ], 201);
    }

    protected function getByCategory(Request $request, string $category)
    {
        $tanggal = $this->reports->dateOrToday($request->input('tanggal'));

        $data = array_map(
            fn(array $row) => $this->reports->toMobileItem($category, $row),
            $this->reports->list($category, $tanggal)
        );

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dimuat.',
            'data' => $data,
        ]);
    }

    protected function updateByCategory(Request $request, string $category)
    {
        $id = $request->input('id');

        if (!$id) {
            return $this->fail('ID data tidak valid.');
        }

        $tanggal = $this->reports->dateOrToday($request->input('tanggal'));
        $payload = $this->reports->payloadFromRequest($request);

        // Jika aplikasi Android update data tanpa memilih foto baru,
        // jangan biarkan foto lama tertimpa nilai kosong.
        $payload = $this->removeEmptyPhotoFields($payload);

        $row = $this->reports->update($category, $id, $tanggal, $payload);

        return $this->mobileSavedResponse($category, $row, 'Data berhasil diperbarui.');
    }
    protected function removeEmptyPhotoFields(array $payload): array
    {
        $photoKeys = [
            'Foto URL',
            'foto_url',
            'fotoUrl',
            'fotoURL',
            'foto',
            'Foto',
            'photo_url',
            'photoUrl',
            'image_url',
            'imageUrl',
        ];

        foreach ($photoKeys as $key) {
            if (array_key_exists($key, $payload) && blank($payload[$key])) {
                unset($payload[$key]);
            }
        }

        return $payload;
    }

    protected function deleteByCategory(Request $request, string $category)
    {
        $id = $request->input('id');

        if (!$id) {
            return $this->fail('ID data tidak valid.');
        }

        $deleted = $this->reports->delete($category, $id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Data berhasil dihapus.' : 'Data tidak ditemukan.',
            'data' => null,
        ], $deleted ? 200 : 404);
    }

    protected function mobileSavedResponse(string $category, array $row, string $message)
    {
        $fotoUrl = $row['Foto URL'] ?? '';
        $tanggal = (string) ($row['Tanggal'] ?? '');
        $total = $this->extractTotal($category, $row);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'id' => (string) ($row['ID'] ?? ''),
                'tanggal' => $tanggal,
                'total' => $total,
                'sheetName' => strtoupper($category) . '_' . $tanggal,
                'folderName' => $category,
                'fotoUrl' => $fotoUrl,
            ],
        ]);
    }

    protected function extractTotal(string $category, array $row): int|float
    {
        return match ($category) {
            'aslap_planning', 'aslap_distribusi' => $this->reports->toNumber($row['Total'] ?? 0),
            'distribusi' => $this->reports->toNumber($row['Jumlah Porsi'] ?? 0),
            default => 0,
        };
    }

    protected function login(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = trim((string) $request->input('password'));

        if ($username === '' || $password === '') {
            return response()->json([
                'success' => false,
                'message' => 'Username dan password wajib diisi.',
                'data' => null,
            ], 422);
        }

        $user = MobileUser::query()
            ->where('username', $username)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
                'data' => null,
            ], 401);
        }

        if (strtolower($user->status) !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif.',
                'data' => null,
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'id' => (string) $user->id,
                'username' => $user->username,
                'nama' => $user->nama,
                'role' => $user->role,
                'status' => $user->status,
                'divisi' => $user->divisi,
            ],
        ]);
    }

    protected function asistenDashboard()
    {
        $tanggalHariIni = now('Asia/Jakarta')->format('Y-m-d');
        $tanggalBesok = now('Asia/Jakarta')->addDay()->format('Y-m-d');

        return response()->json([
            'success' => true,
            'message' => 'Dashboard berhasil dimuat.',
            'data' => [
                'tanggalHariIni' => $tanggalHariIni,
                'tanggalBesok' => $tanggalBesok,
                'totalDistribusiHariIni' => (int) $this->reports->sumByDate('aslap_distribusi', $tanggalHariIni, 'Total'),
                'totalPlanningBesok' => (int) $this->reports->sumByDate('aslap_planning', $tanggalBesok, 'Total'),
            ],
        ]);
    }

    protected function getPemorsianExport(Request $request)
    {
        $tanggal = $this->reports->dateOrToday($request->input('tanggal'));

        $ompreng = array_map(
            fn(array $row) => $this->reports->toMobileItem('pemorsian_ompreng', $row),
            $this->reports->list('pemorsian_ompreng', $tanggal)
        );

        $sisa = array_map(
            fn(array $row) => $this->reports->toMobileItem('pemorsian_sisa', $row),
            $this->reports->list('pemorsian_sisa', $tanggal)
        );

        return response()->json([
            'success' => true,
            'message' => 'Data export pemorsian berhasil dimuat.',
            'data' => [
                'tanggal' => $tanggal,
                'ompreng' => $ompreng,
                'sisa' => $sisa,
            ],
        ]);
    }

    protected function getAslapDashboardAllDivisi(Request $request)
    {
        $tanggal = $this->reports->dateOrToday($request->input('tanggal'));

        $persiapan = $this->reports->countByDate('persiapan_bahan', $tanggal)
            + $this->reports->countByDate('persiapan_limbah', $tanggal);

        $pengolahanSuhu = $this->reports->countByDate('pengolahan_suhu', $tanggal);
        $pengolahanProduksi = $this->reports->countByDate('pengolahan_produksi', $tanggal);
        $totalPengolahan = $pengolahanSuhu + $pengolahanProduksi;

        $pemorsian = $this->reports->countByDate('pemorsian_ompreng', $tanggal)
            + $this->reports->countByDate('pemorsian_sisa', $tanggal);

        $distribusi = $this->reports->countByDate('distribusi', $tanggal);
        $pencucian = $this->reports->countByDate('pencucian_limbah', $tanggal);
        $kebersihan = $this->reports->countByDate('kebersihan_limbah', $tanggal);

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
                'aslapDistribusi' => (int) $this->reports->sumByDate('aslap_distribusi', $tanggal, 'Total'),
                'aslapPlanning' => (int) $this->reports->sumByDate('aslap_planning', $tanggal, 'Total'),
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
        $divisi = $this->normalizeAction((string) $request->input('divisi'));
        $tanggal = $this->reports->dateOrToday($request->input('tanggal'));

        $categories = match ($divisi) {
            'persiapan' => ['persiapan_bahan', 'persiapan_limbah'],
            'pengolahan' => ['pengolahan_suhu', 'pengolahan_produksi'],
            'pemorsian' => ['pemorsian_ompreng', 'pemorsian_sisa'],
            'distribusi' => ['distribusi'],
            'pencucian' => ['pencucian_limbah'],
            'kebersihan' => ['kebersihan_limbah'],
            default => [],
        };

        $data = [];

        foreach ($categories as $category) {
            foreach ($this->reports->list($category, $tanggal) as $row) {
                $data[] = $this->detailItem($category, $row);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail berhasil dimuat.',
            'data' => $data,
        ]);
    }

    protected function detailItem(string $category, array $row): array
    {
        return match ($category) {
            'persiapan_bahan' => [
                'title' => (string) ($row['Nama Bahan'] ?? '-'),
                'subtitle' => 'Pemeriksaan Bahan',
                'value1' => 'Baik: ' . ($row['Baik'] ?? 0),
                'value2' => 'Sedang: ' . ($row['Sedang'] ?? 0),
                'value3' => 'Rusak: ' . ($row['Rusak'] ?? 0),
            ],
            'persiapan_limbah', 'pencucian_limbah', 'kebersihan_limbah' => [
                'title' => (string) ($row['Jenis Limbah'] ?? 'Limbah'),
                'subtitle' => match ($category) {
                    'persiapan_limbah' => 'Limbah Persiapan',
                    'pencucian_limbah' => 'Limbah Pencucian',
                    default => 'Limbah Kebersihan',
                },
                'value1' => 'Berat: ' . ($row['Berat Limbah Kg'] ?? 0) . ' Kg',
                'value2' => 'BA: ' . ($row['NO BA'] ?? '-'),
                'value3' => (string) ($row['Nama Petugas'] ?? '-'),
            ],
            'pengolahan_suhu' => [
                'title' => (string) ($row['Nama Produk'] ?? '-'),
                'subtitle' => 'Pemantauan Suhu',
                'value1' => 'Suhu: ' . ($row['Suhu Produk'] ?? 0) . '°C',
                'value2' => 'Waktu: ' . ($row['Waktu'] ?? '-'),
                'value3' => (string) ($row['Nama Petugas'] ?? '-'),
            ],
            'pengolahan_produksi' => [
                'title' => (string) ($row['Menu'] ?? '-'),
                'subtitle' => 'Monitoring Produksi',
                'value1' => 'Bahan: ' . ($row['Bahan Baku'] ?? '-'),
                'value2' => 'Hasil: ' . ($row['Hasil Akhir'] ?? 0) . ' ' . ($row['Satuan Hasil'] ?? ''),
                'value3' => (string) ($row['Nama Petugas'] ?? '-'),
            ],
            'pemorsian_ompreng' => [
                'title' => (string) ($row['Rute'] ?? '-'),
                'subtitle' => 'Pemorsian Ompreng',
                'value1' => 'Besar: ' . ($row['Qty Ompreng Besar'] ?? 0),
                'value2' => 'Kecil: ' . ($row['Qty Ompreng Kecil'] ?? 0),
                'value3' => 'Waktu: ' . ($row['Waktu Pemorsian'] ?? '-'),
            ],
            'pemorsian_sisa' => [
                'title' => (string) ($row['Jenis Makanan'] ?? '-'),
                'subtitle' => 'Sisa Makanan',
                'value1' => 'Rute: ' . ($row['Rute'] ?? '-'),
                'value2' => 'Berat: ' . ($row['Berat Sisa Kg'] ?? 0) . ' Kg',
                'value3' => (string) ($row['Keterangan'] ?? '-'),
            ],
            'distribusi' => [
                'title' => (string) ($row['Lokasi Tujuan'] ?? '-'),
                'subtitle' => 'Distribusi Makanan',
                'value1' => 'Porsi: ' . ($row['Jumlah Porsi'] ?? 0),
                'value2' => 'Status: ' . ($row['Status'] ?? '-'),
                'value3' => (string) ($row['Nama Petugas'] ?? '-'),
            ],
            default => [
                'title' => 'Data',
                'subtitle' => $category,
                'value1' => '',
                'value2' => '',
                'value3' => '',
            ],
        };
    }

    protected function statusFromCount(int $count): string
    {
        return $count > 0 ? 'Sudah Input' : 'Belum Input';
    }

    protected function categoryFromSaveAction(string $action): ?string
    {
        return $this->lookupAction($action, [
            'asistensaveplanning' => 'aslap_planning',
            'asistensavedistribusi' => 'aslap_distribusi',
            'adddistribusi' => 'distribusi',
            'savedistribusi' => 'distribusi',
            'addpersiapanbahan' => 'persiapan_bahan',
            'addpersiapan' => 'persiapan_bahan',
            'savepersiapan' => 'persiapan_bahan',
            'savepersiapanbahan' => 'persiapan_bahan',
            'addpersiapanlimbah' => 'persiapan_limbah',
            'savepersiapanlimbah' => 'persiapan_limbah',
            'addpengolahansuhu' => 'pengolahan_suhu',
            'addpengolahan' => 'pengolahan_suhu',
            'savepengolahan' => 'pengolahan_suhu',
            'savepengolahansuhu' => 'pengolahan_suhu',
            'addpengolahanproduksi' => 'pengolahan_produksi',
            'savepengolahanproduksi' => 'pengolahan_produksi',
            'addpemorsianompreng' => 'pemorsian_ompreng',
            'savepemorsianompreng' => 'pemorsian_ompreng',
            'addpemorsiansisa' => 'pemorsian_sisa',
            'savepemorsiansisa' => 'pemorsian_sisa',
            'addpencucianlimbah' => 'pencucian_limbah',
            'savepencucianlimbah' => 'pencucian_limbah',
            'addkebersihanlimbah' => 'kebersihan_limbah',
            'savekebersihanlimbah' => 'kebersihan_limbah',
        ]);
    }

    protected function categoryFromGetAction(string $action): ?string
    {
        return $this->lookupAction($action, [
            'asistengetplanning' => 'aslap_planning',
            'asistengetdistribusi' => 'aslap_distribusi',
            'getdistribusi' => 'distribusi',
            'getpersiapanbahan' => 'persiapan_bahan',
            'getpersiapan' => 'persiapan_bahan',
            'getpersiapanlimbah' => 'persiapan_limbah',
            'getpengolahansuhu' => 'pengolahan_suhu',
            'getpengolahan' => 'pengolahan_suhu',
            'getpengolahanproduksi' => 'pengolahan_produksi',
            'getpemorsianompreng' => 'pemorsian_ompreng',
            'getpemorsiansisa' => 'pemorsian_sisa',
            'getpencucianlimbah' => 'pencucian_limbah',
            'getkebersihanlimbah' => 'kebersihan_limbah',
        ]);
    }

    protected function categoryFromUpdateAction(string $action): ?string
    {
        return $this->lookupAction($action, [
            'asistenupdateplanning' => 'aslap_planning',
            'asistenupdatedistribusi' => 'aslap_distribusi',
            'updatedistribusi' => 'distribusi',
            'updatepersiapanbahan' => 'persiapan_bahan',
            'updatepersiapan' => 'persiapan_bahan',
            'updatepersiapanlimbah' => 'persiapan_limbah',
            'updatepengolahansuhu' => 'pengolahan_suhu',
            'updatepengolahan' => 'pengolahan_suhu',
            'updatepengolahanproduksi' => 'pengolahan_produksi',
            'updatepemorsianompreng' => 'pemorsian_ompreng',
            'updatepemorsiansisa' => 'pemorsian_sisa',
            'updatepencucianlimbah' => 'pencucian_limbah',
            'updatepencucian' => 'pencucian_limbah',
            'updatekebersihanlimbah' => 'kebersihan_limbah',
        ]);
    }

    protected function categoryFromDeleteAction(string $action): ?string
    {
        return $this->lookupAction($action, [
            'asistendeleteplanning' => 'aslap_planning',
            'asistendeletedistribusi' => 'aslap_distribusi',
            'deletedistribusi' => 'distribusi',
            'deletepersiapanbahan' => 'persiapan_bahan',
            'deletepersiapan' => 'persiapan_bahan',
            'deletepersiapanlimbah' => 'persiapan_limbah',
            'deletepengolahansuhu' => 'pengolahan_suhu',
            'deletepengolahan' => 'pengolahan_suhu',
            'deletepengolahanproduksi' => 'pengolahan_produksi',
            'deletepemorsianompreng' => 'pemorsian_ompreng',
            'deletepemorsiansisa' => 'pemorsian_sisa',
            'deletepencucianlimbah' => 'pencucian_limbah',
            'deletekebersihanlimbah' => 'kebersihan_limbah',
        ]);
    }

    protected function getGudangDashboard(Request $request)
    {
        $tanggal = $request->input('tanggal') ?: now('Asia/Jakarta')->toDateString();

        $totalMasuk = GudangBahanMasuk::whereDate('tanggal', $tanggal)->sum('jumlah');
        $totalKeluar = GudangBahanKeluar::whereDate('tanggal', $tanggal)->sum('jumlah');

        $jumlahDataMasuk = GudangBahanMasuk::whereDate('tanggal', $tanggal)->count();
        $jumlahDataKeluar = GudangBahanKeluar::whereDate('tanggal', $tanggal)->count();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Dashboard gudang berhasil dimuat.',
            'data' => [
                'tanggal' => $tanggal,
                'total_masuk' => (float) $totalMasuk,
                'total_keluar' => (float) $totalKeluar,
                'jumlah_data_masuk' => $jumlahDataMasuk,
                'jumlah_data_keluar' => $jumlahDataKeluar,
                'stok' => $this->buildGudangStok(),
            ],
        ]);
    }

    protected function addGudangBahanMasuk(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'nama_petugas' => ['nullable', 'string', 'max:255'],
            'nama_supplier' => ['nullable', 'string', 'max:255'],
            'nama_bahan' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'kondisi_bahan' => ['nullable', 'string', 'max:255'],
            'tanggal_kedaluwarsa' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string'],
            'foto_url' => ['nullable', 'string'],
        ]);

        $record = GudangBahanMasuk::create($data);

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data bahan masuk berhasil disimpan.',
            'data' => $record,
        ], 201);
    }

    protected function updateGudangBahanMasuk(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('ID bahan masuk wajib diisi.');
        }

        $record = GudangBahanMasuk::find($id);

        if (! $record) {
            return $this->fail('Data bahan masuk tidak ditemukan.', 404);
        }

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'nama_petugas' => ['nullable', 'string', 'max:255'],
            'nama_supplier' => ['nullable', 'string', 'max:255'],
            'nama_bahan' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'kondisi_bahan' => ['nullable', 'string', 'max:255'],
            'tanggal_kedaluwarsa' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string'],
            'foto_url' => ['nullable', 'string'],
        ]);

        $record->update($data);

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data bahan masuk berhasil diperbarui.',
            'data' => $record,
        ]);
    }

    protected function deleteGudangBahanMasuk(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('ID bahan masuk wajib diisi.');
        }

        $record = GudangBahanMasuk::find($id);

        if (! $record) {
            return $this->fail('Data bahan masuk tidak ditemukan.', 404);
        }

        $record->delete();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data bahan masuk berhasil dihapus.',
            'data' => null,
        ]);
    }

    protected function getGudangBahanMasuk(Request $request)
    {
        $tanggal = $request->input('tanggal');

        $query = GudangBahanMasuk::query();

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        $data = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data bahan masuk berhasil dimuat.',
            'data' => $data,
        ]);
    }

    protected function addGudangBahanKeluar(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'nama_petugas' => ['nullable', 'string', 'max:255'],
            'nama_penerima' => ['nullable', 'string', 'max:255'],
            'divisi_penerima' => ['nullable', 'string', 'max:255'],
            'nama_bahan' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
            'foto_url' => ['nullable', 'string'],
        ]);

        $sisaStok = $this->getSisaStokBahan($data['nama_bahan']);

        if ((float) $data['jumlah'] > $sisaStok) {
            return $this->fail('Stok bahan tidak mencukupi. Sisa stok saat ini: ' . $sisaStok, 422);
        }

        $record = GudangBahanKeluar::create($data);

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data bahan keluar berhasil disimpan.',
            'data' => $record,
        ], 201);
    }

    protected function updateGudangBahanKeluar(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('ID bahan keluar wajib diisi.');
        }

        $record = GudangBahanKeluar::find($id);

        if (! $record) {
            return $this->fail('Data bahan keluar tidak ditemukan.', 404);
        }

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'nama_petugas' => ['nullable', 'string', 'max:255'],
            'nama_penerima' => ['nullable', 'string', 'max:255'],
            'divisi_penerima' => ['nullable', 'string', 'max:255'],
            'nama_bahan' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
            'foto_url' => ['nullable', 'string'],
        ]);

        $stokTersedia = $this->getSisaStokBahan($data['nama_bahan']);

        if ($this->normalizeGudangNamaBahan($record->nama_bahan) === $this->normalizeGudangNamaBahan($data['nama_bahan'])) {
            $stokTersedia += (float) $record->jumlah;
        }

        if ((float) $data['jumlah'] > $stokTersedia) {
            return $this->fail('Stok bahan tidak mencukupi. Sisa stok saat ini: ' . $stokTersedia, 422);
        }

        $record->update($data);

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data bahan keluar berhasil diperbarui.',
            'data' => $record,
        ]);
    }

    protected function deleteGudangBahanKeluar(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('ID bahan keluar wajib diisi.');
        }

        $record = GudangBahanKeluar::find($id);

        if (! $record) {
            return $this->fail('Data bahan keluar tidak ditemukan.', 404);
        }

        $record->delete();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data bahan keluar berhasil dihapus.',
            'data' => null,
        ]);
    }

    protected function getGudangBahanKeluar(Request $request)
    {
        $tanggal = $request->input('tanggal');

        $query = GudangBahanKeluar::query();

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        $data = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data bahan keluar berhasil dimuat.',
            'data' => $data,
        ]);
    }

    protected function getGudangStok(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data stok gudang berhasil dimuat.',
            'data' => $this->buildGudangStok(),
        ]);
    }

    protected function getGudangExport(Request $request)
    {
        $tanggal = $request->input('tanggal') ?: now('Asia/Jakarta')->toDateString();

        $bahanMasuk = GudangBahanMasuk::whereDate('tanggal', $tanggal)
            ->orderBy('nama_bahan')
            ->get();

        $bahanKeluar = GudangBahanKeluar::whereDate('tanggal', $tanggal)
            ->orderBy('nama_bahan')
            ->get();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data export gudang berhasil dimuat.',
            'data' => [
                'tanggal' => $tanggal,
                'bahan_masuk' => $bahanMasuk,
                'bahan_keluar' => $bahanKeluar,
                'stok' => $this->buildGudangStok(),
            ],
        ]);
    }

    protected function getSisaStokBahan(string $namaBahan): float
    {
        $namaBahan = $this->normalizeGudangNamaBahan($namaBahan);

        $totalMasuk = GudangBahanMasuk::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$namaBahan])
            ->sum('jumlah');

        $totalKeluar = GudangBahanKeluar::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$namaBahan])
            ->sum('jumlah');

        return (float) $totalMasuk - (float) $totalKeluar;
    }

    protected function buildGudangStok()
    {
        $bahanList = GudangBahanMasuk::select('nama_bahan', 'satuan')
            ->groupBy('nama_bahan', 'satuan')
            ->orderBy('nama_bahan')
            ->get();

        return $bahanList->map(function ($item) {
            $namaBahanNormal = $this->normalizeGudangNamaBahan($item->nama_bahan);

            $totalMasuk = GudangBahanMasuk::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$namaBahanNormal])
                ->sum('jumlah');

            $totalKeluar = GudangBahanKeluar::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$namaBahanNormal])
                ->sum('jumlah');

            $stok = (float) $totalMasuk - (float) $totalKeluar;

            return [
                'nama_bahan' => $item->nama_bahan,
                'satuan' => $item->satuan,
                'total_masuk' => (float) $totalMasuk,
                'total_keluar' => (float) $totalKeluar,
                'stok' => $stok,
                'status' => $stok <= 0 ? 'Habis' : ($stok <= 5 ? 'Menipis' : 'Aman'),
            ];
        })->values();
    }

    protected function normalizeGudangNamaBahan(?string $namaBahan): string
    {
        return strtolower(trim((string) $namaBahan));
    }

    protected function lookupAction(string $action, array $map): ?string
    {
        return $map[$action] ?? null;
    }

    protected function normalizeAction(string $action): string
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', trim($action)) ?: '');
    }

    protected function fail(string $message, int $status = 422)
    {
        return response()->json([
            'status' => 'error',
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $status);
    }
}
