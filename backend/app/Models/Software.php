<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'size',
        'category',
        'website_url',
        'download_url',
        'icon_url',
        'file_name',
    ];

    protected $table = 'software';
}
