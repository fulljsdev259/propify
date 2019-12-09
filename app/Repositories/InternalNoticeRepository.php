<?php

namespace App\Repositories;

use App\Jobs\Notify\NotifyNewRequestInternalNotice;
use App\Models\InternalNotice;

/**
 * Class InternalNoticeRepository
 * @package App\Repositories
 * @version July 23, 2019, 6:24 am UTC
*/

class InternalNoticeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return InternalNotice::class;
    }

    public function create(array $attributes)
    {
        $model = parent::create($attributes);
        dispatch_now(new NotifyNewRequestInternalNotice($model));
        return $model;
    }
}
