<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'image',
    ];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_details', 'product_id', 'invoice_id')
            ->withPivot('size', 'quantity')
            ->withTimestamps();
    }
}
