<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    protected string $timezone = 'Asia/Jakarta';

    /**
     * Batas maksimal shift kerja.
     * Jika pegawai lupa check-out, sistem akan menutup shift otomatis
     * pada jam_masuk + 16 jam.
     */
    protected int $maxShiftHours = 16;

    /**
     * Setelah check-out, pegawai baru boleh check-in lagi setelah 4 jam.
     */
    protected int $minimumRestHours = 4;

    /**
     * Mencegah kartu yang sama terbaca dua kali berurutan karena masih menempel di reader.
     */
    protected int $minimumSecondsBetweenInOut = 14400;

    // ===================== MODE REGISTRASI =====================
    public function registerCard(Request $request)
    {
        $request->validate([
            'uid_kartu' => 'required|string|max:50',
        ]);

        $uid = strtoupper(trim($request->uid_kartu));

        $alreadyUsed = Pegawai::where('uid_kartu', $uid)->first();

        if ($alreadyUsed) {
            return response()->json([
                'message' => 'Kartu sudah terdaftar atas nama ' . $alreadyUsed->nama,
                'pegawai' => $alreadyUsed->nama,
                'status' => 'already_registered',
            ], 409);
        }

        $pegawai = Pegawai::whereNull('uid_kartu')
            ->orWhere('uid_kartu', '')
            ->orderBy('id', 'asc')
            ->first();

        if (! $pegawai) {
            return response()->json([
                'message' => 'Semua pegawai sudah terdaftar.',
                'status' => 'no_employee',
            ], 404);
        }

        $pegawai->uid_kartu = $uid;
        $pegawai->save();

        return response()->json([
            'message' => 'Kartu berhasil didaftarkan untuk ' . $pegawai->nama,
            'pegawai' => $pegawai->nama,
            'status' => 'success',
        ], 200);
    }

    // ===================== MODE PRESENSI DINAMIS SPPG =====================
    public function store(Request $request)
    {
        $request->validate([
            'uid_kartu' => 'required|string|max:50',
        ]);

        $now = Carbon::now($this->timezone);
        $uid = strtoupper(trim($request->uid_kartu));

        $pegawai = Pegawai::with('divisi')->where('uid_kartu', $uid)->first();

        if (! $pegawai) {
            return response()->json([
                'message' => 'Kartu tidak terdaftar.',
                'action' => 'uid_not_found',
                'status' => 'error',
            ], 401);
        }

        if (! $pegawai->divisi) {
            return response()->json([
                'message' => 'Pegawai belum memiliki divisi.',
                'pegawai' => $pegawai->nama,
                'action' => 'missing_division',
                'status' => 'error',
            ], 400);
        }

        DB::beginTransaction();

        try {
            $this->autoCheckoutExpiredOpenShift($pegawai->id, $now);

            $openShift = Presensi::where('pegawai_id', $pegawai->id)
                ->whereNull('jam_keluar')
                ->latest('jam_masuk')
                ->lockForUpdate()
                ->first();

            // ========== CHECK-OUT ==========
            if ($openShift) {
                $jamMasuk = Carbon::parse($openShift->jam_masuk, $this->timezone);

                if ($jamMasuk->diffInSeconds($now) < $this->minimumSecondsBetweenInOut) {
                    DB::commit();

                    return response()->json([
                        'message' => 'Kartu baru saja terbaca. Silakan tap ulang beberapa saat lagi.',
                        'action' => 'duplicate_tap',
                        'status' => 'blocked',
                        'pegawai' => $pegawai->nama,
                    ], 200);
                }

                $totalMenitKerja = max(1, (int) $jamMasuk->diffInMinutes($now));

                $openShift->jam_keluar = $now;
                $openShift->total_jam = $totalMenitKerja;
                $openShift->telat = 0;
                $openShift->lembur = 0;
                $openShift->status = 'closed';
                $openShift->checkout_type = 'manual';
                $openShift->save();

                DB::commit();

                return response()->json([
                    'message' => 'Presensi keluar tercatat.',
                    'action' => 'check_out',
                    'status' => 'check_out',
                    'pegawai' => $pegawai->nama,
                    'divisi' => $pegawai->divisi->nama ?? '-',
                    'tanggal' => Carbon::parse($openShift->tanggal)->toDateString(),
                    'jam_masuk' => $jamMasuk->format('Y-m-d H:i:s'),
                    'jam_keluar' => $now->format('Y-m-d H:i:s'),
                    'total_menit' => $totalMenitKerja,
                    'durasi' => $this->formatDurasi($totalMenitKerja),
                ], 200);
            }

            // ========== CHECK-IN ==========
            $latestClosedShift = Presensi::where('pegawai_id', $pegawai->id)
                ->whereNotNull('jam_keluar')
                ->latest('jam_keluar')
                ->lockForUpdate()
                ->first();

            if ($latestClosedShift) {
                $lastCheckout = Carbon::parse($latestClosedShift->jam_keluar, $this->timezone);
                $nextAllowedCheckIn = $lastCheckout->copy()->addHours($this->minimumRestHours);

                if ($now->lt($nextAllowedCheckIn)) {
                    $remainingMinutes = (int) ceil($now->diffInSeconds($nextAllowedCheckIn) / 60);

                    DB::commit();

                    return response()->json([
                        'message' => 'Belum bisa check-in lagi. Tunggu minimal 4 jam setelah check-out.',
                        'action' => 'wait_4_hours',
                        'status' => 'blocked',
                        'pegawai' => $pegawai->nama,
                        'last_checkout' => $lastCheckout->format('Y-m-d H:i:s'),
                        'allowed_at' => $nextAllowedCheckIn->format('Y-m-d H:i:s'),
                        'remaining_minutes' => $remainingMinutes,
                    ], 200);
                }
            }

            $presensi = Presensi::create([
                'pegawai_id' => $pegawai->id,
                'tanggal' => $now->toDateString(), // tanggal mulai kerja
                'jam_masuk' => $now,
                'jam_keluar' => null,
                'total_jam' => 0,
                'telat' => 0,
                'lembur' => 0,
                'status' => 'open',
                'checkout_type' => null,
                'catatan' => null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Presensi masuk tercatat.',
                'action' => 'check_in',
                'status' => 'check_in',
                'pegawai' => $pegawai->nama,
                'divisi' => $pegawai->divisi->nama ?? '-',
                'tanggal' => $now->toDateString(),
                'jam_masuk' => $now->format('Y-m-d H:i:s'),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Kesalahan server.',
                'error' => $e->getMessage(),
                'status' => 'error',
            ], 500);
        }
    }

    // ===================== UNTUK DASHBOARD REALTIME =====================
    public function dataPresensi()
    {
        $this->autoCheckoutAllExpiredOpenShifts();

        $today = Carbon::today($this->timezone)->toDateString();

        $presensi = Presensi::with(['pegawai.divisi'])
            ->where(function ($query) use ($today) {
                $query->whereDate('tanggal', $today)
                    ->orWhereNull('jam_keluar');
            })
            ->latest('jam_masuk')
            ->get();

        return response()->json($presensi->map(function (Presensi $p) {
            return [
                'id' => $p->id,
                'nama' => $p->pegawai->nama ?? '-',
                'divisi' => $p->pegawai->divisi->nama ?? '-',
                'tanggal' => Carbon::parse($p->tanggal)->format('Y-m-d'),
                'jam_masuk' => $p->jam_masuk ? Carbon::parse($p->jam_masuk)->format('H:i:s') : '—',
                'jam_keluar' => $p->jam_keluar ? Carbon::parse($p->jam_keluar)->format('H:i:s') : '—',
                'total_menit' => (int) ($p->total_jam ?? 0),
                'total_jam' => $p->jam_keluar ? $this->formatDurasi((int) $p->total_jam) : 'Masih bekerja',
                'status' => $p->status_label,
                'checkout_type' => $p->checkout_type,
            ];
        }));
    }

    protected function autoCheckoutExpiredOpenShift(int $pegawaiId, Carbon $now): void
    {
        $expiredOpenShifts = Presensi::where('pegawai_id', $pegawaiId)
            ->whereNull('jam_keluar')
            ->where('jam_masuk', '<=', $now->copy()->subHours($this->maxShiftHours))
            ->lockForUpdate()
            ->get();

        foreach ($expiredOpenShifts as $shift) {
            $jamMasuk = Carbon::parse($shift->jam_masuk, $this->timezone);
            $autoCheckoutAt = $jamMasuk->copy()->addHours($this->maxShiftHours);

            $shift->jam_keluar = $autoCheckoutAt;
            $shift->total_jam = $this->maxShiftHours * 60;
            $shift->telat = 0;
            $shift->lembur = 0;
            $shift->status = 'auto_checkout';
            $shift->checkout_type = 'auto';
            $shift->catatan = 'Otomatis check-out karena melewati batas 16 jam.';
            $shift->save();
        }
    }

    protected function autoCheckoutAllExpiredOpenShifts(): void
    {
        $now = Carbon::now($this->timezone);

        Presensi::whereNull('jam_keluar')
            ->where('jam_masuk', '<=', $now->copy()->subHours($this->maxShiftHours))
            ->chunkById(100, function ($rows) {
                foreach ($rows as $shift) {
                    $jamMasuk = Carbon::parse($shift->jam_masuk, $this->timezone);
                    $autoCheckoutAt = $jamMasuk->copy()->addHours($this->maxShiftHours);

                    $shift->jam_keluar = $autoCheckoutAt;
                    $shift->total_jam = $this->maxShiftHours * 60;
                    $shift->telat = 0;
                    $shift->lembur = 0;
                    $shift->status = 'auto_checkout';
                    $shift->checkout_type = 'auto';
                    $shift->catatan = 'Otomatis check-out karena melewati batas 16 jam.';
                    $shift->save();
                }
            });
    }

    protected function formatDurasi(int $minutes): string
    {
        $minutes = max(0, $minutes);

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return $hours . ' jam ' . $remainingMinutes . ' menit';
        }

        if ($hours > 0) {
            return $hours . ' jam';
        }

        return $remainingMinutes . ' menit';
    }
    public function resetHariIni()
    {
        $today = \Illuminate\Support\Carbon::today('Asia/Jakarta')->toDateString();

        $jumlah = \App\Models\Presensi::where(function ($query) use ($today) {
            $query->whereDate('tanggal', $today)
                ->orWhereNull('jam_keluar');
        })
            ->delete();

        return redirect()
            ->back()
            ->with('success', "Berhasil reset {$jumlah} data presensi hari ini / presensi terbuka.");
    }
    public function manualCreate()
    {
        $pegawais = Pegawai::with('divisi')
            ->orderBy('nama', 'asc')
            ->get();

        return view('presensi.manual-create', compact('pegawais'));
    }

    public function manualStore(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'tanggal' => ['required', 'date'],
            'jam_masuk' => ['required', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'pegawai_id.required' => 'Pegawai wajib dipilih.',
            'tanggal.required' => 'Tanggal kerja wajib diisi.',
            'jam_masuk.required' => 'Jam masuk wajib diisi.',
            'jam_masuk.date_format' => 'Format jam masuk tidak valid.',
            'jam_keluar.date_format' => 'Format jam keluar tidak valid.',
        ]);

        $timezone = 'Asia/Jakarta';

        $pegawai = Pegawai::with('divisi')->findOrFail($validated['pegawai_id']);

        $jamMasuk = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['tanggal'] . ' ' . $validated['jam_masuk'],
            $timezone
        );

        $jamKeluar = null;
        $totalMenit = 0;
        $status = 'open';
        $checkoutType = null;

        if (! empty($validated['jam_keluar'])) {
            $jamKeluar = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validated['tanggal'] . ' ' . $validated['jam_keluar'],
                $timezone
            );

            // Jika jam keluar lebih kecil/sama dari jam masuk,
            // berarti shift lewat tengah malam.
            if ($jamKeluar->lessThanOrEqualTo($jamMasuk)) {
                $jamKeluar->addDay();
            }

            $totalMenit = max(1, (int) $jamMasuk->diffInMinutes($jamKeluar));

            if ($totalMenit > 16 * 60) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Durasi kerja tidak boleh lebih dari 16 jam.');
            }

            $status = 'closed';
            $checkoutType = 'manual';
        }

        // Jika membuat presensi terbuka, pastikan pegawai belum punya shift aktif.
        if (! $jamKeluar) {
            $hasOpenShift = Presensi::where('pegawai_id', $pegawai->id)
                ->whereNull('jam_keluar')
                ->exists();

            if ($hasOpenShift) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Pegawai ini masih memiliki presensi aktif/belum check-out.');
            }
        }

        // Cek bentrok jam dengan presensi lain.
        if ($jamKeluar) {
            $nearbyPresensis = Presensi::where('pegawai_id', $pegawai->id)
                ->whereBetween('tanggal', [
                    $jamMasuk->copy()->subDay()->toDateString(),
                    $jamMasuk->copy()->addDay()->toDateString(),
                ])
                ->get();

            foreach ($nearbyPresensis as $existing) {
                $existingStart = Carbon::parse($existing->jam_masuk, $timezone);
                $existingEnd = $existing->jam_keluar
                    ? Carbon::parse($existing->jam_keluar, $timezone)
                    : $existingStart->copy()->addHours(16);

                $isOverlap = $jamMasuk->lt($existingEnd) && $jamKeluar->gt($existingStart);

                if ($isOverlap) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Jam presensi manual bentrok dengan presensi pegawai yang sudah ada.');
                }
            }
        }

        Presensi::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => $jamMasuk->toDateString(),
            'jam_masuk' => $jamMasuk,
            'jam_keluar' => $jamKeluar,
            'total_jam' => $totalMenit,
            'telat' => 0,
            'lembur' => 0,
            'status' => $status,
            'checkout_type' => $checkoutType,
            'catatan' => trim('Input manual. ' . ($validated['catatan'] ?? '')),
        ]);

        return redirect()
            ->route('presensi.riwayat.index')
            ->with('success', 'Presensi manual berhasil ditambahkan untuk ' . $pegawai->nama . '.');
    }
}
