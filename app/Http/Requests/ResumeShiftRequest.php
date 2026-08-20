<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Shift;
use Illuminate\Foundation\Http\FormRequest;

class ResumeShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        /** @var Shift|null $shift */
        $shift = $this->route('shift');

        if (! $shift instanceof Shift) {
            return false;
        }

        $shift->loadMissing('performance');

        if (! $shift->performance) {
            return false;
        }

        $performance = $shift->performance;

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if (
            $user->hasRole('Admin') ||
            $user->hasRole('Monitor')
        ) {
            return $user->canAccessStudio(
                $performance->studio_id
            );
        }

        if ($user->hasRole('Performance')) {
            return $performance->user_id === $user->id;
        }

        return false;
    }

    public function rules(): array
    {
        return [];
    }
}
