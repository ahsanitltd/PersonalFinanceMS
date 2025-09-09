<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InvestmentEntity extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['user_id', 'name', 'type', 'contact', 'description'];
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
        return optional($this->user)->name;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y H:i');
    }
}
