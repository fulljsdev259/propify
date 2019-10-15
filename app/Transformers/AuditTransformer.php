<?php

namespace App\Transformers;

use App\Models\Pinboard;
use App\Models\Listing;
use App\Models\Request;
use Illuminate\Database\Eloquent\Relations\Relation;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Arr;

/**
 * Class AuditTransformer.
 *
 * @package namespace App\Transformers;
 */
class AuditTransformer extends BaseTransformer
{
    /**
     * Transform the Audit entity.
     *
     * @param Audit $model
     *
     * @return array
     */
    public function transform(Audit $model)
    {
        $response = [
            'id' => $model->id,
            'event' => $model->event,
            'auditable_type' => $model->auditable_type,
            'auditable_id' => $model->auditable_id,
            'user_id' => $model->user_id,
            'url' => $model->url,
            'message' => $this->getMessage($model),
            'old_values' => $model->old_values,
            'new_values' => $model->new_values,
            'ip_address' => $model->ip_address,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];
        if ($model->user) {
            $response['user'] = (new UserTransformer())->transform($model->user);
        } else {
            $response['user'] = [
                'id' => $model->user_id,
                'name' => 'Deleted user',
                'email' => 'dummy@email.com',
                'phone' => '',
                'avatar' => '',
            ];
        }

        return $response;
    }

    /**
     * @param Audit $a
     * @return mixed|string
     */
    private function getMessage(Audit $a)
    {
        if ($this->getMorphedModel($a->auditable_type) == Pinboard::class) {
            return $this->getPinboardMessage($a);
        }

        if ($this->getMorphedModel($a->auditable_type) == Listing::class) {
            return $this->getListingMessage($a);
        }

        if ($this->getMorphedModel($a->auditable_type) == Request::class) {
            return $this->getRequestMessage($a);
        }

        return "unkown";
    }

    /**
     * @param Audit $a
     * @return mixed
     */
    private function getPinboardMessage(Audit $a)
    {
        if ($a->event == 'created' || $a->event == 'deleted') {
            return $a->event;
        }

        $sMsgs = [
            Pinboard::StatusNew . Pinboard::StatusPublished => "published",
            Pinboard::StatusUnpublished . Pinboard::StatusPublished => "published",
            Pinboard::StatusNotApproved . Pinboard::StatusPublished => "published",
            Pinboard::StatusPublished . Pinboard::StatusUnpublished => "unpublished",
        ];
        if (Arr::has($a->new_values, 'status') &&
            Arr::has($a->old_values, 'status')) {
            return $sMsgs[$a->old_values['status'] . $a->new_values['status']] ?? $a->event;
        }

        return $a->event;
    }

    /**
     * @param Audit $a
     * @return mixed
     */
    private function getListingMessage(Audit $a)
    {
        return $a->event;
    }

    /**
     * @param Audit $a
     * @return mixed
     */
    private function getRequestMessage(Audit $a)
    {
        if ($a->event == 'created' || $a->event == 'deleted') {
            return $a->event;
        }

        if (Arr::has($a->new_values, 'status')) {
            return Request::Status[$a->new_values['status']];
        }

        return $a->event;
    }

    /**
     * @param $alias
     * @return string|null
     */
    private function getMorphedModel($alias)
    {
        return Relation::getMorphedModel($alias) ?? $alias;
    }
}
