<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntradaMercadoriaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loja_id' => ['required', 'integer', 'exists:lojas,id'],
            'fornecedor_id' => ['nullable', 'integer', 'exists:gestao.fornecedores,id'],
            'numero_nota' => ['nullable', 'string', 'max:50'],
            'data_entrada' => ['required', 'date'],
            'valor_total' => ['required', 'numeric', 'min:0.01'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_variacao_id' => ['required', 'integer', 'exists:produto_variacoes,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
            'itens.*.preco_unitario' => ['required', 'numeric', 'min:0.01'],
            'itens.*.total' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'loja_id.required' => 'A loja é obrigatória.',
            'loja_id.exists' => 'A loja selecionada não existe.',
            'fornecedor_id.exists' => 'O fornecedor selecionado não existe.',
            'numero_nota.max' => 'O número da nota não pode ter mais de 50 caracteres.',
            'data_entrada.required' => 'A data de entrada é obrigatória.',
            'data_entrada.date' => 'A data de entrada deve ser uma data válida.',
            'valor_total.required' => 'O valor total é obrigatório.',
            'valor_total.numeric' => 'O valor total deve ser um número.',
            'valor_total.min' => 'O valor total deve ser maior que zero.',
            'observacoes.max' => 'As observações não podem ter mais de 1000 caracteres.',
            'itens.required' => 'É necessário adicionar pelo menos um item.',
            'itens.min' => 'É necessário adicionar pelo menos um item.',
            'itens.*.produto_variacao_id.required' => 'O produto é obrigatório para cada item.',
            'itens.*.produto_variacao_id.exists' => 'O produto selecionado não existe.',
            'itens.*.quantidade.required' => 'A quantidade é obrigatória para cada item.',
            'itens.*.quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'itens.*.quantidade.min' => 'A quantidade deve ser maior que zero.',
            'itens.*.preco_unitario.required' => 'O preço unitário é obrigatório para cada item.',
            'itens.*.preco_unitario.numeric' => 'O preço unitário deve ser um número.',
            'itens.*.preco_unitario.min' => 'O preço unitário deve ser maior que zero.',
            'itens.*.total.required' => 'O total é obrigatório para cada item.',
            'itens.*.total.numeric' => 'O total deve ser um número.',
            'itens.*.total.min' => 'O total deve ser maior que zero.',
        ];
    }
}
