<?php

namespace App\Repositories;

use App\Models\CleanifyRequest;
use App\Models\Comment;
use App\Models\PasswordReset;
use App\Models\Pinboard;
use App\Models\Listing;
use App\Models\Settings;
use App\Models\Request;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Models\Media;

/**
 * Class TemplateRepository
 * @package App\Repositories
 */
class TemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name' => 'like',
        'description' => 'like',
    ];

    /**
     * @var
     */
    protected $settings;

    /**
     * TemplateRepository constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->settings = Settings::first();
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Template::class;
    }

    /**
     * @param User $user
     * @param User $subject
     * @return array
     */
    public function getUserNewAdminTemplate(User $user, User $subject): array
    {
        $template = $this->getByCategoryName('new_admin');

        $context = [
            'user' => $user,
            'subject' => $subject,
        ];
        $tags = $this->getTags($template->category->tag_map, $context);
        return $this->getParsedTemplateData($template, $tags);
    }

    /**
     * @param $templateName
     * @return mixed
     */
    public function getByCategoryName($templateName)
    {
        $template = (new Template)->with('category')->whereHas('category', function ($q) use ($templateName) {
            $q->where('name', $templateName);
        })->first();

        return $template;
    }

    /**
     * @param array $tagMap
     * @param array $context
     * @param null $language
     * @return array
     */
    public function getTags(array $tagMap, array $context, $language = null): array
    {
        $language = $language ?? App::getLocale();
        $tags = [];
        foreach ($tagMap as $tag => $val) {
            if (in_array($tag, ['autologinUrl', 'passwordResetUrl', 'residentCredentials', 'activationUrl'])) {
                $tags[$tag] = $this->getStaticTagValue($tag, $val, $context, $language);
                continue;
            }

            $valMap = explode('.', $val);

            $trString = '';
            if ($valMap[0] == 'constant') {
                unset($valMap[0]);
                $valMap = array_values($valMap);
                $trString = implode('_', $valMap);
            }

            if (!isset($context[$valMap[0]])) {
                continue;
            }

            $cContext = $context[$valMap[0]];
            unset($valMap[0]);
            $valMap = array_values($valMap);

            if ($trString) {
                $val = __('template.' . $trString . '_' . $val, [], $language);
            } else {
                $val = self::getContextValue($cContext, $valMap);
            }

            $tags[$tag] = $val;
        }

        return $tags;
    }

    /**
     * @param string $tag
     * @param string $val
     * @param array $context
     * @param null $language
     * @return string
     */
    private function getStaticTagValue(string $tag, string $val, array $context, $language = null)
    {
        $language = $language ?? App::getLocale();
        $user = $context['user'] ?? null;
        $pwReset = $context['pwReset'] ?? null;
        $resident = $context['resident'] ?? null;

        if ($tag == 'autologinUrl' && $user) {
            $linkText = __('See pinboard', [], $language);
            return $this->button($user->autologinUrl, $linkText);
        }

        if ($tag == 'passwordResetUrl' && $pwReset) {
            $linkHref = url(sprintf('/reset-password?email=%s&token=%s', $user->email, $pwReset->token));
            $linkText = __('Reset password', [], $language);
            return $this->button($linkHref, $linkText);
        }

        if ($tag == 'residentCredentials' && $resident) {
            $linkHref = env('APP_URL') . Storage::url($resident->pdfXFileName());
            $linkText = __('Download Credentials', [], $language);
            return $this->button($linkHref, $linkText);
        }

        if ($tag == 'activationUrl' && $resident) {
            // @TODO hard code query params
            $linkHref = url(sprintf('/activate?&code=%s', $resident->activation_code));
            $linkText = __('template.activate_account', [], $language);
            return $this->ahref($linkHref, $linkText);
        }

        return $val;
    }

    /**
     * @param $url
     * @param $text
     * @return string
     */
    private function button($url, $text)
    {
        $linkClass = 'button button-primary';
        $bgColor = $this->settings->primary_color ?? '#3490DC';
        $linkStyle = 'font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #FFF; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: {color}; border-top: 10px solid {color}; border-right: 18px solid {color}; border-bottom: 10px solid {color}; border-left: 18px solid {color};';
        $linkStyle = str_replace('{color}', $bgColor, $linkStyle);
        return sprintf('<a class="%s" style="%s" href="%s">%s</a>', $linkClass, $linkStyle, $url, $text);
    }

    public function ahref($url, $text = null)
    {
        $text = $text ?? $url; // @TODO sprintf some problem fix later
        return  '<a href="' . $url . '" style="font-size:16px; font-weight: bold; text-decoration: none; line-height:40px; width:100%; display:inline-block">
               <span style="color:#ffffff;">' . $text . '</span>
            </a>';
    }


    /**
     * @param $context
     * @param $field
     * @return string
     */
    private static function getContextValue($context, $field)
    {
        if (! $context) {
            return '';
        }

        if (is_array($field)) {
            $_field = array_shift($field);
        } else {
            $_field = $field;
            $field = [];
        }

        if (is_array($context)) {
            $newContext = $context[$_field] ?? '';
        } else {
            $newContext = $context->{$_field} ?? '';
        }

        if (empty($field)) {
            return $newContext;
        }

        return self::getContextValue($newContext, $field);
    }

    /**
     * @param Template $template
     * @param $tagMap
     * @param $lang
     * @return array
     */
    public function getParsedTemplateData($template, $tagMap, $lang = ''): array
    {
        if (!$template) {
            return [
                'subject' => '',
                'body' => '',
            ];
        }

        $settings = $this->settings;
        $appUrl = env('APP_URL', '');
        $companyAddress = [
            $settings->address->street,
            $settings->address->house_num . ',',
            $settings->address->zip,
            $settings->address->city,
        ];

        $tagMap['primaryColor'] = $settings->primary_color;
        $tagMap['settingsCompany'] = $settings->name;
        $template = self::getParsedTemplate($template, $tagMap, $lang);

        return [
            'subject' => $template->subject,
            'body' => $template->body,
            'company' => $settings,
            'companyLogo' => $appUrl . '/' . $settings->logo,
            'companyName' => $settings->name,
            'companyAddress' => implode(' ', $companyAddress),
            'linkContact' => env('APP_URL', '#'),
            'linkTermsOfUse' => env('APP_URL', '#'),
            'linkDataProtection' => env('APP_URL', '#'),
        ];
    }

    /**
     * @param Template $template
     * @param $tagMap
     * @param $lang
     *
     * @return Template
     */
    public function getParsedTemplate($template, $tagMap, $lang = ''): Template
    {
        if (!$template) {
            return null;
        }

        $languages = Config::get('app.locales');
        $userLanguage = in_array($lang, array_keys($languages)) ? $lang : Config::get('app.locale');

        $translations = $template->translations()->where('language', $userLanguage)->get();
        $templateFields = [
            'subject',
            'body'
        ];

        foreach ($translations as $translation) {
            foreach ($templateFields as $field) {
                if ($translation->name == $field) {
                    $template->$field = $translation->value;
                }
            }
        }

        foreach ($templateFields as $field) {
            foreach ($tagMap as $tag => $value) {
                $template->$field = str_replace('{{' . $tag . '}}', $value, $template->$field);
            }
        }

        return $template;
    }

    /**
     * @param User $user
     * @param PasswordReset|null $pwReset
     * @return array
     */
    public function getUserResetPasswordTemplate(User $user, PasswordReset $pwReset): array
    {
        $template = $this->getByCategoryName('reset_password');

        $context = [
            'user' => $user,
            'pwReset' => $pwReset,
        ];
        $tags = $this->getTags($template->category->tag_map, $context);
        return $this->getParsedTemplateData($template, $tags);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserResetPasswordSuccessTemplate(User $user): array
    {
        $template = $this->getByCategoryName('reset_password_success');

        $context = [
            'user' => $user,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);
        return $this->getParsedTemplateData($template, $tags);
    }

    /**
     * @param Pinboard $pinboard
     * @param User $user
     * @return array
     */
    public function getNewPinboardParsedTemplate(Pinboard $pinboard, User $user): array
    {
        $template = $this->getByCategoryName('new_pinboard');

        $user->redirect = "/admin/pinboard/" . $pinboard->id;
        $context = [
            'user' => $user,
            'pinboard' => $pinboard,
            'subject' => $pinboard->user,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);
        return $this->getParsedTemplateData($template, $tags);
    }

    /**
     * @param Resident $resident
     * @return array
     */
    public function getResidentCredentialsParsedTemplate(Resident $resident): array
    {
        $template = $this->getByCategoryName('resident_credentials');

        $context = [
            'resident' => $resident,
            'user' => $resident->user,
        ];
        $language = $resident->settings->language ?? App::getLocale();
        $tags = $this->getTags($template->category->tag_map, $context, $language);

        if (!empty($tags['salutation'])) {
            if(\App\Models\Resident::TitleCompany == $tags['salutation']) {
                $tags['salutation'] = __('general.pdf_salutation.' . $resident->title, [], $language);
            } else {
                $tags['salutation'] = __('general.pdf_salutation.' . $resident->title, ['name' => $resident->last_name], $language);
            }
        }

        return $this->getParsedTemplateData($template, $tags, $resident->user->settings->language);
    }

    /**
     * @param Pinboard $pinboard
     * @param User $user
     * @return array
     */
    public function getAnnouncementPinboardParsedTemplate(Pinboard $pinboard, User $user): array
    {
        $template = $this->getByCategoryName('announcement_pinboard');

        $user->redirect = '/news/' . $pinboard->id;
        $context = [
            'user' => $user,
            'pinboard' => $pinboard,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $user->settings->language);
    }

    /**
     * @param Pinboard $pinboard
     * @param User $receiver
     * @return array
     */
    public function getPinboardParsedTemplate(Pinboard $pinboard, User $receiver): array
    {
        $template = $this->getByCategoryName('pinboard_published');

	    $receiver->redirect = '/news/' . $pinboard->id;
        $context = [
            'receiver' => $receiver,
            'pinboard' => $pinboard,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
    }

    /**
     * @param Pinboard $pinboard
     * @param User $receiver
     * @return array
     */
    public function getPinboardNewResidentInNeighbourParsedTemplate(Pinboard $pinboard, User $receiver): array
    {
        $template = $this->getByCategoryName('pinboard_new_resident_in_neighbour');

        $receiver->redirect = '/news/' . $pinboard->id;
        $context = [
            'receiver' => $receiver,
            'pinboard' => $pinboard,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
    }

    /**
     * @param Pinboard $pinboard
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public function getPinboradCommentedParsedTemplate(Pinboard $pinboard, User $user, Comment $comment): array
    {
        $template = $this->getByCategoryName('pinboard_commented');

        $pinboard->user->redirect = '/pinboard/' . $pinboard->id;
        $context = [
            'user' => $user,
            'pinboard' => $pinboard,
            'comment' => $comment,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $pinboard->user->settings->language);
    }

    /**
     * @param Pinboard $pinboard
     * @param User $user
     * @return array
     */
    public function getPinboardLikedParsedTemplate(Pinboard $pinboard, User $user): array
    {
        $template = $this->getByCategoryName('pinboard_liked');

        $pinboard->user->redirect = '/pinboard/' . $pinboard->id;
        $context = [
            'user' => $user,
            'pinboard' => $pinboard,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $pinboard->user->settings->language);
    }

    /**
     * @param Listing $listing
     * @param User $user
     * @return array
     */
    public function getListingLikedParsedTemplate(Listing $listing, User $user): array
    {
        $template = $this->getByCategoryName('listing_liked');

        $listing->user->redirect = '/listing';
        $context = [
            'user' => $user,
            'listing' => $listing,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $listing->user->settings->language);
    }

    /**
     * @param Listing $listing
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public function getListingCommentedParsedTemplate(Listing $listing, User $user, Comment $comment): array
    {
        $template = $this->getByCategoryName('listing_commented');

        $listing->user->redirect = '/listing';
        $context = [
            'user' => $user,
            'listing' => $listing,
            'comment' => $comment,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $listing->user->settings->language);
    }

    /**
     * @param Request $request
     * @param User $user
     * @param User $subject
     * @return array
     */
    public function getNewRequestParsedTemplate(Request $request, User $user, User $subject): array
    {
        $template = $this->getByCategoryName('new_request');

        $user->redirect = '/admin/requests/' . $request->id;
        $context = [
            'user' => $user,
            'subject' => $subject,
            'request' => $request,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $user->settings->language);
    }

    /**
     * @param Request $request
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public function getRequestCommentedParsedTemplate(Request $request, User $user, Comment $comment): array
    {
        $template = $this->getByCategoryName('request_comment');

        $user->redirect = '/admin/requests/' . $request->id;
        if ($user->hasRole('resident')) {
            $user->redirect = '/requests';
        }
        $context = [
            'request' => $request,
            'comment' => $comment,
            'user' => $user,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $user->settings->language);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getRequestDueParsedTemplate(Request $request, User $receiver): array
    {
        $template = $this->getByCategoryName('request_due_date_reminder');

        $receiver->redirect = '/admin/requests/' . $request->id;
        $context = [
            'request' => $request,
            'receiver' => $receiver,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
    }

    /**
     * @param Request $request
     * @param User $uploader
     * @param User $receiver
     * @param Media $media
     * @return array
     */
    public function getRequestMediaParsedTemplate(
        Request $request,
        User $uploader,
        User $receiver,
        Media $media
    ): array
    {
        $template = $this->getByCategoryName('request_upload');

        $receiver->redirect = '/admin/requests/' . $request->id;
        if ($receiver->hasRole('resident')) {
            $receiver->redirect = '/requests';
        }
        $context = [
            'request' => $request,
            'media' => $media,
            'uploader' => $uploader,
            'receiver' => $receiver,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        $msg = $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
        $msg['media'] = $media;
        return $msg;
    }

    /**
     * @param Request $request
     * @param Request $originalRequest
     * @param User $user
     * @return array
     */
    public function getRequestStatusChangedParsedTemplate(Request $request, Request $originalRequest, User $user): array
    {
        $template = $this->getByCategoryName('request_admin_change_status');

        $request->resident->user->redirect = '/requests';
        $context = [
            'request' => $request,
            'originalRequest' => $originalRequest,
            'user' => $user,
        ];
        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $user->settings->language);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param User $sender
     * @param User $receiver
     * @return array
     */
    public function getRequestInternalCommentParsedTemplate(
        Request $request,
        Comment $comment,
        User $sender,
        User $receiver
    ): array
    {
        $template = $this->getByCategoryName('request_internal_comment');

        $receiver->redirect = '/admin/requests/' . $request->id;
        $context = [
            'request' => $request,
            'comment' => $comment,
            'sender' => $sender,
            'receiver' => $receiver,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
    }

    /**
     * @param CleanifyRequest $cleanifyRequest
     * @return array
     */
    public function getCleanifyParsedTemplate(CleanifyRequest $cleanifyRequest): array
    {
        $template = $this->getByCategoryName('cleanify_request_email');

        $context = [
            'form' => $cleanifyRequest->form
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $cleanifyRequest->user->settings->language);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Collection
     */
    public function getParsedCommunicationTemplates(Request $request, User $user): Collection
    {
        $templates = (new Template())->whereHas('category', function ($q) {
            $q->where('type', TemplateCategory::TypeCommunication)
                ->where('name', 'communication_resident');
        })->get();

        foreach ($templates as &$template) {
            $context = [
                'user' => $user,
                'subject' => $request->resident->user,
                'request' => $request,
            ];

            $template = self::getTemplate($template, $context);
        }

        return $templates;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Collection
     */
    public function getParsedServiceCommunicationTemplates(Request $request, User $user): Collection
    {
        $templates = (new Template())->whereHas('category', function ($q) {
            $q->where('type', TemplateCategory::TypeCommunication)
                ->where('name', 'communication_service_chat');
        })->get();

        foreach ($templates as &$template) {
            $context = [
                'user' => $user,
                'subject' => $request->resident->user,
                'request' => $request,
            ];
            $template = self::getTemplate($template, $context);
        }

        return $templates;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Collection
     */
    public function getParsedServiceEmailTemplates(Request $request, User $user): Collection
    {
        $templates = (new Template())->whereHas('category', function ($q) {
            $q->where('type', TemplateCategory::TypeEmail)
                ->where('name', 'communication_service_email');
        })->get();

        foreach ($templates as &$template) {
            $context = [
                'user' => $user,
                'subject' => $request->resident->user,
                'request' => $request,
            ];
            $template = self::getTemplate($template, $context);
        }

        return $templates;
    }

    /**
     * @param Template $template
     * @param array $context
     *
     * @return Template
     */
    public function getTemplate(Template $template, $context): Template
    {
        $tags = $this->getTags($template->category->tag_map, $context);

        $parsedTemplate = $this->getParsedTemplate($template, $tags);

        return $parsedTemplate;
    }
}
