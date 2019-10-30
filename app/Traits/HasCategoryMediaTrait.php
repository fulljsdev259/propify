<?php

namespace App\Traits;


use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

trait HasCategoryMediaTrait
{
    use HasMediaTrait;

    public function registerMediaCollections()
    {
        foreach (\ConstantsHelpers::MediaFileCategories as $category)  {
            $this->addMediaCollection($category);
        }
    }
}
