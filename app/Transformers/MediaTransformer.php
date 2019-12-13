<?php

namespace App\Transformers;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;

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


        $response = $this->getAttributesIfExists(
            $model,
            [
                'id',
                'collection_name',
                'model_id',
                'order_column',
                'relations_count',
            ],
            [
                'name' => 'file_name',
            ]
        );
        $response['url'] = $url;
        $response['created_by'] = $model->created_at ? $model->created_at->format('d.m.Y') : '';

        return $response;
    }
}
