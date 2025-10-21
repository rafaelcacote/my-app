<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente');

        return [
            'nome' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:fisica,juridica'],
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:18',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $digits = preg_replace('/\D/', '', $value);
                        $tipo = $this->input('tipo');
                        
                        if ($tipo === 'fisica' && strlen($digits) !== 11) {
                            $fail('O CPF deve ter 11 dígitos.');
                        } elseif ($tipo === 'juridica' && strlen($digits) !== 14) {
                            $fail('O CNPJ deve ter 14 dígitos.');
                        }
                    }
                },
                Rule::unique('clientes', 'cpf_cnpj')
                    ->ignore($clienteId)
                    ->whereNull('deleted_at'),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date', 'before:today'],
            'cep' => ['nullable', 'string', 'max:10'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:2'],
            'ativo' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'O tipo deve ser pessoa física ou pessoa jurídica.',
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'email.email' => 'O email deve ser um endereço válido.',
            'data_nascimento.date' => 'A data de nascimento deve ser uma data válida.',
            'data_nascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'cep.max' => 'O CEP não pode ter mais de 10 caracteres.',
            'logradouro.max' => 'O logradouro não pode ter mais de 255 caracteres.',
            'numero.max' => 'O número não pode ter mais de 20 caracteres.',
            'complemento.max' => 'O complemento não pode ter mais de 255 caracteres.',
            'bairro.max' => 'O bairro não pode ter mais de 255 caracteres.',
            'cidade.max' => 'A cidade não pode ter mais de 255 caracteres.',
            'estado.max' => 'O estado não pode ter mais de 2 caracteres.',
        ];
    }
}