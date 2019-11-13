<?php

namespace App\Repositories;

use App\Models\Quarter;

/**
 * Class QuarterRepository
 * @package App\Repositories
 * @version February 21, 2019, 9:27 pm UTC
 *
 * @method Quarter findWithoutFail($id, $columns = ['*'])
 * @method Quarter find($id, $columns = ['*'])
 * @method Quarter first($columns = ['*'])
 */
class QuarterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name' => 'like',
        'description' => 'like',
        'quarter_format' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Quarter::class;
    }

    /**
     * @param array $attributes
     * @return Quarter|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $quarter = parent::create($attributes);

        if ($quarter) {
            $quarter = $this->saveWorkflows($quarter, $attributes);
        }

        return $quarter;
    }

    /**
     * @param Quarter $quarter
     * @param $data
     * @return Quarter
     */
    protected function saveWorkflows(Quarter $quarter, $data)
    {
        $workflows = $data['workflows'] ?? [];

        if (empty($workflows)) {
            return $quarter;
        }

        foreach ($workflows as $workflowData) {
            $quarter->workflows()->create($workflowData);
        }

        return $quarter;
    }
}
