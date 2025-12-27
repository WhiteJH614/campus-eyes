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
        'urgency',
        'status',
        'resolution_notes',
        'due_at',
        'completed_at',
        'assigned_at',
    ];

    /**
     * Cast attributes to native types.
     * This ensures datetime fields are automatically converted to Carbon instances.
     */
    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    /**
     * Reporter who submitted the report.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Technician assigned to the report.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Room where the issue is reported.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    /**
     * Category of the report.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Attachments related to the report.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
