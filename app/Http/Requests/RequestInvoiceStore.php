<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestInvoiceStore extends FormRequest
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
            'invoice_number' => 'required|unique:invoices,invoice_number,' . ($this->invoice_number ? $this->invoice_number : ''),
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'invoice_number' => 'Nomor Invoice',
        ];
    }
}
