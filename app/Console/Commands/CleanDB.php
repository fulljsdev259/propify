<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingAssignee;
use App\Models\CleanifyRequest;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Country;
use App\Models\InternalNotice;
use App\Models\Like;
use App\Models\LoginDevice;
use App\Models\Media;
use App\Models\Permission;
use App\Models\Pinboard;
use App\Models\PinboardView;
use App\Models\AnnouncementEmailReceptionist;
use App\Models\Listing;
use App\Models\PropertyManager;
use App\Models\Quarter;
use App\Models\QuarterAssignee;
use App\Models\Settings;
use App\Models\Contract;
use App\Models\Role;
use App\Models\ServiceProvider;
use App\Models\Request;
use App\Models\RequestAssignee;
use App\Models\RequestCategory;
use App\Models\State;
use App\Models\Tag;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Models\Translation;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CleanDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean unnecessary data';

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
        $conversations = $this->getAllMorphTable('conversationable_id', 'conversationable_type', Conversation::class);
        $comments = $this->getAllMorphTable('commentable_id', 'commentable_type', Comment::class);
        $media = $this->getAllMorphTable('model_id', 'model_type', Media::class);
        $translations = $this->getAllMorphTable('object_id', 'object_type', [Translation::class,]);

        $notifications = $this->getMorphTable('notifiable_id', 'notifiable_type', [User::class,]);


        $audits = $this->getAllMorphTable('auditable_id', 'auditable_type');
        $audits[] = [
            'relation' => (new User())->getTable(),
            'conditions' => [
                'user_type' => get_morph_type_of(User::class)
            ],
        ];

        $likes = $this->getAllMorphTable('likeable_id', 'likeable_type', [Like::class]);
        $likeCounts = $likes;
        $likes[] = [
            'relation' => (new User())->getTable(),
        ];



        $buildingAssignees = $this->getMorphTable('assignee_id', 'assignee_type', [
                User::class,
                PropertyManager::class,
            ]);
        $buildingAssignees[] = [
            'relation' => (new Building())->getTable(),
        ];


        $quarterAssignees = $this->getMorphTable('assignee_id', 'assignee_type', [
            User::class,
            PropertyManager::class,
        ]);
        $quarterAssignees[] = [
            'relation' => (new Quarter())->getTable(),
        ];



        $requestAssignees = $this->getMorphTable('assignee_id', 'assignee_type', [
            User::class,
            PropertyManager::class,
            ServiceProvider::class,
        ]);
        $requestAssignees[] = [
            'relation' => (new Request())->getTable(),
            'relation_id' => 'request_id'
        ];



        $config = [
            'audits' => $audits,
            'autologins' => [
                'relation' => (new User())->getTable(),
            ],
            'buildings' => [
                [
                    'relation' => (new Quarter())->getTable(),
                ],
                [
                    'relation' => (new Address())->getTable(),
                    'relation_id' => 'address_id'
                ]
            ],
            'cleanify_requests' => [
                'relation' => (new User())->getTable(),
            ],
            'conversations' => $conversations,
            'comments' => $comments,
            'building_assignees' => $buildingAssignees,
            'internal_notices' => [
                [
                    'relation' => (new User())->getTable(),
                ],
                [
                    'relation' => (new Request())->getTable(),
                    'relation_id' => 'request_id'
                ],
            ],
            'love_likes' => $likes,
            'love_like_counters' => $likeCounts,
            'media' => $media,
            'notifications' => $notifications,
            'oauth_access_tokens' => [
                'relation' => (new User())->getTable(),
            ],
            'quarter_assignees' => $quarterAssignees,
            'request_assignees' => $requestAssignees,
            'translations' => $translations
        ];


        foreach ($config as $table => $data) {
            if (Arr::isAssoc($data)) {
                $query = $this->getQuery($table, $data);
                DB::statement($query);
            } else {
                foreach ( $data as $_data) {
                    $query = $this->getQuery($table, $_data);
                    DB::statement($query);
                }
            }
        }

        echo 'final';

    }

    /**
     * @param $table
     * @param $data
     * @return string
     */
    protected function getQuery($table, $data)
    {
        $relation = $data['relation'];
        $relationId = $data['relation_id'] ?? Str::singular($relation) . '_id';
        $relationTableId = $data['relation_table_id'] ?? 'id';

        $query = 'DELETE FROM `' . $table
            . '` WHERE `' . $relationId .
            '` NOT IN (SELECT `' . $relationTableId. '` FROM `' .  $relation .'`)';
        if (!empty($data['conditions'])) {
            foreach ( $data['conditions'] as $condition => $value) {
                $query .= ' and `' . $condition . '` = "' . $value . '"';
            }
        }
        echo $query . PHP_EOL;
        echo '-----------------------' . PHP_EOL;
        return $query;
    }

    /**
     * @param $relatedId
     * @param $relatedType
     * @param $classes
     * @return array
     */
    protected function getMorphTable($relatedId, $relatedType, $classes)
    {

        $data = [];
        foreach ($classes as $class) {
            $data[] = [
                'relation' => (new $class)->getTable(),
                'relation_id' => $relatedId,
                'conditions' => [
                    $relatedType => get_morph_type_of($class)
                ],
            ];
        }

        return $data;
    }

    /**
     * @param $relatedId
     * @param $relatedType
     * @param array $exclude
     * @return array
     */
    protected function getAllMorphTable($relatedId, $relatedType, $exclude = [])
    {
        $classes = [
            Address::class,
            Building::class,
            BuildingAssignee::class,
            CleanifyRequest::class,
            Comment::class,
            Conversation::class,
            Country::class,
            InternalNotice::class,
            Like::class,
            LoginDevice::class,
            Media::class,
            Permission::class,
            Pinboard::class,
            PinboardView::class,
            AnnouncementEmailReceptionist::class,
            Request::class,
            Listing::class,
            PropertyManager::class,
            Quarter::class,
            QuarterAssignee::class,
            Settings::class,
            Contract::class,
            Role::class,
            ServiceProvider::class,
            Request::class,
            RequestAssignee::class,
            RequestCategory::class,
            State::class,
            Tag::class,
            Template::class,
            TemplateCategory::class,
            Translation::class,
            Unit::class,
            User::class,
            UserSettings::class,
        ];

        $exclude = is_array($exclude) ? $exclude : [$exclude];

        $classes = array_diff($classes, $exclude);
        return $this->getMorphTable($relatedId, $relatedType, $classes);
    }
}
