<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bundle_id',
        'software_id',
        'sort_order',
    ];

    /**
     * Get the bundle that owns the item.
     */
    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    /**
     * Get the software for this item.
     */
    public function software()
    {
        return $this->belongsTo(Software::class);
    }
}
