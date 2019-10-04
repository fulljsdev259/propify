<?php

namespace App\Criteria\ServiceProvider;

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
        if (!$language) { return $model; }

        $model->join('user_settings', 'user_settings.user_id', '=', 'service_providers.user_id')
            ->where('user_settings.language', $language);

        return $model;
    }
}
