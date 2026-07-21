<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ControlActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'speed' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'mode' => ['nullable', 'in:auto,manual'],
        ];
    }
}
