<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_token',
    ];
}
