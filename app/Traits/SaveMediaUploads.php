<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Support\Arr;

trait SaveMediaUploads
{
    /**
     * @param \App\Models\Model $model
     * @param $data
     * @return \App\Models\Model
     */
    public function saveMediaUploads(\App\Models\Model $model, $data)
    {
        $audit = $this->getAudit($model);
        $existingMedia = $model->media;

        if (empty($data['media'])) {
            $this->deleteOldImages($existingMedia, $audit);
            $model->addMediaInAudit($audit, $existingMedia, collect(), $existingMedia);

            return $model;
        }

        $media = Arr::wrap($data['media']);
        $deletedMedia = $existingMedia->whereNotIn('id', collect($media)->pluck('id'));
        $this->deleteOldImages($deletedMedia, $audit);

        $savedMedia = [];
        foreach ($media as $mediaData) {
            if (is_string($mediaData)) {
                $savedMedia[] = $this->uploadFile('media', $mediaData, $model, null, true);
            } elseif (!is_array($mediaData)) {
                continue;
            }
            if (isset($mediaData['media']) && is_string($mediaData['media'])) {
                $saved =  $this->uploadFile('media', $mediaData['media'], $model, null, true);
                if ($saved) {
                    $savedMedia[] = $saved;
                }
            }
        }

        $savedMedia = $model->newCollection($savedMedia);
        $model->addMediaInAudit($audit, $existingMedia, $savedMedia, $deletedMedia);
        $model->setRelation('media', $savedMedia);
        return $model;
    }

    /**
     * @param $model
     * @return null
     */
    protected function getAudit($model)
    {
        if ($model->wasRecentlyCreated) {
            return null; // not need merge in audit
        }

        if ($model->relationExists('audit')) {
            return $model->audit;
        }

        return $model->makeNewAudit();
    }

    /**
     * @param $oldMedias
     * @param $model
     * @param $audit
     * @return bool
     */
    protected function deleteOldImages($oldMedias, $audit)
    {
        if ($oldMedias->isEmpty()) {
            return true;
        }

        if ($audit) {
            Media::disableAuditing();
        }
        $oldMedias->each(function ($media) {
            $media->delete();
        });

        if ($audit) {
            Media::enableAuditing();
        }
        return true;
    }
}
