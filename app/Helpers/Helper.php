<?php

namespace App\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Helper
{
    public static function randStr($length = 10){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        return $token;
    }
    public static function mapAuditFieldToLanguage(){
        return [            
            'request' => [
                'category_id' => __('models.request.category'),
                'unit_id' => __('models.resident.unit.name'),
                'resident_id' => __('general.resident'),
                'title' => __('general.title'),
                'description' => __('general.description'),                                                
                'status' => __('models.request.status.label'),
                'contract_id' => __('models.resident.contract.title'),
                'internal_priority' => __('models.request.internal_priority.label'),
                'priority' => __('models.request.priority.label'),
                'qualification' => __('models.request.qualification.label'),
                'visibility' => __('models.request.visibility.label'),                 
            ],
            'quarter' => [
                'internal_quarter_id' => __('general.internal_quarter_id'),
                'name' => __('general.name'),                
            ],
            'building' => [
                'quarter_id' => __('general.assignment_types.quarter'),
                'floor_nr' => __('models.building.floor_nr')
            ],
            'contract' => [],
            'listing' => [],
            'manager' => [
                'title' => __('general.salutation'),
                'first_name' => __('general.first_name'),
                'last_name' => __('general.last_name'),
                'type' => __('models.property_manager.assign_type'),
            ],
            'pinboard' => [
                'execution_period' => __('models.pinboard.execution_period.label'),
                'type' => __('models.pinboard.type.label'),
                'visibility' => __('models.pinboard.visibility.label'),
            ],
            'provider' => [
                'category' => __('models.service.category.label'),
                'name' => __('general.name'),
                'phone' => __('general.phone')
            ],                      
            'resident' => [
                'title' => __('general.salutation'),
                'first_name' => __('general.first_name'),
                'last_name' => __('general.last_name'),
                'status' => __('models.resident.status.label'),
                'address_id' => __('general.address'),
                'building_id' => __('models.resident.building.name'),
                'unit_id' => __('models.resident.unit.name')
            ],
            'settings' => [],
            'unit' => [
                'description' => __('general.description'),                
                'building_id' => __('general.assignment_types.building'),
                'monthly_rent_net' => __('general.monthly_rent_net'),
                'monthly_maintenance' => __('general.maintenance'),
                'monthly_rent_gross' => __('general.gross_rent'),                
            ],
            'user' => [],   
        ];
    }  
    public static function shortenString($str){
        if($str) {
            return Str::limit($str, 20);
        } else{
            return "";
        }
    }
    public static function formatedDate($date){
        if($date) {
            return Carbon::parse($date)->format('d.m.Y');
        } else{
            return "";
        }
    }
    // public static function getFullName($model, $id){
    //     if($model == '')
    // }
}
