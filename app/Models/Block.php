<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    use HasFactory;

    protected $primaryKey = 'block_id';
    public $timestamps = false;

    protected $fillable = [
        'block_name',
    ];

    /**
     * Rooms under this block.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'block_id');
    }
}
