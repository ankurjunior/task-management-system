<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskUpdateAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_update_id',
        'uploaded_by_user_id',
        'original_file_name',
        'stored_file_name',
        'file_path',
        'file_extension',
        'file_size',
    ];

    public function taskUpdate(): BelongsTo
    {
        return $this->belongsTo(TaskUpdate::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
