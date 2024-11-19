<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'amount',
        'status',
        'payment_method',
        'commission_amount',
        'company_amount',
        'paid_at',
    ];



    public function serviceResquest()
    {
        return $this->belongsTo(ServiceRequest::class, );
    }
  
}