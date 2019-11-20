<?php

namespace App\Transformers;

use App\Models\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as FCollection;
use League\Fractal\TransformerAbstract;

/**
 * Class BaseTransformer.
 *
 * @package namespace App\Transformers;
 */
class BaseTransformer extends TransformerAbstract
{
    /**
     * @param Collection $collection
     * @return mixed
     */
    public function transformCollection(Collection $collection)
    {
        $manager = new Manager();
        $media = new FCollection($collection, $this);
        return $manager->createData($media)->toArray()['data'];
    }

    /**
     * @param Collection $collection
     * @param string $method
     * @return array
     */
    public function transformCollectionBy(Collection $collection, $method = 'transform')
    {
        return $collection->transform(function ($value) use ($method) {
            return $this->{$method}($value);
        })->toArray();
    }

    /**
     * @param LengthAwarePaginator $paginator
     * @param string $method
     * @return LengthAwarePaginator
     */
    public function transformPaginator(LengthAwarePaginator $paginator, $method = 'transform')
    {
        $data = $paginator->getCollection()->transform(function ($value) use ($method) {
            return $this->{$method}($value);
        });

        return new LengthAwarePaginator(
            $data,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(), [
                'path' => request()->url(),
                'query' => [
                    'page' => $paginator->currentPage(),
                ]
            ]
        );
    }

    /**
     * @param $model
     * @param $response
     * @return mixed
     */
    protected function addAuditIdInResponseIfNeed($model, $response)
    {
        if ($model->relationExists('audit')) {
            $audit = $model->audit;
            if ($audit) {
                $response['audit_id'] = $audit->id;
            }
        }
        return $response;
    }

    /**
     * @param Model $model
     * @param $attributes
     * @param array $requiredAttributes
     * @return array
     */
    protected function getIfHasInAttributes(Model $model, $attributes, $requiredAttributes = [])
    {
        $response =  array_intersect_key($model->getAttributes(), array_flip($attributes));
        foreach ($requiredAttributes as $key => $attribute) {
            if (is_numeric($key)) {
                $key = $attribute;
            }
            $response[$key] = $model->{$attribute};
        }

        return $response;
    }
}
