<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['product_id', 'rating',  'comment', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
