<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable=['name','state','group_id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function versions()
    {
        return $this->hasMany(Version::class);
    }

}
