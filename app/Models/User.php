<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    protected $fillable = ['name', 'email', 'password', 'role', 'position_id'];
    // Note: It is good practice to link User to Position if possible, or just rely on Employee.

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

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getIsCheckedInAttribute(): bool
    {
        return $this->attendances()->whereNull('checkout_time')->exists();
    }

    public function getLastCheckinTimeAttribute(): ?\Illuminate\Support\Carbon
    {
        $openAttendance = $this->attendances()
            ->whereNull('checkout_time')
            ->orderByDesc('checkin_time')
            ->first();

        if ($openAttendance) {
            return $openAttendance->checkin_time;
        }

        return $this->attendances()
            ->orderByDesc('checkin_time')
            ->value('checkin_time');
    }
}
