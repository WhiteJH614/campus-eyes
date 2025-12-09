<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'technician_id',
        'room_id',
        'category_id',
        'description',
        'urgency',          // Low / Medium / High
        'status',           // Pending / Assigned / In_Progress / Completed
        'resolution_notes', // 技术员填写的处理说明
        'due_at',
        'completed_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reporter(): BelongsTo
    {
        // users.id -> reports.reporter_id
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function technician(): BelongsTo
    {
        // users.id -> reports.technician_id
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'report_id');
    }
    
}
