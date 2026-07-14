<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'task_details',
        'priority_id',
        'current_state_id',
        'created_by_user_id',
        'owner_user_id',
        'planned_completion_date',
        'actual_completion_date',
        'is_perennial',
        'perennial_start_date',
        'perennial_end_date',
        'update_frequency_id',
        'last_update_date',
        'next_update_due_date',
        'is_overdue',
        'is_closed',
    ];

    protected $casts = [
        'planned_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'perennial_start_date' => 'date',
        'perennial_end_date' => 'date',
        'last_update_date' => 'date',
        'next_update_due_date' => 'date',
        'created_at' => 'date',
        'is_perennial' => 'boolean',
        'is_overdue' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(TaskUpdate::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(MasterPriority::class, 'priority_id');
    }
}
