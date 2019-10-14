<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Settings\UpdateRequest;
use App\Http\Requests\API\Settings\ViewRequest;
use App\Models\Settings;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\SettingsRepository;
use App\Transformers\SettingsTransformer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Class RealEstateController
 * @package App\Http\Controllers\API
 */
class SettingsAPIController extends AppBaseController
{
    /** @var  SettingsRepository */
    private $settingsRepository;

    /** @var  AddressRepository */
    private $addressRepository;

    /**
     * RealEstateAPIController constructor.
     * @param SettingsRepository $settingsRepo
     * @param AddressRepository $addressRepo
     */
    public function __construct(SettingsRepository $settingsRepo, AddressRepository $addressRepo)
    {
        $this->settingsRepository = $settingsRepo;
        $this->addressRepository = $addressRepo;
    }

    /**
     * @SWG\Get(
     *      path="/settings",
     *      summary="Display the Settings",
     *      tags={"Settings"},
     *      description="Get Settings",
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
     *                  ref="#/definitions/Settings"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param ViewRequest $r
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function show(ViewRequest $r)
    {
        /** @var Settings $settings */
        $settings = $this->settingsRepository->first();
        if (empty($settings)) {
            return $this->sendError(__('settings.errors.not_found'));
        }

        $pinboardReceiverIds = $settings->pinboard_receiver_ids ?? [];
        $settings->pinboard_receivers = User::whereIn('id', $pinboardReceiverIds)->get();

        $response = (new SettingsTransformer)->transform($settings);
        return $this->sendResponse($response, 'Settings retrieved successfully');
    }

    /**
     * @SWG\Put(
     *      path="/settings",
     *      summary="Update the Settings in storage",
     *      tags={"Settings"},
     *      description="Update Settings",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Settings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Settings")
     *      ),
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
     *                  ref="#/definitions/Settings"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param UpdateRequest $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update(UpdateRequest $request)
    {
        $input = $request->all();
        if (key_exists('iframe_url', $input) && is_null($input['iframe_url'])) {
            $input['iframe_url'] = '';
        }

        /** @var Settings $settings */
        $settings = $this->settingsRepository->first();
        if (empty($settings)) {
            return $this->sendError(__('settings.errors.not_found'));
        }

        // image upload
        $logoFileData = base64_decode($request->get('logo_upload', ''));
        $circleLogoFileData = base64_decode($request->get('circle_logo_upload', ''));
        $residentLogoFileData = base64_decode($request->get('resident_logo_upload', ''));
        $faviconIconFileData = base64_decode($request->get('favicon_icon_upload', ''));

        try {
            if ($logoFileData) {
                $input['logo'] = $this->settingsRepository->uploadImage($logoFileData, $settings);
            }
            if ($circleLogoFileData) {
                $fileName = Str::slug(sprintf('%s-%d', $settings->name, $settings->id)) . '-circle-logo.png';
                $input['circle_logo'] = $this->settingsRepository->uploadImage($circleLogoFileData, $settings, $fileName);
            }
            if ($residentLogoFileData) {
                $fileName = Str::slug(sprintf('%s-%d', $settings->name, $settings->id)) . '-resident-logo.png';
                $input['resident_logo'] = $this->settingsRepository->uploadImage($residentLogoFileData, $settings, $fileName);
            }
            if ($faviconIconFileData) {
                $fileName = Str::slug(sprintf('%s-%d', $settings->name, $settings->id)) . '-favicon-icon.png';
                $input['favicon_icon'] = $this->settingsRepository->uploadImage($faviconIconFileData, $settings, $fileName);
            }
        } catch (\Exception $e) {
            return $this->sendError(__('models.user.errors.image_upload') . $e->getMessage());
        }

        try {
            if (isset($input['address'])) {
                $this->addressRepository->update($input['address'], $settings->address_id);
            }
            $input['address_id'] = $settings->address_id;
            if (isset($input['login_variation_2_slider'])) {
                $input['login_variation_2_slider'] = (int) $input['login_variation_2_slider'];
            }
            $settings = $this->settingsRepository->update($input, $settings->id);
            // Forget weather so the next request to weather
            // brings info from the new location
            Cache::forget('weather_json');
        } catch (\Exception $e) {
            return $this->sendError(__('settings.errors.update'));
        }
        $settings->pinboard_receivers = User::whereIn('id', $settings->pinboard_receiver_ids)->get();

        $response = (new SettingsTransformer)->transform($settings);
        return $this->sendResponse($response, __('settings.saved'));
    }
}
