<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    use HasFactory;
    
    protected $fillable=['number','time','file','file_id','user_id'];
    
    public function file()
    {
        return $this->belongsTo(File::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
