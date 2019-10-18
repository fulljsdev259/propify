<?php

namespace App\Transformers;

use App\Models\AnnouncementEmailReceptionist;
use App\Models\Resident;

/**
 * Class AnnouncementEmailReceptionistTransformer.
 *
 * @package namespace App\Transformers;
 */
class AnnouncementEmailReceptionistTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the Pinboard entity.
     *
     * @param \App\Models\AnnouncementEmailReceptionist $model
     *
     * @return array
     */
    public function transform(AnnouncementEmailReceptionist $model)
    {
        $response = [
            'pinboard_id' => $model->pinboard_id,
            'resident_ids' => $model->resident_ids,
            'failed_resident_ids' => $model->failed_resident_ids,
        ];
        // @TODO improve load resident data other place
        $residents = Resident::whereIn('id', $model->resident_ids)->get(['id', 'first_name', 'last_name']);
        $response['residents'] = $residents->toArray();
        return $response;
    }
}
