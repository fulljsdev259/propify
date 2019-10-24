<?php

namespace App\Traits;

trait OldChagesAttribute
{
    /**
     * @var
     */
    protected $oldChanges = [];

    /**
     * Sync the changed attributes.
     *
     * @return $this
     */
    public function syncChanges()
    {
        $this->changes = $this->getDirty();
        foreach (array_keys($this->changes) as $attribute) {
            $this->oldChanges[$attribute] = $this->getOriginal($attribute);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOldChanges()
    {
        return $this->oldChanges;
    }

}
