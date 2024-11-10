<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
   
    // Add user_id to the $fillable property
    protected $fillable = ['user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->hasMany(Group::class);
    }
}
