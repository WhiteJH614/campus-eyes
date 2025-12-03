<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    public const URGENCY = ['LOW', 'MEDIUM,', 'HIGH'];
    public const STATUS = ['PENDING', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED'];

    protected $fillable = [
        'reporter_id',
        'assigned_technician_id',
        'room_id',
        'category_id',
        'description',
        'urgency',
        'status',
        'resolution_notes',
        'due_at',
        'completed_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignedTechnician()
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
