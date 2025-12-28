<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $table = 'sms_logs';


     protected $fillable = [
        'company_id',
        'module_id',
        'module_record_id',
        'recipient_name',
        'recipient_phone',
        'message',
        'sent_by',
        'status'
    ];
}
