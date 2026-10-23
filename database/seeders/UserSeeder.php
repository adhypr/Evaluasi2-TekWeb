<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun admin awal. Kredensial bisa diganti lewat .env (SEED_ADMIN_EMAIL / SEED_ADMIN_PASSWORD).
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => env('SEED_ADMIN_EMAIL', 'admin@dhyecast.test')]);
        $admin->name = 'Admin Dhyecast';
        $admin->password = env('SEED_ADMIN_PASSWORD', 'admin12345');
        $admin->role = User::ROLE_ADMIN; // role tidak mass-assignable, sengaja diisi langsung
        $admin->email_verified_at ??= now();
        $admin->save();

        $customer = User::firstOrNew(['email' => 'customer@dhyecast.test']);
        $customer->name = 'Demo Customer';
        $customer->password = 'customer12345';
        $customer->role = User::ROLE_CUSTOMER;
        $customer->email_verified_at ??= now();
        $customer->save();
    }
}
