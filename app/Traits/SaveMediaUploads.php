<?php

namespace App\Traits;

trait SaveMediaUploads
{
    public function saveMediaUploads(\App\Models\Model $model, $data, $disableAuditing = false)
    {
        $existingMedia = $model->media;
        if (empty($data['media'])) {
            $existingMedia->each(function ($media) {
                $media->delete();
            });
            return $model;
        }

        $media = $data['media'];
        if (is_string($media)) {
            $media = [
                $media
            ];
        }

        $deletedMedia = $existingMedia->whereNotIn('id', collect($media)->pluck('id'));
        $deletedMedia->each(function ($media) {
            $media->delete();
        });

        $audit = $model->relationExists('audit') ? $model->audit : null;
        $savedMedia = [];
        foreach ($media as $mediaData) {
            if (is_string($mediaData)) {
                $savedMedia[] = $this->uploadFile('media', $mediaData, $model, $audit, $disableAuditing);
            } elseif (!is_array($mediaData)) {
                continue;
            }
            if (isset($mediaData['media']) && is_string($mediaData['media'])) {
                $saved =  $this->uploadFile('media', $mediaData['media'], $model, $audit, $disableAuditing);
                if ($saved) {
                    $savedMedia[] = $saved;
                }
            }
        }

        $model->setRelation('media', $model->newCollection($savedMedia));
        return $model;
    }
}
