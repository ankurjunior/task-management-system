<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * User Model
 *
 * Represents a user in the application with associated roles, designations, and districts.
 * Extends Laravel's Authenticatable for built-in authentication functionality.
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'password_changed_at',
        'role_id',
        'designation_id',
        'district_id',
        'reporting_to_user_id',
        'mobile',
        'employee_code',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * Excludes sensitive information from API responses and serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * Automatically casts specified attributes to their designated types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the district associated with the user.
     *
     * @return BelongsTo
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(MasterDistrict::class, 'district_id');
    }

    /**
     * Get the designation associated with the user.
     *
     * @return BelongsTo
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(MasterDesignation::class, 'designation_id');
    }

    /**
     * Get the user who this user reports to.
     *
     * @return BelongsTo
     */
    public function reportingToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporting_to_user_id');
    }

    /**
     * Get the users who report to this user.
     *
     * @return HasMany
     */
    public function juniors(): HasMany
    {
        return $this->hasMany(User::class, 'reporting_to_user_id');
    }

    /**
     * Get all notifications for this user.
     *
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all unread notifications for this user.
     *
     * Filters notifications that have not yet been read (read_at is null).
     *
     * @return HasMany
     */
    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->whereNull('read_at');
    }

    /**
     * Get the name of the user's designation.
     *
     * Returns null if the designation is not set.
     *
     * @return string|null
     */
    public function getDesignationNameAttribute(): ?string
    {
        return $this->designation?->name;
    }

    /**
     * Get the task assignment capability of the user's designation.
     *
     * Indicates whether the user's designation allows task assignment.
     * Returns null if the designation is not set.
     *
     * @return string|null
     */
    public function getDesignationCanAssignTaskAttribute(): ?string
    {
        return $this->designation?->can_assign_task;
    }
}
