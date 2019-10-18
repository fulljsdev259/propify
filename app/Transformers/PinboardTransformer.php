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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transform(Pinboard $model)
    {
        $ut = new UserTransformer();
        $tt = new ResidentTransformer();

        $ret = [
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
            'pinned' => $model->announcement,           // @TODO delete
            'notify_email' => $model->notify_email,
        ];
        if ($model->relationExists('audit')) {
            $audit = $model->audit;
            if ($audit) {
                $ret['audit_id'] = $audit->id;
            }
        }
        if ($model->announcement) {
            $ret['execution_start'] = $this->formatExecutionTime($model, 'execution_start');
            $ret['execution_end'] = $this->formatExecutionTime($model, 'execution_end');
            $ret['is_execution_time'] = $model->is_execution_time;
            $ret['execution_period'] = $model->execution_period;
        }

        if (key_exists('views_count', $model->getAttributes())) {
            $ret['views_count'] = $model->views_count;
        }

        if ($model->relationExists('buildings')) {
            $ret['buildings'] = (new BuildingTransformer)->transformCollection($model->buildings);
        }
        if ($model->relationExists('quarters')) {
            $ret['quarters'] = (new QuarterTransformer)->transformCollection($model->quarters);
        }
        if ($model->relationExists('providers')) {
            $ret['providers'] = (new ServiceProviderTransformer)->transformCollection($model->providers);
        }
        if ($model->relationExists('likes')) {
            $ret['likes'] = (new LikeTransformer)->transformCollection($model->likes);
        }
        if ($model->relationExists('media')) {
            $ret['media'] = (new MediaTransformer)->transformCollection($model->media);
        }
        if ($model->relationExists('views')) {
            $ret['views'] = $model->views->sum('views');
        }
        if ($model->relationExists('announcement_email_receptionists')) {
            $ret['announcement_email_receptionists'] = (new AnnouncementEmailReceptionistTransformer())
                ->transformCollection($model->announcement_email_receptionists);
        }

        return $ret;
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
