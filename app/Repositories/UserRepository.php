<?php

namespace App\Repositories;

use App;
use App\Jobs\NewAdminNotification;
use App\Models\Model;
use App\Models\User;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager as Image;
use Prettus\Repository\Events\RepositoryEntityUpdated;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version January 11, 2019, 12:27 pm UTC
 *
 * @method User findWithoutFail($id, $columns = ['*'])
 * @method User find($id, $columns = ['*'])
 * @method User first($columns = ['*'])
 */
class UserRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name' => 'like',
        'email' => 'like',
        'phone' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['settings'] = $attributes['settings'] ?? [];

        $model = parent::create($attributes);

        //add user role
        if (!isset($attributes['role'])) {
            $attributes['role'] = 'resident';
        }

        $role = (new RoleRepository(app()))->getRoleByName($attributes['role']);
        $model->attachRole($role);

        $settings = Arr::pull($attributes, 'settings');
        $settings = $model->settings()->create($settings);
        $model->setRelation('settings', $settings);

        if (in_array($role->name, ['administrator'])) {
            dispatch(new NewAdminNotification($model));
        }

        return $model;
    }

    /**
     * @param array $attributes
     * @param $id
     * @return Model|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $attributes, $id)
    {
        $model = $this->model->findOrFail($id);
        return $this->updateExistingUser($model, $attributes);
    }

    /**
     * @param User $model
     * @param $attributes
     * @return User
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateExistingUser(User $model, $attributes)
    {
        if (isset($attributes['password']) && !empty($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }

        $model->fill($attributes);
        $model->save();
        $this->resetModel();
        event(new RepositoryEntityUpdated($this, $model));

        //change user role
        if (isset($attributes['role'])) {
            $role = (new RoleRepository(app()))->getRoleByName($attributes['role']);
            if ($role) {
                $model->detachRoles();
                $model->attachRole($role);
            }
        }

        $settings = Arr::pull($attributes, 'settings');
        if ($settings) {
            $model->settings()->update($settings);
        }

        return $model;
    }

    // TODO: move function to media repository

    /**
     * @param string $fileData
     * @param User $user
     * @return string
     */
    public function uploadImage(string $fileData, User $user)
    {
        $avatar = Str::slug(sprintf('%s-%d', $user->name, $user->id)) . '.png';
        $imgPath = storage_path(sprintf('app/public/avatar/%s', $avatar));

        (new Image)->make($fileData)->encode('png', 100)->fit(800, 800)->save($imgPath);

        return sprintf('storage/avatar/%s', $avatar);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function resetPassword(array $attributes, $id)
    {
        $attributes['password'] = bcrypt($attributes['password']);
        $model = parent::update($attributes, $id);
        $model->notify(new PasswordResetSuccess($model));

        return $model;
    }

    /**
     * @return mixed
     */
    public function exists()
    {
        $this->applyCriteria();
        return $this->model->exists();
    }
}
