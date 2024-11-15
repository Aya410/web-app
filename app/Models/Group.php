<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'photo',
        'description',
       'admin_id'
    ];


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function groupUsers()
    {
        return $this->hasMany(group_user::class, 'group_id');
    }
}
