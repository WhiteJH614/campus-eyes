<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['block_id', 'floor_number', 'room_name'];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
