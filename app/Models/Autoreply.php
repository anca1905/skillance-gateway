<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autoreply extends Model
{
    use HasFactory;

    protected $fillable = ['device_id', 'keyword', 'response', 'search_type'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
