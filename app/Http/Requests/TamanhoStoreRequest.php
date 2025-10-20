<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TamanhoStoreRequest extends FormRequest
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
            'tipo' => ['nullable', 'string', 'in:alfanumerico,numerico,texto'],
            'ordem' => ['nullable', 'integer', 'min:0'],
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
            'nome.required' => 'O nome do tamanho é obrigatório.',
            'nome.max' => 'O nome do tamanho não pode ter mais de 255 caracteres.',
            'tipo.in' => 'O tipo deve ser: alfanumerico, numerico ou texto.',
            'ordem.integer' => 'A ordem deve ser um número inteiro.',
            'ordem.min' => 'A ordem deve ser maior ou igual a zero.',
        ];
    }
}
