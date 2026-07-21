<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group' => ['nullable', 'string', 'max:50'],
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable'],
        ];
    }
}
