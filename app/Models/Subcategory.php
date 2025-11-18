<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    protected $primaryKey = 'slug';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'slug',
        'name',
        'subcollection_id',
        'image_url',
    ];

    public function subcollection(): BelongsTo
    {
        return $this->belongsTo(Subcollection::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'subcategory_slug', 'slug');
    }
}
