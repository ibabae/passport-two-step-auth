<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    use HasFactory;
    protected $fillable = [
        'phone',
        'verification_code',
        'verified_at',
    ];

}
