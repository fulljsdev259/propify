<?php

namespace App\Transformers;

use App\Models\AnnouncementEmailReceptionist;
use App\Models\Building;
use App\Models\Quarter;
use App\Models\Resident;
use Illuminate\Support\Str;

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
        $residents = Resident::whereIn('id', $residentIds)
            ->select(['id', 'first_name', 'last_name'])
            ->withCount([
                'pinboard_views' => function ($q) use ($model) {
                    $q->where('pinboard_id', $model->pinboard_id);
                }
            ])
            ->get();
        $quarterResidents = $this->getResidentsDataBy($residentsData, $residents, 'quarters', Quarter::class);
        $buildingResidents = $this->getResidentsDataBy($residentsData, $residents, 'buildings', Building::class);
        $residentsResponse = array_merge($quarterResidents, $buildingResidents);

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
        $items = $class::whereIn('id', array_keys($residentsData[$key]))
            ->select('id', 'name', 'address_id')
            ->with('address:id,street,house_num')
            ->get();
        foreach ($residentsData[$key] as $id => $residentIds) {
            $item = $items->where('id', $id)->first();

            foreach ($residentIds as $residentId) {
                $resident = $residents->where('id', $residentId)->first();
                if (empty($resident)) {
                    // when resident deleted
                    continue;
                }

                $resident->building_or_quarter = ! empty($item->address)
                    ? $item->address->street . ' ' . $item->address->house_num
                    : '';
                $residentsResponse[] = $resident->emptyAppends()->toArray();
            }
        }

        return $residentsResponse;
    }
}
