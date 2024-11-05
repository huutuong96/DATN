<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    // Các thuộc tính có thể được gán
    protected $fillable = [
        'blog_id',
        'slug',
        'title',
        'content',
        'image',
        'updated_by',
        'create_by', 
    ];

    public $timestamps = true; 


    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id'); 
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'create_by'); 
    }
    
    
}
