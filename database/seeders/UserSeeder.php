<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $wilkernya = [
            'Dwikora',
            'Kijing',
            'Padang Tikar',
            'Teluk Batang',
            'Ketapang',
            'Kendawangan',
        ];

        $roles = [
            'user',
            'petugas',
            'petugas-kapal',
            'arsiparis',
            'keuangan',
        ];

        foreach ($wilkernya as $wilker) {
            foreach ($roles as $role) {
                User::create([
                    'nama_petugas' => ucfirst($role).' '.ucwords($wilker),
                    'nama_perusahaan' => ucfirst($role).' '.ucwords($wilker),
                    'no_hp' => '+628'.rand(1000000000, 9999999999),
                    'wilayah_kerja' => $wilker,
                    'status' => 'aktif',
                    'email' => str_replace(' ', '', $role.'_'.$wilker).'@mail.com',
                    'username' => str_replace(' ', '', $role.'_'.$wilker),
                    'email_verified_at' => null,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
