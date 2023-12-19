<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'creator_id',
    ];
    public function user(){
        return $this->belongsTo(User::class,'creator_id','id','user');
    }
}
