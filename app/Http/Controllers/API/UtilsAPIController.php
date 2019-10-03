<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\AppBaseController;
use App\Models\Pinboard;
use App\Models\Listing;
use App\Models\PropertyManager;
use App\Models\ServiceProvider;
use App\Models\Request;
use App\Models\TemplateCategory;
use App\Models\Tenant;
use App\Repositories\BuildingRepository;
use App\Repositories\TenantRepository;
use App\Repositories\UnitRepository;
use Illuminate\Http\Response;

/**
 * Class UtilsAPIController
 * @package App\Http\Controllers\API
 */
class UtilsAPIController extends AppBaseController
{
    /** @var  BuildingRepository */
    private $buildingRepository;

    /** @var  UnitRepository */
    private $unitRepository;

    /** @var  TenantRepository */
    private $tenantRepository;

    /**
     * UtilsAPIController constructor.
     * @param BuildingRepository $buildingRepo
     * @param UnitRepository $unitRepo
     * @param TenantRepository $tenantRepo
     */
    public function __construct(BuildingRepository $buildingRepo, UnitRepository $unitRepo, TenantRepository $tenantRepo)
    {
        $this->buildingRepository = $buildingRepo;
        $this->unitRepository = $unitRepo;
        $this->tenantRepository = $tenantRepo;
    }

    /**
     * @return Response
     *
     * @SWG\Get(
     *      path="/constants",
     *      summary="Display the app constants",
     *      description="Get he app constants",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Building"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function constants()
    {
        $translations = __('general.languages');
        $languages = config('app.locales');
        foreach ($languages as $key => $__) {
            $languages[$key] = $translations[$key] ?? $languages[$key];
        }

        $app = [
            'languages' => $languages,
        ];

        $settings = App\Models\Settings::first(['login_variation', 'login_variation_2_slider', 'primary_color', 'primary_color_lighter', 'accent_color', 'logo', 'circle_logo', 'tenant_logo', 'favicon_icon']);

        if ($settings) {
            $colors = $settings->only(['primary_color', 'accent_color', 'primary_color_lighter']);
            $logo = $settings->only(['logo', 'circle_logo', 'favicon_icon', 'tenant_logo']);
            $login = [
                'variation' => $settings->login_variation,
                'variation_2_slider' => (bool) $settings->login_variation_2_slider,
            ];
        } else {
            $colors = [
                'primary_color_lighter' => '#c55a9059',
                'primary_color' => '#6AC06F',
                'accent_color' => '#F7CA18'
            ];
            $logo = [
                'logo' => null,
                'circle_logo' => null,
                'favicon_icon' => null,
                'tenant_logo' => null,
            ];
            $login = [
                'variation' => 1,
                'variation_2_slider' => true,
            ];
        }
        $response = [
            'app' => $app,
            'buildings' => [], // @TODO is need return building related constants
            'units' => $this->getUnitConstants(),
            'tenants' => $this->getTenantConstants(),
            'rentContracts' => $this->getRentContractConstants(),
            'serviceProviders' => $this->getServiceProviderConstants(),
            'requests' => $this->getRequestsConstants(),
            'serviceRequests' => $this->getRequestsConstants(), // @TODO delete
            'propertyManager' => $this->getPropertyManagerConstants(),
            'pinboard' => $this->getPinboardConstants(),
            'products' => $this->getListingConstants(), // @TODO delete
            'listings' => $this->getListingConstants(),
            'templates' => $this->getTemplateConstants(),
            'audits' => $this->getAuditConstants(),
            'colors' => $colors,
            'logo' => $logo,
            'login' => $login
        ];

        return $this->sendResponse($response, 'App constants statistics retrieved successfully');
    }

    /**
     * @return array|false
     */
    protected function getAuditConstants()
    {
        $events = App\Models\AuditableModel::Events;
        return array_combine($events, $events);
    }

    /**
     * @return array
     */
    protected function getTenantConstants()
    {
        $result = [
            'title' => Tenant::Title,
            'status' => Tenant::Status,
            'client_type' => Tenant::ClientType,
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected function getRentContractConstants()
    {
        $result = [
            'type' => App\Models\RentContract::Type,
            'duration' => App\Models\RentContract::Duration,
            'status' => App\Models\RentContract::Status,
            'deposit_type' => App\Models\RentContract::DepositType,
            'deposit_status' => App\Models\RentContract::DepositStatus,
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected function getUnitConstants()
    {
        $result = [
            'type' => App\Models\Unit::Type,
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected function getServiceProviderConstants()
    {
        $result = [
            'category' => ServiceProvider::ServiceProviderCategory,
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected function getRequestsConstants()
    {
        $result = [
            'status' => Request::Status,
            'priority' => Request::Priority,
            'internal_priority' => Request::Priority,
            'qualification' => Request::Qualification,
            'statusByTenant' => Request::StatusByTenant,
            'statusByService' => Request::StatusByService,
            'statusByAgent' => Request::StatusByAgent,
            'visibility' => Request::Visibility,
            'location' => Request::Location,
            'room' => Request::Room,
            'capture_phase' => Request::CapturePhase,
            'payer' => Request::Payer,
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected function getPropertyManagerConstants()
    {
        $result = [
            'title' => PropertyManager::Title,
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected function getPinboardConstants()
    {
        $result = [
            'type' => Pinboard::Type,
            'sub_type' => Pinboard::SubType,
            'visibility' => Pinboard::Visibility,
            'status' => Pinboard::Status,
            'category' => Pinboard::Category,
            'execution_period' => Pinboard::ExecutionPeriod,
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected function getListingConstants()
    {
        $result = [
            'type' => Listing::Type,
            'visibility' => Listing::Visibility,
            'status' => Listing::Status,
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected function getTemplateConstants()
    {
        $result = [
            'type' => TemplateCategory::Type,
        ];

        return $result;
    }
}
