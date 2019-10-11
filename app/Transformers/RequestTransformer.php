<?php

namespace App\Transformers;

use App\Models\Request;

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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transform(Request $model)
    {
        $response = [
            'id' => $model->id,
            'request_format' => $model->request_format,
            'title' => $model->title,
            'description' => $model->description,
            'status' => $model->status,
            'priority' => $model->priority,
            'internal_priority' => $model->internal_priority,
            'qualification' => $model->qualification,
            'is_public' => $model->is_public,
            'notify_email' => $model->notify_email,
            'room' => $model->room,
            'capture_phase' => $model->capture_phase,
            'component' => $model->component,
            'payer' => $model->payer,
            'location' => $model->location,
            'created_at' => $model->created_at->format('d.m.Y H:i:s'),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'visibility' => $model->visibility,
            'active_reminder' => $model->active_reminder,
            'reminder_user_id' => $model->reminder_user_id,
            'days_left_due_date' => $model->days_left_due_date,
            'sent_reminder_user_ids' => $model->sent_reminder_user_ids,
        ];

        if ($model->relationExists('audit')) {
            $audit = $model->audit;
            if ($audit) {
                $response['audit_id'] = $audit->id;
            }
        }

        if ($model->due_date) {
            $response['due_date'] = $model->due_date->format('Y-m-d');
        }

        if ($model->solved_date) {
            $response['solved_date'] = $model->solved_date->format('Y-m-d');
        }

        $assignedUsers = $model->newCollection();
        if ($model->relationExists('providers')) {
            $assignedUsers = $assignedUsers->merge($model->providers->pluck('user'));
            $response['service_providers'] = (new ServiceProviderTransformer)->transformCollection($model->providers);
        }

        if ($model->relationExists('managers')) {
            $usersCollection = $model->newCollection($model->managers->pluck('user')->all());
            $assignedUsers = $assignedUsers->merge($usersCollection);

            $response['property_managers'] = (new PropertyManagerTransformer())->transformCollection($model->managers);
        }

        if ($model->relationExists('users')) {
            $assignedUsers = $assignedUsers->merge($model->users);
            $response['users'] = (new UserTransformer())->transformCollection($model->users);
        }

        if ($assignedUsers->count()) {
            $response['assignedUsers'] = (new UserTransformer)->transformCollection($assignedUsers);
        } else {
            $response['assignedUsers'] = [];
        }


        if ($model->relationExists('category')) {
            $response['category'] = (new RequestCategorySimpleTransformer)->transform($model->category);
        }
        if ($model->relationExists('remainder_user')) {
            $response['remainder_user'] = (new UserTransformer())->transform($model->remainder_user);
        }

        if ($model->relationExists('resident')) {
            $response['tenant'] = (new ResidentTransformer)->transform($model->resident);  // @TODO delete
            $response['resident'] = (new ResidentTransformer)->transform($model->resident);
        }

        if ($model->relationExists('contract')) {
            $response['rent_contract'] = (new ContractTransformer())->transform($model->contract);
            $response['contract'] = (new ContractTransformer())->transform($model->contract);
        }

        if ($model->relationExists('comments')) {
            $response['comments'] = (new CommentTransformer)->transformCollection($model->comments);
        }

        if ($model->relationExists('creator')) {
            $response['creator'] = (new UserTransformer())->transformCollection($model->creator);
        }

        $response['media'] = [];
        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer)->transformCollection($model->media);
        }

        return $response;
    }
}
