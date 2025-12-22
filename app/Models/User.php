<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',                 // Primary display name
        'email',
        'password',
        'phone_number',
        'role',                 // Reporter / Technician / Admin
        'reporter_role',        // Student / Staff
        'campus',
        'specialization',       // Technician specialization
        'availability_status',  // Technician availability: Available / Busy / On_Leave
        'admin_level',          // Admin level, e.g., Supervisor
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the reports submitted by this user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Get the reports assigned to this user (as technician).
     */
    public function assignedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'technician_id');
    }

    /**
     * Check if user is a Reporter.
     */
    public function isReporter(): bool
    {
        return $this->role === 'Reporter';
    }

    /**
     * Check if user is a Technician.
     */
    public function isTechnician(): bool
    {
        return $this->role === 'Technician';
    }

    /**
     * Check if user is an Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }
}
