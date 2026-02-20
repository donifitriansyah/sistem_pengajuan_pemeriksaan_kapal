<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['nama_petugas' => 'RHEZKA IMANIAR FITRANTO, SKM', 'username' => '198706162008121002', 'wilayah_kerja' => 'dwikora', 'role' => 'kawilker'],
            ['nama_petugas' => 'RIKA, SKM', 'username' => '197510111998032001', 'wilayah_kerja' => 'dwikora', 'role' => 'bendahara_wilker'],
            ['nama_petugas' => 'DEDE MARTIN KURNIAWAN, S.A.P.', 'username' => '197903032006041021', 'wilayah_kerja' => 'dwikora', 'role' => 'arsiparis_wilker'],
            ['nama_petugas' => 'UTAMI', 'username' => '197408032005012001', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal'],
            ['nama_petugas' => 'ADI WIJAYANTO, SST', 'username' => '198003282008011013', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal'],
            ['nama_petugas' => 'FELLIANDRE MARAFELINO, SKM', 'username' => '198707042008121001', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal'],
            ['nama_petugas' => 'BRIGITA DWITA ANTA LINI, A.Md.KL', 'username' => '199203242014022002', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal'],
            ['nama_petugas' => 'FARIS ANDRIANTO', 'username' => '199310282018011001', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal'],
            ['nama_petugas' => 'PUTRI PRATIWININGRUM', 'username' => '199308252022032001', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal'],
            ['nama_petugas' => 'dr. RIRI FATMA', 'username' => '197708252010122002', 'wilayah_kerja' => 'kendawangan', 'role' => 'kawilker'],
            ['nama_petugas' => 'EKO HADMA DEWANTARA, S.ST', 'username' => '199012122025211099', 'wilayah_kerja' => 'kendawangan', 'role' => 'arsiparis_wilker'],
            ['nama_petugas' => 'RANDI RUSTIAWAN, A.md.KL', 'username' => '199403182025211038', 'wilayah_kerja' => 'kendawangan', 'role' => 'bendahara_wilker'],
            ['nama_petugas' => 'MUJI UTOMO', 'username' => '197101011997031006', 'wilayah_kerja' => 'ketapang', 'role' => 'kawilker'],
            ['nama_petugas' => 'SITI SUSANTI, SKM', 'username' => '198205212008122001', 'wilayah_kerja' => 'ketapang', 'role' => 'bendahara_wilker'],
            ['nama_petugas' => 'UTIN ENNY MAHARANI', 'username' => '198210302014122003', 'wilayah_kerja' => 'ketapang', 'role' => 'arsiparis_wilker'],
            ['nama_petugas' => 'JUNAIDI, A.Md.Kep', 'username' => '198007102007011018', 'wilayah_kerja' => 'ketapang', 'role' => 'petugas-kapal'],
            ['nama_petugas' => 'dr. MUHAMMAD FADHIL AMRULLAH', 'username' => '199607272025061009', 'wilayah_kerja' => 'ketapang', 'role' => 'petugas-kapal'],
            ['nama_petugas' => 'SUHARNO, SKM', 'username' => '197805082000031003', 'wilayah_kerja' => 'kijing', 'role' => 'kawilker'],
            ['nama_petugas' => 'ZAINUL AMBIYA, SKM', 'username' => '198906162012121001', 'wilayah_kerja' => 'kijing', 'role' => 'arsiparis_wilker'],
            ['nama_petugas' => 'SULINDAR ANDRYADMA, A.Md.Kep', 'username' => '199103042020121006', 'wilayah_kerja' => 'kijing', 'role' => 'bendahara_wilker'],
            ['nama_petugas' => 'ABINAWA ASOTJA', 'username' => '199108112012121001', 'wilayah_kerja' => 'padang tikar', 'role' => 'kawilker'],
            ['nama_petugas' => 'OKTAFIYAN PRIHADI KUSUMA', 'username' => '199110042022031002', 'wilayah_kerja' => 'padang tikar', 'role' => 'kawilker'],
            ['nama_petugas' => 'ERWIN KURNIAWAN, A.Md.Kep', 'username' => '199207222025211051', 'wilayah_kerja' => 'padang tikar', 'role' => 'arsiparis_wilker'],
            ['nama_petugas' => 'WILLIANUS DEO, A.Md.Kep.', 'username' => '199402252025211053', 'wilayah_kerja' => 'padang tikar', 'role' => 'arsiparis_wilker'],
        ];

        foreach ($users as $user) {
            User::create([
                'nama_petugas' => $user['nama_petugas'],
                'username' => $user['username'],
                'wilayah_kerja' => strtolower($user['wilayah_kerja']), // Convert wilayah_kerja to lowercase
                'role' => $user['role'],
                'email' => strtolower(str_replace([' ', ','], '.', $user['nama_petugas'])) . '@mail.com', // Create dummy email based on nama_petugas
                'password' => bcrypt('password123'), // You can set a default password for all users
                'nama_perusahaan' => 'BKK', // You can set a default password for all users
                'no_hp' => '-', // You can set a default password for all users
            ]);
        }
    }
}
