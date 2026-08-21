<?php

namespace App\Http\Requests;

use App\Enums\WorkShift;
use App\Models\Performance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerformanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $performance = $this->route('performance');
        if (! $performance instanceof Performance && $performance) {
            $performance = Performance::find($performance);
        }

        if (! $user) {
            return false;
        }

        if ($this->isMethod('post')) {
            return $user->can('create', Performance::class);
        }

        return $performance instanceof Performance
            && $user->can('update', $performance);
    }

    public function rules(): array
    {
        $performance = $this->route('performance');

        $performanceId = $performance instanceof Performance
            ? $performance->id
            : $performance;

        return [
            'studio_id' => [
                'required',
                'exists:studios,id',
            ],

            'user_id' => [
                'nullable',
                'exists:users,id',
            ],

            'first_name' => [
                'required',
                'string',
                'max:255',
            ],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'nickname' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('performances', 'email')
                    ->ignore($performanceId),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'country' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'document_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'document_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . now()
                    ->subYears(18)
                    ->toDateString(),
            ],

            'profile_photo' => [
                'nullable',
                'string',
            ],

            'active' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Turno habitual
            |--------------------------------------------------------------------------
            */

            'work_shift' => [
                'required',
                Rule::enum(WorkShift::class),
            ],

            /*
            |--------------------------------------------------------------------------
            | Estadísticas
            |--------------------------------------------------------------------------
            */

            'hours_streamed' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'ranking_score' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Plataformas
            |--------------------------------------------------------------------------
            */

            'platforms' => [
                'required',
                'array',
                'min:1',
            ],

            'platforms.*' => [
                'exists:platforms,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Distribución financiera
            |--------------------------------------------------------------------------
            */

            'split' => [
                'nullable',
                'array',
            ],

            'split.model_percentage' => [
                'required_with:split',
                'numeric',
                'min:50',
                'max:100',
            ],

            'split.studio_percentage' => [
                'required_with:split',
                'numeric',
                'min:0',
                'max:50',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'birth_date.required' =>
                'La fecha de nacimiento es obligatoria.',

            'birth_date.date' =>
                'La fecha de nacimiento no tiene un formato válido.',

            'birth_date.before_or_equal' =>
                'Debes ser mayor de edad para registrarte.',

            'studio_id.required' =>
                'Debes seleccionar un estudio.',

            'studio_id.exists' =>
                'El estudio seleccionado no existe.',

            'work_shift.required' =>
                'Debes seleccionar un turno.',

            'work_shift.enum' =>
                'El turno seleccionado no es válido.',

            'platforms.required' =>
                'Debes seleccionar al menos una plataforma.',

            'platforms.min' =>
                'Debes seleccionar al menos una plataforma.',

            'platforms.*.exists' =>
                'Una de las plataformas seleccionadas no existe.',

            'split.required' =>
                'La distribución de ganancias es obligatoria.',

            'split.model_percentage.required' =>
                'La participación de la modelo es obligatoria.',

            'split.model_percentage.min' =>
                'La participación de la modelo debe ser mínimo del 50%.',

            'split.model_percentage.max' =>
                'La participación de la modelo no puede superar el 100%.',

            'split.studio_percentage.required' =>
                'La participación del estudio es obligatoria.',

            'split.studio_percentage.max' =>
                'La participación del estudio no puede superar el 50%.',
        ];
    }
}
