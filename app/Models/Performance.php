<?php

namespace App\Models;

use App\Enums\WorkShift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Performance extends Model
{
    protected $fillable = [
        'studio_id',
        'user_id',

        'first_name',
        'last_name',
        'nickname',

        'email',
        'phone',

        'country',
        'city',
        'address',

        'document_type',
        'document_number',

        'birth_date',
        'profile_photo',

        /*
        |--------------------------------------------------------------------------
        | Estado operativo
        |--------------------------------------------------------------------------
        */

        'active',

        /*
        |--------------------------------------------------------------------------
        | Turno habitual
        |--------------------------------------------------------------------------
        */

        'work_shift',

        /*
        |--------------------------------------------------------------------------
        | Estadísticas
        |--------------------------------------------------------------------------
        */

        'hours_streamed',
        'ranking_score',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',

            'active' => 'boolean',

            'work_shift' => WorkShift::class,

            'hours_streamed' => 'integer',

            'ranking_score' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    public function bonuses(): HasMany
    {
        return $this->hasMany(Bonus::class);
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(Deduction::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function split(): HasOne
    {
        return $this->hasOne(PerformanceSplit::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Turnos ejecutados
    |--------------------------------------------------------------------------
    |
    | work_shift = turno habitual/configurado.
    |
    | shifts = ejecuciones reales de turnos.
    |
    */

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(
            Platform::class,
            'performance_platform',
            'performance_id',
            'platform_id'
        )
            ->withPivot([
                'hours_streamed',
                'earnings_usd',
                'tokens',
                'multiplier',
                'conversion_rate',
                'recorded_at',
            ])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getNameAttribute(): string
    {
        return trim(
            "{$this->first_name} {$this->last_name}"
        ) ?: ($this->nickname ?? 'Sin nombre');
    }

    public function getWorkShiftLabelAttribute(): ?string
    {
        return $this->work_shift?->label();
    }
}