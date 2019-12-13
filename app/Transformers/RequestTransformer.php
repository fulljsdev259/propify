<?php

namespace App\Transformers;

use App\Models\PropertyManager;
use App\Models\Request;
use App\Models\RequestAssignee;
use App\Models\Settings;

/**
 * Class RequestTransformer
 *
 * @package namespace App\Transformers;
 */
class RequestTransformer extends BaseTransformer
{
    /**
     * @param Request $model
     * @return array
     */
    public function transform(Request $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'request_format',
            'action',
            'title',
            'description',
            'status',
            'category_id',
            'sub_category_id',
            'qualification_category',
            //'priority',
            //'internal_priority',
            'is_public',
            'notify_email',
            'room',
            'capture_phase',
            'component',
            'location',
            'updated_at',
            'visibility',
            'active_reminder',
            'reminder_user_ids',
            'days_left_due_date',
            'sent_reminder_user_ids',
            'percentage',
            'cost_impact',
        ]);
        $response['created_at'] = $model->created_at->format('d.m.Y H:i:s');

        if ($model->due_date) {
            $response['due_date'] = $model->due_date->format('Y-m-d');
        }

        if ($model->solved_date) {
            $response['solved_date'] = $model->solved_date->format('Y-m-d');
        }

        if ($model->relationExists('assignees')) {
            $accountableUser = $model->assignees->where('type', RequestAssignee::TypeAccountable)->pluck('user')->first();
            if ($accountableUser) {
                $response['accountable_user'] = (new UserTransformer())->transform($accountableUser);
                $response['accountable_user']['company_name'] = $accountableUser->service_provider->company_name ?? \Cache::remember(
                        'company_name',
                        60,
                        function () {
                            return Settings::value('name');
                        }
                    );
            } else {
                $response['accountable_user'] = null;
            }

            $competentUser = $model->assignees->where('type', RequestAssignee::TypeCompetent)->pluck('user')->first();
            if ($competentUser) {
                $response['competent_user'] = (new UserTransformer())->transform($competentUser);
                $response['competent_user']['company_name'] = $competentUser->service_provider->company_name ?? \Cache::remember(
                        'company_name',
                        60,
                        function () {
                            return Settings::value('name');
                        }
                    );
            } else {
                $response['competent_user'] = null;
            }

            // @TODO delete
            $users = $model->assignees->pluck('user');
            $response['assignedUsers'] = (new UserTransformer())->transformCollection($users);
            $users = collect($response['assignedUsers']);

            $propertyManager = $users->whereIn('roles.0.name', PropertyManager::Type)->values()->all();
            $response['property_managers'] = $propertyManager;

            $serviceProviders = $users->where('roles.0.name', 'provider')->values()->all();
            $response['service_providers'] = $serviceProviders;
        }

        if ($model->category_id) {
            $response['category'] = get_category_details($model->category_id);
        }

        if ($model->sub_category_id) {
            $response['sub_category'] = get_sub_category_details($model->sub_category_id);
        }

        $response['media'] = [];
        $response = $this->includeRelationIfExists($model, $response, [
            'resident' => ResidentTransformer::class,
            'relation' => RelationTransformer::class,
            'comments' => CommentTransformer::class,
            'creator' => UserTransformer::class,
            'media' => MediaTransformer::class,
        ]);

        return $this->addAuditIdInResponseIfNeed($model, $response);
    }
}
