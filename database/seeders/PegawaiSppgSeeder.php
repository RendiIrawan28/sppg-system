<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PegawaiSppgSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['uid_kartu' => '903A296F', 'nama' => 'Juwadi', 'divisi' => 'Kepala Divisi Kebersihan', 'status' => 'Aktif'],
            ['uid_kartu' => '8E82286F', 'nama' => 'Topo rudihartono', 'divisi' => 'Kebersihan', 'status' => 'Aktif'],
            ['uid_kartu' => '6BB6296F', 'nama' => 'Alan Ardianto Kurniawan', 'divisi' => 'Chef', 'status' => 'Aktif'],
            ['uid_kartu' => '50C9296F', 'nama' => 'Soleh Joko Prihatin', 'divisi' => 'Kepala Divisi Persiapan', 'status' => 'Aktif'],
            ['uid_kartu' => 'E02D296F', 'nama' => 'Veronica Dian Puji Lestari', 'divisi' => 'Persiapan', 'status' => 'Aktif'],
            ['uid_kartu' => 'F9CB296F', 'nama' => 'Sukeni', 'divisi' => 'Persiapan', 'status' => 'Aktif'],
            ['uid_kartu' => 'B0A1296F', 'nama' => 'Inatri Suparmi', 'divisi' => 'Persiapan', 'status' => 'Aktif'],
            ['uid_kartu' => '29A5296F', 'nama' => 'Yuniarti', 'divisi' => 'Persiapan', 'status' => 'Aktif'],
            ['uid_kartu' => '3109296F', 'nama' => 'Destri Djoko Djatmiko', 'divisi' => 'Persiapan', 'status' => 'Aktif'],
            ['uid_kartu' => 'D9BA296F', 'nama' => 'Caru Felliy Heteru', 'divisi' => 'Persiapan', 'status' => 'Aktif'],
            ['uid_kartu' => '2964296F', 'nama' => 'Arfian', 'divisi' => 'Persiapan', 'status' => 'Aktif'],
            ['uid_kartu' => 'B28E7D2F', 'nama' => 'Septiana Citra Wulaningrum', 'divisi' => 'Kepala Divisi Pengolahan', 'status' => 'Aktif'],
            ['uid_kartu' => '1E0C7D2F', 'nama' => 'Addin Sidik Purnomo', 'divisi' => 'Pengolahan', 'status' => 'Aktif'],
            ['uid_kartu' => '7AAF7A2F', 'nama' => 'Muhammad Thoriq Azis', 'divisi' => 'Pengolahan', 'status' => 'Aktif'],
            ['uid_kartu' => '5414762F', 'nama' => 'Jaka Mulyana', 'divisi' => 'Pengolahan', 'status' => 'Aktif'],
            ['uid_kartu' => 'CB8C742F', 'nama' => 'Evi Andriyani Cahyawati', 'divisi' => 'Pengolahan', 'status' => 'Aktif'],
            ['uid_kartu' => 'DADB7D2F', 'nama' => 'Natan Irsa Aliffian', 'divisi' => 'Pengolahan', 'status' => 'Aktif'],
            ['uid_kartu' => '427C7E2F', 'nama' => 'Warsiyah', 'divisi' => 'Pengolahan', 'status' => 'Aktif'],
            ['uid_kartu' => '6ABC752F', 'nama' => 'Sulistiyani', 'divisi' => 'Pengolahan', 'status' => 'Aktif'],
            ['uid_kartu' => '8398286F', 'nama' => 'Yani Sofiatiningrum', 'divisi' => 'Kepala Divisi Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => '07EF286F', 'nama' => 'Setiyo Budi Nugroho', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => 'A57C296F', 'nama' => 'Muhammad Wakhid Firmansyah', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => 'EC87296F', 'nama' => 'Danang Ari Santoso', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => '74CC286F', 'nama' => 'Hendrichus Feryindarta', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => '449B391E', 'nama' => 'Rizal Dwi Nugroho', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => '2CA83B1E', 'nama' => 'Sekti Fikriyah', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => '96FA7D2F', 'nama' => 'Cahyo Wibowo', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => 'F6883B1E', 'nama' => 'Ismail', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => '42EF391E', 'nama' => 'Diyamto', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => '2977296F', 'nama' => 'Mulyadi', 'divisi' => 'Distribusi', 'status' => 'Aktif'],
            ['uid_kartu' => 'EDF0286F', 'nama' => 'Tono Purwiyanto', 'divisi' => 'Distribusi', 'status' => 'Aktif'],
            ['uid_kartu' => '7A88296F', 'nama' => 'Abdul Jalil', 'divisi' => 'Distribusi', 'status' => 'Aktif'],
            ['uid_kartu' => 'BB96296F', 'nama' => 'Erva Yudianto', 'divisi' => 'Distribusi', 'status' => 'Aktif'],
            ['uid_kartu' => '2C67286F', 'nama' => 'Rendi Irawan', 'divisi' => 'Kepala Divisi Distribusi', 'status' => 'Aktif'],
            ['uid_kartu' => 'D993286F', 'nama' => 'Avi Zuhana', 'divisi' => 'Kepala Divisi Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => 'C1FC286F', 'nama' => 'Suryani', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => '2226296F', 'nama' => 'Tia Savitri', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => 'DC6B296F', 'nama' => 'Anita Rahmawati', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => '4AEB286F', 'nama' => 'Novi Nur Azizah', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => '3BC6296F', 'nama' => 'Priyanti Wulandari', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => '5578762F', 'nama' => 'Verayuliana', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => '3E7A7D2F', 'nama' => 'Sukini', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => 'FC6F296F', 'nama' => 'Endri Aryanto', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => '52BD296F', 'nama' => 'Fajar Eiy. K', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => 'CDF9286F', 'nama' => 'Alfian Nur Iksan', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => 'B021296F', 'nama' => 'Arief Nur Rosyid', 'divisi' => 'Pencucian', 'status' => 'Aktif'],
            ['uid_kartu' => '91D5286F', 'nama' => 'Muhammad Putra Khaerudin', 'divisi' => 'Admin Gudang', 'status' => 'Aktif'],
            ['uid_kartu' => 'E7C6296F', 'nama' => 'Alvin Fauzi Hakim', 'divisi' => 'Akuntan', 'status' => 'Aktif'],
            ['uid_kartu' => '7E74296F', 'nama' => 'Masna Luthfia Rahma', 'divisi' => 'Ahli Gizi', 'status' => 'Aktif'],
            ['uid_kartu' => 'A9CE51A3', 'nama' => 'Sujaryanto', 'divisi' => 'Keamanan', 'status' => 'Aktif'],
            ['uid_kartu' => 'F955F7A3', 'nama' => 'Ferry Subekti', 'divisi' => 'Keamanan', 'status' => 'Aktif'],
            ['uid_kartu' => 'E56262BC', 'nama' => 'Nabila Septia Aditya', 'divisi' => 'Pemorsian', 'status' => 'Aktif'],
            ['uid_kartu' => '0B70BEA9', 'nama' => 'Muhammad Aziz N.', 'divisi' => 'Distribusi', 'status' => 'Aktif'],
            ['uid_kartu' => '0DF23S42', 'nama' => 'Ahmad Makarim Pramudita', 'divisi' => 'Kepala SPPG', 'status' => 'Aktif'],
        ];

        foreach ($data as $row) {
            $divisi = Divisi::firstOrCreate(
                ['nama' => $row['divisi']],
                [
                    'jam_masuk' => null,
                    'jam_keluar' => null,
                ]
            );

            $payload = [
                'nama' => $row['nama'],
                'divisi_id' => $divisi->id,
                'uid_kartu' => strtoupper(trim($row['uid_kartu'])),
            ];

            if (Schema::hasColumn('pegawais', 'status')) {
                $payload['status'] = $row['status'];
            }

            $pegawai = Pegawai::where('uid_kartu', $payload['uid_kartu'])
                ->orWhere('nama', $row['nama'])
                ->first();

            if ($pegawai) {
                $pegawai->update($payload);
            } else {
                Pegawai::create($payload);
            }
        }
    }
}
