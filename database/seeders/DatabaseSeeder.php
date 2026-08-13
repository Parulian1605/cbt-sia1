<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()
            ->where('email', 'josuasimbolon1605@gmail.com')
            ->first();

        if ($admin) {
            $admin->forceFill([
                'name' => 'Josua Simbolon',
                'username' => 'josua',
                'is_staff' => true,
                'password' => Hash::make('rahasia'),
            ])->save();

            return;
        }

        User::factory()->create([
            'name' => 'Josua Simbolon',
            'email' => 'josuasimbolon1605@gmail.com',
            'username' => 'josua',
            'is_staff' => true,
            'password' => Hash::make('rahasia'),
        ]);
    }
}
