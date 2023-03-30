<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'code', 'is_active', 'logo'];

    public function branches()
    {
        return $this->hasMany(Branch::class());
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
