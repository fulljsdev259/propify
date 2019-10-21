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
                'due_date' => __('models.request.due_date'),
                'solved_date' => __('models.request.solved_date'), 
                'status' => __('models.request.status.label'),
                'contract_id' => __('models.resident.contract.title'),
                'internal_priority' => __('models.request.internal_priority.label'),
                'priority' => __('models.request.priority.label'),
                'qualification' => __('models.request.qualification.label'),
                'visibility' => __('models.request.visibility.label'), 
                'is_public' => __('models.request.is_public'), 
            ],
            'building' => [],
            'contract' => [],
            'listing' => [],
            'manager' => [],
            'pinboard' => [],
            'provider' => [],
            'quarter' => [],            
            'resident' => [],
            'settings' => [],
            'unit' => [],
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
