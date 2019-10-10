<?php

namespace App\Transformers;

use App\Models\Settings;
use League\Fractal\TransformerAbstract;

/**
 * Class SettingsTransformer.
 *
 * @package namespace App\Transformers;
 */
class SettingsTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'user',
        'address',
    ];

    /**
     * Transform the Settings entity.
     *
     * @param Settings $model
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function transform(Settings $model)
    {
        $response = [
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'phone' => $model->phone,
            'language' => $model->language,
            'logo' => $model->logo,
            'circle_logo' => $model->circle_logo,
            'tenant_logo' => $model->resident_logo, // @TODO delete
            'resident_logo' => $model->resident_logo,
            'favicon_icon' => $model->favicon_icon,
            'pdf_font_family' => $model->pdf_font_family,
            'login_variation' => $model->login_variation,
            'login_variation_2_slider' => (bool) $model->login_variation_2_slider,
            'blank_pdf' => $model->blank_pdf,
            'quarter_enable' => $model->quarter_enable,
            'contact_enable' => $model->contact_enable,
            'gocaution_enable' => $model->gocaution_enable,
            'cleanify_enable' => $model->cleanify_enable,
            'listing_approval_enable' => $model->listing_approval_enable,
            'pinboard_approval_enable' => $model->pinboard_approval_enable,
            'comment_update_timeout' => $model->comment_update_timeout,
            'free_apartments_enable' => $model->free_apartments_enable,
            'free_apartments_url' => $model->free_apartments_url,
            'opening_hours' => $model->opening_hours,
            'iframe_url' => $model->iframe_url,
            'iframe_enable' => $model->iframe_enable,
            'cleanify_email' => $model->cleanify_email,
            'pinboard_receiver_ids' => $model->pinboard_receiver_ids,
            'email_powered_by' => $model->email_powered_by,
        ];
        if (\Auth::user()->can('edit-settings')) {
            $response['mail_host'] = $model->mail_host;
            $response['mail_port'] = $model->mail_port;
            $response['mail_username'] = $model->mail_username;
            $response['mail_password'] = $model->mail_password;
            $response['mail_encryption'] = $model->mail_encryption;
            $response['mail_from_address'] = $model->mail_from_address;
            $response['mail_from_name'] = $model->mail_from_name;
        }

        // @TODO check and use ->relationExists
        if ($model->address) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        // @TODO check and use ->relationExists
        if (isset($model->pinboard_receivers)) {
            $response['pinboard_receivers'] = (new UserTransformer)->transformCollection($model->pinboard_receivers);
        }

        return $response;
    }
}
