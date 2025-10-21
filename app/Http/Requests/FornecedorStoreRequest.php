<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FornecedorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:18',
                'regex:/^(\d{3}\.\d{3}\.\d{3}-\d{2}|\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2})$/',
                Rule::unique('gestao.fornecedores', 'cpf_cnpj')->where('empresa_id', auth()->user()->empresa_id),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'ativo' => ['boolean'],
            'endereco.endereco' => ['nullable', 'string', 'max:255'],
            'endereco.numero' => ['nullable', 'string', 'max:20'],
            'endereco.complemento' => ['nullable', 'string', 'max:255'],
            'endereco.bairro' => ['nullable', 'string', 'max:255'],
            'endereco.municipio_id' => ['nullable', 'integer', 'exists:municipios,id'],
            'endereco.cep' => ['nullable', 'string', 'max:10'],
            'endereco.referencia' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'cpf_cnpj.regex' => 'O CPF/CNPJ deve estar no formato correto (XXX.XXX.XXX-XX ou XX.XXX.XXX/XXXX-XX).',
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'telefone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'ativo.boolean' => 'O status deve ser ativo ou inativo.',
            'endereco.endereco.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'endereco.numero.max' => 'O número não pode ter mais de 20 caracteres.',
            'endereco.complemento.max' => 'O complemento não pode ter mais de 255 caracteres.',
            'endereco.bairro.max' => 'O bairro não pode ter mais de 255 caracteres.',
            'endereco.municipio_id.exists' => 'O município selecionado não existe.',
            'endereco.cep.max' => 'O CEP não pode ter mais de 10 caracteres.',
            'endereco.referencia.max' => 'A referência não pode ter mais de 255 caracteres.',
        ];
    }
}
