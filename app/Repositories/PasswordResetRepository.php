<?php

namespace App\Repositories;

use App;
use App\Models\PasswordReset;
use App\Notifications\PasswordResetRequest;

/**
 * @TODO delete not used
 *
 * Class UserRepository
 * @package App\Repositories
 * @version January 11, 2019, 12:27 pm UTC
 *
 * @method PasswordReset findWithoutFail($id, $columns = ['*'])
 * @method PasswordReset find($id, $columns = ['*'])
 * @method PasswordReset first($columns = ['*'])
 */
class PasswordResetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'email',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PasswordReset::class;
    }

    /**
     * @param array $attributes
     * @param App\Models\User $user
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createToken(array $attributes, App\Models\User $user)
    {
        $model = parent::updateOrCreate(['email' => $attributes['email']], $attributes);

        $model->notify(
            new PasswordResetRequest($user, $model)
        );

        return $model;
    }

    public function findToken(string $token)
    {
        $model = parent::findWhere(['token' => $token])->first();
        return $model;
    }
}
