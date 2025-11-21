<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassWork extends Model
{
    use HasFactory, HasMedia, SoftDeletes;

    protected $fillable = [
        'classroom_id',
        'title',
        'description',
        'content',
        'order',
        'due_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'due_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
