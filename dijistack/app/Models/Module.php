<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';

    public function features()
    {
        return $this->hasMany(ModuleFeature::class);
    }
      public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_modules', 'module_id', 'company_id')
                    ->withPivot('status','activated_at','deactivated_at')
                    ->withTimestamps();
    }
}
