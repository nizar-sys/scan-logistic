<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestUpdateProduct extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'code' => 'required|unique:products,code,' . $this->product,
            'name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|',
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'Kode Produk',
            'name' => 'Nama Produk',
            'image' => 'Gambar Produk',
        ];
    }
}
