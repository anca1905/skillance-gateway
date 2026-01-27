<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageHistory extends Model
{
    use HasFactory;

    protected $fillable = ['device_id', 'number', 'message', 'status', 'error_log'];

    // Relasi ke Device (Biar tau ini pesan dari HP siapa)
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
