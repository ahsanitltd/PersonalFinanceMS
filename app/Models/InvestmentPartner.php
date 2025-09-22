<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class InvestmentPartner extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['user_id', 'name', 'type', 'contact', 'company_id', 'due', 'description'];
    protected $appends = ['created_by'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }


    // Attribute - get user name from relationship 
    public function getCreatedByAttribute()
    {
        return $this->user?->name;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y H:i');
    }
}
