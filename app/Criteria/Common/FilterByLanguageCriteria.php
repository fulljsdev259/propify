<?php

namespace App\Criteria\Common;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByLanguageCriteria
 * @package App\Criteria\ServiceProvider
 */
class FilterByLanguageCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * Apply criteria in query repository
     *
     * @param         Builder|Model     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $language = $this->request->get('language', null);

        if (! empty($language)) {
            $model->whereHas('settings', function ($q) use ($language) {
                $q->where('language', $language);
            });
        }

        return $model;
    }
}
