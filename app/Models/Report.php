<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    /**
     * The attributes that are mass assignable.
     */
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
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user who submitted this report.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the technician assigned to this report.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Get the room where the issue is located.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    /**
     * Get the category of this report.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the attachments for this report.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'report_id');
    }

    /**
     * Scope to get reports by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get reports by urgency.
     */
    public function scopeUrgency($query, string $urgency)
    {
        return $query->where('urgency', $urgency);
    }
}
