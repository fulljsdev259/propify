<?php

namespace App\Repositories;

use App\Models\Workflow;

/*
 *
 */
class WorkflowRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Workflow::class;
    }
}
