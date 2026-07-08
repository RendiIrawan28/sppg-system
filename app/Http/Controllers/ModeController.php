<?php

namespace App\Http\Controllers;

use App\Models\Mode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModeController extends Controller
{
    public function status()
    {
        $mode = Mode::find(1);
        if (!$mode) {
            $mode = Mode::create(['id' => 1, 'status' => 'presensi']);
        }
        // ESP8266 hanya perlu menerima string mode
        return response($mode->status, 200);
    }

    public function setMode(Request $request)
    {
        $request->validate([
            'mode' => ['required', 'string', Rule::in(['presensi', 'registrasi'])],
        ]);

        $mode = Mode::updateOrCreate(['id' => 1], ['status' => $request->mode]);

        return response()->json([
            'message' => 'Mode sistem berhasil diubah.',
            'new_mode' => $mode->status,
        ], 200);
    }
}