<?php

namespace App\Models;

use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Concerns\HasMorphedByManyEvents;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * App\Models\AuditableModel
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AuditableModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AuditableModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AuditableModel query()
 * @mixin \Eloquent
 */
class AuditableModel extends Model implements Auditable
{
    use \App\Traits\Auditable,
        HasBelongsToManyEvents,
        HasMorphedByManyEvents;

    const UpdateOrCreate = 'updateOrCreate';
    const MergeInMainData = '';
    const System = 'system';

    const EventCreated = 'created';
    const EventUpdated = 'updated';
    const EventDeleted = 'deleted';
    const EventUserAssigned = 'user_assigned';
    const EventUserUnassigned = 'user_unassigned';
    const EventQuarterAssigned = 'quarter_assigned';
    const EventQuarterUnassigned = 'quarter_unassigned';
    const EventBuildingAssigned = 'building_assigned';
    const EventBuildingUnassigned = 'building_unassigned';
    const EventManagerAssigned = 'manager_assigned';
    const EventManagerUnassigned = 'manager_unassigned';
    const EventProviderAssigned = 'provider_assigned';
    const EventProviderUnassigned = 'provider_unassigned';
    const EventProviderNotified = 'provider_notified';
    const EventMediaUploaded = 'media_uploaded';
    const EventMediaDeleted = 'media_deleted';

    const SyncAuditConfig = [
        'attach' => [
            'service_providers' =>  AuditableModel::EventProviderAssigned,
            'providers' =>  AuditableModel::EventProviderAssigned,
            'managers' =>  AuditableModel::EventManagerAssigned,
            'propertyManagers' =>  AuditableModel::EventManagerAssigned,
            'users' =>  AuditableModel::EventUserAssigned,
            'buildings' => AuditableModel::EventBuildingAssigned,
            'quarters' => AuditableModel::EventQuarterAssigned,
        ],
        'detach' => [
            'service_providers' =>  AuditableModel::EventProviderUnassigned,
            'providers' =>  AuditableModel::EventProviderUnassigned,
            'managers' =>  AuditableModel::EventManagerUnassigned,
            'propertyManagers' =>  AuditableModel::EventManagerUnassigned,
            'users' =>  AuditableModel::EventUserAssigned,
            'buildings' => AuditableModel::EventBuildingUnassigned,
            'quarters' => AuditableModel::EventQuarterUnassigned,
        ],
    ];

    protected $syncAuditable = [
        'managers' => ['first_name', 'last_name'],
        'propertyManagers' => ['first_name', 'last_name'],
        'providers' => ['name'],
        'service_providers' => ['name'],
        'users' => ['name'],
        'buildings' => ['name'],
        'quarters' => ['name'],
    ];


    const Events = [
        self::EventCreated,
        self::EventUpdated,
        self::EventDeleted,
        self::EventProviderAssigned,
        self::EventProviderUnassigned,
        self::EventProviderNotified,
        self::EventUserAssigned,
        self::EventUserUnassigned,
        self::EventManagerAssigned,
        self::EventManagerUnassigned,
        self::EventMediaUploaded,
        self::EventMediaDeleted,
    ];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::belongsToManyDetached(function ( $relation, $parent, $ids, $attributes) {
            $auditType = self::SyncAuditConfig['detach'][$relation] ?? '';
            if ( ! empty($auditType)) {
                self::auditManyRelations($relation, $parent, $ids, $auditType);
            }
        });

        static::belongsToManyAttached(function ( $relation, $parent, $ids, $attributes) {
            $auditType = self::SyncAuditConfig['attach'][$relation] ?? '';
            if ( ! empty($auditType)) {
                self::auditManyRelations($relation, $parent, $ids, $auditType);
            }
        });

        static::morphedByManyDetached(function ( $relation, $parent, $ids, $attributes) {
            $auditType = self::SyncAuditConfig['detach'][$relation] ?? '';
            if ( ! empty($auditType)) {
                self::auditManyRelations($relation, $parent, $ids, $auditType);
            }
        });

        static::morphedByManyAttached(function ($relation, $parent, $ids, $attributes) {
            $auditType = self::SyncAuditConfig['attach'][$relation] ?? '';
            if ( ! empty($auditType)) {
                self::auditManyRelations($relation, $parent, $ids, $auditType);
            }
        });
    }

    /**
     * @param $key
     * @param $value
     * @param null $event
     * @param bool $isSingle
     * @param array $tags
     * @param bool $changeOldValues
     * @return Audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function newSystemNotificationAudit($value, $event = null, $isSingle = true, $tags = [], $changeOldValues = false)
    {
        $key = self::MergeInMainData;
        $event = $event ?? AuditableModel::EventCreated;
        $this->auditEvent = self::EventUpdated;
        $audit =  new Audit($this->toAudit());
        $audit->event = $event;
        $audit->user_type = self::System;
        $audit->auditable_id = $audit->auditable_id ?? 0;
        $audit->auditable_type = $audit->auditable_id ? $audit->auditable_type  :  'system';
        $audit->new_values = [];
        $audit->old_values = [];

        if (!empty($tags)) {
            $tags = Arr::wrap($tags);
            $audit->tags = json_encode($tags); // @TODO correct later
        }
        $_value = [];
        foreach ($value as $morph => $data) {
            if ($data->pluck('resident.id')->isEmpty()) {
                continue;
            }
            $_value[$morph] = [
                'resident_ids' => $data->pluck('resident.id')->all(),
                'failed_resident_ids' => []
            ];
        }

        $value = $_value;

        if (AuditableModel::EventCreated == $event) {
            $this->saveCreatedEventMerging($audit, $key, $value, $isSingle);
        } elseif (AuditableModel::EventUpdated == $event) {
            dd('@TODO');
            $this->saveUpdatedEventMerging($audit, $key, $value, $isSingle, $changeOldValues);
        } else {
            dd('@TODO');
        }

        return $audit;
    }

    /**
     * @param $key
     * @param $value
     * @param null $event
     * @param bool $isSingle
     * @param array $tags
     * @param bool $changeOldValues
     * @return Audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function newSystemAudit($key, $value, $event = null, $isSingle = true, $tags = [], $changeOldValues = false)
    {
        $event = $event ?? AuditableModel::EventCreated;
        $this->auditEvent = self::EventUpdated;
        $audit =  new Audit($this->toAudit());
        $audit->event = $event;
        $audit->user_type = self::System;
        $audit->auditable_id = $audit->auditable_id ?? 0;
        $audit->auditable_type = $audit->auditable_id ? $audit->auditable_type  :  'system';

        if (!empty($tags)) {
            $tags = Arr::wrap($tags);
            $audit->tags = json_encode($tags); // @TODO correct later
        }

        if (AuditableModel::EventCreated == $event) {
            $this->saveCreatedEventMerging($audit, $key, $value, $isSingle);
        } elseif (AuditableModel::EventUpdated == $event) {
            $this->saveUpdatedEventMerging($audit, $key, $value, $isSingle, $changeOldValues);
        } else {
            dd('@TODO');
        }

        return $audit;
    }


    /**
     * @param $key
     * @param $value
     * @param null $audit
     * @param bool $isSingle
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function addDataInAudit($key, $value, $audit = null, $isSingle = true)
    {
        $audit = $this->getAudit($audit);
        if (empty($audit)) {
            return;
        }

        if ('media' == $key) {
            $value = $this->getMediaAudit($value);
        }

        if (self::EventCreated == $audit->event) {
            $this->saveCreatedEventMerging($audit, $key, $value, $isSingle);
        } elseif (self::EventUpdated == $audit->event) {
            $this->saveUpdatedEventMerging($audit, $key, $value, $isSingle);
        } else {
            // @TODO
        }
    }

    /**
     * @param $key
     * @param $value
     * @param null $audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function addAssigneesDataInAudit($key, $value, $audit = null)
    {
        $audit = $this->getAudit($audit);
        if (empty($audit)) {
            return;
        }

        if (self::EventCreated == $audit->event) {
            $newValues =  $audit->new_values;
            $newValues['assignees'][$key] = $value;
            $audit->new_values = $newValues;
            $audit->save();
        } elseif (self::EventUpdated == $audit->event) {
            dd('@TODO');
        } else {
            dd('@TODO');
        }
    }


    /**
     * @param $audit
     * @param $key
     * @param $value
     * @param bool $isSingle
     */
    protected function saveCreatedEventMerging($audit, $key, $value, $isSingle = true)
    {
        $value = $this->correctCreatedAuditValue($value);
        $audit->new_values = $this->fixAddedData($audit->new_values, $key, $value, $isSingle);
        $audit->save();
    }


    /**
     * @param $audit
     * @param $key
     * @param $value
     * @param bool $isSingle
     * @param bool $changeOldValues
     */
    protected function saveUpdatedEventMerging($audit, $key, $value, $isSingle = true, $changeOldValues = true)
    {
        $newAuditValue = $this->getChangedAuditValue($value);
        $audit->new_values = $this->fixAddedData($audit->new_values, $key, $newAuditValue, $isSingle);

        if ($changeOldValues) {
            $oldAuditValue = $this->getChangedOriginalAuditValue($value);
            $audit->old_values = $this->fixAddedData($audit->old_values, $key, $oldAuditValue, $isSingle);
        }

        $audit->save();
    }

    /**
     * @param $audit
     * @return mixed|Audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    protected function getAudit($audit)
    {
        if (is_null($audit)) {
            $audit = $this->audit;
        }

        if (empty($audit)) {
            return $audit;
        }

        if ($audit != self::UpdateOrCreate) {
            return $audit;
        }

        if ($this->audit) {
            return $this->audit;
        }

        $this->auditEvent = self::EventUpdated;
        return new Audit($this->toAudit());
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function getChangedAuditValue($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_a($value, EloquentModel::class)) {
            if ($value->wasRecentlyCreated) {
                $value = $this->getSingleRelationAuditData($value);
            } else {
                if (is_a($value, User::class)) {
                    $value = $this->getUpdatedUserAudit($value);
                } else {
                    $value = $value->getChanges();
                }
            }
            unset($value['updated_at']);
            return $value;
        }

        if (is_a($value, Collection::class)) {
            return $this->getManyRelationAuditData($value);
        }

        dd('@TODO1');
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function getChangedOriginalAuditValue($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_a($value, EloquentModel::class)) {
            if (is_a($value, User::class)) {
                $value = $this->getUpdatedUserOriginalData($value);
            } else {
                $value = $value->getOldChanges();
            }
            unset($value['updated_at']);
            return $value;
        }

        if (is_a($value, Collection::class)) {
            dd('@TODO');
            $value = [];
        } else {
            dd('@TODO');
        }

        return $value;
    }

    /**
     * @param $value
     * @return array|mixed
     */
    protected function correctCreatedAuditValue($value)
    {
        if (is_a($value, EloquentModel::class)) {
            $value = $this->getSingleRelationAuditData($value);
        } elseif (is_a($value, Collection::class)) {
            $value = $this->getManyRelationAuditData($value);
        }

        return $value;
    }

    /**
     * @param $savedValues
     * @param $key
     * @param $newValue
     * @param $isSingle
     * @return mixed
     */
    protected function fixAddedData($savedValues, $key, $newValue, $isSingle)
    {
        // @TODO improve code readability
        if ($isSingle) {
            if (self::MergeInMainData == $key) {
                if (is_array($newValue)) {
                    $savedValues = array_merge($newValue, $savedValues);
                } else {
                    $savedValues[] = $newValue;
                }
            } else {
                $savedValues[$key] = $newValue;
            }
        } else  {
            if (self::MergeInMainData == $key) {
                dd('@TODO');
            } else {
                $savedValues[$key][] = $newValue;
            }
        }

        return $savedValues;
    }

    /**
     * @param $media
     * @return mixed
     */
    protected function getMediaAudit($media)
    {
        $data = $media->only('name', 'file_name', 'disk', 'collection_name', 'mime_type', 'size', 'order_column');
        $data['media_id'] = $media->id;
        $data['media_url'] = $media->getFullUrl();
        return $data;
    }

    /**
     * @param $user
     * @return mixed
     */
    protected function getUserAudit(User $user)
    {

        $auditData = $user->getAttributes();
        unset($auditData['created_at']);
        unset($auditData['updated_at']);
        unset($auditData['password']);

        if ($user->relationExists('settings')) {
            $auditData = array_merge($auditData, $this->getSingleRelationAuditData($user->settings));
            unset($auditData['user_id']);
        }

        if ($user->relationExists('role')) {
            $auditData['role'] = $user->role->name;
        }

        return $auditData;
    }

    /**
     * @param $user
     * @return mixed
     */
    protected function getUpdatedUserAudit(User $user)
    {
        $auditData = $user->getChanges();

        if ($user->relationExists('settings')) {
            $auditData = array_merge($auditData, $user->settings->getChanges());
            unset($auditData['user_id']);
        }

        if ($user->relationExists('role')) {
            $auditData['role'] = $user->role->name;
        }

        return $auditData;
    }

    /**
     * @param $user
     * @return mixed
     */
    protected function getUpdatedUserOriginalData(User $user)
    {
        $auditData = $user->getOldChanges();

        if ($user->relationExists('role')) {
            $auditData['role'] = $user->role->name;
        }

        if ($user->relationExists('settings')) {
            $auditData = array_merge($auditData, $user->settings->getOldChanges());
            unset($auditData['user_id']);
        }

        return $auditData;
    }

    /**
     * @param $relationData
     * @return array|mixed
     */
    public function getModelRelationAuditData($relationData)
    {
        if (is_a($relationData, Collection::class)) {
            return $this->getManyRelationAuditData($relationData);
        } elseif(is_a($relationData, EloquentModel::class)) {
            return $this->getSingleRelationAuditData($relationData);
        }
        return [];//'@TODO'
    }

    /**
     * @param $relationData
     * @return array
     */
    protected function getManyRelationAuditData($relationData)
    {
        $auditData = [];
        foreach ($relationData as $relation) {
            $auditData[] = $this->getSingleRelationAuditData($relation);
        }
        return $auditData;
    }

    /**
     * @param $relation
     * @return mixed
     */
    protected function getSingleRelationAuditData($relation)
    {
        if (is_a($relation, Media::class)) {
            return $this->getMediaAudit($relation);
        }

        if (is_a($relation, User::class)) {
            return $this->getUserAudit($relation);
        }

        // @TODO use auditable attributes or similar thing
        $auditData = $relation->getAttributes();
        unset($auditData['created_at']);
        unset($auditData['updated_at']);
        foreach ($relation->getExistingRelations() as $_relation) {
            $auditData[$_relation] = $this->getModelRelationAuditData($relation->{$_relation});
        }
        return $auditData;
    }

    /**
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        if (method_exists($this, 'getFormatColumnName')) {
            $data['auditable_format'] = $this->{$this->getFormatColumnName()};
        };

        return $data;
    }
}
