<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterPriority extends Model
{
    use HasFactory;

    protected $table = 'master_task_priorities';

    protected $fillable = [
        'priority_name',
        'priority_code',
        'sort_order',
        'default_sla_days',
        'color_code',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'default_sla_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'priority_id');
    }
}
