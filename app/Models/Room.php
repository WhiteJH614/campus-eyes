<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'room_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'block_id',
        'floor_number',
        'room_name',
    ];

    /**
     * Get the block that this room belongs to.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id', 'block_id');
    }

    /**
     * Get the reports for this room.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'room_id', 'room_id');
    }
}
