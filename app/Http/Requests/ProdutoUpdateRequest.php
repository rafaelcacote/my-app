<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProdutoUpdateRequest extends FormRequest
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
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('produtosestoques.produtos', 'sku')->ignore($this->route('produto')->id)->whereNull('deleted_at'),
            ],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'categoria_id' => ['required', 'integer', 'exists:produtosestoques.categorias,id'],
            'marca_id' => ['required', 'integer', 'exists:produtosestoques.marcas,id'],
            'preco_custo' => ['required', 'numeric', 'min:0'],
            'preco_venda' => ['required', 'numeric', 'min:0'],
            'margem_lucro' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ativo' => ['nullable', 'boolean'],
            'controle_estoque' => ['nullable', 'boolean'],
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
            'sku.required' => 'O SKU é obrigatório.',
            'sku.unique' => 'Este SKU já está cadastrado.',
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.max' => 'O nome do produto não pode ter mais de 255 caracteres.',
            'categoria_id.required' => 'A categoria é obrigatória.',
            'categoria_id.exists' => 'A categoria selecionada não existe.',
            'marca_id.required' => 'A marca é obrigatória.',
            'marca_id.exists' => 'A marca selecionada não existe.',
            'preco_custo.required' => 'O preço de custo é obrigatório.',
            'preco_custo.min' => 'O preço de custo deve ser maior ou igual a zero.',
            'preco_venda.required' => 'O preço de venda é obrigatório.',
            'preco_venda.min' => 'O preço de venda deve ser maior ou igual a zero.',
            'margem_lucro.min' => 'A margem de lucro deve ser maior ou igual a zero.',
            'margem_lucro.max' => 'A margem de lucro não pode ser maior que 100%.',
        ];
    }
}
