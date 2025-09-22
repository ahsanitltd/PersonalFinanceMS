<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobEarning extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['company_id', 'amount', 'currency', 'earn_month', 'is_paid', 'paid_at', 'notes'];
    protected $appends = ['company_name'];


    // defined Relationship 
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }



    // Attribute - get name from relationship 
    public function getCompanyNameAttribute()
    {
        return $this->company?->name;
    }

    public function getIsPaidAttribute($value)
    {
        return $value ? 'Yes' : 'No';
    }
}
