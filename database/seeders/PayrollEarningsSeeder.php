<?php

namespace Database\Seeders;

use App\Models\Earning;
use App\Models\Performance;
use App\Models\Platform;
use App\Models\User;
use App\Services\EarningService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollEarningsSeeder extends Seeder
{
    public function run(): void
    {
        $earningService = app(EarningService::class);

        /*
        |--------------------------------------------------------------------------
        | Usuario administrativo
        |--------------------------------------------------------------------------
        |
        | EarningService::create() requiere un usuario autorizado.
        |
        | Los seeders se ejecutan desde CLI, por lo que Auth::user()
        | no existe.
        |
        | Además, las performances tienen prohibido crear earnings
        | directamente.
        |
        | Por eso utilizamos el Super Admin como actor del registro.
        |
        */

        $systemUser = User::query()
            ->where('email', 'admin@example.com')
            ->first();

        if (! $systemUser) {
            $this->command?->error(
                'No existe el usuario administrativo admin@example.com.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Performances de prueba
        |--------------------------------------------------------------------------
        |
        | ID 1 -> Camila
        | ID 2 -> Valentina
        |
        | Cargamos el usuario asociado solamente para validar
        | que la performance tenga relación con un usuario.
        |
        */

        $performances = Performance::query()
            ->with('user')
            ->whereIn('id', [1, 2])
            ->get()
            ->keyBy('id');

        if ($performances->isEmpty()) {
            $this->command?->error(
                'No existen las performances de prueba.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Plataformas existentes
        |--------------------------------------------------------------------------
        |
        | No creamos plataformas.
        | Utilizamos únicamente las existentes.
        |
        */

        $platforms = Platform::query()
            ->whereIn('name', [
                'Cam4',
                'Chaturbate',
                'LoyalFans',
                'Cams',
                'Stripchat',
            ])
            ->get()
            ->keyBy('name');

        if ($platforms->isEmpty()) {
            $this->command?->error(
                'No existen plataformas disponibles.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Producción histórica de prueba
        |--------------------------------------------------------------------------
        |
        | Cam4 / Chaturbate / Stripchat:
        |     amount = tokens
        |
        | LoyalFans / Cams:
        |     amount = USD
        |
        */

        $records = [

            /*
            |--------------------------------------------------------------------------
            | CAMILA - PERFORMANCE 1
            |--------------------------------------------------------------------------
            */

            // Enero - primera quincena

            [
                'performance_id' => 1,
                'date' => '2026-01-03',
                'platform' => 'Cam4',
                'amount' => 2500,
            ],

            [
                'performance_id' => 1,
                'date' => '2026-01-08',
                'platform' => 'Chaturbate',
                'amount' => 3500,
            ],

            [
                'performance_id' => 1,
                'date' => '2026-01-14',
                'platform' => 'Cam4',
                'amount' => 4200,
            ],

            // Enero - segunda quincena

            [
                'performance_id' => 1,
                'date' => '2026-01-18',
                'platform' => 'Chaturbate',
                'amount' => 5000,
            ],

            [
                'performance_id' => 1,
                'date' => '2026-01-23',
                'platform' => 'Cam4',
                'amount' => 3000,
            ],

            [
                'performance_id' => 1,
                'date' => '2026-01-29',
                'platform' => 'Chaturbate',
                'amount' => 4500,
            ],

            // Febrero - primera quincena

            [
                'performance_id' => 1,
                'date' => '2026-02-03',
                'platform' => 'Cam4',
                'amount' => 3200,
            ],

            [
                'performance_id' => 1,
                'date' => '2026-02-10',
                'platform' => 'Chaturbate',
                'amount' => 4800,
            ],

            [
                'performance_id' => 1,
                'date' => '2026-02-14',
                'platform' => 'Cam4',
                'amount' => 2500,
            ],

            // Febrero - segunda quincena

            [
                'performance_id' => 1,
                'date' => '2026-02-18',
                'platform' => 'Chaturbate',
                'amount' => 6000,
            ],

            [
                'performance_id' => 1,
                'date' => '2026-02-24',
                'platform' => 'Cam4',
                'amount' => 3500,
            ],

            [
                'performance_id' => 1,
                'date' => '2026-02-27',
                'platform' => 'Chaturbate',
                'amount' => 4200,
            ],

            /*
            |--------------------------------------------------------------------------
            | VALENTINA - PERFORMANCE 2
            |--------------------------------------------------------------------------
            */

            // Enero - primera quincena

            [
                'performance_id' => 2,
                'date' => '2026-01-04',
                'platform' => 'Cam4',
                'amount' => 1800,
            ],

            [
                'performance_id' => 2,
                'date' => '2026-01-09',
                'platform' => 'Chaturbate',
                'amount' => 3000,
            ],

            [
                'performance_id' => 2,
                'date' => '2026-01-13',
                'platform' => 'Cam4',
                'amount' => 2500,
            ],

            // Enero - segunda quincena

            [
                'performance_id' => 2,
                'date' => '2026-01-19',
                'platform' => 'Chaturbate',
                'amount' => 4200,
            ],

            [
                'performance_id' => 2,
                'date' => '2026-01-24',
                'platform' => 'Cam4',
                'amount' => 2800,
            ],

            [
                'performance_id' => 2,
                'date' => '2026-01-29',
                'platform' => 'Chaturbate',
                'amount' => 3500,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Crear earnings
        |--------------------------------------------------------------------------
        */

        foreach ($records as $record) {

            $performance = $performances->get(
                $record['performance_id']
            );

            if (! $performance) {
                $this->command?->warn(
                    "Performance {$record['performance_id']} no encontrada."
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Validar usuario de la performance
            |--------------------------------------------------------------------------
            */

            if (! $performance->user_id || ! $performance->user) {
                $this->command?->warn(
                    "La performance {$performance->id} "
                    . "{$performance->first_name} "
                    . "no tiene usuario asociado."
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Plataforma
            |--------------------------------------------------------------------------
            */

            $platform = $platforms->get(
                $record['platform']
            );

            if (! $platform) {
                $this->command?->warn(
                    "Plataforma {$record['platform']} no encontrada."
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Fecha
            |--------------------------------------------------------------------------
            */

            $date = Carbon::parse(
                $record['date']
            );

            /*
            |--------------------------------------------------------------------------
            | Evitar duplicados
            |--------------------------------------------------------------------------
            */

            $exists = Earning::query()
                ->where(
                    'performance_id',
                    $performance->id
                )
                ->where(
                    'platform_id',
                    $platform->id
                )
                ->whereDate(
                    'earned_at',
                    $date
                )
                ->where(
                    'original_amount',
                    $record['amount']
                )
                ->exists();

            if ($exists) {
                $this->command?->line(
                    "Ya existe: "
                    . "{$performance->first_name} "
                    . "{$platform->name} "
                    . "{$record['amount']} "
                    . "{$record['date']}"
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Crear earning
            |--------------------------------------------------------------------------
            |
            | IMPORTANTE:
            |
            | El actor es el Super Admin, NO la Performance.
            |
            | Esto mantiene intacta la regla de EarningService:
            |
            | Performance -> NO puede crear earnings.
            | Admin       -> SÍ puede crear earnings.
            |
            */

            $earningService->create(
                [
                    'performance_id' =>
                        $performance->id,

                    'platform_id' =>
                        $platform->id,

                    'original_amount' =>
                        $record['amount'],

                    'earned_at' =>
                        $date,

                    /*
                    | Usuario propietario de la producción.
                    */
                    'user_id' =>
                        $performance->user_id,

                    'status' =>
                        'draft',
                ],
                $systemUser
            );

            /*
            |--------------------------------------------------------------------------
            | Salida
            |--------------------------------------------------------------------------
            */

            $this->command?->info(
                sprintf(
                    '%s %s | %s | %s | %s | creado por %s',
                    $performance->first_name,
                    $performance->last_name,
                    $platform->name,
                    number_format(
                        $record['amount'],
                        2,
                        '.',
                        ','
                    ),
                    $date->format('Y-m-d'),
                    $systemUser->email
                )
            );
        }

        $this->command?->newLine();

        $this->command?->info(
            'PayrollEarningsSeeder finalizado correctamente.'
        );
    }
}