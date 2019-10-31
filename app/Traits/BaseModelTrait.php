<?php

namespace App\Traits;

trait BaseModelTrait
{
    /**
     * @var array
     */
    protected $permittedExtensions = [];


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

    /**
     * @return array
     */
    public function getExistingRelations()
    {
        return array_keys($this->relations);
    }

    // relationExists returns whether the relation named $key exists, is loaded
    // and is not null
    public function relationExists($key)
    {
        return parent::relationLoaded($key) && isset($this->$key);
    }

    /**
     * @param $relation
     */
    public function setHasRelation($relation)
    {
        $this->setAttribute('has_' . $relation, $this->{$relation}()->exists());
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        return key_exists($attribute, $this->getAttributes());
    }
}
