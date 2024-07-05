<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_details', 'product_detail_id', 'invoice_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
