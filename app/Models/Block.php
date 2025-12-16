<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'block_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'block_name',
    ];

    /**
     * Get the rooms in this block.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'block_id', 'block_id');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'block_id';
    }
}
