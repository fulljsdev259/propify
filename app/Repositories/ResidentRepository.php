<?php

namespace App\Repositories;

use App\Models\Media;
use App\Models\Model;
use App\Models\Contract;
use App\Models\Request;
use App\Models\Resident;
use App\Models\Unit;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Prettus\Repository\Events\RepositoryEntityDeleted;

/**
 * Class ResidentRepository
 * @package App\Repositories
 * @version January 28, 2019, 8:27 pm UTC
 *
 * @method Resident findWithoutFail($id, $columns = ['*'])
 * @method Resident find($id, $columns = ['*'])
 * @method Resident first($columns = ['*'])
 */
class ResidentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'first_name' => 'like',
        'last_name' => 'like',
        'birth_date' => 'like',
        'mobile_phone' => 'like',
        'private_phone' => 'like',
        'work_phone' => 'like',
        'user.email' => 'like',
        'resident_format' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Resident::class;
    }

    /**
     * @param array $attributes
     * @return Resident|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        if (!isset($attributes['title']) || $attributes['title'] != 'company') {
            unset($attributes['company']);
        }

        if (! isset($attributes['status'])) {
            $attributes['status'] = $this->getStatusBasedContracts($attributes);
        }

        $model = parent::create($attributes);
        if ($model) {
            $model = $this->saveContracts($model, $attributes);
        }
        return $model;
    }

    /**
     * @param $attributes
     * @return int
     */
    protected function getStatusBasedContracts($attributes)
    {
        if (empty($attributes['contracts'])) {
            return Resident::StatusInActive;
        }
        return collect($attributes['contracts'])->contains('status', Contract::StatusActive)
            ? Resident::StatusActive
            : Resident::StatusInActive;
    }

    /**
     * @param Resident $resident
     * @param $data
     * @return Resident
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    protected function saveContracts(Resident $resident, $data)
    {
        $contracts =  $resident->contracts; // TODO check created or updated
        if (empty($data['contracts']) || ! is_array($data['contracts']) || Arr::isAssoc($data['contracts'])) {
            $contracts->each(function ($renContract) {
                $renContract->delete();
            });
            return $resident;
        }

        /**
         * @var $contractRepo ContractRepository
         */
        $contractRepo = App::make(ContractRepository::class);
        $contractSavedData = collect();

        Contract::disableAuditing();
        Media::disableAuditing();

        // @TODO make good auditing
        $deleteContracts = $contracts->whereNotIn('id', collect($data['contracts'])->pluck('id'));
        $deleteContracts->each(function ($contract) {
            $contract->delete();
        });

        foreach ($data['contracts'] as $contractData) {
            // @TODO if need validate this data
            if (!is_array($contractData)) {
                continue;
            }
            if (!isset($contractData['id'])) {
                $contractData['resident_id'] = $resident->id;
                $contractSavedData->push($contractRepo->create($contractData));
                continue;
            }
            $existingContract = $contracts->where('id', $contractData['id'])->first();
            if (empty($existingContract)) {
                continue;
            }
            $contractRepo->updateExisting($existingContract, $contractData);
        }
        Contract::enableAuditing();
        Media::enableAuditing();

        $resident->setRelation('contracts', $contractSavedData);
        $auditData = $resident->getModelRelationAuditData($contractSavedData);
        $resident->addDataInAudit('contracts', $auditData);
        return $resident;
    }


    /**
     * @param array $attributes
     * @param $id
     * @return Model|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $attributes, $id)
    {
        $model = $this->model->findOrFail($id);
        return $this->updateExisting($model, $attributes);
    }

    /**
     * @param Model $model
     * @param $attributes
     * @return Model|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateExisting(Model $model, $attributes)
    {
        if (!isset($attributes['title']) || $attributes['title'] != 'company') {
            unset($attributes['company']);
        }

        if (! empty($attributes['status']) && $attributes['status'] != $model->status && $attributes['status'] == Resident::StatusInActive) {
            $model->contracts()
                ->where('status', Contract::StatusActive)
                ->update(['status' =>  Contract::StatusInActive]);
        }

        $model =  parent::updateExisting($model, $attributes); // TODO: Change the autogenerated stub
//        if ($model) {
//            $model = $this->saveContracts($model, $attributes);
//        }
        return $model;
    }

    /**
     * @param $attributes
     * @param $currentStatus
     * @return bool
     */
    public function checkStatusPermission($attributes, $currentStatus)
    {
        if (!$attributes['status'] || $currentStatus == $attributes['status']) {
            return true;
        }

        return true;
    }

    /**
     * @param $id
     * @return bool|int|null
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Exception
     */
    public function delete($id)
    {
        $this->applyScope();
        $model = $this->find($id);
        $originalModel = clone $model;
        $this->resetModel();

        $requestsInProgress = $model->requests()
            ->whereIn('status', [
                Request::StatusInProcessing,
                Request::StatusAssigned,
                Request::StatusReactivated,
            ])->get();

        if (count($requestsInProgress)) {
            throw new \Exception('Resident has requests in progress');
        }

        $deleted = $model->forceDelete();

        event(new RepositoryEntityDeleted($this, $originalModel));

        return $deleted;
    }

    /**
     * @param $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Model[]|null|void
     */
    public function findForCredentials($id, $columns = ['*'])
    {
         try {
             if (["*"] != $columns) {
                 $columns[] = 'user_id';
                 $columns = array_unique($columns);
             }

             return $this->model->with(['user' => function($q) {
                 $q->with('settings:user_id,language');
             }])->find($id, $columns);
         } catch (\Exception $e) {
             return;
         }
    }
}
