<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_number', 'marketplace', 'status_print'];

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
}
