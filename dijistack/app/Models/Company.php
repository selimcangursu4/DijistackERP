<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

      public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'company_modules', 'company_id', 'module_id')
                    ->withPivot('status','activated_at','deactivated_at')
                    ->withTimestamps();
    }
}
