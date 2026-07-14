<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notification Model
 *
 * Represents a notification in the application for users regarding tasks and updates.
 * Supports marking notifications as read/unread and filtering by read status.
 *
 * @package App\Models
 */
class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'task_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'sent_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * Automatically casts specified attributes to their designated types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the user associated with this notification.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the task associated with this notification.
     *
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Scope to retrieve only unread notifications.
     *
     * Filters notifications where read_at is null.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to retrieve only read notifications.
     *
     * Filters notifications where read_at is not null.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark this notification as read.
     *
     * Sets the read_at timestamp to the current time.
     * Returns true if the notification was already read or is successfully marked as read.
     *
     * @return bool
     */
    public function markAsRead(): bool
    {
        if ($this->read_at) {
            return true;
        }

        return $this->forceFill(['read_at' => now()])->save();
    }
}
