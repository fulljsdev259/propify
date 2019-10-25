<?php

namespace App\Criteria\Audit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByAuditableCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByAuditableCriteria implements CriteriaInterface
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
        $type = $this->request->get('auditable_type', null);
        if($type !== 'all'){
            if($type){
                $model->where('auditable_type', $type);
            }
            if ($id = $this->request->get('auditable_id', null)) {            
                $model->where('auditable_id', $id);
            }
        }          
        return $model;
    }
}
