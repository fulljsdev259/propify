<?php

namespace App\Transformers;

use App\Models\InternalNotice;

/**
 * Class BuildingTransformer.
 *
 * @package namespace App\Transformers;
 */
class InternalNotesTransformer extends BaseTransformer
{

    /**
     * @param InternalNotice $model
     * @return array
     */
    public function transform(InternalNotice $model)
    {
        $response = [
            'id' => $model->id,
            'request_id' => $model->request_id,
            'user_id' => $model->user_id,
            'comment' => $model->comment,
            'created_at'=> $model->created_at->toDateTimeString(),
            'updated_at'=> $model->updated_at->toDateTimeString(),
        ];
        if ($model->relationExists('user')) {
            $response['user'] = (new UserTransformer())->transform($model->user);
        }
        if ($model->relationExists('users')) {
            $response['users'] = (new UserTransformer())->transformCollection($model->users);
        }

        return $response;
    }
}
