<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDistrict extends Model
{
    use HasFactory;

    protected $table = 'master_districts';

    protected $fillable = [
        'division_name',
        'district_name',
        'district_lgd_code',
        'district_code',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'district_id');
    }
}
