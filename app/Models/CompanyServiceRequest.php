<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'company_id',
        'company_user_id',
        'status',
        'bid_amount',
        'notes',
    ];


    public function serviceResquest()
    {
        return $this->belongsTo(ServiceRequest::class, );
    }
    public function company()
    {
        return $this->belongsTo(Company::class, );
    }

    
    // public function user()
    // {
    //     return $this->belongsTo(User::class, );
    // }
}
