<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // admin
         $user = User::factory()->create([
             'name' => 'Super Admin',
             'email' => 'admin@taskflow.com',
             'password' => Hash::make('swi12n2&23sa'),
         ]);

         $user->assignRole('Super Admin');
    }
}
