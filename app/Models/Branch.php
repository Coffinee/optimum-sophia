<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'address',
        'country',
        'state',
        'region',
        'province',
        'city',
        'zip_code',
        'prefix',
        'logo',
        'status',
        'partner_code',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function countryName()
    {
        return $this->belongsTo(Country::class, 'country')->select(['id', 'name']);
    }
    public function stateName()
    {
        return $this->belongsTo(State::class, 'state', 'id')->select(['id','name']);
    }
    public function regionName()
    {
        return $this->belongsTo(State::class, 'region', 'id')->select(['id','name']);
    }
    public function provinceName()
    {
        return $this->belongsTo(State::class, 'province', 'id')->select(['id','name']);
    }
    public function cityName()
    {
        return $this->belongsTo(City::class, 'city')->select(['id', 'name']);
    }
    public function rates()
    {
        return $this->hasMany(Rates::class, 'branch_id');
    }
    public function currencyName()
    {
        return $this->belongsTo(Country::class, 'country_id')->select(['id', 'currency']);
    }
    public function currency($id)
    {
        return Country::select('currency')->where('id', $id)->first();
    }
    public function currencyUsed($id, $type)
    {
        $data = Rates::where('currency_from',$id)->where('branch_id', $this->id)->first();
        return $type == 'single' ? $this->currency($data->currency_from)->currency : $this->currency($data->currency_from)->currency .' - '. $this->currency($data->currency_to)->currency;
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
