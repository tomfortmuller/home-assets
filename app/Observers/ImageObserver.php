<?php

namespace App\Observers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageObserver
{
    /**
     * Handle the Image "updated" event.
     */
    public function updated(Image $image): void
    {
        if ($image->isDirty('path') && !is_null($image->getOriginal('path'))) {
            Storage::disk('public')->delete($image->getOriginal('path'));
        }
    }

    /**
     * Handle the Image "deleted" event.
     */
    public function deleted(Image $image): void
    {
        if (!is_null($image->path)) {
            Storage::disk('public')->delete($image->path);
        }
    }
}
