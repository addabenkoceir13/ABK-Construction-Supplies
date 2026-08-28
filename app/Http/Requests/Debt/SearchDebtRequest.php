<?php

namespace App\Http\Requests\Debt;

use Illuminate\Foundation\Http\FormRequest;

class SearchDebtRequest extends FormRequest
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        // No Policy/Gate exists for Debt — any authenticated user may search
        // today. See KNOWN_ISSUES.md.
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
