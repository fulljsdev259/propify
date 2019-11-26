<?php

namespace App\Criteria\Building;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByTypeCriteria
 * @package App\Criteria\Quarter
 */
class FilterByTypeCriteria implements CriteriaInterface
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $types = $this->request->get('types', null);
        if ($types) {
            $types = Arr::wrap($types);
            $model->where(function ($q) use ($types) {
                $firstType = array_shift($types);
                $q->where('types', 'like', '%' . $firstType . '%');
                foreach ($types as $type) {
                    // This is correct until Building::Type is smaller then 10
                    $q->orWhere('types', 'like', '%' . $type . '%');
                }
            });
        }

        return $model;
    }
}
