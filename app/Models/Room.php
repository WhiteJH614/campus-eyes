<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $primaryKey = 'room_id';
    public $timestamps = false;

    protected $fillable = [
        'block_id',
        'floor_number',
        'room_name',
    ];

    /**
     * Block this room belongs to.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * Reports related to this room.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'room_id');
    }
}
