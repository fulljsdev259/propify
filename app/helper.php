<?php

class ConstFileCategories
{
    const MediaCategories = [
        'house_rules',
        'operating_instructions',
        'care_instructions',
        'other',
    ];
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

    if (! key_exists($currentLanguage, config('app.locales'))) {
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
                    echo '[' . $oldValue  . "] replaced to [" . $value . ']'. PHP_EOL;
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
    $notifications = \Illuminate\Notifications\DatabaseNotification::where('data', 'like', '%' . $replace .'%')->get();
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
    // @TODO cache
    $roleName = \App\Models\PropertyManager::Type[$type] ?? \App\Models\PropertyManager::TypeManager;
    return \Illuminate\Support\Facades\Cache::remember('role_' . $roleName, 3000, function () use ($roleName) {
        return \App\Models\Role::whereName($roleName)->first();
    });
}

function get_category_details($category)
{
    $values = \App\Models\Request::CategoryAttributes;
    return [
        'id' => $category,
        'parent_id' => null,
        'name' => \App\Models\Request::Category[$category] ?? 'not exists',
        'name_en' => \App\Models\Request::Category[$category] ?? 'not exists',
        'name_de' => \App\Models\Request::Category[$category] ?? 'not exists',
        'name_it' => \App\Models\Request::Category[$category] ?? 'not exists',
        'name_fr' => \App\Models\Request::Category[$category] ?? 'not exists',
        'description' => 'description @TODO',
        'acquisition' => get_category_attribute(\App\Models\Request::Acquisition, $values, $category),
        'has_qualifications' => get_category_attribute(\App\Models\Request::HasQualifications, $values, $category),
        'location' => get_category_attribute(\App\Models\Request::LocationAttr, $values, $category),
        'room' => get_category_attribute(\App\Models\Request::RoomAttr, $values, $category),
        'categories' => []
    ];
}

function get_sub_category_details($subCategory)
{
    if (is_null($subCategory)) {
        return [];
    }

    $config = \App\Models\Request::CategorySubCategory;
    $parentId = null;
    foreach ($config as $id => $values) {
        if (in_array($subCategory, $values)) {
            $parentId = $id;
            break;
        }
    }


    $values = \App\Models\Request::SubCategoryAttributes;
    return [
        'id' => $subCategory,
        'parent_id' => $parentId,
        'name' => \App\Models\Request::SubCategory[$subCategory] ?? 'not exists',
        'name_en' => \App\Models\Request::SubCategory[$subCategory] ?? 'not exists',
        'name_de' => \App\Models\Request::SubCategory[$subCategory] ?? 'not exists',
        'name_fr' => \App\Models\Request::SubCategory[$subCategory] ?? 'not exists',
        'name_it' => \App\Models\Request::SubCategory[$subCategory] ?? 'not exists',
        'description' => 'description @TODO',
        'acquisition' => get_category_attribute(\App\Models\Request::Acquisition, $values, $subCategory),
        'has_qualifications' => get_category_attribute(\App\Models\Request::HasQualifications, $values, $subCategory),
        'location' => get_category_attribute(\App\Models\Request::LocationAttr, $values, $subCategory),
        'room' => get_category_attribute(\App\Models\Request::RoomAttr, $values, $subCategory),
        'categories' => []
    ];
}

function get_category_attribute($attribute, $attributeValues, $key)
{
    if (empty($attributeValues[$key])) {
        return 0;
    }
    return (int) in_array($attribute, $attributeValues[$key]);
}