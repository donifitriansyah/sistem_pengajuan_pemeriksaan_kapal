<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['nama_petugas' => 'RHEZKA IMANIAR FITRANTO, SKM', 'username' => 'RHEZKA_IMANIAR_FITRANTO', 'wilayah_kerja' => 'dwikora', 'role' => 'kawilker', 'nip' => '198706162008121002'],
            ['nama_petugas' => 'RIKA, SKM', 'username' => 'RIKA', 'wilayah_kerja' => 'dwikora', 'role' => 'bendahara_wilker', 'nip' => '197510111998032001'],
            ['nama_petugas' => 'DEDE MARTIN KURNIAWAN, S.A.P.', 'username' => 'DEDE_MARTIN_KURNIAWAN', 'wilayah_kerja' => 'dwikora', 'role' => 'arsiparis_wilker', 'nip' => '197903032006041021'],
            ['nama_petugas' => 'UTAMI', 'username' => 'UTAMI', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal', 'nip' => '197408032005012001'],
            ['nama_petugas' => 'ADI WIJAYANTO, SST', 'username' => 'ADI_WIJAYANTO', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal', 'nip' => '198003282008011013'],
            ['nama_petugas' => 'FELLIANDRE MARAFELINO, SKM', 'username' => 'FELLIANDRE_MARAFELINO', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal', 'nip' => '198707042008121001'],
            ['nama_petugas' => 'BRIGITA DWITA ANTA LINI, A.Md.KL', 'username' => 'BRIGITA_DWITA_ANTA_LINI', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal', 'nip' => '199203242014022002'],
            ['nama_petugas' => 'FARIS ANDRIANTO', 'username' => 'FARIS_ANDRIANTO', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal', 'nip' => '199310282018011001'],
            ['nama_petugas' => 'PUTRI PRATIWININGRUM', 'username' => 'PUTRI_PRATIWININGRUM', 'wilayah_kerja' => 'dwikora', 'role' => 'petugas-kapal', 'nip' => '199308252022032001'],
            ['nama_petugas' => 'dr. RIRI FATMA', 'username' => 'RIRI_FATMA', 'wilayah_kerja' => 'kendawangan', 'role' => 'kawilker', 'nip' => '197708252010122002'],
            ['nama_petugas' => 'EKO HADMA DEWANTARA, S.ST', 'username' => 'EKO_HADMA_DEWANTARA', 'wilayah_kerja' => 'kendawangan', 'role' => 'arsiparis_wilker', 'nip' => '199012122025211099'],
            ['nama_petugas' => 'RANDI RUSTIAWAN, A.md.KL', 'username' => 'RANDI_RUSTIAWAN', 'wilayah_kerja' => 'kendawangan', 'role' => 'bendahara_wilker', 'nip' => '199403182025211038'],
            ['nama_petugas' => 'MUJI UTOMO', 'username' => 'MUJI_UTOMO', 'wilayah_kerja' => 'ketapang', 'role' => 'kawilker', 'nip' => '197101011997031006'],
            ['nama_petugas' => 'SITI SUSANTI, SKM', 'username' => 'SITI_SUSANTI', 'wilayah_kerja' => 'ketapang', 'role' => 'bendahara_wilker', 'nip' => '198205212008122001'],
            ['nama_petugas' => 'UTIN ENNY MAHARANI', 'username' => 'UTIN_ENNY_MAHARANI', 'wilayah_kerja' => 'ketapang', 'role' => 'arsiparis_wilker', 'nip' => '198210302014122003'],
            ['nama_petugas' => 'JUNAIDI, A.Md.Kep', 'username' => 'JUNAIDI', 'wilayah_kerja' => 'ketapang', 'role' => 'petugas-kapal', 'nip' => '198007102007011018'],
            ['nama_petugas' => 'dr. MUHAMMAD FADHIL AMRULLAH', 'username' => 'MUHAMMAD_FADHIL_AMRULLAH', 'wilayah_kerja' => 'ketapang', 'role' => 'petugas-kapal', 'nip' => '199607272025061009'],
            ['nama_petugas' => 'SUHARNO, SKM', 'username' => 'SUHARNO', 'wilayah_kerja' => 'kijing', 'role' => 'kawilker', 'nip' => '197805082000031003'],
            ['nama_petugas' => 'ZAINUL AMBIYA, SKM', 'username' => 'ZAINUL_AMBIYA', 'wilayah_kerja' => 'kijing', 'role' => 'arsiparis_wilker', 'nip' => '198906162012121001'],
            ['nama_petugas' => 'SULINDAR ANDRYADMA, A.Md.Kep', 'username' => 'SULINDAR_ANDRYADMA', 'wilayah_kerja' => 'kijing', 'role' => 'bendahara_wilker', 'nip' => '199103042020121006'],
            ['nama_petugas' => 'ABINAWA ASOTJA', 'username' => 'ABINAWA_ASOTJA', 'wilayah_kerja' => 'padang tikar', 'role' => 'kawilker', 'nip' => '199108112012121001'],
            ['nama_petugas' => 'OKTAFIYAN PRIHADI KUSUMA', 'username' => 'OKTAFIYAN_PRIHADI_KUSUMA', 'wilayah_kerja' => 'padang tikar', 'role' => 'kawilker', 'nip' => '199110042022031002'],
            ['nama_petugas' => 'ERWIN KURNIAWAN, A.Md.Kep', 'username' => 'ERWIN_KURNIAWAN', 'wilayah_kerja' => 'padang tikar', 'role' => 'arsiparis_wilker', 'nip' => '199207222025211051'],
            ['nama_petugas' => 'WILLIANUS DEO, A.Md.Kep.', 'username' => 'WILLIANUS', 'wilayah_kerja' => 'padang tikar', 'role' => 'arsiparis_wilker', 'nip' => '199402252025211053'],
            // Admin user
            ['nama_petugas' => 'ADMIN', 'username' => 'ADMIN', 'wilayah_kerja' => 'dwikora', 'role' => 'admin', 'nip' => '199402252025211053'], // Admin role
        ];

        foreach ($users as $user) {
            $namaPetugas = $user['nama_petugas'];

            User::create([
                'nama_petugas' => $namaPetugas,
                'username' => $user['username'], // Use the exact username from the seeder data
                'wilayah_kerja' => strtolower($user['wilayah_kerja']), // Convert wilayah_kerja to lowercase
                'role' => $user['role'],
                'email' => $user['nip'] . '@mail.com', // Email generated using NIP
                'password' => bcrypt('password123'), // You can set a default password for all users
                'nama_perusahaan' => 'BKK', // You can set a default company name for all users
                'no_hp' => '-', // You can set a default phone number for all users
            ]);
        }
    }
}
