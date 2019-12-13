<?php

namespace App\Transformers;

use App\Models\Comment;

/**
 * Class CommentTransformer.
 *
 * @package namespace App\Transformers;
 */
class CommentTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the Comment entity.
     *
     * @param \App\Models\Comment $model
     *
     * @return array
     */
    public function transform(Comment $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'user_id',
            'comment',
            'created_at',
            'updated_at',
            'children_count',
        ]);

        $response['children_count'] = $model->children_count;
        $response = $this->includeRelationIfExists($model, $response, [
            'user' => UserTransformer::class,
        ]);

        return $response;
    }
}
