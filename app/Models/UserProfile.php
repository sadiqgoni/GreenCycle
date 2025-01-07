<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'address',
        'local_government',
        'account_number',
        'account_name',
        'user_id',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
  

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
