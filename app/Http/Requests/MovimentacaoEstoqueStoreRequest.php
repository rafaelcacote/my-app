<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovimentacaoEstoqueStoreRequest extends FormRequest
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
            'loja_id' => ['required', 'integer', 'exists:lojas,id'],
            'produto_variacao_id' => ['required', 'integer', 'exists:produtosestoques.produto_variacoes,id'],
            'tipo' => ['required', 'string', 'in:entrada,saida,ajuste,transferencia'],
            'quantidade' => ['required', 'integer', 'min:1'],
            'quantidade_anterior' => ['required', 'integer', 'min:0'],
            'quantidade_atual' => ['required', 'integer', 'min:0'],
            'motivo' => ['nullable', 'string', 'max:255'],
            'observacao' => ['nullable', 'string'],
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
            'loja_id.required' => 'A loja é obrigatória.',
            'loja_id.exists' => 'A loja selecionada não existe.',
            'produto_variacao_id.required' => 'A variação do produto é obrigatória.',
            'produto_variacao_id.exists' => 'A variação do produto selecionada não existe.',
            'tipo.required' => 'O tipo de movimentação é obrigatório.',
            'tipo.in' => 'O tipo deve ser: entrada, saida, ajuste ou transferencia.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.min' => 'A quantidade deve ser maior que zero.',
            'quantidade_anterior.required' => 'A quantidade anterior é obrigatória.',
            'quantidade_anterior.min' => 'A quantidade anterior deve ser maior ou igual a zero.',
            'quantidade_atual.required' => 'A quantidade atual é obrigatória.',
            'quantidade_atual.min' => 'A quantidade atual deve ser maior ou igual a zero.',
            'motivo.max' => 'O motivo não pode ter mais de 255 caracteres.',
        ];
    }
}
