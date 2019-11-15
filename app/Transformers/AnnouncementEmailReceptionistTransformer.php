<?php

namespace App\Transformers;

use App\Models\AnnouncementEmailReceptionist;
use App\Models\Quarter;
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
        // @TODO improve load resident data other place
        // @TODO in case when quarter deleted, building deleted
        $residentsData = $model->residents_data ?? [];
        $residentIds = collect($residentsData)->collapse()->collapse();
        $residents = Resident::whereIn('id', $residentIds)->get(['id', 'first_name', 'last_name']);
        $residentsResponse = $this->getResidentsDataBy($residentsData, $residents, 'quarters', Quarter::class);

        $response = [
            'pinboard_id' => $model->pinboard_id,
            'residents' => $residentsResponse,
            'failed_resident_ids' => $model->failed_resident_ids,
        ];
        $response['residents'] = $residentsResponse;
        return $response;
    }

    /**
     * @param $residentsData
     * @param $residents
     * @param $key
     * @param $class
     * @return array
     */
    protected function getResidentsDataBy($residentsData, $residents, $key, $class)
    {
        if (empty($residentsData[$key])) {
            return [];
        }

        $residentsResponse = [];
        $quarters = $class::whereIn('id', array_keys($residentsData[$key]))
            ->select('id', 'name', 'address_id')
            ->with('address:id,street,house_num')
            ->get();
        foreach ($residentsData[$key] as $quarterId => $quarterResidentIds) {
            $quarter = $quarters->where('id', $quarterId)->first();

            foreach ($quarterResidentIds as $residentId) {
                $resident = $residents->where('id', $residentId)->first();
                if (empty($resident)) {
                    // when resident deleted
                    continue;
                }

                $resident->setRelation('quarter', $quarter);
                $residentsResponse[] = $resident->toArray();
            }
        }

        return $residentsResponse;
    }
}
