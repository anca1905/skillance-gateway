<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',        // Nama Device (Misal: Admin 1)
        'nomor_hp',
        'api_token',
        'status',
        'expired_date',
        'quota',
        'webhook_url'
    ];
}
