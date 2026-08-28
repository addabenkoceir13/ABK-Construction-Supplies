<?php

namespace App\Http\Requests\Debt;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreDebtRequest extends FormRequest
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        // No Policy/Gate exists for Debt — any authenticated user may create
        // one today. See KNOWN_ISSUES.md.
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric'],
            'date_debut_debt' => ['required', 'date'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function failedValidation(Validator $validator)
    {
        toastr()->error($validator->errors()->first());

        parent::failedValidation($validator);
    }
}
