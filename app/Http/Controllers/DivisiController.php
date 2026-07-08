<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::orderBy('nama')->get();

        return view('divisi.index', compact('divisis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:divisis,nama',
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i'],
        ], [
            'jam_masuk.date_format' => 'Format Jam Masuk tidak valid. Gunakan HH:MM.',
            'jam_keluar.date_format' => 'Format Jam Keluar tidak valid. Gunakan HH:MM.',
        ]);

        Divisi::create($validated);

        return back()->with('success', 'Divisi ' . $validated['nama'] . ' berhasil ditambahkan.');
    }

    public function update(Request $request, Divisi $divisi)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:divisis,nama,' . $divisi->id,
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i'],
        ], [
            'jam_masuk.date_format' => 'Format Jam Masuk tidak valid. Gunakan HH:MM.',
            'jam_keluar.date_format' => 'Format Jam Keluar tidak valid. Gunakan HH:MM.',
        ]);

        $divisi->update($validated);

        return back()->with('success', 'Data Divisi berhasil diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        try {
            $divisi->delete();

            return back()->with('success', 'Divisi berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus Divisi. Pastikan tidak ada Pegawai yang terhubung.');
        }
    }
}
