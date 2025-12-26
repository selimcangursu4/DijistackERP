<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicalService extends Model
{
    protected $table = 'technical_services';
    

     protected $fillable = [
        'company_id',
        'customer_id',
        'product_id',
        'serial_number',
        'service_fault_category_id',
        'fault_description',
        'service_priority_id',
        'service_status_id',
        'rack_section_id',
        'estimated_completion_date',
        'actual_completion_date',
        'invoice_date',
        'part_required',
        'delivery_method_id',
        'notes',
        'user_id',
        'service_ticket',
    ];

    protected $casts = [
        'service_ticket' => 'array',
        'part_required' => 'boolean',
        'estimated_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'invoice_date' => 'date',
    ];
}
