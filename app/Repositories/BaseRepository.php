<?php

namespace App\Repositories;

use App\Models\AuditableModel;
use App\Models\Media;
use App\Models\Model;
use OwenIt\Auditing\Models\Audit;
use Prettus\Repository\Events\RepositoryEntityUpdated;

abstract class BaseRepository extends \Prettus\Repository\Eloquent\BaseRepository
{
    /**
     * @var array
     */
    protected $mimeToExtension = [];

    /**
     * @param string $collectionName
     * @param string $dataBase64
     * @param Model $model
     * @param null $mergeInAudit
     * @param bool $disableAuditing
     * @return bool
     */
    public function uploadFile(string $collectionName, string $dataBase64, Model $model, $mergeInAudit = null, $disableAuditing = false)
    {
        if (!$data = base64_decode($dataBase64)) {
            return false;
        }

        $file = finfo_open();
        $mimeType = finfo_buffer($file, $data, FILEINFO_MIME_TYPE);
        finfo_close($file);

        $extension = $this->getExtension($mimeType, $model);
        if (empty($extension)) {
            return false;
        }

        if ($mergeInAudit) {
            $audit = is_integer($mergeInAudit)
                ? Audit::where('auditable_type', get_morph_type_of($model))->find($mergeInAudit)
                : $mergeInAudit;
            if (empty($audit)) {
                return false;
            }
            $disableAuditing = true;
        }

        if ($disableAuditing) {
            Media::disableAuditing();
        }


        $diskName = $model->getDiskPreName() . $collectionName;
        $media = $model->addMediaFromBase64($dataBase64)
            ->sanitizingFileName(function ($fileName) use ($extension) {
                return sprintf('%s.%s', str_slug($fileName), $extension);
            })
            ->toMediaCollection($collectionName, $diskName);

        if ($disableAuditing) {
            Media::enableAuditing();
        }

        if ($mergeInAudit) {
            (new AuditableModel())->addDataInAudit('media', $media, $audit, false);
        }

        return $media;
    }

    public function doesntHave($relation)
    {
        $this->model = $this->model->doesntHave($relation);
        return $this;
    }

    public function scope($scopeName)
    {
        $this->model = $this->model->{$scopeName}();
        return $this;
    }

    public function findWithoutFail($id, $columns = ['*'])
    {
        try {
            return $this->find($id, $columns);
        } catch (\Exception $e) {
            return;
        }
    }


    /**
     * @param Model $model
     * @param $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateExisting(Model $model, $attributes)
    {
        $model->fill($attributes);
        $model->save();
        $this->resetModel();
        event(new RepositoryEntityUpdated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * @param $data
     * @param $key
     * @param int $default
     * @return mixed
     */
    protected function fixBollInt($data, $key, $default = 0)
    {
        if (key_exists($key, $data)) {
            $data[$key]  = (int) ('true' == $data[$key] || true == $data[$key]);
        } else {
            $data[$key] = $default;
        }
        return $data;
    }

    /**
     * @param $model
     * @param $mimeType
     * @return mixed
     */
    protected function getExtension($mimeType, $model)
    {
        return $this->mimeToExtension[$mimeType] ?? '';
    }
}
