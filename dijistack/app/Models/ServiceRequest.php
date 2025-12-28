<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $table = "service_requests";

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusRelation()
    {
        return $this->belongsTo(ServiceRequestStatus::class, 'service_requests_status_id');
    }

    public function priorityRelation()
    {
        return $this->belongsTo(ServicePriorityStatus::class, 'priority_id');
    }
    public function moduleRelation()
    {
       return $this->belongsTo(Module::class, 'module_id');
    }
}
