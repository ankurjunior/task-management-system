<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'updated_by_user_id',
        'state_id',
        'completion_percentage',
        'expected_completion_date',
        'remarks',
    ];

    protected $casts = [
        'completion_percentage' => 'decimal:2',
        'expected_completion_date' => 'date',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(MasterTaskState::class, 'state_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskUpdateAttachment::class);
    }
}
