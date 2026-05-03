<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'Marco',
                'last_name'  => 'Rossi',
                'email'      => 'marco@nahida.it',
                'password'   => Hash::make('password1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Giulia',
                'last_name'  => 'Bianchi',
                'email'      => 'giulia@nahida.it',
                'password'   => Hash::make('password1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Luca',
                'last_name'  => 'Verdi',
                'email'      => 'luca@nahida.it',
                'password'   => Hash::make('password1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
