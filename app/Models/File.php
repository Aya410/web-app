<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable=['name','state', 'request_join','group_id'];
/*
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
*/

    public function group()
{
    return $this->belongsTo(Group::class, 'group_id', 'id');
}
    public function versions()
    {
        return $this->hasMany(Version::class);
    }
    public static function boot()
    {
        parent::boot();

        // Automatically delete associated versions when a file is deleted
        static::deleting(function ($file) {
            $file->versions()->delete();
        });
    }

}
