<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_account_name',
        'admin_account_number',
        'admin_bank_name',
        'status',
    ];
}
