<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Divisi; 
use App\Models\Mode; 
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    /**
     * Tampilkan daftar semua Pegawai (halaman indeks admin).
     */
    public function index()
    {
        // Muat relasi divisi untuk setiap pegawai
        $pegawais = Pegawai::with('divisi')->orderBy('nama')->get();
        $divisis = Divisi::all(); // Ambil semua divisi untuk form tambah/edit
        
        // Ambil mode sistem saat ini
        // Menggunakan find(1) karena asumsi tabel Mode hanya memiliki satu record
        $modeStatus = Mode::find(1)->status ?? 'presensi'; 
        
        return view('pegawai.index', compact('pegawais', 'modeStatus', 'divisis'));
    }

    /**
     * Simpan Pegawai baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            // Memastikan ID divisi valid
            'divisi_id' => 'required|exists:divisis,id', 
            // UID kartu opsional saat pembuatan, harus unik jika diisi
            'uid_kartu' => 'nullable|string|max:50|unique:pegawais,uid_kartu',
        ]);

        $pegawai = Pegawai::create($validated);

        return back()->with('success', 'Pegawai ' . $pegawai->nama . ' berhasil ditambahkan.');
    }

    /**
     * Perbarui data Pegawai di database.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            // Memastikan ID divisi valid
            'divisi_id' => 'required|exists:divisis,id', 
            // Pengecualian validasi unique untuk data pegawai saat ini
            'uid_kartu' => 'nullable|string|max:50|unique:pegawais,uid_kartu,' . $pegawai->id,
        ]);

        $pegawai->update($validated);

        return back()->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Hapus Pegawai dari database.
     */
    public function destroy(Pegawai $pegawai)
    {
        try {
            $pegawai->delete();
            return back()->with('success', 'Pegawai berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani jika ada foreign key constraint (misal: pegawai punya data presensi)
            return back()->with('error', 'Gagal menghapus Pegawai. Pastikan Pegawai tidak memiliki data presensi terkait.');
        }
    }
    
    // Metode untuk mengubah mode sistem
    public function setRegistrationMode(Request $request)
    {
        Mode::updateOrCreate(['id' => 1], ['status' => 'registrasi']);
        return back()->with('success', 'Mode sistem diubah menjadi REGISTRASI.');
    }

    public function setPresensiMode(Request $request)
    {
        Mode::updateOrCreate(['id' => 1], ['status' => 'presensi']);
        return back()->with('success', 'Mode sistem diubah menjadi PRESENSI.');
    }
    
    // Metode yang tidak digunakan karena form digabung di index
    public function create() { abort(404); }
    public function show(Pegawai $pegawai) { abort(404); }
    public function edit(Pegawai $pegawai) { return response()->json($pegawai); }
}
