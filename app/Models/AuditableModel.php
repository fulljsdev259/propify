<?php

namespace App\Models;

use App\Mails\NotifyServiceProvider;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Concerns\HasMorphedByManyEvents;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
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
    const EventLiked = 'liked';
    const EventUnLiked = 'unliked';
    const EventRelationCreated = 'relation_created';
    const EventRelationUpdated = 'relation_updated';
    const EventRelationDeleted = 'relation_deleted';
    const EventWorkflowCreated = 'workflow_created';
    const EventWorkflowUpdated = 'workflow_updated';
    const EventWorkflowDeleted = 'workflow_deleted';
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
    const EventManageEmailReceptionists = 'manage_email_receptionists';
    const EventAvatarUploaded = 'avatar_uploaded';
    const BuildingUnitsCreated = 'building_units_created';
    const NewResidentPinboardCreated = 'new_resident_pinboard_created';
    const NotificationsSent = 'notifications_sent';
    const DownloadCredentials = 'download_credentials';
    const SendCredentials = 'send_credentials';
    const MassAssignments = 'mass_assignments';

    const SyncAuditConfig = [
        'attach' => [
            'service_providers' =>  AuditableModel::EventProviderAssigned,
            'providers' =>  AuditableModel::EventProviderAssigned,
            'managers' =>  AuditableModel::EventManagerAssigned,
            'propertyManagers' =>  AuditableModel::EventManagerAssigned,
            'property_managers' =>  AuditableModel::EventManagerAssigned,
            'users' =>  AuditableModel::EventUserAssigned,
            'buildings' => AuditableModel::EventBuildingAssigned,
            'quarters' => AuditableModel::EventQuarterAssigned,
        ],
        'detach' => [
            'service_providers' =>  AuditableModel::EventProviderUnassigned,
            'providers' =>  AuditableModel::EventProviderUnassigned,
            'managers' =>  AuditableModel::EventManagerUnassigned,
            'propertyManagers' =>  AuditableModel::EventManagerUnassigned,
            'property_managers' =>  AuditableModel::EventManagerUnassigned,
            'users' =>  AuditableModel::EventUserAssigned,
            'buildings' => AuditableModel::EventBuildingUnassigned,
            'quarters' => AuditableModel::EventQuarterUnassigned,
        ],
    ];

    const Events = [
        self::EventCreated,
        self::EventUpdated,
        self::EventDeleted,
        self::EventLiked,
        self::EventUnLiked,
        self::EventRelationCreated,
        self::EventRelationUpdated,
        self::EventRelationDeleted,
        self::EventWorkflowCreated,
        self::EventWorkflowUpdated,
        self::EventWorkflowDeleted,
        self::EventUserAssigned,
        self::EventUserUnassigned,
        self::EventQuarterAssigned,
        self::EventQuarterUnassigned,
        self::EventBuildingAssigned,
        self::EventBuildingUnassigned,
        self::EventManagerAssigned,
        self::EventManagerUnassigned,
        self::EventProviderAssigned,
        self::EventProviderUnassigned,
        self::EventProviderNotified,
        self::EventMediaUploaded,
        self::EventMediaDeleted,
        self::EventManageEmailReceptionists,
        self::EventAvatarUploaded,
        self::BuildingUnitsCreated,
        self::NewResidentPinboardCreated,
        self::NotificationsSent,
        self::DownloadCredentials,
        self::SendCredentials,
    ];

    /**
     *
     */
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
     * @param $audit
     * @param $existing
     * @param $created
     * @param $deleted
     * @return mixed
     */
    public function addMediaInAudit($audit, Collection $existing, Collection $created, Collection $deleted)
    {
        if (empty($audit)|| ($created->isEmpty() && $deleted->isEmpty())) {
            return $audit;
        }

        $needUpdate = false;
        $oldValues = $audit->old_values;
        $newValues = $audit->new_values;

        if ($existing->isNotEmpty()) {
            $needUpdate = true;
            $oldValues['media'] = $this->getMediaAuditByCollection($existing);
        }

        if ($created->isNotEmpty()) {
            $needUpdate = true;
            $newValues['media']['created'] = $this->getMediaAuditByCollection($created);
        }

        if ($deleted->isNotEmpty()) {
            $needUpdate = true;
            $newValues['media']['deleted'] = $this->getMediaAuditByCollection($deleted);
        }

        if ($needUpdate) {
            $audit->new_values = $newValues;
            $audit->old_values = $oldValues;
            $audit->save();
        }

        return $audit;
    }

    /**
     * @param $value
     * @return Audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function newSystemNotificationAudit($value)
    {
        $audit = $this->makeNewSystemAudit(self::NotificationsSent);
        $notifyServiceProvider = get_morph_type_of(NotifyServiceProvider::class);
        $_value = [];

        foreach ($value as $morph => $data) {
            if (is_a($data, Collection::class)) {
                if ($data->pluck('id')->isEmpty()) {
                    continue;
                }
                $_value[$morph] = [
                    'user_ids' => $data->pluck('id')->all(),
                    'failed_user_ids' => []
                ];
            } elseif (is_a($data, \Illuminate\Database\Eloquent\Model::class)) {
                $_value[$morph] = [
                    'user_ids' => [$data->id],
                    'failed_user_ids' => []
                ];
            } elseif ($notifyServiceProvider == $morph) {
                $userIds = $data['users']->pluck('id')->all();
                $_value[$morph] = [
                    'user_id' => $userIds,
                    'failed_user_id' => [],
                    'to' => $data['to'],
                    'cc' => $data['cc'],
                    'bcc' => $data['bcc'],
                ];
            } else {
                dd('@TODO6', $morph);
            }
        }

        $audit->new_values = $_value;
        $audit->save();
        return $audit;
    }

    /**
     * @param $user
     * @param $event
     * @return Audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function newLikedAudit($user, $event)
    {
        $event = $event == self::EventLiked ? self::EventLiked : self::EventUnLiked;
        $audit = $this->makeNewAudit($event);

        if (self::EventLiked == $event) {
            $audit->new_values = ['user_id' => $user->id];
        } else {
            // when unliked
            $audit->old_values = ['user_id' => $user->id];
        }

        $audit->save();
        return $audit;
    }

    /**
     * @param Collection $items
     * @return Audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function newMassAssignmentAudit(Collection $items)
    {
        $audit = $this->makeNewAudit(self::MassAssignments);
        $audit->new_values = $items->filter(function ($item) {
                return $item->wasRecentlyCreated;
            })->groupBy('assignee_type')->transform(function ($_items) {
                return $_items->pluck('assignee_id')->all();
            })->all();;

        $audit->save();
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
        $audit = new Audit($this->toAudit());
        $audit->event = $event;
        $audit->user_type = self::System;
        $audit->auditable_id = $audit->auditable_id ?? 0;
        $audit->auditable_type = $audit->auditable_id ? $audit->auditable_type  :  'system';

        if (!empty($tags)) {
            $tags = Arr::wrap($tags);
            $audit->tags = json_encode($tags); // @TODO correct later
        }

        $createdEvents = [
            AuditableModel::EventCreated,
            AuditableModel::EventRelationCreated,
            AuditableModel::EventWorkflowCreated,
            AuditableModel::BuildingUnitsCreated,
        ];

        $updatedEvents = [
            AuditableModel::EventUpdated,
            AuditableModel::EventRelationUpdated,
            AuditableModel::EventWorkflowUpdated,
        ];

        if (in_array($event, $createdEvents)) {
            $this->saveCreatedEventMerging($audit, $key, $value, $isSingle);
        } elseif (in_array($event, $updatedEvents)) {
            $this->saveUpdatedEventMerging($audit, $key, $value, $isSingle, $changeOldValues);
        } else {
            dd('@TODO', $event);
        }

        return $audit;
    }


    /**
     * @param $key
     * @param $value
     * @param null $audit
     * @param bool $isSingle
     * @param null $event
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function addDataInAudit($key, $value, $audit = null, $isSingle = true, $event = null)
    {
        $audit = $this->getAudit($audit, $event);
        if (empty($audit)) {
            return;
        }

        $createdEvents = [
            self::EventCreated,
            self::EventRelationCreated,
            self::EventWorkflowCreated,
            self::DownloadCredentials,
            self::SendCredentials,
            self::EventAvatarUploaded
        ];

        $updatedEvents = [
            AuditableModel::EventUpdated,
            AuditableModel::EventRelationUpdated,
            AuditableModel::EventWorkflowUpdated,
        ];

        if (in_array($audit->event, $createdEvents)) {
            $this->saveCreatedEventMerging($audit, $key, $value, $isSingle);
        } elseif (in_array($audit->event, $updatedEvents)) {
            $this->saveUpdatedEventMerging($audit, $key, $value, $isSingle);
        } else {
            dd('@TODO5', $audit->event);
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


        $createdEvents = [
            self::EventCreated,
            self::EventRelationCreated,
            self::EventWorkflowCreated,
            self::DownloadCredentials,
            self::SendCredentials,
            self::EventAvatarUploaded
        ];

        $updatedEvents = [
            AuditableModel::EventUpdated,
            AuditableModel::EventRelationUpdated,
            AuditableModel::EventWorkflowUpdated,
        ];

        if (in_array($audit->event, $updatedEvents)) {
            $newValues =  $audit->new_values;
            $newValues['assignees'][$key] = $value;
            $audit->new_values = $newValues;
            $audit->save();
        } elseif (in_array($audit->event, $updatedEvents)) {
            dd('@TODO', $audit->event);
        } else {
            dd('@TODO', $audit->event);
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
     * @param null $event
     * @return Audit|mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    protected function getAudit($audit, $event = null)
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
        $audit = new Audit($this->toAudit());
        if ($event) {
            $audit->event = $event;
        }

        return $audit;
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

        if (is_string($value)) {
            return $value;
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
            dd('@TODO2', $value);
            $value = [];
        } elseif (is_string($value)) {
            return $value;
        } else {
            dd('@TODO3', $value);
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
                dd('@TODO', $key, $savedValues);
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
     * @param $media
     * @return mixed
     */
    protected function getMediaAuditByCollection(Collection $media)
    {
        return $media->map(function ($_media) {
            return $this->getMediaAudit($_media);
        });
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
            $auditData = array_merge($auditData, $user->role->getOldChanges());
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

    /**
     * @param null $event
     */
    public function makeAuditAsSystem($event = null)
    {
        if ($this->audit) {
            $this->audit->user_type = self::System;
            if ($event) {
                $this->audit->event = $event;
            }
            $this->audit->save();
        }
    }

    /**
     * @param $old
     * @param $new
     * @param null $globalEmailReceptionist
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function auditEmailReceptionists($old, $new, $globalEmailReceptionist = null)
    {
        $this->auditEvent = self::EventUpdated;
        $audit = new Audit($this->toAudit());
        $audit->event = self::EventManageEmailReceptionists;
        $oldValues = $this->fixEmailReceptionists($old);
        $newValues = $this->fixEmailReceptionists($new);

        if (! is_null($globalEmailReceptionist)) {
            $oldValues['global_email_receptionist'] = ! $globalEmailReceptionist;
            $newValues['global_email_receptionist'] = $globalEmailReceptionist;
        }

        $audit->old_values = $oldValues;
        $audit->new_values = $newValues;
        if ($audit->old_values != $audit->new_values) {
            $audit->save();
        }
    }

    /**
     * @param $items
     * @return array
     */
    protected function fixEmailReceptionists($items)
    {
        $data = [];

        foreach ($items->groupBy('category') as $category => $_item) {
            $data['email_receptionists'][] = [
                'category' => $category,
                'property_manager_ids' => $_item->pluck('property_manager_id')->all()
            ];
        }

        return $data;
    }














    /**
     * @param null $event
     * @return Audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function makeNewAudit($event = null)
    {
        $this->setAuditEvent(self::EventUpdated);
        $audit = new Audit($this->toAudit());

        if ($event) {
            $audit->event = $event;
        }

        return $audit;
    }

    /**
     * @param null $event
     * @return Audit
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function makeNewSystemAudit($event = null)
    {
        $audit = $this->makeNewAudit($event);
        $audit->user_type = self::System;
        $audit->auditable_id = $audit->auditable_id ?? 0;
        $audit->auditable_type = $audit->auditable_id ? $audit->auditable_type  :  'system';
        $audit->new_values = [];
        $audit->old_values = [];

        return $audit;
    }

}
