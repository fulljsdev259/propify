<?php

class ConstantsHelpers
{
    const MediaFileCategories = [
        'house_rules',
        'operating_instructions',
        'care_instructions',
        'other',
    ];
    
}
class AvatarHelper
{
    const AVATAR_SIZES = [
        32,
        48,
        64,
        800
    ];
    public $id;
    public $avatar;
}
function getDefaultAvatar($user, $pixel = '32')
{   
    if(!$user->avatar){
        return null;
    } else{
        return 'storage/avatar/' . $user->id . '/' . $pixel  . 'x' . $pixel . '/' . $user->avatar;
    }
}
function getUserAvatars($user)
{
    if(!$user->avatar){
        return null;
    }
    $avatarVariations = [];
    foreach (AvatarHelper::AVATAR_SIZES as $pixel) {
        $avatarVariations[] = 'storage/avatar/' . $user->id . '/' . $pixel  . 'x' . $pixel . '/' . $user->avatar;
    }
    return $avatarVariations;
}


function get_morph_type_of($class)
{
    if (is_object($class)) {
        $class = get_class($class);
    }
    return array_flip(\Illuminate\Database\Eloquent\Relations\Relation::$morphMap)[$class] ?? $class;
}

function get_translated_filed($model, $field = 'name')
{
    $currentLanguage = config('app.locale');

    if ('en' != $currentLanguage) {
        $fieldTranslation = get_translation_attribute_name($field);
        if (isset($model->{$fieldTranslation})) {
            return $model->{$fieldTranslation};
        }
    }

    return $model->{$field};
}

function get_translation_attribute_name($attribute)
{
    $currentLanguage = config('app.locale');

    if (!key_exists($currentLanguage, config('app.locales'))) {
        return $attribute;
    }

    if ('en' != $currentLanguage) {
        return $attribute . '_' . $currentLanguage;
    }

    return $attribute;
}

function update_db_fields($class, $fields, $replace, $to, $isUcFirst = true)
{
    $model = new $class();
    if (method_exists($class, 'disableAuditing')) {
        $class::disableAuditing();
    }
    $query = $model->newQuery();
    $fields = \Illuminate\Support\Arr::wrap($fields);
    foreach ($fields as $field) {
        $query->orWhere($field, 'like', '%' . $replace . '%');
    }
    $items = $query->select($fields)->addSelect('id')->get();
    foreach ($items as $item) {
        foreach ($fields as $field) {
            $oldValue = $item->{$field};
            $isAssoc = false;
            if (is_array($oldValue) && \Illuminate\Support\Arr::isAssoc($oldValue)) {
                $isAssoc = true;
                $oldValue = json_encode($oldValue);
            }

            $value = $oldValue;
            $value = str_replace($replace, $to, $value);
            if ($isUcFirst) {
                $value = str_replace(ucfirst($replace), ucfirst($to), $value);
            }

            if ($oldValue != $value) {
                if (is_array($oldValue)) {
                    foreach ($oldValue as $i => $_val) {
                        echo '[' . $_val  . "] replaced to [" . ($value[$i] ?? '') . ']' . PHP_EOL;
                    }
                } else {
                    echo '[' . $oldValue  . "] replaced to [" . $value . ']' . PHP_EOL;
                }
                echo  '------------------------' .  PHP_EOL;
                if ($isAssoc) {
                    $value = json_decode($value, JSON_PRETTY_PRINT);
                }
                $item->{$field} = $value;
                $item->save();
            }
        }
    }
    if (method_exists($class, 'disableAuditing')) {
        $class::enableAuditing();
    }
}

function update_notifications_data_value($replace, $to)
{
    $notifications = \Illuminate\Notifications\DatabaseNotification::where('data', 'like', '%' . $replace . '%')->get();
    foreach ($notifications as $notification) {
        $value = json_encode($notification->data);
        $newValue = str_replace($replace, $to, $value);
        echo '[' . $value  . "] replaced to [" . $newValue . ']' . PHP_EOL;
        $notification->data = json_decode($newValue);
        $notification->save();
    }
}

function get_type_correspond_role($type)
{
    return \App\Models\PropertyManager::Type[$type] ?? \App\Models\PropertyManager::TypeManager;
}

function get_category_details($categoryId)
{
    $values = \App\Models\Request::CategoryAttributes;
    if ($categoryId != \App\Models\Request::CategoryGeneral) {
        return [
            'id' => $categoryId,
            'name' => \App\Models\Request::Category[$categoryId] ?? 'not exists',
            'cost_impact' => get_category_attribute(\App\Models\Request::CostImpactAttr, $values, $categoryId),
            'action' => get_category_attribute(\App\Models\Request::ActionAttr, $values, $categoryId),
            'capture_phase' => get_category_attribute(\App\Models\Request::CapturePhaseAttr, $values, $categoryId),
            'component' => get_category_attribute(\App\Models\Request::ComponentAttr, $values, $categoryId),
            'qualification_category' => get_category_attribute(\App\Models\Request::SubQualificationCategoryAttr, $values, $categoryId),
        ];
    }

    return [
        'id' => $categoryId,
        'name' => \App\Models\Request::Category[$categoryId] ?? 'not exists',
        'capture_phase' => get_category_attribute(\App\Models\Request::CapturePhaseAttr, $values, $categoryId),
        'cost_impact' => get_category_attribute(\App\Models\Request::CostImpactAttr, $values, $categoryId),
        'action' => get_category_attribute(\App\Models\Request::ActionAttr, $values, $categoryId),
        'component' => get_category_attribute(\App\Models\Request::ComponentAttr, $values, $categoryId),
        'qualification_category' => get_category_attribute(\App\Models\Request::SubQualificationCategoryAttr, $values, $categoryId),
    ];
}

function get_sub_category_details($subCategoryId)
{
    if (is_null($subCategoryId)) {
        return [];
    }

    $config = \App\Models\Request::CategorySubCategory;
    $parentId = null;
    foreach ($config as $id => $values) {
        if (in_array($subCategoryId, $values)) {
            $parentId = $id;
            break;
        }
    }


    $values = \App\Models\Request::SubCategoryAttributes;
    return [
        'id' => $subCategoryId,
        'parent_id' => $parentId,
        'name' => \App\Models\Request::SubCategory[$subCategoryId] ?? 'not exists',
        'location' => get_category_attribute(\App\Models\Request::LocationAttr, $values, $subCategoryId),
        'room' => get_category_attribute(\App\Models\Request::RoomAttr, $values, $subCategoryId),
    ];
}

function get_category_attribute($attribute, $attributeValues, $key)
{
    if (empty($attributeValues[$key])) {
        return 0;
    }
    return (int) in_array($attribute, $attributeValues[$key]);
}

function str_fill_to($string, $length, $symbol = '0') {
    $len = strlen($string);
    if ($len < $length) {
        for ($i = 0; $i < ($length - $len); $i++) {
            $string = $symbol . $string;
        }
    }

    return $string;
}

// https://stackoverflow.com/questions/13169588/how-to-check-if-multiple-array-keys-exists
function array_keys_exists(array $keys, array $arr) {
    return !array_diff_key(array_flip($keys), $arr);
}