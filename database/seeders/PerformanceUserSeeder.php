<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Performance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PerformanceUserSeeder extends Seeder
{
    public function run(): void
    {
        $models = [

            [
                'first_name' => 'Camila',
                'email' => 'camila@example.com',
            ],

            [
                'first_name' => 'Valentina',
                'email' => 'valentina@example.com',
            ],

        ];

        foreach ($models as $modelData) {

            /**
             * 1. Crear o actualizar usuario del sistema
             */
            $user = User::updateOrCreate(
                [
                    'email' => $modelData['email']
                ],
                [
                    'name' => $modelData['first_name'],
                    'password' => Hash::make('password')
                ]
            );

            $user->assignRole('Performance');

            /**
             * 2. Vincular Performance usando email (CORRECTO)
             * Evita depender de first_name (no es único)
             */
            Performance::where('email', $modelData['email'])
                ->update([
                    'user_id' => $user->id
                ]);
        }
    }
}