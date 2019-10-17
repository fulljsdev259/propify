<?php

namespace App\Traits;


use Spatie\MediaLibrary\HasMedia\HasMediaTrait as MediaTrait;

trait HasMediaTrait
{
    use MediaTrait;

    /**
     *
     */
    public function registerMediaCollections()
    {
        $this->addMediaCollection('media');
    }
}
