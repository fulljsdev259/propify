<?php

namespace App\Providers;

use App\Mails\NewRequestForReceptionist;
use App\Mails\NotifyServiceProvider;
use App\Models\Building;
use App\Models\Quarter;
use App\Models\Pinboard;
use App\Models\Listing;
use App\Models\PropertyManager;
use App\Models\Settings;
use App\Models\Request;
use App\Models\Template;
use App\Models\Conversation;
use App\Models\Resident;
use App\Models\Contract;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\NewResidentInNeighbour;
use App\Notifications\NewResidentPinboard;
use App\Notifications\NewResidentRequest;
use App\Notifications\AnnouncementPinboardPublished;
use App\Notifications\PinboardCommented;
use App\Notifications\PinboardLiked;
use App\Notifications\PinboardPublished;
use App\Notifications\ListingPublished;
use App\Notifications\RequestCommented;
use App\Notifications\RequestDue;
use App\Notifications\RequestMedia;
use App\Notifications\ResidentCredentials;
use App\Notifications\StatusChangedRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Audit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	    Schema::defaultStringLength(191);

        Audit::creating(function (Audit $model) {
            if (empty($model->old_values) && empty($model->new_values)) {
                return false;
            }
        });

	    Relation::morphMap([
            'unit' => Unit::class,
            'user' => User::class,
            'pinboard' => Pinboard::class,
            'resident' => Resident::class,
            'listing' => Listing::class,
            'quarter' => Quarter::class,
            'building' => Building::class,
            'templates' => Template::class,
            'request' => Request::class,
            'settings' => Settings::class,
            'manager' => PropertyManager::class,
            'translation' => \App\Models\Translation::class,
            'provider' => \App\Models\ServiceProvider::class,
            'contract' => Contract::class,
            'conversation' => Conversation::class,

            'pinboard_published' => PinboardPublished::class,
            'new_resident_pinboard' => NewResidentPinboard::class,
            'announcement_pinboard_published' => AnnouncementPinboardPublished::class,
            'new_resident_in_neighbour' => NewResidentInNeighbour::class,
            'listing_published' => ListingPublished::class,
            'new_resident_request' => NewResidentRequest::class,
            'status_change_request' => StatusChangedRequest::class,
            'new_request_for_receptionist' => NewRequestForReceptionist::class,
            'request_due' => RequestDue::class,
            'request_commented' => RequestCommented::class,
            'request_media' => RequestMedia::class,
            'pinboard_liked' => PinboardLiked::class,
            'pinboard_commented' => PinboardCommented::class,
            'resident_credentials' => ResidentCredentials::class,
            'notify_service_provider' => NotifyServiceProvider::class,
        ]);

        if (!Collection::hasMacro('paginate')) {
            Collection::macro('paginate', function ($perPage = 15, $page = null, $options = []) {
                $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                $paginator = new LengthAwarePaginator($this->forPage($page, $perPage), $this->count(), $perPage, $page, $options);
                return $paginator->withPath(\Request::url());
            });
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Cog\Contracts\Love\Like\Models\Like::class,
            \App\Models\Like::class
        );
    }
}
