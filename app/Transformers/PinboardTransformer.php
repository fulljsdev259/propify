<?php

namespace App\Transformers;

use App\Models\Pinboard;

/**
 * Class PinboardTransformer
 * @package App\Transformers
 */
class PinboardTransformer extends BaseTransformer
{
    /**
     * @param Pinboard $model
     * @return array
     */
    public function transform(Pinboard $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'type',
            'sub_type',
            'is_execution_time',
            'status',
            'visibility',
            'category_image',
            'category',
            'content',
            'title',
            'created_at',
            'updated_at',
            'published_at',
            'user_id',
//            'user' => $ut->transform($model->user),
//            'resident' => $model->user->resident ? $tt->transform($model->user->resident) : null,
            'comments_count',
            'announcement',
            'notify_email',
            'views_count'
        ], [
            'liked',
            'likes_count' => 'likesCount',
            'comments_count' => 'all_comments_count'
        ]);
        $response = $this->includeRelationIfExists($model, $response, [
            'user' => UserTransformer::class,
            'buildings' => BuildingTransformer::class,
            'quarters' => QuarterTransformer::class,
            'providers' => ServiceProviderTransformer::class,
            'likes' => LikeTransformer::class,
            'media' => MediaTransformer::class,
            'announcement_email_receptionists' => AnnouncementEmailReceptionistTransformer::class,
        ]);
//        if (empty($response['announcement_email_receptionists'][0]['residents'])) {
//            $response['announcement_email_receptionists'][0]['residents'] = [];
//        } // uncomment it for vue js not show bug
        $response['resident'] = $model->user->resident ? (new ResidentTransformer)->transform($model->user->resident) : null;

        if ($model->announcement) {
            $response['execution_start'] = $this->formatExecutionTime($model, 'execution_start');
            $response['execution_end'] = $this->formatExecutionTime($model, 'execution_end');
            $response['is_execution_time'] = $model->is_execution_time;
            $response['execution_period'] = $model->execution_period;
        }

        if ($model->relationExists('views')) {
            $response['views'] = $model->views->sum('views');
        }

        return $this->addAuditIdInResponseIfNeed($model, $response);
    }

    protected function formatExecutionTime($model, $col)
    {
        $value = $model->{$col};
        if ($value) {
            return $model->is_execution_time ? $value->toDateTimeString()  : $value->format('Y-m-d');
        }
        return null;
    }
}
