<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SppgReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardReportController extends Controller
{
    public function __construct(
        protected SppgReportService $reports
    ) {}

    public function dashboard(Request $request)
    {
        $tanggal = $request->query('tanggal') ?: now('Asia/Jakarta')->format('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => 'Format tanggal harus yyyy-MM-dd.',
                'data' => null,
            ], 422);
        }

        $data = $this->reports->dashboard($tanggal);

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Data dashboard berhasil dimuat.',
            'tanggal' => $tanggal,
            'timezone' => 'Asia/Jakarta',
            'timestamp' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
            'summary' => $this->reports->summary($data),
            'data' => $data,
        ]);
    }
}
