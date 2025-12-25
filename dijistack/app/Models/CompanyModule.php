<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyModule extends Model
{
    protected $table = 'company_modules';

     public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function features()
    {
        return $this->module->features();
    }
}
