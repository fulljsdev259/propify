<?php

namespace App\Repositories;

use App\Models\Resident;
use App\Models\Unit;
use App\Models\Request;
use OwenIt\Auditing\Models\Audit;

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
                return $model->first_name . ' ' . $model->last_name;            
            } 
            elseif($fieldname == 'unit_id'){            
                $model = Unit::find($fieldvalue);
                return $model->name;            
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'status')){   
                return __('models.request.status.' . Request::Status[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'is_public')){                   
                return ($fieldvalue) ? __('general.yes') : __('general.no');
            }
            elseif(in_array($fieldname, ['internal_priority','priority'])){
                return __('models.request.internal_priority.' . Request::Priority[$fieldvalue]);
            }
            elseif(($auditable_type == 'request') && ($fieldname == 'visibility')){
                return __('models.request.visibility.' . Request::Visibility[$fieldvalue]);
            }
        }
        else {
            return "";
        }    
    }
}
