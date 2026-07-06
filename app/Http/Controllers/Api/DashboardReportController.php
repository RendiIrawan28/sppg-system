<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DashboardReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DashboardReportController extends Controller
{
    protected array $categories = [
        'persiapan_bahan',
        'persiapan_limbah',
        'pengolahan_suhu',
        'pengolahan_produksi',
        'pemorsian_ompreng',
        'pemorsian_sisa',
        'distribusi',
        'pencucian_limbah',
        'kebersihan_limbah',
        'aslap_distribusi',
        'aslap_planning',
    ];

    public function dashboard(Request $request)
    {
        $tanggal = $request->query('tanggal');

        if (!$tanggal) {
            $tanggal = now('Asia/Jakarta')->format('Y-m-d');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => 'Format tanggal harus yyyy-MM-dd.',
            ], 422);
        }

        $data = $this->emptyDashboardData();

        $reports = DashboardReport::query()
            ->whereDate('report_date', $tanggal)
            ->orderBy('created_at')
            ->get();

        foreach ($reports as $report) {
            if (!array_key_exists($report->category, $data)) {
                continue;
            }

            $data[$report->category][] = $this->formatReportRow($report);
        }

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data dashboard berhasil dimuat.',
            'tanggal' => $tanggal,
            'timezone' => 'Asia/Jakarta',
            'timestamp' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
            'summary' => $this->makeSummary($data),
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $category = $request->input('category') ?: $request->input('kategori');

        if (!$category && $request->input('action')) {
            $category = $this->categoryFromAction($request->input('action'));
        }

        $request->merge([
            'category' => $category,
            'tanggal' => $request->input('tanggal') ?: $request->input('report_date'),
        ]);

        $validated = $request->validate([
            'category' => [
                'required',
                'string',
                Rule::in($this->categories),
            ],
            'tanggal' => [
                'required',
                'date_format:Y-m-d',
            ],
            'data' => [
                'nullable',
                'array',
            ],
            'foto' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

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

        $photoPath = null;
        $photoUrl = null;

        if ($request->hasFile('foto')) {
            $photoPath = $request->file('foto')->store('dashboard-reports', 'public');
            $photoUrl = url(Storage::url($photoPath));

            $payload['Foto URL'] = $photoUrl;
        }

        $payload['Tanggal'] = $payload['Tanggal'] ?? $validated['tanggal'];

        $report = DashboardReport::create([
            'category' => $validated['category'],
            'report_date' => $validated['tanggal'],
            'payload' => $payload,
            'photo_path' => $photoPath,
            'photo_url' => $photoUrl,
            'source' => $request->input('source', 'android'),
            'submitted_by' => $payload['Nama Petugas'] ?? $payload['Petugas'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Laporan berhasil disimpan.',
            'data' => $this->formatReportRow($report),
        ], 201);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'category' => [
                'required',
                'string',
                Rule::in($this->categories),
            ],
            'tanggal' => [
                'required',
                'date_format:Y-m-d',
            ],
            'rows' => [
                'required',
                'array',
            ],
            'rows.*' => [
                'array',
            ],
        ]);

        $saved = [];

        foreach ($validated['rows'] as $row) {
            $row['Tanggal'] = $row['Tanggal'] ?? $validated['tanggal'];

            $report = DashboardReport::create([
                'category' => $validated['category'],
                'report_date' => $validated['tanggal'],
                'payload' => $row,
                'source' => 'bulk',
                'submitted_by' => $row['Nama Petugas'] ?? $row['Petugas'] ?? null,
            ]);

            $saved[] = $this->formatReportRow($report);
        }

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'count' => count($saved),
            'data' => $saved,
        ], 201);
    }

    public function destroy(DashboardReport $dashboardReport)
    {
        if ($dashboardReport->photo_path) {
            Storage::disk('public')->delete($dashboardReport->photo_path);
        }

        $dashboardReport->delete();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    protected function emptyDashboardData(): array
    {
        $data = [];

        foreach ($this->categories as $category) {
            $data[$category] = [];
        }

        return $data;
    }

    protected function formatReportRow(DashboardReport $report): array
    {
        $payload = $report->payload ?? [];

        $payload['ID'] = $payload['ID'] ?? $report->id;
        $payload['Tanggal'] = $payload['Tanggal'] ?? $report->report_date->format('Y-m-d');

        if ($report->photo_url && empty($payload['Foto URL'])) {
            $payload['Foto URL'] = $report->photo_url;
        }

        $payload['Created At'] = $report->created_at
            ? $report->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')
            : null;

        return $payload;
    }

    protected function makeSummary(array $data): array
    {
        return [
            'persiapan_bahan_count' => count($data['persiapan_bahan']),
            'persiapan_limbah_count' => count($data['persiapan_limbah']),

            'pengolahan_suhu_count' => count($data['pengolahan_suhu']),
            'pengolahan_produksi_count' => count($data['pengolahan_produksi']),

            'pemorsian_ompreng_count' => count($data['pemorsian_ompreng']),
            'pemorsian_sisa_count' => count($data['pemorsian_sisa']),

            'distribusi_count' => count($data['distribusi']),

            'pencucian_limbah_count' => count($data['pencucian_limbah']),
            'kebersihan_limbah_count' => count($data['kebersihan_limbah']),

            'aslap_distribusi_count' => count($data['aslap_distribusi']),
            'aslap_planning_count' => count($data['aslap_planning']),

            'distribusi_total_porsi' => $this->sumDistribusi($data['distribusi']),

            'aslap_distribusi_grand_total' => $this->sumAslapDistribusi($data['aslap_distribusi']),

            'aslap_planning_grand_total' => $this->sumPlanning($data['aslap_planning']),
        ];
    }

    protected function sumDistribusi(array $rows): int|float
    {
        $total = 0;

        foreach ($rows as $row) {
            $jumlahPorsi = $this->toNumber($row['Jumlah Porsi'] ?? 0);

            if ($jumlahPorsi > 0) {
                $total += $jumlahPorsi;
                continue;
            }

            $total += $this->toNumber($row['Porsi Besar'] ?? 0)
                + $this->toNumber($row['Porsi Kecil'] ?? 0);
        }

        return $total;
    }

    protected function sumAslapDistribusi(array $rows): int|float
    {
        $total = 0;

        foreach ($rows as $row) {
            $rawTotal = $this->toNumber($row['Total'] ?? 0);

            if ($rawTotal > 0) {
                $total += $rawTotal;
                continue;
            }

            $total += $this->toNumber($row['Tendik'] ?? 0)
                + $this->toNumber($row['Porsi Besar'] ?? 0)
                + $this->toNumber($row['Porsi Kecil'] ?? 0);
        }

        return $total;
    }

    protected function sumPlanning(array $rows): int|float
    {
        $total = 0;

        foreach ($rows as $row) {
            $rawTotal = $this->toNumber($row['Total'] ?? 0);

            if ($rawTotal > 0) {
                $total += $rawTotal;
                continue;
            }

            $total += $this->toNumber($row['Porsi Besar'] ?? 0)
                + $this->toNumber($row['Porsi Kecil'] ?? 0);
        }

        return $total;
    }

    protected function toNumber(mixed $value): int|float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $cleaned = preg_replace('/[^\d,.-]/', '', (string) $value);
        $cleaned = str_replace(',', '.', $cleaned);

        if (!is_numeric($cleaned)) {
            return 0;
        }

        return $cleaned + 0;
    }

    protected function categoryFromAction(?string $action): ?string
    {
        if (!$action) {
            return null;
        }

        $action = strtolower(trim($action));

        if (in_array($action, $this->categories, true)) {
            return $action;
        }

        $map = [
            'save_persiapan_bahan' => 'persiapan_bahan',
            'save_persiapan_limbah' => 'persiapan_limbah',
            'save_pengolahan_suhu' => 'pengolahan_suhu',
            'save_pengolahan_produksi' => 'pengolahan_produksi',
            'save_pemorsian_ompreng' => 'pemorsian_ompreng',
            'save_pemorsian_sisa' => 'pemorsian_sisa',
            'save_distribusi' => 'distribusi',
            'save_pencucian_limbah' => 'pencucian_limbah',
            'save_kebersihan_limbah' => 'kebersihan_limbah',
            'save_aslap_distribusi' => 'aslap_distribusi',
            'save_aslap_planning' => 'aslap_planning',
        ];

        return $map[$action] ?? null;
    }
}