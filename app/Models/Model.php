<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use App\Traits\OldChagesAttribute;

/**
 * App\Models\Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Model query()
 * @mixin \Eloquent
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    use OldChagesAttribute, BaseModelTrait;

    public function emptyAppends()
    {
        $this->appends = [];
        return $this;
    }
}
