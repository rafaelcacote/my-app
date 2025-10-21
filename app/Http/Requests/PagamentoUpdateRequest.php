<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagamentoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'venda_id' => ['required', 'exists:vendas,id'],
            'forma_pagamento' => ['required', 'in:dinheiro,cartao_debito,cartao_credito,pix,boleto,transferencia'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'status' => ['nullable', 'in:pendente,pago,cancelado'],
            'data_pagamento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'venda_id.required' => 'A venda é obrigatória.',
            'venda_id.exists' => 'A venda selecionada não existe.',
            'forma_pagamento.required' => 'A forma de pagamento é obrigatória.',
            'forma_pagamento.in' => 'A forma de pagamento deve ser uma das opções válidas.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.min' => 'O valor deve ser maior que zero.',
            'status.in' => 'O status deve ser pendente, pago ou cancelado.',
            'data_pagamento.date' => 'A data do pagamento deve ser uma data válida.',
            'observacoes.max' => 'As observações não podem ter mais de 1000 caracteres.',
        ];
    }
}