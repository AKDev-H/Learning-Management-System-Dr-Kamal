<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

trait HasMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function getMedia(string $collection = 'default'): ?Media
    {
        return $this->media()->where('collection', $collection)->first();
    }

    public function getAllMedia(string $collection = 'default')
    {
        return $this->media()->where('collection', $collection)->get();
    }

    public function attachMedia(string $path, string $name, string $collection = 'default', ?string $disk = null, ?array $metadata = null): Media
    {
        $disk = $disk ?? config('filesystems.default');
        $storage = Storage::disk($disk);

        $mimeType = null;
        $size = null;

        if ($storage->exists($path)) {
            $mimeType = $storage->mimeType($path);
            $size = $storage->size($path);
        }

        return $this->media()->create([
            'disk' => $disk,
            'path' => $path,
            'name' => $name,
            'mime_type' => $mimeType,
            'size' => $size,
            'collection' => $collection,
            'metadata' => $metadata ?? [],
        ]);
    }

    public function getMediaUrl(?Media $media = null, string $collection = 'default'): ?string
    {
        if (! $media) {
            $media = $this->getMedia($collection);
        }

        if (! $media) {
            return null;
        }

        return Storage::disk($media->disk)->url($media->path);
    }

    public function getMediaPath(?Media $media = null, string $collection = 'default'): ?string
    {
        if (! $media) {
            $media = $this->getMedia($collection);
        }

        if (! $media) {
            return null;
        }

        return Storage::disk($media->disk)->path($media->path);
    }
}
