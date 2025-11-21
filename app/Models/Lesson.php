<?php

namespace App\Models;

use App\LessonType;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, HasMedia, SoftDeletes;

    protected $fillable = [
        'chapter_id',
        'title',
        'description',
        'content',
        'type',
        'order',
        'duration_minutes',
        'is_active',
        'is_preview',
    ];

    protected function casts(): array
    {
        return [
            'type' => LessonType::class,
            'order' => 'integer',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
            'is_preview' => 'boolean',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
