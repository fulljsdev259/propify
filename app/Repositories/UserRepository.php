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
use OwenIt\Auditing\Models\Audit;
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
        $model->setRelation('role', $role);

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
                $result = $model->roles()->sync($role);
                // Need for audit
                if (!empty($result['detached'])) {
                    $oldRoleName = App\Models\Role::whereIn('id', $result['detached'])->value('name');
                    $role->setOldChanges(['role' => $oldRoleName]);
                }
                // for audit
                $model->setRelation('role', $role);
            }
        }

        $settings = Arr::pull($attributes, 'settings');
        if ($settings) {
            $userSettings = $model->settings;
            $userSettings->update($settings);
            // for audit
            $model->setRelation('settings', $userSettings);
        }

        return $model;
    }

    // TODO: move function to media repository

    /**
     * @param string $fileData
     * @param User $user
     * @param null $mergeInAudit
     * @return string
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function uploadImage(string $fileData, User $user, $mergeInAudit = null)
    {
        $pixels = \AvatarHelper::AVATAR_SIZES;
        $avatar = Str::slug(sprintf($user->name)) .'.png';

        foreach($pixels as $pixel){
            $path = storage_path('app/public/avatar/'.$user->id .'/'. $pixel  . 'x' . $pixel);
            if(!file_exists($path)){
                \File::makeDirectory($path, $mode = 0777, true, true);
            }
            $imgPath = storage_path(sprintf('app/public/avatar/' .$user->id .'/' . $pixel . 'x' . $pixel . '/' . $avatar));
            (new Image)->make($fileData)->encode('png', 100)->fit($pixel, $pixel)->save($imgPath);
        }

        if ($mergeInAudit) {
            $audit = is_integer($mergeInAudit)
                ? Audit::find($mergeInAudit)
                : $mergeInAudit;
            (new App\Models\AuditableModel())->addDataInAudit('avatar', $avatar, $audit);
        }

        return sprintf($avatar);
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
