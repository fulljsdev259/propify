<?php

namespace App\Transformers;

use App\Helpers\Helper;
use App\Models\Pinboard;
use App\Models\Listing;
use App\Models\Request;
use App\Models\RequestCategory;
use App\Models\Resident;
use App\Models\Unit;
use Carbon\Carbon;
use App\Repositories\AuditRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Arr;

/**
 * Class AuditTransformer.
 *
 * @package namespace App\Transformers;
 */
class AuditTransformer extends BaseTransformer
{
    /**
     * Transform the Audit entity.
     *
     * @param Audit $model
     *
     * @return array
     */
    public function transform(Audit $model)
    {
        $locale = app()->getLocale();
        $fieldMapToLanguage = Helper::mapAuditFieldToLanguage();
        $response = [
            'id' => $model->id,
            'event' => $model->event,
            'auditable_type' => $model->auditable_type,
            'auditable_id' => $model->auditable_id,
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
                'avatar' => '',
            ];
        }        
        if($model->event == 'updated'){            
            $statement = "";
            foreach($model->new_values as $field => $fieldvalue){
                $old_value = (isset($model->old_values[$field])) ? $model->old_values[$field] : "";
                $new_value = (isset($model->new_values[$field])) ? $model->new_values[$field] : "";                
                $fieldname = (isset($fieldMapToLanguage[$model->auditable_type][$field])) ? $fieldMapToLanguage[$model->auditable_type][$field] : $field;
                if($field == 'description'){                
                    $old_value = ($old_value) ? Helper::shortenString($old_value) : "";
                    $new_value = ($new_value) ? Helper::shortenString($new_value) : "";
                }
                elseif(($model->auditable_type == 'request') && (in_array($field, ['status','visibility','resident_id','unit_id','internal_priority','priority','is_public']))){
                    $old_value = ($old_value) ? (AuditRepository::getDataFromField($field, $old_value, $model->auditable_type)) : "";
                    $new_value = ($new_value) ? (AuditRepository::getDataFromField($field, $new_value, $model->auditable_type)) : "";
                }                
                elseif(in_array($field, ['due_date','solved_date'])){                    
                    $old_value = ($old_value) ? Helper::formatedDate($old_value) : "";
                    $new_value = ($new_value) ? Helper::formatedDate($new_value) : "";
                }                              
                elseif(($model->auditable_type == 'request') && ($field == 'category_id')){                    
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
                }                
                $statement .= __("general.components.common.audit.content.with_id.".$model->auditable_type.".updated",['fieldname' => $fieldname, 'old' => $old_value, 'new' => $new_value]);
                $statement .= " ";
            }
            $statement = rtrim($statement, ',');
            $response['statement'] = $statement;
        }
        elseif($model->event == 'created'){                        
            $response['statement'] = __("general.components.common.audit.content.with_id.".$model->auditable_type.".created",['userName' => $response['user']['name'],'auditable_type' => $model->auditable_type]);
        }
        elseif($model->event == 'manager_assigned'){            
            $response['statement'] = __("general.components.common.audit.content.with_id.".$model->auditable_type.".manager_assigned",['propertyManagerFirstName' => $model->new_values['property_manager_first_name'],'propertyManagerLastName' => $model->new_values['property_manager_last_name']]);
        }
        elseif($model->event == 'manager_unassigned'){            
            $response['statement'] = __("general.components.common.audit.content.with_id.".$model->auditable_type.".manager_unassigned",['propertyManagerFirstName' => $model->old_values['property_manager_first_name'],'propertyManagerLastName' => $model->old_values['property_manager_last_name']]);
        }
        elseif($model->event == 'provider_assigned'){            
            $response['statement'] = __("general.components.common.audit.content.with_id.".$model->auditable_type.".provider_assigned",['providerName' => $model->new_values['service_provider_name']]);
        }
        elseif($model->event == 'provider_unassigned'){            
            $response['statement'] = __("general.components.common.audit.content.with_id.".$model->auditable_type.".provider_unassigned",['providerName' => $model->old_values['service_provider_name']]);
        }
        elseif($model->event == 'media_uploaded'){            
            $response['statement'] = __("general.components.common.audit.content.with_id.".$model->auditable_type.".media_uploaded");
        }
        elseif($model->event == 'media_deleted'){            
            $response['statement'] = __("general.components.common.audit.content.with_id.".$model->auditable_type.".media_deleted");
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
            Pinboard::StatusNew . Pinboard::StatusPublished => "published",
            Pinboard::StatusUnpublished . Pinboard::StatusPublished => "published",
            Pinboard::StatusNotApproved . Pinboard::StatusPublished => "published",
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
}
