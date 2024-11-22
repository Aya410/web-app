<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class group_user extends Model
{
    use HasFactory;

    // Specify the table name explicitly, if necessary
    protected $table = 'group_users';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'group_id',
        'request_join',
        
    ];
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
