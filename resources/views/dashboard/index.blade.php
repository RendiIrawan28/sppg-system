@extends('layouts.main')

@section('content')
<div class="flex flex-col md:flex-row gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-lg flex-1">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Status Mode Sistem</h2>

        <div class="flex items-center justify-between gap-4">
            <span class="text-3xl font-bold uppercase @if($modeStatus == 'registrasi') text-red-600 @else text-green-600 @endif">
                {{ $modeStatus }}
            </span>
            <span class="text-sm text-gray-500">Mode saat ini digunakan oleh perangkat RFID.</span>
        </div>

        <p class="mt-2 text-sm text-gray-500">
            @if($modeStatus == 'registrasi')
            Mode REGISTRASI: kartu yang di-scan akan didaftarkan ke pegawai yang belum memiliki UID.
            @else
            Mode PRESENSI: kartu yang di-scan akan mencatat jam masuk atau jam keluar.
            @endif
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
            @if($modeStatus == 'presensi')
            <form action="{{ route('presensi.mode.registrasi') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow">
                    Ubah ke Mode REGISTRASI
                </button>
            </form>
            @else
            <form action="{{ route('presensi.mode.presensi') }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow">
                    Ubah ke Mode PRESENSI
                </button>
            </form>
            @endif

            <a href="{{ route('presensi.manual.create') }}"
                class="h-[46px] bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 rounded-xl shadow-sm transition flex items-center justify-center whitespace-nowrap">
                Tambah Manual
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-2">
        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-indigo-500">
            <p class="text-sm font-medium text-gray-500">Total Pegawai</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ \App\Models\Pegawai::count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">Masuk Hari Ini</p>
            <p class="text-3xl font-bold text-gray-900 mt-1" id="stat-hadir">{{ $presensiHariIni->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500">Belum Keluar</p>
            <p class="text-3xl font-bold text-gray-900 mt-1" id="stat-belum-keluar">{{ $presensiHariIni->whereNull('jam_keluar')->count() }}</p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex flex-wrap items-center gap-3">
        Log Presensi Hari Ini ({{ \Carbon\Carbon::today('Asia/Jakarta')->translatedFormat('l, d F Y') }})
        <span id="update-status" class="text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-blue-600">Memuat...</span>
    </h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kerja</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody id="presensi-table-body" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Memuat data real-time...</td>
                </tr>
            </tbody>
        </table>

        <div id="empty-state" class="hidden text-center py-8 text-gray-500">
            Belum ada presensi yang tercatat hari ini.
        </div>
    </div>
</div>

<script>
    const tableBody = document.getElementById('presensi-table-body');
    const updateStatus = document.getElementById('update-status');
    const emptyState = document.getElementById('empty-state');
    const statHadir = document.getElementById('stat-hadir');
    const statBelumKeluar = document.getElementById('stat-belum-keluar');

    function fetchData() {
        updateStatus.textContent = 'Memperbarui...';
        updateStatus.className = 'text-xs font-medium px-2 py-1 rounded-full bg-yellow-100 text-yellow-600';

        fetch("{{ route('presensi.data.presensi') }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data: ' + response.statusText);
                }

                return response.json();
            })
            .then(data => {
                tableBody.innerHTML = '';

                if (data.length === 0) {
                    emptyState.classList.remove('hidden');
                    tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada presensi.</td></tr>';
                    statHadir.textContent = '0';
                    statBelumKeluar.textContent = '0';
                } else {
                    emptyState.classList.add('hidden');
                    let countBelumKeluar = 0;

                    data.forEach(item => {
                        const row = document.createElement('tr');
                        const isOpen = item.jam_keluar === '—';

                        if (isOpen) {
                            countBelumKeluar++;
                            row.className = 'bg-yellow-50';
                        }

                        const jamKeluarDisplay = isOpen ?
                            '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Keluar</span>' :
                            item.jam_keluar;

                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.nama ?? '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.divisi ?? '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.tanggal ?? '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.jam_masuk ?? '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${jamKeluarDisplay}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.total_jam ?? '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.status ?? '-'}</td>
                        `;

                        tableBody.appendChild(row);
                    });

                    statHadir.textContent = data.length;
                    statBelumKeluar.textContent = countBelumKeluar;
                }

                updateStatus.textContent = 'Terakhir diupdate: ' + new Date().toLocaleTimeString('id-ID');
                updateStatus.className = 'text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-600';
            })
            .catch(error => {
                console.error('Fetch error:', error);
                updateStatus.textContent = 'Gagal mengambil data';
                updateStatus.className = 'text-xs font-medium px-2 py-1 rounded-full bg-red-100 text-red-600';
            });
    }

    fetchData();
    setInterval(fetchData, 15000);
</script>
@endsection