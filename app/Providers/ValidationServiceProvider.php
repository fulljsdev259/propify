<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationServiceProvider extends ServiceProvider
{

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('in_sub', function ($attribute, $value, $parameters, $validator) {
            $key = $parameters[0];
            $class = $parameters[1];
            $constantKey = $parameters[2];
            $subValues = constant("$class::$constantKey");
            $data = $validator->getData();

            if (! isset($data[$key])) {
                $validator->customMessages['in_sub'] = __('validation.required_with', ['attribute' => $key, 'values' => $attribute]);
                return false;
            }

            $keyValue = $data[$key];

            if (! isset($subValues[$keyValue])) {
                $validator->customMessages['in_sub'] = sprintf('You can not pass %s when %s is %s', $attribute, $key, $keyValue);
                return false;
            }

            if (! in_array($value, $subValues[$keyValue])) {
                $validator->customMessages['in_sub'] = __('validation.in');
                return false;
            }

            return true;
        });

        // validate base64_encode text by extension
        Validator::extend('base_mimes', function ($attribute, $value, $parameters, $validator) {
            $mineTypes = config('filesystems.mime_types');
            $permitted = [];
            foreach ($parameters as $extension) {
                $permitted[] = $mineTypes[$extension]; // must be already filled in config all cases
            }

            $file = finfo_open();
            $base64Decode = base64_decode($value);
            if (! $base64Decode) {
                return false;
            }

            $mimeType = finfo_buffer($file, $base64Decode, FILEINFO_MIME_TYPE);
            finfo_close($file);

            return in_array($mimeType, $permitted);

        });

        Validator::replacer('base_mimes', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':attribute', ':values'], ['file', '.' . implode(', .', $parameters)], __('validation.mimes'));
        });
    }

}
