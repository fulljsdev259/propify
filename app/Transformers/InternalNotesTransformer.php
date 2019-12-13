<?php

namespace App\Transformers;

use App\Models\InternalNotice;

/**
 * Class InternalNotesTransformer
 * @package App\Transformers
 */
class InternalNotesTransformer extends BaseTransformer
{

    /**
     * @param InternalNotice $model
     * @return array
     */
    public function transform(InternalNotice $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'request_id',
            'user_id',
            'comment',
            'created_at',
            'updated_at'
        ]);

        $response = $this->includeRelationIfExists($model, $response, [
            'user' => UserTransformer::class,
            'users' => UserTransformer::class,
        ]);

        return $response;
    }
}
