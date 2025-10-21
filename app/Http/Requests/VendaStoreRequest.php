<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loja_id' => ['required', 'exists:lojas,id'],
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'numero_venda' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:pendente,concluida,cancelada'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'desconto' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'forma_pagamento' => ['required', 'in:dinheiro,cartao_debito,cartao_credito,pix,boleto,transferencia'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'data_venda' => ['nullable', 'date'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_variacao_id' => ['required', 'exists:produto_variacoes,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
            'itens.*.preco_unitario' => ['required', 'numeric', 'min:0'],
            'itens.*.desconto' => ['nullable', 'numeric', 'min:0'],
            'pagamento' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'loja_id.required' => 'A loja é obrigatória.',
            'loja_id.exists' => 'A loja selecionada não existe.',
            'cliente_id.exists' => 'O cliente selecionado não existe.',
            'numero_venda.max' => 'O número da venda não pode ter mais de 50 caracteres.',
            'status.in' => 'O status deve ser pendente, concluída ou cancelada.',
            'subtotal.required' => 'O subtotal é obrigatório.',
            'subtotal.numeric' => 'O subtotal deve ser um número.',
            'subtotal.min' => 'O subtotal deve ser maior ou igual a zero.',
            'desconto.numeric' => 'O desconto deve ser um número.',
            'desconto.min' => 'O desconto deve ser maior ou igual a zero.',
            'total.required' => 'O total é obrigatório.',
            'total.numeric' => 'O total deve ser um número.',
            'total.min' => 'O total deve ser maior ou igual a zero.',
            'forma_pagamento.required' => 'A forma de pagamento é obrigatória.',
            'forma_pagamento.in' => 'A forma de pagamento deve ser uma das opções válidas.',
            'observacoes.max' => 'As observações não podem ter mais de 1000 caracteres.',
            'data_venda.date' => 'A data da venda deve ser uma data válida.',
            'itens.required' => 'A venda deve ter pelo menos um item.',
            'itens.array' => 'Os itens devem ser um array.',
            'itens.min' => 'A venda deve ter pelo menos um item.',
            'itens.*.produto_variacao_id.required' => 'O produto é obrigatório para cada item.',
            'itens.*.produto_variacao_id.exists' => 'O produto selecionado não existe.',
            'itens.*.quantidade.required' => 'A quantidade é obrigatória para cada item.',
            'itens.*.quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'itens.*.quantidade.min' => 'A quantidade deve ser maior que zero.',
            'itens.*.preco_unitario.required' => 'O preço unitário é obrigatório para cada item.',
            'itens.*.preco_unitario.numeric' => 'O preço unitário deve ser um número.',
            'itens.*.preco_unitario.min' => 'O preço unitário deve ser maior ou igual a zero.',
            'itens.*.desconto.numeric' => 'O desconto deve ser um número.',
            'itens.*.desconto.min' => 'O desconto deve ser maior ou igual a zero.',
        ];
    }
}