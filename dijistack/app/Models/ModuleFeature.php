<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleFeature extends Model
{
    protected $table = 'module_features';


    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
