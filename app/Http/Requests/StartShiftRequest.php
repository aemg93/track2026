<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Performance;
use Illuminate\Foundation\Http\FormRequest;

class StartShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        /** @var Performance|null $performance */
        $performance = $this->route('performance');

        if (! $performance instanceof Performance) {
            return false;
        }

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
