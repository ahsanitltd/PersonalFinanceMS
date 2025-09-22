<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['investment_partner_id', 'agreed_amount', 'amount_invested', 'your_due', 'partner_due', 'profit_type', 'profit_value', 'status', 'notes', 'user_id'];

    protected $appends = ['created_by', 'partner_name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function investmentPartner()
    {
        return $this->belongsTo(InvestmentPartner::class, 'investment_partner_id', 'id');
    }

    // Attribute - get user name from relationship 
    public function getCreatedByAttribute()
    {
        return $this->user?->name;
    }

    public function getPartnerNameAttribute()
    {
        return $this->investmentPartner?->name;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y H:i');
    }
}
