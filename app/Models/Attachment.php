<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'file_name',
        'file_path',
        'file_type',
        'attachment_type', // REPORTER_PROOF / TECHNICIAN_PROOF
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
