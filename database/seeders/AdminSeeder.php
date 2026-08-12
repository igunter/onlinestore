<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'is_admin'          => true,
            'name'              => 'Ian Gunter',
            'email'             => 'admin@example.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('P4$$w0rd'),
        ]);
    }
}
