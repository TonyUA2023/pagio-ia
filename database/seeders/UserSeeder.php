<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Estudiante de Prueba',
            'email' => 'estudiante@continental.edu.pe',
            'password' => Hash::make('password'), // La contraseña es "password"
            'role' => 'estudiante',
        ]);
    }
}
