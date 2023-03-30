<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biller extends Model
{
    use HasFactory;
    protected $fillable = [
        'biller_category_id',
        'name',
        'company_id',
        'branch_id',
        'fields',
    ];

    public function billerCategoryName(){
        return $this->hasOne(Maintenance::class,'id','biller_category_id')->select(['id','name', 'ref']);
    }
    public function serviceFee(){
        return $this->hasOne(Fees::class,'biller_id','id')->select(['id','biller_id','amount']);
    }
}
