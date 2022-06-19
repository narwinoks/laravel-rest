<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageManipulation extends Model
{
    use HasFactory;

    protected $table = 'image_manipulations';
    // public $timestamps = false;
    const TYPE_RESIZE = 'resize';
    const  updated_at = null;
    protected $guarded = ['id'];
}
