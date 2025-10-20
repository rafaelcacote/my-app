<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($userId)
                    ->whereNull('deleted_at'),
            ],
            'cpf' => [
                'nullable', 
                'string', 
                'min:11',
                'max:11', 
                Rule::unique('users', 'cpf')
                    ->ignore($userId)
                    ->whereNull('deleted_at')
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id'],
            'tipo' => ['nullable', 'string', 'in:proprietario,gerente,vendedor,super_admin'],
            'ativo' => ['nullable', 'boolean'],
            'lojas' => ['nullable', 'array'],
            'lojas.*' => ['integer', 'exists:lojas,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
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
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'cpf.size' => 'O CPF deve ter exatamente 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'empresa_id.exists' => 'A empresa selecionada não existe.',
            'tipo.in' => 'O tipo deve ser: Proprietário, Gerente, Vendedor ou Super Admin.',
            'lojas.array' => 'As lojas devem ser uma lista.',
            'lojas.*.exists' => 'Uma das lojas selecionadas não existe.',
            'roles.array' => 'Os perfis devem ser uma lista.',
            'roles.*.exists' => 'Um dos perfis selecionados não existe.',
        ];
    }
}