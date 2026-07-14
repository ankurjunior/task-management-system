<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDesignation extends Model
{
    use HasFactory;

    protected $table = 'master_designations';

    protected $fillable = [
        'designation_type_id',
        'name',
        'short_name',
        'hierarchy_level',
        'can_assign_task',
        'is_active',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'designation_id');
    }
}
