<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestUpdateInvoice extends FormRequest
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
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $this->invoice,
            'marketplace' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'invoice_number' => 'Nomor resi',
            'marketplace' => 'Marketplace',
        ];
    }
}
