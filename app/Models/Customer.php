<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['user_id', 'name', 'email', 'phone', 'company'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }
}
