<?php

namespace App\Models;

class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var array
     */
    protected $permittedExtensions = [];

    // relationExists returns whether the relation named $key exists, is loaded
    // and is not null
    public function relationExists($key)
    {
        return parent::relationLoaded($key) && isset($this->$key);
    }

    /**
     * @return string
     */
    public function getDiskPreName()
    {
        return $this->getTable() . '_';
    }

    /**
     * @return array
     */
    public function getExistingRelations()
    {
        return array_keys($this->relations);
    }

    /**
     * @return array
     */
    public function getPermittedExtensions()
    {
        return $this->permittedExtensions;
    }

    /**
     * @param $mimeType
     * @return bool|false|int|string
     */
    public function getExtension($mimeType)
    {
        $extension = array_search($mimeType, config('filesystems.mime_types'));
        return in_array($extension, $this->permittedExtensions) ? $extension : false;
    }
}
