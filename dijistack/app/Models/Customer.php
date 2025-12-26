<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
    'company_id',
    'fullname',
    'company_name',
    'email',
    'phone',
    'phone_secondary',
    'address',
    'country_id',
    'city_id',
    'district_id',
    'postal_code',
    'tax_office',
    'tax_number',
    'customer_type_id',
    'customer_status_id',
    'notes',
    'created_by',
    'customer_preferred_contact_method_id'
];

}
