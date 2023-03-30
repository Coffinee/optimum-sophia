<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fees extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'biller_id',
        'currency',
        'amount',
        'last_updated_by',
    ];

    public function biller(){
        return $this->belongsTo(Biller::class, 'biller_id')->select(['id','name', 'biller_category_id']);
    }

    public function fullname(){
        return $this->belongsTo(User::class, 'last_updated_by')->select(['id','first_name', 'last_name', 'role']);
    }

}
