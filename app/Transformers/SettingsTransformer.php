<?php

namespace App\Transformers;

use App\Models\Settings;
use League\Fractal\TransformerAbstract;

/**
 * Class SettingsTransformer.
 *
 * @package namespace App\Transformers;
 */
class SettingsTransformer extends BaseTransformer
{
    /**
     * Transform the Settings entity.
     *
     * @param Settings $model
     * @return array
     */
    public function transform(Settings $model)
    {
        $response = $this->getAttributesIfExists($model, [
            'id',
            'name',
            'email',
            'phone',
            'language',
            'logo',
            'circle_logo',
            'resident_logo',
            'favicon_icon',
            'pdf_font_family',
            'login_variation',
            'login_variation_2_slider',
            'blank_pdf',
            'quarter_enable',
            'contact_enable',
            'gocaution_enable',
            'cleanify_enable',
            'listing_approval_enable',
            'pinboard_approval_enable',
//            'comment_update_timeout',
            'free_apartments_enable',
            'free_apartments_url',
            'opening_hours',
            'iframe_url',
            'iframe_enable',
            'cleanify_email',
            'pinboard_receiver_ids',
            'email_powered_by',
        ]);

        if (\Auth::user()->can('edit-settings')) {
            $response['mail_host'] = $model->mail_host;
            $response['mail_port'] = $model->mail_port;
            $response['mail_username'] = $model->mail_username;
            $response['mail_password'] = $model->mail_password;
            $response['mail_encryption'] = $model->mail_encryption;
            $response['mail_from_address'] = $model->mail_from_address;
            $response['mail_from_name'] = $model->mail_from_name;
        }

        // @TODO remove $model->address
        if ($model->relationExists('address') || $model->address) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        // @TODO remove  isset($model->pinboard_receivers)
        if ($model->relationExists('pinboard_receivers') || isset($model->pinboard_receivers)) {
            $response['pinboard_receivers'] = (new UserTransformer)->transformCollection($model->pinboard_receivers);
        }

        return $response;
    }
}
