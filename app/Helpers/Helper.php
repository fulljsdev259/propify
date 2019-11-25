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
            'settings' => [
                'name' => __('general.name'),
                'iframe_enable' => __('settings.iframe'),
                'email_receptionist_ids' => __('general.recipients'),
                'email' => __('general.email'),
                'phone' => __('general.phone'),
                'blank_pdf' => __('settings.blank_pdf'),
                'pdf_font_family' => __('settings.font_family'),
                'mail_from_name' => __('settings.mail_from_name.label'),
                'mail_from_address' => __('settings.mail_from_address.label'),
                'mail_host' => __('settings.mail_host.label'),
                'mail_port' => __('settings.mail_port.label'),
                'mail_username' => __('settings.mail_username.label'),
                'mail_encryption' => __('settings.mail_encryption'),
                'mail_password' => __('settings.mail_password.label'),
                'logo' => __('models.user.logo'),
                'circle_logo' => __('models.user.circle_logo'),
                'resident_logo' => __('models.user.resident_logo'),
                'favicon_icon' => __('models.user.favicon_icon'),
                'primary_color' => __('settings.primary_color'),
                'primary_color_lighter' => __('settings.accent_color'),
            ],
            'templates' => [
                'category_id' => __('models.template.category'),
                'body' => __('models.template.body'),
            ],    
            'request' => [
                'category_id' => __('models.request.category'),
                'sub_category_id' => __('models.request.defect_location.label'),
                'location' => __('models.request.category_options.range'),
                'capture_phase' => __('models.request.category_options.capture_phase'),
                'unit_id' => __('models.resident.unit.name'),
                'resident_id' => __('general.resident'),
                'title' => __('general.title'),
                'description' => __('general.description'),                                                
                'status' => __('models.request.status.label'),
                'relation_id' => __('models.resident.relation.title'),
                'internal_priority' => __('models.request.internal_priority.label'),
                'priority' => __('models.request.priority.label'),
                'visibility' => __('models.request.visibility.label'),
                'percentage' => __('models.request.category_options.payer_percent'),
                'amount' => __('models.request.category_options.payer_amount'),
                'active_reminder' => __('models.request.active_reminder_switcher'),
                'days_left_due_date' => __('models.request.days_left_due_date'),
                'reminder_user_ids' => __('models.request.notified_persons'),
                'request_format' => __('models.request.title')." ".__('general.format'),                            
            ],
            'quarter' => [
                'type' => __('models.quarter.types.label'),
                'internal_quarter_id' => __('general.internal_quarter_id'),
                'name' => __('general.name'),                
                'zip' => __('general.zip'),
                'city' => __('general.city'),
                'state_id' => __('general.state'),
                'house_num' => __('general.house_num'),
                'quarter_format' => __('models.quarter.title')." ".__('general.format'),            
            ],
            'building' => [
                'quarter_id' => __('general.assignment_types.quarter'),
                'floor_nr' => __('models.building.floor_nr'),  
                'name' => __('general.name'),                
                'zip' => __('general.zip'),
                'city' => __('general.city'),
                'street' => __('general.street'),
                'state_id' => __('general.state'),
                'house_num' => __('general.house_num'),  
                'building_format' => __('models.building.title')." ".__('general.format'),            
            ],
            'relation' => [
                'relation_format' => __('models.relation.title')." ".__('general.format'),
            ],
            'listing' => [],
            'manager' => [
                'name' => __('general.name'),
                'phone' => __('general.phone'),
                'email' => __('general.email'),
                'password' => __('general.password'),
                'role' => __('general.function'),
                'title' => __('general.salutation'),
                'first_name' => __('general.first_name'),
                'last_name' => __('general.last_name'),
                'type' => __('models.property_manager.assign_type'),
                'language' => __('general.language'),
                'property_manager_format' => __('models.property_manager.title')." ".__('general.format'),
            ],
            'pinboard' => [
                'execution_period' => __('models.pinboard.execution_period.label'),
                'type' => __('models.pinboard.type.label'),
                'status' => __('models.pinboard.status.label'),
                'visibility' => __('models.pinboard.visibility.label'),
                'execution_start' => __('models.pinboard.execution_interval.start'),
                'execution_end' => __('models.pinboard.execution_interval.end'),
                'category' => __('models.pinboard.category.label'),
                'sub_type' => __('models.pinboard.sub_type.label'),
                'is_execution_time' => __('models.pinboard.execution_interval.label')
            ],
            'provider' => [
                'email' => __('general.email'),
                'password' => __('general.password'),
                'category' => __('models.service.category.label'),
                'name' => __('general.name'),
                'phone' => __('general.phone'),
                'language' => __('general.language'),
                'state_id' => __('general.state'),
                'city' => __('general.city'),
                'street' => __('general.street'),
                'zip' => __('general.zip'),
                'service_provider_format' => __('models.service.title')." ".__('general.format'),            
            ],                      
            'resident' => [
                'email' => __('general.email'),
                'password' => __('general.password'),
                'title' => __('general.salutation'),
                'first_name' => __('general.first_name'),
                'last_name' => __('general.last_name'),
                'status' => __('models.resident.status.label'),
                'address_id' => __('general.address'),
                'building_id' => __('models.resident.building.name'),
                'unit_id' => __('models.resident.unit.name'),
                'language' => __('general.language'),
                'resident_format' => __('models.resident.name')." ".__('general.format'),
            ],            
            'unit' => [
                'description' => __('general.description'),                
                'building_id' => __('general.assignment_types.building'),
                'monthly_rent_net' => __('general.monthly_rent_net'),
                'monthly_maintenance' => __('general.maintenance'),
                'monthly_rent_gross' => __('general.gross_rent'),    
                'type' => __('models.unit.type.label'),
                'unit_format' => __('models.unit.title')." ".__('general.format'),            
            ],
            'user' => [
                'last_login_at' => __('general.last_login_at')
            ],   
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
