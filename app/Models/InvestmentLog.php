<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InvestmentLog extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['investment_id', 'type', 'amount', 'paid_by', 'log_date', 'note', 'user_id'];

    protected $appends = ['created_by', 'paid_by_name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id', 'id');
    }

    // Attribute - get user name from relationship 
    public function getCreatedByAttribute()
    {
        return optional($this->user)->name;
    }

    public function getPaidByNameAttribute()
    {
        return optional($this->user)->name;
    }


    // format to human readable structure
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y H:i');
    }
}
