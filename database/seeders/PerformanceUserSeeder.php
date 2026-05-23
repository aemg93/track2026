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
        $models=[

            [

                'name'=>'Model A',

                'email'=>'modela@example.com'

            ],

            [

                'name'=>'Model B',

                'email'=>'modelb@example.com'

            ]

        ];

        foreach(
            $models as $modelData
        ){

            $user=
            User::updateOrCreate(

                [

                    'email'=>
                    $modelData['email']

                ],

                [

                    'name'=>
                    $modelData['name'],

                    'password'=>
                    Hash::make(
                        'password'
                    )

                ]

            );

            $user->assignRole(
                'Performance'
            );

            Performance::where(
                'name',
                $modelData['name']
            )->update([

                'user_id'=>
                $user->id

            ]);

        }
    }
}