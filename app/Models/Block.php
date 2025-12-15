<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    use HasFactory;

    // 使用默认主键 id，不需要写 primaryKey
    protected $fillable = [
        'campus_id',
        'block_name',
    ];

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'block_id');
    }
}
