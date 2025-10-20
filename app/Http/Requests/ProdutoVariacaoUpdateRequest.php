<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoVariacaoUpdateRequest extends FormRequest
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
            'produto_id' => ['required', 'integer', 'exists:produtosestoques.produtos,id'],
            'tamanho_id' => ['required', 'integer', 'exists:produtosestoques.tamanhos,id'],
            'cor_id' => ['required', 'integer', 'exists:produtosestoques.cores,id'],
            'sku_variacao' => ['nullable', 'string', 'max:255'],
            'preco_adicional' => ['nullable', 'numeric', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
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
            'produto_id.required' => 'O produto é obrigatório.',
            'produto_id.exists' => 'O produto selecionado não existe.',
            'tamanho_id.required' => 'O tamanho é obrigatório.',
            'tamanho_id.exists' => 'O tamanho selecionado não existe.',
            'cor_id.required' => 'A cor é obrigatória.',
            'cor_id.exists' => 'A cor selecionada não existe.',
            'preco_adicional.min' => 'O preço adicional deve ser maior ou igual a zero.',
        ];
    }
}
