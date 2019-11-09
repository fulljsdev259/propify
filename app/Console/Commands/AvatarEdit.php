<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Repositories\UserRepository;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager as Image;

class AvatarEdit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AvatarEdit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pixels = \AvatarHelper::AVATAR_SIZES;
        $users = User::whereNotNull('avatar')->get();
        foreach ($users as $user) {
            $avatar = explode('/', $user->avatar);
            $originalPath = storage_path('app/public/avatar/' . $avatar[count($avatar) - 1]);
            foreach ($pixels as $pixel) {
                $avatar = Str::slug(sprintf($user->name)) . '.png';
                $path = storage_path('app/public/avatar/' . $user->id . '/' . $pixel  . 'x' . $pixel);
                if (!file_exists($path)) {
                    \File::makeDirectory($path, $mode = 0777, true, true);
                }
                $imgPath = storage_path(sprintf('app/public/avatar/' . $user->id . '/' . $pixel . 'x' . $pixel . '/' . $avatar));
                (new Image)->make(file_get_contents($originalPath))->fit($pixel, $pixel)->save($imgPath);
            }
            if(\File::exists($user->avatar)){
                unlink($user->avatar);
            }else{
                $avatar = null;
            }
            $user->avatar = $avatar;
            $user->save();
        }
        echo 'Avatar Paths Recreated!';
        return;
    }
}
