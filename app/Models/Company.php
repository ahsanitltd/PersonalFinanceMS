<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['name', 'mobile', 'address'];


    public function jobEarnings()
    {
        return $this->hasMany(JobEarning::class);
    }
}
