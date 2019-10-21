<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Facades\Auditor;
use Spatie\MediaLibrary\Models\Media as SpatieMedia;

/**
 * App\Models\Media
 *
 * @SWG\Definition (
 *      definition="Media",
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="collection_name",
 *          description="collection_name",
 *          type="string",
 *      ),
 *      @SWG\Property(
 *          property="file_name",
 *          description="file_name",
 *          type="string",
 *      ),
 *      @SWG\Property(
 *          property="mime_type",
 *          description="mime_type",
 *          type="string",
 *      ),
 *      @SWG\Property(
 *          property="disk",
 *          description="disk",
 *          type="string",
 *      ),
 *      @SWG\Property(
 *          property="order_column",
 *          description="order_column",
 *          type="string",
 *      ),
 *      @SWG\Property(
 *          property="size",
 *          description="size",
 *          type="string",
 *      ),
 * )
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property int $size
 * @property array $manipulations
 * @property array $custom_properties
 * @property array $responsive_images
 * @property int|null $order_column
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read mixed $extension
 * @property-read mixed $human_readable_size
 * @property-read mixed $type
 * @property-read \App\Models\Media $model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\MediaLibrary\Models\Media ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCustomProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereManipulations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereResponsiveImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class Media extends SpatieMedia implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
     * @var array
     */
    protected $auditEvents = [
        AuditableModel::EventMediaUploaded,
        AuditableModel::EventMediaDeleted,
    ];

    /**
     * @var array
     */
    protected $auditInclude = [
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'size',
        'order_column',
    ];

    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            Auditor::execute($model->setAuditEvent(AuditableModel::EventMediaUploaded));
        });

        static::deleted(function ($model) {
            Auditor::execute($model->setAuditEvent(AuditableModel::EventMediaDeleted));
        });
        // @TODO check deleted related file or not
    }

    /**
     * @return array
     */
    public function getMedia_uploadedEventAttributes()
    {
        $values = $this->getCreatedEventAttributes();
        $values[1]['media_id'] = $this->id;
        $values[1]['media_url'] = $this->getFullUrl();
        return $values ;
    }

    /**
     * @return array
     */
    public function getMedia_deletedEventAttributes()
    {
        $values = $this->getDeletedEventAttributes();
        $values[0]['media_id'] = $this->id;
        $values[0]['media_url'] = $this->getFullUrl();
        return $values ;
    }

    /**
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        $data['auditable_id'] = $this->model_id;
        $data['auditable_type'] = $this->model_type;
        return $data;
    }

    public function getExistingRelations()
    {
        return array_keys($this->relations);
    }
}
