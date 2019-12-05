<?php

namespace App\Transformers;

use App\Helpers\Helper;
use App\Models\AuditableModel;
use App\Models\Building;
use App\Models\Pinboard;
use App\Models\PropertyManager;
use App\Models\Quarter;
use App\Models\Resident;
use App\Models\Listing;
use App\Models\Request;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Repositories\AuditRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Arr;
use Lang;

/**
 * Class AuditTransformer.
 *
 * @package namespace App\Transformers;
 */
class AuditTransformer extends BaseTransformer
{
    protected $currentModel;

    /**
     * Transform the Audit entity.
     *
     * @param Audit $model
     *
     * @return array
     */
    public function transform(Audit $model)
    {
        $this->currentModel = $model;
        $locale = app()->getLocale();
        $fieldMapToLanguage = Helper::mapAuditFieldToLanguage();
        $response = [
            'id' => $model->id,
            'event' => $model->event,
            'user_type' => $model->user_type,
            'auditable_type' => $model->auditable_type,
            'auditable_id' => $model->auditable_id,
            'auditable_format' => $model->auditable_format,
            'user_id' => $model->user_id,
            'url' => $model->url,
            'message' => $this->getMessage($model),
            'old_values' => $model->old_values,
            'new_values' => $model->new_values,
            'ip_address' => $model->ip_address,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'statement' => ''
        ];

        if ($model->user) {
            $response['user'] = (new UserTransformer())->transform($model->user);
        } else {
            $response['user'] = [
                'id' => $model->user_id,
                'name' => 'Deleted user',
                'email' => 'dummy@email.com',
                'phone' => '',
                'avatar' => null,
            ];
        }        
        if($model->event == 'updated'){            
            $statement = "";
            foreach($model->new_values as $field => $fieldvalue){
                if(in_array($field,['internal_priority', 'priority','notifications','avatar'])){
                    continue;
                }
                $old_value = (isset($model->old_values[$field])) ? $model->old_values[$field] : "";
                $new_value = (isset($model->new_values[$field])) ? $model->new_values[$field] : "";
                if(is_array($old_value) || is_array($new_value)){
                    continue;
                }
                $language_map = $model->auditable_type;
                if($model->auditable_type == 'manager'){
                    $language_map = 'property_manager';
                }elseif($model->auditable_type == 'provider'){
                    $language_map = 'service';
                }                
                if(isset($fieldMapToLanguage[$model->auditable_type][$field])){
                    $fieldname = $fieldMapToLanguage[$model->auditable_type][$field];
                } else {                    
                    if(Lang::has('models.'.$language_map.'.'.$field) && gettype(Lang::get('models.'.$language_map.'.'.$field)) == 'string'){
                        $fieldname = Lang::get('models.'.$language_map.'.'.$field);
                    }
                    else{
                        $fieldname = $field;
                    }                    
                }
                // $fieldname = (isset($fieldMapToLanguage[$model->auditable_type][$field])) ? $fieldMapToLanguage[$model->auditable_type][$field] : __('models.'.$language_map.'.'.$field);
                if(in_array($field, ['content','description'])){
                    $old_value = ($old_value) ? Helper::shortenString($old_value) : "";
                    $new_value = ($new_value) ? Helper::shortenString($new_value) : "";
                }
                elseif(in_array($model->auditable_type,['quarter','building']) && $field == 'types'){
                    $old_value = ($old_value) ? (AuditRepository::getDataFromField($field, $old_value, $model->auditable_type)) : "";
                    $new_value = ($new_value) ? (AuditRepository::getDataFromField($field, $new_value, $model->auditable_type)) : "";
                }
                elseif(in_array($model->auditable_type,['manager','resident']) && $field == 'title'){
                    $old_value = ($old_value) ? __('general.salutation_option.'.$old_value) : "";
                    $new_value = ($new_value) ? __('general.salutation_option.'.$new_value) : "";
                }
                elseif(in_array($model->auditable_type,['manager','resident','provider']) && $field == 'language'){
                    $old_value = ($old_value) ? __('general.languages.'.$old_value) : "";
                    $new_value = ($new_value) ? __('general.languages.'.$new_value) : "";
                }
                elseif(in_array($field, ['location','type','status','visibility','building_id','resident_id', 'quarter_id','unit_id','address_id','internal_priority','priority','is_public','category','nation','state_id','category_id','sub_category_id','sub_type','capture_phase','execution_period','reminder_user_ids'])){
                    $old_value = ($old_value) ? (AuditRepository::getDataFromField($field, $old_value, $model->auditable_type)) : "";
                    $new_value = ($new_value) ? (AuditRepository::getDataFromField($field, $new_value, $model->auditable_type)) : "";
                }                
                elseif(in_array($field, ['attic','announcement','is_execution_time','iframe_enable','blank_pdf','active_reminder','notify_email'])){
                    $old_value = ($old_value == 1) ? __('general.enabled') : __('general.disabled');
                    $new_value = ($new_value == 1) ? __('general.enabled') : __('general.disabled');                                    
                }
                elseif(in_array($field, ['due_date','solved_date','published_at','birth_date','execution_start','execution_end','last_login_at'])){
                    $old_value = ($old_value) ? Helper::formatedDate($old_value) : "";
                    $new_value = ($new_value) ? Helper::formatedDate($new_value) : "";
                }                
                /*elseif(($model->auditable_type == 'request') && ($field == 'category_id')){                    
                    $old_category = RequestCategory::find($old_value);
                    $new_category = RequestCategory::find($new_value);                    
                    if($locale == 'de'){
                        $old_value = $old_category->name_de;
                        $new_value = $new_category->name_de;                        
                    } elseif($locale == 'fr'){
                        $old_value = $old_category->name_fr;
                        $new_value = $new_category->name_fr;
                    } elseif($locale == 'it'){
                        $old_value = $old_category->name_it;
                        $new_value = $new_category->name_it;
                    } else {
                        $old_value = $old_category->name;
                        $new_value = $new_category->name;
                    }
                }*/    
                if(in_array($field, ['logo','circle_logo','resident_logo','favicon_icon','mail_password','password'])){
                    $statement .= $this->translate_audit("update_no_fieldvalue",['fieldname' => $fieldname]);
                }
                else{
                    $old_value = (empty($old_value)) ? __('general.empty') : $old_value;
                    $statement .= $this->translate_audit("updated",['fieldname' => $fieldname, 'old' => $old_value, 'new' => $new_value]);
                }                
                $statement .= " ";
            }
            $statement = rtrim($statement, ',');
            $response['statement'] = $statement;
        }
        elseif($model->event == 'created'){                        
            $response['statement'] = $this->translate_audit("created",['userName' => $response['user']['name'],'auditable_type' => $model->auditable_type]);
        }
        elseif($model->event == 'deleted'){
            $response['statement'] = $this->translate_audit("deleted",['userName' => $response['user']['name'],'auditable_type' => $model->auditable_type]);
        }
        elseif($model->event == 'manager_assigned'){
            $ids = $model->new_values['ids'] ?? [];
            $assigneeNames = $this->getAssigneesName(PropertyManager::class, $ids);
            $response['statement'] = $this->translate_audit("manager_assigned",['assignee' => $assigneeNames]);
        }
        elseif($model->event == 'manager_unassigned'){
            $ids = $model->old_values['ids'] ?? [];
            $assigneeNames = $this->getAssigneesName(PropertyManager::class, $ids);
            $response['statement'] = $this->translate_audit("manager_unassigned", ['assignee' => $assigneeNames]);
        }
        elseif($model->event == 'provider_assigned'){
            $ids = $model->old_values['ids'] ?? [];
            $assigneeNames = $this->getAssigneesName(ServiceProvider::class, $ids);
            $response['statement'] = $this->translate_audit("provider_assigned",['assignee' => $assigneeNames]);
        }
        elseif($model->event == 'provider_unassigned'){
            $ids = $model->old_values['ids'] ?? [];
            $assigneeNames = $this->getAssigneesName(ServiceProvider::class, $ids);
            $response['statement'] = $this->translate_audit("provider_unassigned",['assignee' => $assigneeNames]);
        }
        elseif($model->event == 'media_uploaded'){
            $data = [];
            if ($model->auditable_type == 'building') {
                $disk = $model->new_values['disk'] ?? '';
                $disk = str_replace('buildings_', '', $disk);
//                MediaFileCategories
                $data['category'] = __('models.building.media_category.' . $disk);
            }
            $response['statement'] = $this->translate_audit("media_uploaded", $data);
        }
        elseif($model->event == 'media_deleted'){
            $response['statement'] = $this->translate_audit("media_deleted");
        }
        elseif($model->event == 'avatar_uploaded'){            
            $response['statement'] = $this->translate_audit("avatar_uploaded",['auditable_type' => $model->auditable_type]);
        }
        elseif($model->event == 'provider_notified'){
            $providerName = $model->new_values['service_provider']['company_name'] ?? $model->new_values['service_provider']['name'];
            $response['statement'] = $this->translate_audit("provider_notified",['providerName' => $providerName]);
        }  
        elseif($model->event == 'quarter_assigned'){
            $ids = $model->new_values['ids'] ?? [];
            $quarterNames = $this->getAssigneesName(Quarter::class, $ids, 'name', ['name']);
            $response['statement'] = $this->translate_audit("quarter_assigned",['quarterName' => $quarterNames]);
        }
        elseif($model->event == 'quarter_unassigned'){
            $ids = $model->old_values['ids'] ?? [];
            $quarterNames = $this->getAssigneesName(Quarter::class, $ids, 'name', ['name']);
            $response['statement'] = $this->translate_audit("quarter_unassigned",['quarterName' => $quarterNames]);
        }
        elseif($model->event == 'building_assigned'){
            $ids = $model->new_values['ids'] ?? [];
            $buildingNames = $this->getAssigneesName(Building::class, $ids, 'internal_building_id', ['internal_building_id']);
            $response['statement'] = $this->translate_audit("building_assigned",['buildingName' => $buildingNames]);
        }
        elseif($model->event == 'building_unassigned'){
            $ids = $model->old_values['ids'] ?? [];
            $buildingNames = $this->getAssigneesName(Building::class, $ids, 'internal_building_id', ['internal_building_id']);
            $response['statement'] = $this->translate_audit("building_unassigned",['buildingName' => $buildingNames]);
        }
        elseif($model->event == 'notifications_sent'){
            $response['statement'] = $this->translate_audit("notifications_sent",['auditable_type' => $model->auditable_type]);
        }
        elseif($model->event == 'liked'){
            $response['statement'] = $this->translate_audit("liked", ['userName' => $response['user']['name']]);
        }
        elseif($model->event == 'unliked'){
            $response['statement'] = $this->translate_audit("unliked", ['userName' => $response['user']['name']]);
        }
        elseif($model->event == 'new_resident_pinboard_created'){
            $pinboard = Pinboard::find($model->auditable_id);
            if($pinboard){
                $user = User::find($pinboard->user_id);
                if($user){
                    $response['statement'] = $this->translate_audit("new_resident_pinboard_created",['userName' => $user->name]);
                }                
            }            
        } elseif(AuditableModel::EventWorkflowCreated == $model->event){
            $response['statement'] = $this->translate_audit("workflow_created");
        }
        elseif($model->event == 'relation_created'){
            $resident = Resident::find($model->auditable_id);
            if($resident){                                
                $response['statement'] = $this->translate_audit("relation_created", ['userName' => $resident->getNameAttribute()]);
            }
        } elseif(AuditableModel::MassAssignments == $model->event) {
            $users = [];
            foreach ($model->new_values as $type => $ids) {
                if($type == get_morph_type_of(PropertyManager::class)) {
                    $users[] = $this->getAssigneesName(PropertyManager::class, $ids);
                } elseif ($type == get_morph_type_of(ServiceProvider::class)) {
                    $users[] = $this->getAssigneesName(ServiceProvider::class, $ids);
                } else {
                    // @TODO if need(now not need)
                }
            }
            $response['statement'] = $this->translate_audit("mass_assigned", ['users' => implode(', ', $users)]);
        } else {
            $response['statement'] = 'TODO';
        }

        return $response;
    }

    /**
     * @param Audit $a
     * @return mixed|string
     */
    private function getMessage(Audit $a)
    {
        if ($this->getMorphedModel($a->auditable_type) == Pinboard::class) {
            return $this->getPinboardMessage($a);
        }

        if ($this->getMorphedModel($a->auditable_type) == Listing::class) {
            return $this->getListingMessage($a);
        }

        if ($this->getMorphedModel($a->auditable_type) == Request::class) {
            return $this->getRequestMessage($a);
        }

        return "unkown";
    }

    /**
     * @param Audit $a
     * @return mixed
     */
    private function getPinboardMessage(Audit $a)
    {
        if ($a->event == 'created' || $a->event == 'deleted') {
            return $a->event;
        }

        $sMsgs = [
            Pinboard::StatusUnpublished . Pinboard::StatusPublished => "published",
            Pinboard::StatusPublished . Pinboard::StatusUnpublished => "unpublished",
        ];
        if (Arr::has($a->new_values, 'status') &&
            Arr::has($a->old_values, 'status')) {
            return $sMsgs[$a->old_values['status'] . $a->new_values['status']] ?? $a->event;
        }

        return $a->event;
    }

    /**
     * @param Audit $a
     * @return mixed
     */
    private function getListingMessage(Audit $a)
    {
        return $a->event;
    }

    /**
     * @param Audit $a
     * @return mixed
     */
    private function getRequestMessage(Audit $a)
    {
        if ($a->event == 'created' || $a->event == 'deleted') {
            return $a->event;
        }

        if (Arr::has($a->new_values, 'status')) {
            return Request::Status[$a->new_values['status']];
        }

        return $a->event;
    }

    /**
     * @param $alias
     * @return string|null
     */
    private function getMorphedModel($alias)
    {
        return Relation::getMorphedModel($alias) ?? $alias;
    }

    protected function getAssigneesName($class, $ids, $pluck = 'name', $columns = ['first_name', 'last_name'])
    {
        // @TODO here used many queries need optimize
        $items = $class::whereIn('id', $ids)->get($columns);
        return $items->pluck($pluck)->implode(', ');
    }

    protected function translate_audit($key, $replace = [], $locale = null)
    {
        $key = 'general.components.common.audit.content.general.' . $key;
        $value =  __($key, $replace, $locale);

        if (is_array($value)) {
            return $value[$this->currentModel->auditable_type] ?? $value['default'] ?? '';
        }

        return $value;
    }
}
