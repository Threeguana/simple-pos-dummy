<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $items = $this->input('items', []);

            if (!is_array($items)) {
                return;
            }

            foreach ($items as $index => $item) {
                if (empty($item['product_id']) || empty($item['qty'])) {
                    continue;
                }

                $product = Product::find($item['product_id']);

                if ($product && $item['qty'] > $product->stock) {
                    $errorMessage = "Stok untuk produk {$product->name} tidak mencukupi (tersedia: {$product->stock}, diminta: {$item['qty']}).";
                    $validator->errors()->add("items.{$index}.qty", $errorMessage);
                    $validator->errors()->add('items', $errorMessage);
                }
            }
        });
    }
}