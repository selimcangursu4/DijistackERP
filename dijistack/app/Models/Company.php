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
}
