<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the bundle.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bundle items for the bundle.
     */
    public function items()
    {
        return $this->hasMany(BundleItem::class)->orderBy('sort_order');
    }

    /**
     * Get the software through bundle items.
     */
    public function software()
    {
        return $this->hasManyThrough(
            Software::class,
            BundleItem::class,
            'bundle_id',
            'id',
            'id',
            'software_id'
        )->orderBy('bundle_items.sort_order');
    }
}
