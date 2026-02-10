<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $wilkernya = [
            'dwikora',
            'kijing',
            'padang tikar',
            'teluk batang',
            'ketapang',
            'kenawangan',
        ];

        $roles = [
            'user',
            'petugas',
            'petugas-kapal',
            'arsiparis',
        ];

        foreach ($wilkernya as $wilker) {
            foreach ($roles as $role) {
                User::create([
                    'nama_petugas'    => ucfirst($role) . ' ' . ucwords($wilker),
                    'nama_perusahaan' => ucfirst($role) . ' ' . ucwords($wilker),
                    'no_hp'           => '+628' . rand(1000000000, 9999999999),
                    'wilayah_kerja'   => $wilker,
                    'status'          => 'aktif',
                    'email'           => str_replace(' ', '', $role . '_' . $wilker) . '@mail.com',
                    'email_verified_at' => null,
                    'password'        => Hash::make('password'),
                    'role'            => $role,
                    'remember_token'  => Str::random(10),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}
