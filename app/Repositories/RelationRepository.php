<?php

namespace App\Repositories;

use App\Models\AuditableModel;
use App\Models\Model;
use App\Models\Relation;
use App\Models\Resident;
use App\Models\Unit;
use App\Traits\SaveMediaUploads;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

/**
 * Class RelationRepository
 * @package App\Repositories
 */
class RelationRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'relation_format' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Relation::class;
    }

    /**
     * @param array $attributes
     * @return Relation|Model|mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        /** @var Relation $model */
        $data = $attributes;
        $mediaIds = $this->getMediaFileIds($attributes);

        // @TODO correct make many audits or single
        foreach ($attributes['unit_id'] as $unitId) {
            $data['unit_id'] = $unitId;
            $model = parent::create($data);
            if (empty($model))  {
                return $model;
            }
            if ($mediaIds->isNotEmpty()) {
                $model->media()->attach($mediaIds);
            }
        }

        /** @var $pinboardRepository PinboardRepository */
//        $pinboardRepository = App::make(PinboardRepository::class);
//        $pinboardRepository->newResidentRelationPinboard($model);
//        $this->setAsResidentDefaultRelationIfNeed($model);

        return $model;
    }

    /**
     * @param $relation
     * @return mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    protected function setAsResidentDefaultRelationIfNeed($relation)
    {
        if ($relation->status != Relation::StatusActive) {
            return $relation;
        }

        // save default_relation_id if need
        $relation->load('resident:id,default_relation_id');
        $resident = $relation->resident;

        if (! ($resident && is_null($resident->default_relation_id))) {
            return $relation;
        }
        
        Resident::disableAuditing();
        $resident->update(['default_relation_id' => $relation->id]);
        Resident::enableAuditing();
        (new AuditableModel())->newSystemAudit(
            'resident',
            $resident,
            AuditableModel::EventUpdated,
            true,
            [],
            true
        );

        return $relation;
    }

    /**
     * @param $unit
     * @param $residentId
     * @return Relation|Model|mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function newRelationForUnit($unit, $residentId)
    {
        // @TODO delete
        $attr = [
            // @TODO other attributes
            'start_date' => now(),
            'unit_id' => $unit->id,
            'resident_id' => $residentId
        ];
        return  $this->create($attr);
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
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateExisting(Model $model, $attributes)
    {
        $model =  parent::updateExisting($model, $attributes); // TODO: Change the autogenerated stub
        if ($model)  {
            $media = $this->getMediaFileIds($model, $attributes);
        }

        // @TODO if status changed active to inactive how must be organize resident status
        return $model;
    }

    /**
     * @param $data
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function getMediaFileIds($data)
    {
        if (empty($data['media'])) {
            return collect();
        }
        $resident = Resident::find($data['resident_id']);
        $media = Arr::wrap($data['media']);

        $savedMedia = [];
        foreach ($media as $mediaData) {
            if (is_string($mediaData)) {
                $savedMedia[] = $this->uploadFile('media', $mediaData, $resident, null, true);
            } elseif (!is_array($mediaData)) {
                continue;
            }
            if (isset($mediaData['media']) && is_string($mediaData['media'])) {
                $saved =  $this->uploadFile('media', $mediaData['media'], $resident, null, true);
                if ($saved) {
                    $savedMedia[] = $saved;
                }
            }
        }

        return collect($savedMedia)->pluck('id');
    }
}
