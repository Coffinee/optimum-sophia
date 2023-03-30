<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'user_name',
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'mobile_number',
        'email',
        'company_id',
        'branch_id',
        'status',
        'is_online',
        'profile_picture',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // public function companyName()
    // {
    //     return $this->hasOne(Company::class, 'id', 'company_id')->select(['id', 'name']);
    // }

    // public function branchName()
    // {
    //     return $this->hasOne(Branch::class, 'id', 'branch_id')->select(['id', 'name']);
    // }

    // public function currency()
    // {
    //     $branch = Branch::where('id', $this->branch_id)->first();
    //     return Country::where('id', $branch->country)->first('currency');
    // }

    public function permissions()
    {
        return $this->hasOne(UserPermission::class, 'user_id', 'id');
    }

    public function transactions()
    {
        return $this->belongsTo(Transaction::class);
    }
}
