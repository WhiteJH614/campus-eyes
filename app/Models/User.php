<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'full_name',            // 对应 users.full_name
        'email',
        'password',             // 建议数据库里也用 password 这个字段名
        'phone_number',
        'role',                 // Reporter / Technician / Admin
        'reporter_role',        // Student / Staff （只对 Reporter 有意义）
        'campus',
        'specialization',       // Technician 用
        'availability_status',  // Technician 用：Available / Busy / On_Leave
        'admin_level',          // Admin 用：Supervisor 等
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
     * Reports created by this user as Reporter.
     */
    public function reports(): HasMany
    {
        // users.id -> reports.reporter_id
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Reports assigned to this user as Technician.
     */
    public function assignedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'technician_id');
    }
    
}
