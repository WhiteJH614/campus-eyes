<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',                 // Admin / Technician / Reporter
        'reporter_role',        // Student / Staff
        'campus',
        'specialization',       // Technician specialization
        'availability_status',  // Available / Busy / On_Leave
        'admin_level',          // Admin hierarchy
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* =======================================================
     | RELATIONSHIPS
     |=======================================================*/

    /**
     * Reports submitted by this user (Reporter).
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Reports assigned to this user (Technician).
     */
    public function assignedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'technician_id');
    }

    /* =======================================================
     | ROLE HELPERS (VERY IMPORTANT FOR CLEAN CONTROLLERS)
     |=======================================================*/

    /**
     * Check if user is an Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    /**
     * Check if user is a Technician.
     */
    public function isTechnician(): bool
    {
        return $this->role === 'Technician';
    }

    /**
     * Check if user is a Reporter.
     */
    public function isReporter(): bool
    {
        return $this->role === 'Reporter';
    }

    /* =======================================================
     | QUERY SCOPES (OPTIONAL BUT PROFESSIONAL)
     |=======================================================*/

    /**
     * Scope: only technicians.
     */
    public function scopeTechnicians($query)
    {
        return $query->where('role', 'Technician');
    }

    /**
     * Scope: only admins.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'Admin');
    }

    /**
     * Scope: only reporters.
     */
    public function scopeReporters($query)
    {
        return $query->where('role', 'Reporter');
    }
}
