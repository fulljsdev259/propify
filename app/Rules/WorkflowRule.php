<?php

namespace App\Rules;

use App\Models\Building;
use App\Models\Request;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Contracts\Validation\Rule;

class WorkflowRule implements Rule
{
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     * check workflows is array
     *      in each array
     *          title => required
     *          category_id => required
     *          category_id => must be valid category by Request::Category
     *          building_ids => required
     *          building_ids => numeric array
     *          building_ids => exists
     *          to_user_ids => required
     *          to_user_ids => numeric array
     *          to_user_ids => exists
     *          cc_user_ids => numeric array
     *          cc_user_ids => exists
     *          id => numeric
     *          id => exists
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! is_array($value)) {
            $this->message = __('validation.array', compact('attribute'));
            return false;
        }


        foreach ($value as $key => $data) {
            if (empty($data['title'])) {
                $this->message = __('validation.required', ['attribute' => $this->getAttributeByKey($key, 'title')]);
                return false;
            }

            if (! isset($data['category_id'])) {
                $this->message = __('validation.required', ['attribute' => $this->getAttributeByKey($key, 'category_id')]);
                return false;
            } elseif (! is_numeric($data['category_id'])) {
                $this->message = __('validation.numeric', ['attribute' => $this->getAttributeByKey($key, 'category_id')]);
                return false;
            } elseif (! in_array($data['category_id'], array_keys(Request::Category))) {
                $this->message = __('validation.in', ['attribute' => $this->getAttributeByKey($key, 'category_id')]);
                return false;
            }

            if (empty($data['building_ids'])) {
                $this->message = __('validation.required', ['attribute' => $this->getAttributeByKey($key, 'building_ids')]);
                return false;
            } elseif (! is_array($data['building_ids'])) {
                $this->message = __('validation.array', ['attribute' => $this->getAttributeByKey($key, 'building_ids')]);
                return false;
            } else {
                foreach ($data['building_ids'] as $_value) {
                    if (! is_numeric($_value)) {
                        $this->message = __('validation.is_numeric_array', ['attribute' => $this->getAttributeByKey($key, 'building_ids')]);
                        return false;
                    }
                }
            }

            if (empty($data['to_user_ids'])) {
                $this->message = __('validation.required', ['attribute' => $this->getAttributeByKey($key, 'to_user_ids')]);
                return false;
            } elseif (! is_array($data['to_user_ids'])) {
                $this->message = __('validation.array', ['attribute' => $this->getAttributeByKey($key, 'to_user_ids')]);
                return false;
            } else {
                foreach ($data['to_user_ids'] as $_value) {
                    if (! is_numeric($_value)) {
                        $this->message = __('validation.is_numeric_array', ['attribute' => $this->getAttributeByKey($key, 'to_user_ids')]);
                        return false;
                    }
                }
            }

            if (! empty($data['id']) && ! is_numeric($data['id'])) {
                $this->message = __('validation.numeric', ['attribute' => $this->getAttributeByKey($key, 'id')]);
                return false;
            }

            if (empty($data['cc_user_ids'])) {
                continue;
            }

            if (! is_array($data['cc_user_ids'])) {
                $this->message = __('validation.array', ['attribute' => $this->getAttributeByKey($key, 'cc_user_ids')]);
                return false;
            } else {
                foreach ($data['cc_user_ids'] as $_value) {
                    if (! is_numeric($_value)) {
                        $this->message = __('validation.is_numeric_array', ['attribute' => $this->getAttributeByKey($key, 'cc_user_ids')]);
                        return false;
                    }
                }
            }
        }

        $value = collect($value);
        $buildingIds = $value->pluck('building_ids')->collapse()->unique();
        // @TODO check building belong quarter
        $buildingsCount = Building::whereIn('id', $buildingIds)->count();
        if ($buildingIds->count() != $buildingsCount) {
            $this->message = 'One of selected building is not correct';
            return false;
        }

        // @TODO check users exists and admins
        $userIds = $value->pluck('to_user_ids')->merge($value->pluck('cc_user_ids'))->collapse()->unique();
        $usersCount = User::whereIn('id', $userIds)->count();
        if ($userIds->count() != $usersCount) {
            $this->message = 'One of selected user is not correct';
            return false;
        }

        $workflowIds = $value->pluck('id')->unique()->filter(function ($item) {
            return ! is_null($item);
        });
        if ($workflowIds->count()) {
            $workflowCount = Workflow::whereIn('id', $workflowIds)->count();
            if ($workflowIds->count() != $workflowCount) {
                $this->message = 'One of selected workflow is not correct';
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * @param $key
     * @param $name
     * @return string
     */
    protected function getAttributeByKey($key, $name)
    {
        return $key . '.' . $name;
    }
}
