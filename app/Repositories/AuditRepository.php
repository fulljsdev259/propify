<?php

namespace App\Repositories;

use App\Models\Resident;
use App\Models\Unit;
use App\Models\Request;
use App\Models\Quarter;
use App\Models\Building;
use App\Models\Pinboard;
use App\Models\PropertyManager;
use App\Models\ServiceProvider;
use App\Models\Country;
use App\Models\Address;
use App\Models\State;
use App\Models\Audit;
use App\Models\User;
use App\Models\TemplateCategory;

/**
 * Class AuditRepository
 * @package App\Repositories
 * @version March 08, 2019, 9:44 pm UTC
 *
 * @method Audit findWithoutFail($id, $columns = ['*'])
 * @method Audit find($id, $columns = ['*'])
 * @method Audit first($columns = ['*'])
*/
class AuditRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'auditable_type',
        'auditable_id',
        'ip_address',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Audit::class;
    }
    public static function getDataFromField($fieldname, $fieldvalue, $auditable_type = ''){
        if($fieldvalue){
            if($fieldname == 'resident_id'){            
                $model = Resident::find($fieldvalue);
                return ($model) ? $model->first_name . ' ' . $model->last_name : "";
            } 
            if($fieldname == 'address_id'){                
                $model = Address::find($fieldvalue);
                return ($model) ? $model->house_num. ' '. $model->street . ' '. $model->city : "";
            }
            if($fieldname == 'building_id'){            
                $model = Building::find($fieldvalue);
                return ($model) ? $model->name : "";
            } 
            if($fieldname == 'quarter_id'){                                            
                $model = Quarter::find($fieldvalue);                
                return ($model) ? $model->name : "";
            } 
            elseif($fieldname == 'unit_id'){            
                $model = Unit::find($fieldvalue);
                return ($model) ? $model->name : "";
            }
            elseif($fieldname == 'nation'){
                $model = Country::find($fieldvalue);
                return ($model) ? $model->name : "";
            }
            elseif($fieldname == 'state_id'){
                $model = State::find($fieldvalue);
                return ($model) ? $model->name : "";
            }
            elseif(($auditable_type == 'resident') && ($fieldname == 'status')){   
                return __('models.resident.status.' . Resident::Status[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'status')){   
                return __('models.request.status.' . Request::Status[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'is_public')){                   
                return ($fieldvalue) ? __('general.yes') : __('general.no');
            }
            elseif(($auditable_type == 'request') && (in_array($fieldname, ['internal_priority','priority']))){
                return __('models.request.internal_priority.' . Request::Priority[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'visibility')){
                return __('models.request.visibility.' . Request::Visibility[$fieldvalue]);
            }       
            elseif(($auditable_type == 'request') && ($fieldname == 'reminder_user_ids')){
                $ids = json_decode($fieldvalue);
                $user_name = '';
                if(is_array($ids)){                      
                    $users = User::whereIn('id', $ids)->get();
                    foreach($users as $user){
                        $user_name .= $user->name;
                        $user_name .= ', ';
                    }                    
                }      
                return rtrim($user_name,', ');
            }
            elseif(($auditable_type == 'pinboard') && ($fieldname == 'type')){   
                return __('models.pinboard.type.' . Pinboard::Type[$fieldvalue]);
            }
            elseif(($auditable_type == 'pinboard') && ($fieldname == 'execution_period')){   
                return __('models.pinboard.execution_period.' . Pinboard::ExecutionPeriod[$fieldvalue]);
            }            
            elseif(($auditable_type == 'pinboard') && ($fieldname == 'category')){                   
                return __('models.pinboard.category.' . Pinboard::Category[$fieldvalue]);
            } 
            elseif(($auditable_type == 'pinboard') && ($fieldname == 'sub_type')){   
                if(isset(Pinboard::SubType[Pinboard::TypeAnnouncement][$fieldvalue])){
                    return __('models.pinboard.sub_type.' . Pinboard::SubType[Pinboard::TypeAnnouncement][$fieldvalue]);
                }                
            }            
            elseif(($auditable_type == 'manager') && ($fieldname == 'type')){
                if($fieldvalue == PropertyManager::TypeManager){
                    return __('general.assignment_types.managers');
                }
                elseif($fieldvalue == PropertyManager::TypeAdministrator){
                    return __('general.assignment_types.administrator');
                }
            }
            elseif(($auditable_type == 'quarter') && ($fieldname == 'type')){
                return __('models.quarter.types.' . Quarter::Type[$fieldvalue]);
            }
            elseif(($auditable_type == 'unit') && ($fieldname == 'type')){
                return __('models.unit.type.' . Unit::Type[$fieldvalue]);
            }
            elseif(($auditable_type == 'pinboard') && ($fieldname == 'visibility')){
                return __('models.pinboard.visibility.' . Pinboard::Visibility[$fieldvalue]);
            }  
            elseif(($auditable_type == 'pinboard') && ($fieldname == 'status')){
                return __('models.pinboard.status.' . Pinboard::Status[$fieldvalue]);
            }            
            elseif(($auditable_type == 'provider') && ($fieldname == 'category')){
                return __('models.service.category.' . ServiceProvider::Category[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'category_id')){
                return __('models.request.category_list.' . Request::Category[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'sub_category_id')){
                return __('models.request.sub_category.' . Request::SubCategory[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'location')){
                return __('models.request.location.' . Request::Location[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'capture_phase')){
                return __('models.request.capture_phase.' . Request::CapturePhase[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'qualification')){
                return __('models.request.qualification.' . Request::Qualification[$fieldvalue]);
            }
            elseif(($auditable_type == 'templates') && ($fieldname == 'category_id')){
                $model = TemplateCategory::find($fieldvalue);
                if($model){
                    return $model->name;
                }
                return "";
            }
        }
        else {
            return "";
        }    
    }
}
