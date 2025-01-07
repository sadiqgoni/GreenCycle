<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'ceo_name',
        'ceo_email',
        'contact_info',
        'description',
        'registration_number',
        'tax_number',
        'availability_status',
        'verification_status',
        'service_radius',
        'commission_rate',
        'bank_details',
        'image',
        'rejection_note'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'commission_rate' => 'float',
    ];
    public function companyServiceRequest()
    {
        return $this->hasMany(CompanyServiceRequest::class, 'company_id');
    }
    /**
     * Define a one-to-many relationship with ServiceRequest.
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
