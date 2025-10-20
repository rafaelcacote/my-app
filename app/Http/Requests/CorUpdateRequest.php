<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'codigo_hex' => ['nullable', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da cor é obrigatório.',
            'nome.max' => 'O nome da cor não pode ter mais de 255 caracteres.',
            'codigo_hex.regex' => 'O código hex deve estar no formato #RRGGBB ou #RGB.',
        ];
    }
}
