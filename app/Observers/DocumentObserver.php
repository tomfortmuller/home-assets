<?php

namespace App\Observers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentObserver
{
    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        if ($document->isDirty('path') && ! is_null($document->getOriginal('path'))) {
            Storage::disk('public')->delete($document->getOriginal('path'));
        }
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        if (! is_null($document->path)) {
            Storage::disk('public')->delete($document->path);
        }
    }
}
