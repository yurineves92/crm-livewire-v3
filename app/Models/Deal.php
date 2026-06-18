<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = ['customer_id', 'user_id', 'title', 'value', 'stage'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
