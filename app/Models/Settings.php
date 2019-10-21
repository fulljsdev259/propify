<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Settings
 *
 * @SWG\Definition (
 *      definition="Settings",
 *      required={"name", "language"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="address_id",
 *          description="address_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="phone",
 *          description="phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="language",
 *          description="language",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="logo",
 *          description="logo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="circle_logo",
 *          description="circle_logo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="resident_logo",
 *          description="resident_logo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="favicon_icon",
 *          description="favicon_icon",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="primary_color",
 *          description="primary_color",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="primary_color_lighter",
 *          description="primary_color_lighter",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="pdf_font_family",
 *          description="pdf_font_family",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="blank_pdf",
 *          description="blank_pdf",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quarter_enable",
 *          description="quarter_enable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contact_enable",
 *          description="contact_enable",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="listing_approval_enable",
 *          description="listing_approval_enable",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="pinboard_approval_enable",
 *          description="pinboard_approval_enable",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="email_powered_by",
 *          description="email_powered_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="comment_update_timeout",
 *          description="comment_update_timeout",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="iframe_url",
 *          description="iframe url",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 * @property int $id
 * @property int $address_id
 * @property int $login_variation
 * @property int $login_variation_2_slider
 * @property int $email_powered_by
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $language
 * @property string|null $logo
 * @property string|null $circle_logo
 * @property string|null $resident_logo
 * @property string|null $pdf_font_family
 * @property string|null $favicon_icon
 * @property string|null $accent_color
 * @property string|null $primary_color
 * @property string $primary_color_lighter
 * @property bool $blank_pdf
 * @property bool $quarter_enable
 * @property bool $gocaution_enable
 * @property bool $cleanify_enable
 * @property bool $free_apartments_enable
 * @property string|null $free_apartments_url
 * @property string|null $cleanify_email
 * @property array|null $opening_hours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $listing_approval_enable
 * @property bool $pinboard_approval_enable
 * @property int $comment_update_timeout
 * @property string $iframe_url
 * @property bool $iframe_enable
 * @property bool $contact_enable
 * @property array|null $pinboard_receiver_ids
 * @property string|null $mail_host
 * @property int|null $mail_port
 * @property string|null $mail_username
 * @property mixed $mail_password
 * @property string|null $mail_encryption
 * @property string|null $mail_from_address
 * @property string|null $mail_from_name
 * @property-read \App\Models\Address $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereAccentColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereBlankPdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereCircleLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereCleanifyEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereCleanifyEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereCommentUpdateTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereContactEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereEmailPoweredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereFaviconIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereFreeApartmentsEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereFreeApartmentsUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereGocautionEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereIframeEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereIframeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereListingApprovalEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereLoginVariation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereLoginVariation2Slider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereMailEncryption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereMailFromAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereMailFromName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereMailHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereMailPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereMailPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereMailUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereOpeningHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings wherePdfFontFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings wherePinboardApprovalEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings wherePinboardReceiverIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings wherePrimaryColorLighter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereQuarterEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereResidentLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Settings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings withoutTrashed()
 * @mixin \Eloquent
 */
class Settings extends AuditableModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    public $fillable = [
        'address_id',
        'name',
        'email',
        'phone',
        'language',
        'logo',
        'circle_logo',
        'resident_logo',
        'favicon_icon',
        'pdf_font_family',
        'blank_pdf',
        'quarter_enable',
        'contact_enable',
        'listing_approval_enable',
        'pinboard_approval_enable',
        'comment_update_timeout',
        'free_apartments_enable',
        'free_apartments_url',
        'opening_hours',
        'iframe_url',
        'iframe_enable',
        'gocaution_enable',
        'cleanify_enable',
        'cleanify_email',
        'pinboard_receiver_ids',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'primary_color',
        'primary_color_lighter',
        'accent_color',
        'login_variation',
        'login_variation_2_slider',
        'email_powered_by',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'address_id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'language' => 'string',
        'logo' => 'string',
        'circle_logo' => 'string',
        'resident_logo' => 'string',
        'favicon_icon' => 'string',
        'pdf_font_family' => 'string',
        'blank_pdf' => 'boolean',
        'quarter_enable' => 'boolean',
        'contact_enable' => 'boolean',
        'gocaution_enable' => 'boolean',
        'cleanify_enable' => 'boolean',
        'listing_approval_enable' => 'boolean',
        'pinboard_approval_enable' => 'boolean',
        'comment_update_timeout' => 'integer',
        'free_apartments_enable' => 'boolean',
        'free_apartments_url' => 'string',
        'opening_hours' => 'array',
        'iframe_url' => 'string',
        'iframe_enable' => 'boolean',
        'cleanify_email' => 'string',
        'pinboard_receiver_ids' => 'array',
        'mail_host' => 'string',
        'mail_port' => 'integer',
        'mail_username' => 'string',
        'mail_password' => 'string',
        'mail_encryption' => 'string',
        'mail_from_address' => 'string',
        'mail_from_name' => 'string',
        'primary_color' => 'string',
        'primary_color_lighter' => 'string',
        'accent_color' => 'string',
        'login_variation' => 'integer',
        'login_variation_2_slider' => 'integer',
        'email_powered_by' => 'integer',
    ];

    /**
     * @return array
     */
    public static function rules()
    {
        return [
            'name' => 'required',
            'language' => 'required',
            'cleanify_email' => 'email',
            'mail_from_address' => 'email',
            'iframe_url' => function($attr, $val, $fail) {
                if (is_string($val)) {
                    if (!filter_var($val, FILTER_VALIDATE_URL)) {
                        $fail('The iframe url format is invalid');
                    }
                }
            },
            'pinboard_receiver_ids' => function($attr, $val, $fail) {
                $us = User::whereIn('id', $val)->get();
                foreach ($us as $u) {
                    if (!$u->hasRole('administrator')) {
                        $fail('News email receivers must be administrators');
                    }
                }
            },
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    /**
     * @param $val
     * @return mixed
     */
    public function getMailPasswordAttribute($val)
    {
        return \Crypt::decryptString($val);
    }

    /**
     * @param $val
     */
    public function setMailPasswordAttribute($val)
    {
        $original = $this->getOriginal('mail_password');
        if ($original && \Crypt::decryptString($original) == $val) {
            $this->attributes['mail_password'] = $original;
        } else {
            $this->attributes['mail_password'] = \Crypt::encryptString($val);
        }
    }
}
