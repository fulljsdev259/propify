<?php

namespace App\Repositories;

use App\Models\Media;
use App\Models\Model;
use App\Models\RentContract;
use App\Models\Request;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Prettus\Repository\Events\RepositoryEntityDeleted;

/**
 * Class TenantRepository
 * @package App\Repositories
 * @version January 28, 2019, 8:27 pm UTC
 *
 * @method Tenant findWithoutFail($id, $columns = ['*'])
 * @method Tenant find($id, $columns = ['*'])
 * @method Tenant first($columns = ['*'])
 */
class TenantRepository extends BaseRepository
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
    ];

    /**
     * @var array
     */
    protected $mimeToExtension = [
        "application/pdf" => "pdf",
        "image/png" => "png",
        "image/jpeg" => "jpg",
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tenant::class;
    }

    /**
     * @param array $attributes
     * @return Tenant|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        if (isset($attributes['unit_id'])) {
            $unit = Unit::with('building')->find($attributes['unit_id']);
            if ($unit) {
                $attributes['building_id'] = $unit->building_id;
                $attributes['unit_id'] = $unit->id;
                $attributes['address_id'] = $unit->building->address_id;
            }
            unset($attributes['unit']);
        }

        if (isset($attributes['address'])) {
            unset($attributes['address']);
        }

        if (isset($attributes['building'])) {
            unset($attributes['building']);
        }

        if (isset($attributes['user'])) {
            unset($attributes['user']);
        }

        if (!isset($attributes['title']) || $attributes['title'] != 'company') {
            unset($attributes['company']);
        }

        $attributes['status'] = Tenant::StatusActive;
        $model = parent::create($attributes);
        if ($model) {
            $model = $this->saveRentContracts($model, $attributes);
        }
        return $model;
    }

    /**
     * @param Tenant $tenant
     * @param $data
     * @return Tenant
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    protected function saveRentContracts(Tenant $tenant, $data)
    {
        $rentContracts =  $tenant->rent_contracts; // TODO check created or updated
        if (empty($data['rent_contracts']) || ! is_array($data['rent_contracts']) || Arr::isAssoc($data['rent_contracts'])) {
            $rentContracts->each(function ($renContract) {
                $renContract->delete();
            });
            return $tenant;
        }

        /**
         * @var $rentContractRepo RentContractRepository
         */
        $rentContractRepo = App::make(RentContractRepository::class);
        $rentContractSavedData = collect();

        RentContract::disableAuditing();
        Media::disableAuditing();

        // @TODO make good auditing
        $deleteRentContracts = $rentContracts->whereNotIn('id', collect($data['rent_contracts'])->pluck('id'));
        $deleteRentContracts->each(function ($rentContract) {
            $rentContract->delete();
        });

        foreach ($data['rent_contracts'] as $rentContractData) {
            // @TODO if need validate this data
            if (!is_array($rentContractData)) {
                continue;
            }
            if (!isset($rentContractData['id'])) {
                $rentContractData['tenant_id'] = $tenant->id;
                $rentContractSavedData->push($rentContractRepo->create($rentContractData));
                continue;
            }
            $existingRentContract = $rentContracts->where('id', $rentContractData['id'])->first();
            if (empty($existingRentContract)) {
                continue;
            }
            $rentContractRepo->updateExisting($existingRentContract, $rentContractData);
        }
        RentContract::enableAuditing();
        Media::enableAuditing();

        $tenant->setRelation('rent_contracts', $rentContractSavedData);
        $auditData = $tenant->getModelRelationAuditData($rentContractSavedData);
        $tenant->addDataInAudit('rent_contracts', $auditData);
        return $tenant;
    }

    /**
     * @param int $building_id
     * @return mixed
     */
    public function getTotalTenantsFromBuilding(int $building_id)
    {
        return $this->model->where('building_id', $building_id)->count();
    }

    /**
     * @param int $tenant_id
     * @param Unit $unit
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function moveTenantInUnit(int $tenant_id, Unit $unit)
    {
        // Move old tenants outs of this unit
        $this->model->where('unit_id', $unit->id)->update(['unit_id' => null]);

        // Move in the new tenant
        $attrs = [
            'unit_id' => $unit->id,
            'building_id' => $unit->building_id,
            'address_id' => $unit->building->address_id,
        ];
        return $this->update($attrs, $tenant_id);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
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
        if (isset($attributes['unit_id'])) {
            $unit = Unit::with('building')->find($attributes['unit_id']);
            if ($unit) {
                $attributes['building_id'] = $unit->building_id;
                $attributes['unit_id'] = $unit->id;
                $attributes['address_id'] = $unit->building->address_id;
            }
            unset($attributes['unit']);
        }

        if (isset($attributes['address'])) {
            unset($attributes['address']);
        }

        if (isset($attributes['building'])) {
            unset($attributes['building']);
        }

        if (isset($attributes['user'])) {
            unset($attributes['user']);
        }

        if (!isset($attributes['title']) || $attributes['title'] != 'company') {
            unset($attributes['company']);
        }

        if (! empty($attributes['status']) && $attributes['status'] != $model->status && $attributes['status'] == Tenant::StatusNotActive) {
            $model->rent_contracts()
                ->where('status', RentContract::StatusActive)
                ->update(['status' =>  RentContract::StatusInactive]);
        }

        $model =  parent::updateExisting($model, $attributes); // TODO: Change the autogenerated stub
//        if ($model) {
//            $model = $this->saveRentContracts($model, $attributes);
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
            throw new \Exception('Tenant has requests in progress');
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
