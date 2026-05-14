<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_ADMIN_RESTRICTED = 'admin_restricted';
    public const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'birthday',
        'email',
        'password',
        'role',
        'status',
        'is_admin',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'birthday' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            $fullName = trim(implode(' ', array_filter([
                $user->first_name,
                $user->last_name,
            ])));

            if ($fullName !== '') {
                $user->name = $fullName;
            }

            if (! $user->role) {
                $user->role = $user->is_admin ? self::ROLE_ADMIN : self::ROLE_USER;
            }

            $user->status = $user->status ?: 'active';
            $user->is_admin = $user->isAdminRole();
        });
    }

    public function isAdminRole(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_ADMIN_RESTRICTED], true);
    }

    public function canAccessAmicus(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getFullNameAttribute(): string
    {
        $fullName = trim(implode(' ', array_filter([
            $this->first_name,
            $this->last_name,
        ])));

        return $fullName !== '' ? $fullName : ($this->name ?: $this->email);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function aiRequests(): HasMany
    {
        return $this->hasMany(AiRequest::class);
    }
}
