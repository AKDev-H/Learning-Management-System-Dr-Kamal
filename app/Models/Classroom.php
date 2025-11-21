<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use HasFactory, HasMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'instructor_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function classWorks(): HasMany
    {
        return $this->hasMany(ClassWork::class)->orderBy('order');
    }
}
