<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceWarranty extends Model
{
    protected $table = "service_warranties";

     protected $fillable = [
        'company_id',
        'product_id',
        'imei',
        'invoice_date',
        'warranty_end_date',
        'warranty_status'
    ];

    public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}
}
