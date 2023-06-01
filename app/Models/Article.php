<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use Notifiable,
        SoftDeletes;

    use HasFactory;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
