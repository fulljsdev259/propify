<?php

namespace App\Repositories;

use App\Models\UserFilter;

/**
 * Class UserFilterRepository
 * @package App\Repositories
 * @version January 28, 2019, 8:32 am UTC
 *
 * @method UserFilter findWithoutFail($id, $columns = ['*'])
 * @method UserFilter find($id, $columns = ['*'])
 * @method UserFilter first($columns = ['*'])
*/
class UserFilterRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserFilter::class;
    }
}
