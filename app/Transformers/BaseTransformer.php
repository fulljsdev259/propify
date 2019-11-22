<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
//use League\Fractal\Manager;
//use League\Fractal\Resource\Collection as FCollection;
use League\Fractal\TransformerAbstract;

/**
 * Class BaseTransformer.
 *
 * @package namespace App\Transformers;
 */
class BaseTransformer extends TransformerAbstract
{
    const IsSingleRelation = 1;
    const IsManyRelation = 2;
    /**
     * @param Collection $collection
     * @return mixed
     */
    public function transformCollection(Collection $collection)
    {
        return $collection->map(function ($value) {
            return $this->transform($value);
        })->toArray();
        // @TODO delete This is not needed because we are not using This package logic
        // I can ask this package not needed here
//        $manager = new Manager();
//        $media = new FCollection($collection, $this);
//        return $manager->createData($media)->toArray()['data'];
    }

    /**
     * @param Collection $collection
     * @param string $method
     * @return array
     */
    public function transformCollectionBy(Collection $collection, $method = 'transform')
    {
        return $collection->map(function ($value) use ($method) {
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
        $data = $paginator->getCollection()->map(function ($value) use ($method) {
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
    protected function getAttributesIfExists(Model $model, $attributes, $requiredAttributes = [])
    {
        $response =  array_intersect_key($model->attributesToArray(), array_flip($attributes));
        foreach ($requiredAttributes as $key => $attribute) {
            if (is_numeric($key)) {
                $key = $attribute;
            }
            $response[$key] = $model->{$attribute};
        }

        return $response;
    }

    /**
     * @param Model $model
     * @param $response
     * @param $relations
     * @return mixed
     */
    protected function includeRelationIfExists(Model $model, $response, $relations)
    {
        foreach ($relations as $relation => $transformer) {
            if (! $model->relationExists($relation)) {
                continue;
            }
            $transformMethod = is_a($model->{$relation}, Collection::class) ? 'transformCollection' : 'transform';

            $response[$relation] =  (new $transformer())->{$transformMethod}($model->{$relation});
        }

        return $response;
    }
}
