<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VipUserSeeder extends Seeder
{
    public function run(): void
    {
        // Bisa diatur via .env, kalau kosong pakai default
        $email    = env('VIP_EMAIL', 'vip@example.com');
        $password = env('VIP_PASSWORD', 'password123'); // ganti di .env untuk produksi
        $name     = env('VIP_NAME', 'VIP User');

        // Pastikan kolom is_vip sudah ada di tabel users
        // Jalankan migrasi: php artisan migrate

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(60),
                'is_vip'            => true,
            ]
        );

        // Tampilkan info di console saat seeding
        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('VIP user seeded: '.$user->email.' (is_vip=true)');
        }
    }
}
