<?php

namespace App\Criteria\Listing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByUserCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByResidentCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * FilterByResidentCriteria constructor.
     * @param Request $request
     */
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
        $resident_id = $this->request->get('resident_id', null);
        // @TODO ask use this one or nested relation criteria
//        $user_id = Resident::where('id', $resident_id)->value('user_id');
//        if ($user_id) {
//            $model->where('listings.user_id', $user_id);
//        }
        if ($resident_id) {
            $model->whereHas('user', function($q) use ($resident_id) {
                $q->whereHas('resident', function($q)  use ($resident_id) {
                    $q->where('id', $resident_id);
                });
            });
        }

        return $model;
    }
}
