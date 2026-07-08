<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    /**
     * Menampilkan daftar Divisi.
     */
    public function index()
    {
        $divisis = Divisi::orderBy('nama')->get(); 
        return view('divisi.index', compact('divisis'));
    }

    /**
     * Menyimpan Divisi baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:divisis,nama',
            'jam_masuk' => [
                'required',
                'regex:/^\d{2}:\d{2}(:\d{2})?$/', // ✅ bisa H:i atau H:i:s
            ],
            'jam_keluar' => [
                'required',
                'regex:/^\d{2}:\d{2}(:\d{2})?$/',
            ],
        ], [
            'jam_keluar.after' => 'Jam Keluar harus setelah Jam Masuk.',
            'jam_masuk.regex' => 'Format Jam Masuk tidak valid. Gunakan HH:MM atau HH:MM:SS.',
            'jam_keluar.regex' => 'Format Jam Keluar tidak valid. Gunakan HH:MM atau HH:MM:SS.',
        ]);

        Divisi::create($validated);
        return back()->with('success', 'Divisi ' . $validated['nama'] . ' berhasil ditambahkan.');
    }

    /**
     * Memperbarui data Divisi di database.
     */
    public function update(Request $request, Divisi $divisi)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:divisis,nama,' . $divisi->id,
            'jam_masuk' => [
                'required',
                'regex:/^\d{2}:\d{2}(:\d{2})?$/',
            ],
            'jam_keluar' => [
                'required',
                'regex:/^\d{2}:\d{2}(:\d{2})?$/',
            ],
        ], [
            'jam_keluar.after' => 'Jam Keluar harus setelah Jam Masuk.',
            'jam_masuk.regex' => 'Format Jam Masuk tidak valid. Gunakan HH:MM atau HH:MM:SS.',
            'jam_keluar.regex' => 'Format Jam Keluar tidak valid. Gunakan HH:MM atau HH:MM:SS.',
        ]);

        $divisi->update($validated);
        return back()->with('success', 'Data Divisi berhasil diperbarui.');
    }

    /**
     * Menghapus Divisi dari database.
     */
    public function destroy(Divisi $divisi)
    {
        try {
            $divisi->delete();
            return back()->with('success', 'Divisi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus Divisi. Pastikan tidak ada Pegawai yang terhubung.');
        }
    }
}
