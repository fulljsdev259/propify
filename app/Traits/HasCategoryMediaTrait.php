<?php

namespace App\Traits;


use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

trait HasCategoryMediaTrait
{
    use HasMediaTrait;

    public function registerMediaCollections()
    {
        foreach (\ConstFileCategories::MediaCategories as $category)  {
            $this->addMediaCollection($category);
        }
    }
}
