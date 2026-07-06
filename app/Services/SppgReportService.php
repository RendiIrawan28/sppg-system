<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SppgReportService
{
    public function configs(): array
    {
        return [
            'aslap_planning' => [
                'table' => 'aslap_planning_reports',
                'fields' => [
                    'No' => ['column' => 'nomor', 'aliases' => ['No', 'no', 'nomor', 'Nomor']],
                    'Nama Penerima' => ['column' => 'nama_penerima', 'aliases' => ['Nama Penerima', 'namaPenerima', 'nama_penerima', 'nama', 'Nama']],
                    'Porsi Besar' => ['column' => 'porsi_besar', 'aliases' => ['Porsi Besar', 'porsiBesar', 'porsi_besar', 'besar']],
                    'Porsi Kecil' => ['column' => 'porsi_kecil', 'aliases' => ['Porsi Kecil', 'porsiKecil', 'porsi_kecil', 'kecil']],
                    'Total' => ['column' => 'total', 'aliases' => ['Total', 'total', 'jumlah', 'grandTotal']],
                ],
            ],

            'aslap_distribusi' => [
                'table' => 'aslap_distribusi_reports',
                'fields' => [
                    'No' => ['column' => 'nomor', 'aliases' => ['No', 'no', 'nomor', 'Nomor']],
                    'Nama Penerima' => ['column' => 'nama_penerima', 'aliases' => ['Nama Penerima', 'namaPenerima', 'nama_penerima', 'nama', 'Nama']],
                    'Tendik' => ['column' => 'tendik', 'aliases' => ['Tendik', 'tendik']],
                    'Porsi Besar' => ['column' => 'porsi_besar', 'aliases' => ['Porsi Besar', 'porsiBesar', 'porsi_besar', 'besar']],
                    'Porsi Kecil' => ['column' => 'porsi_kecil', 'aliases' => ['Porsi Kecil', 'porsiKecil', 'porsi_kecil', 'kecil']],
                    'Total' => ['column' => 'total', 'aliases' => ['Total', 'total', 'jumlah', 'grandTotal']],
                ],
            ],

            'distribusi' => [
                'table' => 'distribusi_reports',
                'fields' => [
                    'Lokasi Tujuan' => ['column' => 'lokasi_tujuan', 'aliases' => ['Lokasi Tujuan', 'lokasiTujuan', 'lokasi_tujuan', 'tujuan', 'lokasi']],
                    'Porsi Besar' => ['column' => 'porsi_besar', 'aliases' => ['Porsi Besar', 'porsiBesar', 'porsi_besar', 'besar']],
                    'Porsi Kecil' => ['column' => 'porsi_kecil', 'aliases' => ['Porsi Kecil', 'porsiKecil', 'porsi_kecil', 'kecil']],
                    'Jumlah Porsi' => ['column' => 'jumlah_porsi', 'aliases' => ['Jumlah Porsi', 'jumlahPorsi', 'jumlah_porsi', 'total']],
                    'Jam Berangkat' => ['column' => 'jam_berangkat', 'aliases' => ['Jam Berangkat', 'jamBerangkat', 'jam_berangkat', 'berangkat']],
                    'Jam Tiba' => ['column' => 'jam_tiba', 'aliases' => ['Jam Tiba', 'jamTiba', 'jam_tiba', 'tiba']],
                    'Status' => ['column' => 'status', 'aliases' => ['Status', 'status']],
                    'Nama Petugas' => ['column' => 'nama_petugas', 'aliases' => ['Nama Petugas', 'namaPetugas', 'nama_petugas', 'petugas', 'Petugas']],
                    'Catatan' => ['column' => 'catatan', 'aliases' => ['Catatan', 'catatan', 'keterangan', 'Keterangan']],
                    'Foto URL' => ['column' => 'foto_url', 'aliases' => ['Foto URL', 'fotoUrl', 'foto_url', 'foto']],
                ],
            ],

            'pengolahan_suhu' => [
                'table' => 'pengolahan_suhu_reports',
                'fields' => [
                    'Waktu' => ['column' => 'waktu', 'aliases' => ['Waktu', 'waktu', 'jam']],
                    'Nama Produk' => ['column' => 'nama_produk', 'aliases' => ['Nama Produk', 'namaProduk', 'nama_produk', 'produk']],
                    'Suhu Produk' => ['column' => 'suhu_produk', 'aliases' => ['Suhu Produk', 'suhuProduk', 'suhu_produk', 'suhu']],
                    'Paraf' => ['column' => 'paraf', 'aliases' => ['Paraf', 'paraf']],
                    'Nama Petugas' => ['column' => 'nama_petugas', 'aliases' => ['Nama Petugas', 'namaPetugas', 'nama_petugas', 'petugas', 'Petugas']],
                    'Foto URL' => ['column' => 'foto_url', 'aliases' => ['Foto URL', 'fotoUrl', 'foto_url', 'foto']],
                ],
            ],

            'pengolahan_produksi' => [
                'table' => 'pengolahan_produksi_reports',
                'fields' => [
                    'Menu' => ['column' => 'menu', 'aliases' => ['Menu', 'menu', 'namaMenu']],
                    'Bahan Baku' => ['column' => 'bahan_baku', 'aliases' => ['Bahan Baku', 'bahanBaku', 'bahan_baku', 'bahan']],
                    'Qty' => ['column' => 'qty', 'aliases' => ['Qty', 'qty', 'jumlah', 'banyaknya']],
                    'Satuan Bahan' => ['column' => 'satuan_bahan', 'aliases' => ['Satuan Bahan', 'satuanBahan', 'satuan_bahan', 'satuan']],
                    'Waktu Produksi' => ['column' => 'waktu_produksi', 'aliases' => ['Waktu Produksi', 'waktuProduksi', 'waktu_produksi', 'durasi']],
                    'Jam Mulai' => ['column' => 'jam_mulai', 'aliases' => ['Jam Mulai', 'jamMulai', 'jam_mulai']],
                    'Hasil Akhir' => ['column' => 'hasil_akhir', 'aliases' => ['Hasil Akhir', 'hasilAkhir', 'hasil_akhir', 'hasil']],
                    'Satuan Hasil' => ['column' => 'satuan_hasil', 'aliases' => ['Satuan Hasil', 'satuanHasil', 'satuan_hasil']],
                    'Nama Petugas' => ['column' => 'nama_petugas', 'aliases' => ['Nama Petugas', 'namaPetugas', 'nama_petugas', 'petugas', 'Petugas']],
                    'Foto URL' => ['column' => 'foto_url', 'aliases' => ['Foto URL', 'fotoUrl', 'foto_url', 'foto']],
                ],
            ],

            'persiapan_bahan' => [
                'table' => 'persiapan_bahan_reports',
                'fields' => [
                    'Nama Bahan' => ['column' => 'nama_bahan', 'aliases' => ['Nama Bahan', 'namaBahan', 'nama_bahan', 'bahan']],
                    'Banyaknya' => ['column' => 'banyaknya', 'aliases' => ['Banyaknya', 'banyaknya', 'jumlah', 'qty']],
                    'Satuan' => ['column' => 'satuan', 'aliases' => ['Satuan', 'satuan']],
                    'Baik' => ['column' => 'baik', 'aliases' => ['Baik', 'baik']],
                    'Sedang' => ['column' => 'sedang', 'aliases' => ['Sedang', 'sedang']],
                    'Rusak' => ['column' => 'rusak', 'aliases' => ['Rusak', 'rusak']],
                    'Nama Petugas' => ['column' => 'nama_petugas', 'aliases' => ['Nama Petugas', 'namaPetugas', 'nama_petugas', 'petugas', 'Petugas']],
                    'Foto URL' => ['column' => 'foto_url', 'aliases' => ['Foto URL', 'fotoUrl', 'foto_url', 'foto']],
                ],
            ],

            'persiapan_limbah' => [
                'table' => 'persiapan_limbah_reports',
                'fields' => $this->limbahFields(),
            ],

            'pemorsian_ompreng' => [
                'table' => 'pemorsian_ompreng_reports',
                'fields' => [
                    'Rute' => ['column' => 'rute', 'aliases' => ['Rute', 'rute']],
                    'Qty Ompreng Kecil' => ['column' => 'qty_ompreng_kecil', 'aliases' => ['Qty Ompreng Kecil', 'qtyOmprengKecil', 'qty_ompreng_kecil', 'omprengKecil', 'kecil']],
                    'Qty Ompreng Besar' => ['column' => 'qty_ompreng_besar', 'aliases' => ['Qty Ompreng Besar', 'qtyOmprengBesar', 'qty_ompreng_besar', 'omprengBesar', 'besar']],
                    'Waktu Pemorsian' => ['column' => 'waktu_pemorsian', 'aliases' => ['Waktu Pemorsian', 'waktuPemorsian', 'waktu_pemorsian', 'waktu']],
                    'Nama Petugas' => ['column' => 'nama_petugas', 'aliases' => ['Nama Petugas', 'namaPetugas', 'nama_petugas', 'petugas', 'Petugas']],
                    'Foto URL' => ['column' => 'foto_url', 'aliases' => ['Foto URL', 'fotoUrl', 'foto_url', 'foto']],
                ],
            ],

            'pemorsian_sisa' => [
                'table' => 'pemorsian_sisa_reports',
                'fields' => [
                    'Rute' => ['column' => 'rute', 'aliases' => ['Rute', 'rute']],
                    'Waktu Cek Sisa' => ['column' => 'waktu_cek_sisa', 'aliases' => ['Waktu Cek Sisa', 'waktuCekSisa', 'waktu_cek_sisa', 'waktu']],
                    'Jenis Makanan' => ['column' => 'jenis_makanan', 'aliases' => ['Jenis Makanan', 'jenisMakanan', 'jenis_makanan', 'makanan']],
                    'Berat Sisa Kg' => ['column' => 'berat_sisa_kg', 'aliases' => ['Berat Sisa Kg', 'beratSisaKg', 'berat_sisa_kg', 'berat']],
                    'Keterangan' => ['column' => 'keterangan', 'aliases' => ['Keterangan', 'keterangan', 'catatan', 'Catatan']],
                    'Nama Petugas' => ['column' => 'nama_petugas', 'aliases' => ['Nama Petugas', 'namaPetugas', 'nama_petugas', 'petugas', 'Petugas']],
                    'Foto URL' => ['column' => 'foto_url', 'aliases' => ['Foto URL', 'fotoUrl', 'foto_url', 'foto']],
                ],
            ],

            'pencucian_limbah' => [
                'table' => 'pencucian_limbah_reports',
                'fields' => $this->limbahFields(),
            ],

            'kebersihan_limbah' => [
                'table' => 'kebersihan_limbah_reports',
                'fields' => $this->limbahFields(),
            ],
        ];
    }

    protected function limbahFields(): array
    {
        return [
            'NO BA' => ['column' => 'no_ba', 'aliases' => ['NO BA', 'No BA', 'noBa', 'no_ba', 'nomorBa']],
            'Nama Pihak Kedua' => ['column' => 'nama_pihak_kedua', 'aliases' => ['Nama Pihak Kedua', 'namaPihakKedua', 'nama_pihak_kedua', 'pihakKedua', 'penerima']],
            'Jenis Limbah' => ['column' => 'jenis_limbah', 'aliases' => ['Jenis Limbah', 'jenisLimbah', 'jenis_limbah', 'limbah']],
            'Berat Limbah Kg' => ['column' => 'berat_limbah_kg', 'aliases' => ['Berat Limbah Kg', 'beratLimbahKg', 'berat_limbah_kg', 'berat']],
            'Catatan' => ['column' => 'catatan', 'aliases' => ['Catatan', 'catatan', 'keterangan', 'Keterangan']],
            'Nama Petugas' => ['column' => 'nama_petugas', 'aliases' => ['Nama Petugas', 'namaPetugas', 'nama_petugas', 'petugas', 'Petugas']],
            'Foto URL' => ['column' => 'foto_url', 'aliases' => ['Foto URL', 'fotoUrl', 'foto_url', 'foto']],
        ];
    }

    public function categories(): array
    {
        return array_keys($this->configs());
    }

    public function hasCategory(string $category): bool
    {
        return array_key_exists($category, $this->configs());
    }

    public function getConfig(string $category): array
    {
        $configs = $this->configs();

        if (!isset($configs[$category])) {
            throw new \InvalidArgumentException("Kategori tidak dikenal: {$category}");
        }

        return $configs[$category];
    }

    public function dateOrToday(?string $tanggal): string
    {
        if ($tanggal && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return $tanggal;
        }

        return now('Asia/Jakarta')->format('Y-m-d');
    }

    public function payloadFromRequest(Request $request): array
    {
        $payload = $request->input('data');

        if (!is_array($payload)) {
            $payload = $request->except([
                'category',
                'kategori',
                'tanggal',
                'report_date',
                'action',
                'data',
                'foto',
            ]);
        }

        return $this->attachPhotoIfAny($payload, $request);
    }

    public function attachPhotoIfAny(array $payload, Request $request): array
    {
        $fotoUrl = null;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('dashboard-reports', 'public');
            $fotoUrl = url(Storage::url($path));
        }

        $base64 = $request->input('fotoBase64') ?: ($payload['fotoBase64'] ?? null);
        $fileName = $request->input('fotoName') ?: ($payload['fotoName'] ?? null);

        if (!$fotoUrl && is_string($base64) && trim($base64) !== '') {
            $fotoUrl = $this->storeBase64Image($base64, $fileName);
        }

        if ($fotoUrl) {
            $payload['Foto URL'] = $fotoUrl;
            $payload['fotoUrl'] = $fotoUrl;
        }

        return $payload;
    }

    protected function storeBase64Image(string $base64, ?string $fileName = null): ?string
    {
        $base64 = trim($base64);
        $extension = 'jpg';

        if (str_contains($base64, ',')) {
            [$meta, $base64] = explode(',', $base64, 2);

            if (str_contains($meta, 'png')) {
                $extension = 'png';
            } elseif (str_contains($meta, 'webp')) {
                $extension = 'webp';
            } elseif (str_contains($meta, 'jpeg') || str_contains($meta, 'jpg')) {
                $extension = 'jpg';
            }
        } elseif ($fileName) {
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $extension = $ext === 'jpeg' ? 'jpg' : $ext;
            }
        }

        $binary = base64_decode($base64, true);

        if ($binary === false) {
            return null;
        }

        $safeName = $fileName
            ? Str::slug(pathinfo($fileName, PATHINFO_FILENAME))
            : 'foto';

        $path = 'dashboard-reports/' . date('Y/m/d') . '/' . $safeName . '-' . Str::uuid() . '.' . $extension;

        Storage::disk('public')->put($path, $binary);

        return url(Storage::url($path));
    }

    public function store(string $category, string $tanggal, array $payload): array
    {
        $config = $this->getConfig($category);
        $payload = $this->normalizePayload($category, $payload);

        $insert = [
            'tanggal' => $tanggal,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach ($config['fields'] as $canonical => $field) {
            $column = $field['column'];
            $value = $payload[$canonical] ?? null;

            if ($this->isNumericColumn($column)) {
                $insert[$column] = $this->toNumber($value);
            } else {
                $insert[$column] = $this->emptyToNull($value);
            }
        }

        $insert = $this->calculateDerivedFields($category, $insert, $tanggal);

        $id = DB::table($config['table'])->insertGetId($insert);
        $row = DB::table($config['table'])->where('id', $id)->first();

        return $this->formatDbRow($category, $row);
    }

    public function update(string $category, int|string $id, string $tanggal, array $payload): array
    {
        $config = $this->getConfig($category);
        $payload = $this->normalizePayload($category, $payload);

        $update = [
            'tanggal' => $tanggal,
            'updated_at' => now(),
        ];

        foreach ($config['fields'] as $canonical => $field) {
            $column = $field['column'];

            if ($column === 'nomor') {
                continue;
            }

            $value = $payload[$canonical] ?? null;

            if ($this->isNumericColumn($column)) {
                $update[$column] = $this->toNumber($value);
            } else {
                $update[$column] = $this->emptyToNull($value);
            }
        }

        $update = $this->calculateDerivedFields($category, $update, $tanggal);

        DB::table($config['table'])->where('id', $id)->update($update);

        $row = DB::table($config['table'])->where('id', $id)->first();

        if (!$row) {
            throw new \RuntimeException('Data tidak ditemukan.');
        }

        return $this->formatDbRow($category, $row);
    }

    public function delete(string $category, int|string $id): bool
    {
        $config = $this->getConfig($category);

        return DB::table($config['table'])->where('id', $id)->delete() > 0;
    }

    public function list(string $category, string $tanggal): array
    {
        $config = $this->getConfig($category);

        return DB::table($config['table'])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => $this->formatDbRow($category, $row))
            ->values()
            ->all();
    }

    public function dashboard(string $tanggal): array
    {
        $data = $this->emptyDashboardData();

        foreach ($this->categories() as $category) {
            $data[$category] = $this->list($category, $tanggal);
        }

        return $data;
    }

    public function emptyDashboardData(): array
    {
        return array_fill_keys($this->categories(), []);
    }

    public function summary(array $data): array
    {
        return [
            'persiapan_bahan_count' => count($data['persiapan_bahan'] ?? []),
            'persiapan_limbah_count' => count($data['persiapan_limbah'] ?? []),
            'pengolahan_suhu_count' => count($data['pengolahan_suhu'] ?? []),
            'pengolahan_produksi_count' => count($data['pengolahan_produksi'] ?? []),
            'pemorsian_ompreng_count' => count($data['pemorsian_ompreng'] ?? []),
            'pemorsian_sisa_count' => count($data['pemorsian_sisa'] ?? []),
            'distribusi_count' => count($data['distribusi'] ?? []),
            'pencucian_limbah_count' => count($data['pencucian_limbah'] ?? []),
            'kebersihan_limbah_count' => count($data['kebersihan_limbah'] ?? []),
            'aslap_distribusi_count' => count($data['aslap_distribusi'] ?? []),
            'aslap_planning_count' => count($data['aslap_planning'] ?? []),
            'distribusi_total_porsi' => $this->sumRows($data['distribusi'] ?? [], ['Jumlah Porsi']),
            'aslap_distribusi_grand_total' => $this->sumRows($data['aslap_distribusi'] ?? [], ['Total']),
            'aslap_planning_grand_total' => $this->sumRows($data['aslap_planning'] ?? [], ['Total']),
        ];
    }

    public function countByDate(string $category, string $tanggal): int
    {
        $config = $this->getConfig($category);

        return DB::table($config['table'])->whereDate('tanggal', $tanggal)->count();
    }

    public function sumByDate(string $category, string $tanggal, string $canonicalColumn): int|float
    {
        $rows = $this->list($category, $tanggal);

        return $this->sumRows($rows, [$canonicalColumn]);
    }

    public function sumRows(array $rows, array $columns): int|float
    {
        $total = 0;

        foreach ($rows as $row) {
            foreach ($columns as $column) {
                $total += $this->toNumber($row[$column] ?? 0);
            }
        }

        return $total;
    }

    public function normalizePayload(string $category, array $payload): array
    {
        $config = $this->getConfig($category);
        $normalized = [];

        foreach ($config['fields'] as $canonical => $field) {
            $normalized[$canonical] = $this->firstValue($payload, $field['aliases']);
        }

        return $normalized;
    }

    protected function firstValue(array $payload, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $payload)) {
                return $payload[$key];
            }
        }

        return null;
    }

    protected function calculateDerivedFields(string $category, array $data, string $tanggal): array
    {
        if (in_array($category, ['aslap_planning', 'aslap_distribusi'], true)) {
            if (empty($data['nomor'])) {
                $data['nomor'] = $this->nextNomor($category, $tanggal);
            }
        }

        if ($category === 'distribusi') {
            $besar = $this->toNumber($data['porsi_besar'] ?? 0);
            $kecil = $this->toNumber($data['porsi_kecil'] ?? 0);
            $jumlah = $this->toNumber($data['jumlah_porsi'] ?? 0);

            $data['porsi_besar'] = $besar;
            $data['porsi_kecil'] = $kecil;
            $data['jumlah_porsi'] = $jumlah > 0 ? $jumlah : $besar + $kecil;
        }

        if ($category === 'aslap_planning') {
            $besar = $this->toNumber($data['porsi_besar'] ?? 0);
            $kecil = $this->toNumber($data['porsi_kecil'] ?? 0);
            $total = $this->toNumber($data['total'] ?? 0);

            $data['porsi_besar'] = $besar;
            $data['porsi_kecil'] = $kecil;
            $data['total'] = $total > 0 ? $total : $besar + $kecil;
        }

        if ($category === 'aslap_distribusi') {
            $tendik = $this->toNumber($data['tendik'] ?? 0);
            $besar = $this->toNumber($data['porsi_besar'] ?? 0);
            $kecil = $this->toNumber($data['porsi_kecil'] ?? 0);
            $total = $this->toNumber($data['total'] ?? 0);

            $data['tendik'] = $tendik;
            $data['porsi_besar'] = $besar;
            $data['porsi_kecil'] = $kecil;
            $data['total'] = $total > 0 ? $total : $tendik + $besar + $kecil;
        }

        return $data;
    }

    protected function nextNomor(string $category, string $tanggal): int
    {
        $config = $this->getConfig($category);

        return DB::table($config['table'])
            ->whereDate('tanggal', $tanggal)
            ->count() + 1;
    }

    public function formatDbRow(string $category, object $row): array
    {
        $config = $this->getConfig($category);

        $result = [
            'ID' => $row->id,
            'Tanggal' => $row->tanggal,
        ];

        foreach ($config['fields'] as $canonical => $field) {
            $column = $field['column'];
            $result[$canonical] = $row->{$column} ?? null;
        }

        $result['Created At'] = isset($row->created_at)
            ? Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')
            : null;

        return $result;
    }

    public function toMobileItem(string $category, array $row): array
    {
        $base = [
            'id' => (string) ($row['ID'] ?? ''),
            'tanggal' => (string) ($row['Tanggal'] ?? ''),
            'createdAt' => (string) ($row['Created At'] ?? ''),
        ];

        return match ($category) {
            'aslap_planning' => $base + [
                'no' => (string) ($row['No'] ?? ''),
                'nama' => (string) ($row['Nama Penerima'] ?? ''),
                'porsiBesar' => (string) ($row['Porsi Besar'] ?? 0),
                'porsiKecil' => (string) ($row['Porsi Kecil'] ?? 0),
                'total' => (string) ($row['Total'] ?? 0),
            ],
            'aslap_distribusi' => $base + [
                'no' => (string) ($row['No'] ?? ''),
                'nama' => (string) ($row['Nama Penerima'] ?? ''),
                'tendik' => (string) ($row['Tendik'] ?? 0),
                'porsiBesar' => (string) ($row['Porsi Besar'] ?? 0),
                'porsiKecil' => (string) ($row['Porsi Kecil'] ?? 0),
                'total' => (string) ($row['Total'] ?? 0),
            ],
            'distribusi' => $base + [
                'divisi' => 'Distribusi',
                'namaPetugas' => (string) ($row['Nama Petugas'] ?? ''),
                'lokasiTujuan' => (string) ($row['Lokasi Tujuan'] ?? ''),
                'porsiBesar' => (string) ($row['Porsi Besar'] ?? 0),
                'porsiKecil' => (string) ($row['Porsi Kecil'] ?? 0),
                'jumlahPorsi' => (string) ($row['Jumlah Porsi'] ?? 0),
                'jamBerangkat' => (string) ($row['Jam Berangkat'] ?? ''),
                'jamTiba' => (string) ($row['Jam Tiba'] ?? ''),
                'status' => (string) ($row['Status'] ?? ''),
                'catatan' => (string) ($row['Catatan'] ?? ''),
                'fotoUrl' => (string) ($row['Foto URL'] ?? ''),
            ],
            default => $this->genericMobileItem($base, $row),
        };
    }

    protected function genericMobileItem(array $base, array $row): array
    {
        $item = $base;

        foreach ($row as $key => $value) {
            $camel = Str::camel(str_replace(['/', '.', '-'], ' ', $key));
            $item[$camel] = is_scalar($value) || $value === null ? (string) ($value ?? '') : $value;
        }

        return $item;
    }

    protected function isNumericColumn(string $column): bool
    {
        return Str::contains($column, [
            'nomor',
            'porsi',
            'jumlah',
            'total',
            'tendik',
            'qty',
            'baik',
            'sedang',
            'rusak',
            'banyak',
            'suhu',
            'berat',
            'hasil_akhir',
        ]);
    }

    public function toNumber(mixed $value): int|float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $cleaned = preg_replace('/[^\d,.-]/', '', (string) $value);
        $cleaned = str_replace(',', '.', $cleaned);

        if ($cleaned === '' || !is_numeric($cleaned)) {
            return 0;
        }

        return $cleaned + 0;
    }

    protected function emptyToNull(mixed $value): mixed
    {
        if ($value === '') {
            return null;
        }

        return $value;
    }
}
