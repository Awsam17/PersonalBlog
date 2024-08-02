<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title','post','image','user_id','category_id'];

    public function category () : BelongsTo
    {
        return $this->belongsTo(Category::class ,'category_id');
    }

    public function user () : BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }


}
