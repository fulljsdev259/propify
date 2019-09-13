<?php

namespace App\Transformers;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\Resource\Collection as FCollection;
use League\Fractal\Manager;

/**
 * Class MediaTransformer.
 *
 * @package namespace App\Transformers;
 */
class MediaTransformer extends BaseTransformer
{
    /**
     * Transform the User entity.
     *
     * @param \Spatie\MediaLibrary\Models\Media $model
     *
     * @return array
     */
    public function transform(Media $model)
    {
        $url = $model->getFullUrl();
        if (Str::startsWith($url, '//')) {
            $url = Str::replaceFirst('//', '/', $url);
        }
        return [
            'id' => $model->id,
            'name' => $model->file_name,
            'url' => $url,
            'collection_name' => $model->collection_name,
            'model_id' => $model->model_id,
            'order_column' => $model->order_column,
        ];
    }
}
