<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::withCount('pegawais')
            ->orderBy('nama')
            ->get();

        return view('divisi.index', compact('divisis'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Divisi::create($validated);

        return redirect()
            ->route('presensi.divisi.index')
            ->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function update(Request $request, Divisi $divisi)
    {
        $validated = $this->validateData($request, $divisi);

        $divisi->update($validated);

        return redirect()
            ->route('presensi.divisi.index')
            ->with('success', 'Data divisi berhasil diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        if ($divisi->pegawais()->exists()) {
            return redirect()
                ->route('presensi.divisi.index')
                ->with('error', 'Divisi tidak bisa dihapus karena masih digunakan oleh pegawai.');
        }

        $divisi->delete();

        return redirect()
            ->route('presensi.divisi.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }

    protected function validateData(Request $request, ?Divisi $divisi = null): array
    {
        $ignoreId = $divisi ? $divisi->id : null;

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('divisis', 'nama')->ignore($ignoreId),
            ],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i'],
        ]);

        $validated['jam_masuk'] = blank($validated['jam_masuk'] ?? null)
            ? null
            : $validated['jam_masuk'];

        $validated['jam_keluar'] = blank($validated['jam_keluar'] ?? null)
            ? null
            : $validated['jam_keluar'];

        return $validated;
    }
}