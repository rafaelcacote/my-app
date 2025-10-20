<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LojaStoreRequest extends FormRequest
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
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
                Rule::unique('lojas', 'cnpj')->whereNull('deleted_at'),
            ],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'ativo' => ['boolean'],
            
            // Endereço
            'endereco.endereco' => ['nullable', 'string', 'max:150'],
            'endereco.numero' => ['nullable', 'string', 'max:10'],
            'endereco.complemento' => ['nullable', 'string', 'max:150'],
            'endereco.bairro' => ['nullable', 'string', 'max:45'],
            'endereco.municipio_id' => ['nullable', 'integer', 'exists:municipios,id'],
            'endereco.cep' => ['nullable', 'string', 'max:20'],
            'endereco.referencia' => ['nullable', 'string', 'max:255'],
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
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'cnpj.regex' => 'O CNPJ deve estar no formato XX.XXX.XXX/XXXX-XX.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'email.email' => 'O email deve ser um endereço válido.',
        ];
    }
}
