<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerformanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $performanceId = $this->route('id');

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

            'platforms' => [
                'required',
                'array',
                'min:1',
            ],

            'platforms.*' => [
                'exists:platforms,id',
            ],

            'split' => [
                'required',
                'array',
            ],

            'split.model_percentage' => [
                'required',
                'numeric',
                'min:50',
                'max:100',
            ],

            'split.studio_percentage' => [
                'required',
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
                'Actualmente no cumples con la mayoría de edad requerida para registrarte.',

            'studio_id.required' =>
                'Debes pertenecer a un estudio para registrar una modelo.',

            'studio_id.exists' =>
                'El estudio seleccionado no existe.',

            'platforms.required' =>
                'Debes seleccionar al menos una plataforma.',

            'platforms.min' =>
                'Debes seleccionar al menos una plataforma.',

            'platforms.*.exists' =>
                'Una de las plataformas seleccionadas no existe.',

            'split.required' =>
                'La distribución de ganancias es obligatoria.',

            'split.model_percentage.min' =>
                'La participación de la modelo debe ser mínimo del 50%.',

            'split.model_percentage.max' =>
                'La participación de la modelo no puede superar el 100%.',

            'split.studio_percentage.max' =>
                'La participación del estudio no puede superar el 50%.',
        ];
    }
}