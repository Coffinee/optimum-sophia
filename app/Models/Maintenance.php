<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'ref', 'name'];

    public function scopeIsBank($query){
        $query->where('code','!=','source-of-funds')->where('code','!=','biller-category')->where('code','!=','transaction-type');
    }
    public function transactionName(){
        return $this->belongsTo(Maintenance::class, 'ref')->select(['id', 'name']);
    }
}
