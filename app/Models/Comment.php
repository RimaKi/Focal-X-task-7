<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable=[
        'content',
        'commentable_id',
        'commentable_type',
        'user_id'
    ];

    public function commentable(){
        return $this->morphTo();
    }

}
