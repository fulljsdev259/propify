<?php

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