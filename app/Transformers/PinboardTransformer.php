<?php

namespace App\Transformers;

use App\Models\Pinboard;

/**
 * Class PinboardTransformer
 * @package App\Transformers
 */
class PinboardTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * @param Pinboard $model
     * @return array
     */
    public function transform(Pinboard $model)
    {
        $ut = new UserTransformer();
        $tt = new ResidentTransformer();

        $response = [
            'id' => $model->id,
            'type' => $model->type,
            'sub_type' => $model->sub_type,
            'is_execution_time' => (bool)$model->is_execution_time,
            'status' => $model->status,
            'visibility' => $model->visibility,
            'category_image' => (bool) $model->category_image,
            'category' => $model->category,
            'content' => $model->content,
            'title' => $model->title,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'published_at' => $model->published_at ? $model->published_at->toDateTimeString() : null,
            'user_id' => $model->user_id,
            'user' => $ut->transform($model->user),
            'resident' => $model->user->resident ? $tt->transform($model->user->resident) : null,
            'liked' => $model->liked,
            'likes_count' => $model->likesCount,
            'comments_count' => $model->all_comments_count,
            'announcement' => $model->announcement,
            'notify_email' => $model->notify_email,
        ];

        if ($model->announcement) {
            $response['execution_start'] = $this->formatExecutionTime($model, 'execution_start');
            $response['execution_end'] = $this->formatExecutionTime($model, 'execution_end');
            $response['is_execution_time'] = $model->is_execution_time;
            $response['execution_period'] = $model->execution_period;
        }

        if (key_exists('views_count', $model->getAttributes())) {
            $response['views_count'] = $model->views_count;
        }

        if ($model->relationExists('buildings')) {
            $response['buildings'] = (new BuildingTransformer)->transformCollection($model->buildings);
        }
        if ($model->relationExists('quarters')) {
            $response['quarters'] = (new QuarterTransformer)->transformCollection($model->quarters);
        }
        if ($model->relationExists('providers')) {
            $response['providers'] = (new ServiceProviderTransformer)->transformCollection($model->providers);
        }
        if ($model->relationExists('likes')) {
            $response['likes'] = (new LikeTransformer)->transformCollection($model->likes);
        }
        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer)->transformCollection($model->media);
        }
        if ($model->relationExists('views')) {
            $response['views'] = $model->views->sum('views');
        }
        if ($model->relationExists('announcement_email_receptionists')) {
            $response['announcement_email_receptionists'] = (new AnnouncementEmailReceptionistTransformer())
                ->transformCollection($model->announcement_email_receptionists);
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
