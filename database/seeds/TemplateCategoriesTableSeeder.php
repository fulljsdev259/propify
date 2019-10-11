<?php

use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Class TemplateCategoriesTableSeeder
 */
class TemplateCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::unprepared(file_get_contents(database_path('sql' . DIRECTORY_SEPARATOR . 'template_categories.sql')));
        Schema::enableForeignKeyConstraints();
        return;

        $this->createParentCategories();
        $this->createUserCategories();
        $this->createResidentCategories();
        $this->createPostCategories();
        $this->createProductCategories();
        $this->createRequestCategories();
        $this->createManagerCategories();
        $this->createServiceProviderCategories();
        $this->createCleanifyRequestCategories();
    }

    private function createParentCategories()
    {
        $templates = [
            [
                'parent_id' => null,
                'name' => 'user',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'resident',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'pinboard',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'listing',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'request',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'manager',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'service_provider',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'cleanify_request',
                'description' => '',
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createUserCategories()
    {
        $templates = [
            [
                'parent_id' => 1,
                'system' => 1,
                'name' => 'new_admin',
                'description' => 'User create admin notification',
                'tag_map' => [
                    'name' => 'user.name',
                    'email' => 'user.email',
                    'phone' => 'user.phone',
                    'title' => 'constant.user.title',

                    'subjectName' => 'subject.name',
                    'subjectEmail' => 'subject.email',
                    'subjectPhone' => 'subject.phone',
                    'subjectTitle' => 'constant.subject.title',
                ],
                'subject' => 'New admin created',
                'body' => <<<HTML
<p>Hello {{name}}</p>
<p>A new admin account was created:</p>
<p>User {{subjectName}}</p>
<p>Email {{subjectEmail}}</p>
HTML
            ],
            [
                'parent_id' => 1,
                'system' => 1,
                'name' => 'reset_password',
                'description' => 'User reset password email',
                'tag_map' => [
                    'name' => 'user.name',
                    'email' => 'user.email',
                    'phone' => 'user.phone',
                    'title' => 'constant.user.title',
                    'passwordResetUrl' => 'passwordResetUrl',
                ],
                'subject' => 'Password reset request for your account',
                'body' => <<<HTML
<p>Hello {{title}} {{name}}</p>
<p>You are receiving this email because we received a password reset request for your account.</p>
<p>Reset Password {{passwordResetUrl}}</p><p>If you did not request a password reset, no further action is required.</p>
HTML
            ],
            [
                'parent_id' => 1,
                'system' => 1,
                'name' => 'reset_password_success',
                'description' => 'Password changed successfully',
                'tag_map' => [
                    'name' => 'user.name',
                    'email' => 'user.email',
                    'phone' => 'user.phone',
                    'title' => 'constant.user.title',
                ],
                'subject' => 'Password changed successfully',
                'body' => <<<HTML
<p>You changed your password successfully.</p>
<p>If you did change password, no further action is required.</p>
<p>If you did not change password, protect your account.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createResidentCategories()
    {
        $templates = [
            [
                'parent_id' => 2,
                'system' => 1,
                'name' => 'resident_credentials',
                'description' => 'Email sent to resident, containing the PDF with the credentials and tenancy details.',
                'tag_map' => [
                    'name' => 'user.name',
                    'birthDate' => 'resident.birthDate',
                    'mobilePhone' => 'resident.mobile_phone',
                    'privatePhone' => 'resident.private_phone',
                    'email' => 'resident.email',
                    'phone' => 'resident.phone',
                    'title' => 'constant.resident.title',
                    'residentCredentials' => 'residentCredentials',
                    'activationUrl' => 'activationUrl',
                    'activationCode' => 'resident.activation_code'
                ],
                'subject' => 'Account created',
                'body' => <<<HTML
<p>Hello {{title}} {{name}}</p>
<p>Your account was created.</p>
<p>Here is an pdf with credentials.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createPostCategories()
    {
        $templates = [
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'announcement_pinboard',
                'description' => 'Email sent to residents when admin publishes a announcement pinboard',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'title' => 'pinboard.title',
                    'content' => 'pinboard.content',
                    'execution_start' => 'pinboard.executionStartStr',
                    'execution_end' => 'pinboard.executionEndStr',
                    'category' => 'pinboard.categoryStr',
                    'providers' => 'pinboard.providersStr',
                    'buildings' => 'pinboard.buildingsStr',
                    'quarters' => 'pinboard.quartersStr',
                    'autologin' => 'user.autologinUrl',
                ],
                'subject' => 'New Pined pinboard: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Title {{title}}.</p>
<p>{{content}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the announcement.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'pinboard_published',
                'description' => 'Email sent to neighbour residents when admin publishes a pinboard, or the pinboard is automatically published',
                'tag_map' => [
                    'authorSalutation' => 'pinboard.user.title',
                    'authorName' => 'pinboard.user.name',
                    'salutation' => 'receiver.title',
                    'name' => 'receiver.name',
                    'content' => 'pinboard.content',
                    'autologin' => 'receiver.autologinUrl',
                ],
                'subject' => 'New pinboard published {{authorSalutation}} {{authorName}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>{{authorSalutation}} {{authorName}} published a new pinboard.</p>
<p><em>{{content}}</em></p>
<p>Use <a href="{{autologin}}">this link</a> to view the published pinboard.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'new_pinboard',
                'description' => 'Email sent to admins when resident creates a new pinboard',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'content' => 'pinboard.content',
                    'type' => 'pinboard.type',
                    'autologin' => 'user.autologinUrl',
                ],
                'subject' => 'New resident pinboard',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Resident {{subjectSalutation}} {{subjectName}} added a new pinboard</p>
<p>{{content}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the published pinboard.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'pinboard_liked',
                'description' => 'Email sent to pinboard author when resident liked the pinboard',
                'tag_map' => [
                    'salutation' => 'pinboard.user.title',
                    'name' => 'pinboard.user.name',
                    'likerSalutation' => 'user.title',
                    'likerName' => 'user.name',
                    'content' => 'pinboard.content',
                    'autologin' => 'pinboard.user.autologinUrl',
                ],
                'subject' => '{{likerSalutation}} {{likerName}} liked your pinboard',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Resident {{likerSalutation}} {{likerName}} liked your pinboard:</p>
<p>{{content}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the liked pinboard.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'pinboard_commented',
                'description' => 'Email sent to pinboard author when resident comments on the pinboard',
                'tag_map' => [
                    'salutation' => 'pinboard.user.title',
                    'name' => 'pinboard.user.name',
                    'commenterSalutation' => 'user.title',
                    'commenterName' => 'user.name',
                    'content' => 'pinboard.content',
                    'comment' => 'comment.comment',
                    'autologin' => 'pinboard.user.autologinUrl',
                ],
                'subject' => '{{commenterSalutation}} {{commenterName}} commented on your pinboard',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Resident {{commenterSalutation}} {{commenterName}} commented on your pinboard:</p>
<p><em>{{comment}}</em>.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the pinboard.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'pinboard_new_resident_in_neighbour',
                'description' => 'Email sent to neighbour residents when new neighbour moves in the neighbourhood',
                'tag_map' => [
                    'subjectSalutation' => 'pinboard.user.title',
                    'subjectName' => 'pinboard.user.name',
                    'salutation' => 'receiver.title',
                    'name' => 'receiver.name',
                    'content' => 'pinboard.content',
                    'autologin' => 'receiver.autologinUrl',
                ],
                'subject' => 'New resident in the neighbour',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>You got a new neighbour: {{subjectSalutation}} {{subjectName}}.</p>
<p><em>{{content}}</em></p>
<p>Use <a href="{{autologin}}">this link</a> to view the pinboard.</p>
HTML
            ],
        ];
        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createProductCategories()
    {
        $templates = [
            [
                'parent_id' => 4,
                'name' => 'listing_liked',
                'description' => 'Email sent to listing author when resident liked the listing in marketplace',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'authorSalutation' => 'listing.user.title',
                    'authorName' => 'listing.user.name',
                    'title' => 'listing.title',
                    'type' => 'listing.type',
                    'autologin' => 'listing.user.autologinUrl',
                ],
                'subject' => '{{salutation}} {{name}} liked your pinboard',
                'body' => <<<HTML
<p>Hello {{authorSalutation}} {{authorName}},</p>
<p>Resident {{salutation}} {{name}} liked your listing:</p>
<p>{{title}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the listing.</p>
HTML
            ],
            [
                'parent_id' => 4,
                'name' => 'listing_commented',
                'description' => 'Email sent to listing author when resident comments on the listing',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'authorSalutation' => 'listing.user.title',
                    'authorName' => 'listing.user.name',
                    'title' => 'listing.title',
                    'type' => 'listing.type',
                    'comment' => 'comment.comment',
                    'autologin' => 'listing.user.autologinUrl',
                ],
                'subject' => '{{salutation}} {{name}} commented on your pinboard',
                'body' => <<<HTML
<p>Hello {{authorSalutation}} {{authorName}},</p>
<p>Resident {{salutation}} {{name}} commented on  your listing:</p>
<p><em>{{title}}</em>.</p>
<p>Comment:</p>
<p><em>{{comment}}</em>.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the listing.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createRequestCategories()
    {
        $templates = [
            [
                'parent_id' => 5,
                'system' => 0,
                'type' => TemplateCategory::TypeCommunication,
                'name' => 'communication_resident',
                'description' => 'Request Resident communication templates',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'unit' => 'request.unit.name',
                    'building' => 'request.unit.building.name',
                ],
                'subject' => 'Hello {{subjectSalutation}} {{subjectName}}',
                'body' => '',
            ],
            [
                'parent_id' => 5,
                'system' => 0,
                'type' => TemplateCategory::TypeCommunication,
                'name' => 'communication_service_chat',
                'description' => 'Request Service providers communication templates',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'unit' => 'request.unit.name',
                    'building' => 'request.unit.building.name',
                ],
                'subject' => 'Hello {{subjectSalutation}} {{subjectName}}',
                'body' => '',
            ],
            [
                'parent_id' => 5,
                'system' => 1,
                'name' => 'new_request',
                'description' => 'Email sent to property managers when resident creates a new request.',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'unit' => 'request.unit.name',
                    'building' => 'request.unit.building.name',
                    'autologin' => 'user.autologinUrl',
                ],
                'subject' => 'New Resident request: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Resident {{subjectSalutation}} {{subjectName}} created a new request</p>
<p>Unit: {{category}}.</p>
<p>Category: {{category}}.</p>
<p>Title: {{title}}.</p>
<p>{{description}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the request.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'communication_service_email',
                'system' => 0,
                'description' => 'Notify service provider -> sends email to service provider and others (BCC, CC).',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                ],
                'subject' => 'New assignment to request: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>You have been assigned to an new request {{title}</p>
<p>Category: {{category}}.</p>
<p>Title: {{title}}.</p>
<p>{{description}}.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_comment',
                'description' => 'When any party adds a new comment (resident, property manager, service partner, admin or super admin) we notify all assigned people',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'commenterSalutation' => 'comment.user.title',
                    'commenterName' => 'comment.user.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'comment' => 'comment.comment',
                    'autologin' => 'user.autologinUrl',
                ],
                'subject' => 'New comment for request: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>A new comment by {{commenterSalutation}} {{commenterName}} was made for request: {{title}}</p>
<p><em>{{comment}}</em>.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the request.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_upload',
                'description' => 'When any party uploads a document/image',
                'tag_map' => [
                    'receiverSalutation' => 'receiver.title',
                    'receiverName' => 'receiver.name',
                    'uploaderSalutation' => 'uploader.title',
                    'uploaderName' => 'uploader.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'autologin' => 'receiver.autologinUrl',
                ],
                'subject' => '{{uploaderSalutation}} {{uploaderName}} uploaded a new document for request: {{title}}',
                'body' => <<<HTML
<p>Hello {{receiverSalutation}} {{receiverName}},</p>
<p>{{uploaderSalutation}} {{uploaderName}} uploaded a new document for request: {{title}}.</p>
<p>Please find the uploaded file in the attachments of this email.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the request.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_admin_change_status',
                'description' => 'When the Property Manager, Admin or Service partner change the status we notify the resident, each time when status is changed from X to XY',
                'tag_map' => [
                    'salutation' => 'request.resident.title',
                    'name' => 'request.resident.user.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'status' => 'constant.request.status',
                    'originalStatus' => 'constant.originalRequest.status',
                    'autologin' => 'request.resident.user.autologinUrl',
                ],
                'subject' => 'Status changed for request: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Admin changed status for request {{title}} from {{originalStatus}} to {{status}}</p>
<p>Use <a href="{{autologin}}">this link</a> to view the request.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_internal_comment',
                'description' => 'When admin or service partner add a internal comment, we will notify each other.',
                'tag_map' => [
                    'receiverSalutation' => 'receiver.title',
                    'receiverName' => 'receiver.name',
                    'senderSalutation' => 'sender.title',
                    'senderName' => 'sender.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'comment' => 'comment.comment',
                    'autologin' => 'receiver.autologinUrl',
                ],
                'subject' => 'New internal comment for request: {{title}}',
                'body' => <<<HTML
<p>Hello {{receiverSalutation}} {{receiverName}},</p>
<p>{{senderSalutation}} {{senderName}} added a new private comment for request: {{title}}</p>
<p><em>{{comment}}.</em></p>
<p>Use <a href="{{autologin}}">this link</a> to view the request.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_due_date_reminder',
                'description' => 'Send reminder email to property manager / admin 1 day before the due date is reached',
                'tag_map' => [
                    'salutation' => 'receiver.title',
                    'name' => 'receiver.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'dueDate' => 'request.due_date',
                    'category' => 'request.category.name',
                    'autologin' => 'receiver.autologinUrl',
                ],
                'subject' => 'Request: {{title}} is approaching its due date',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Due date for request {{title}} is {{dueDate}}</p>
<p>Use <a href="{{autologin}}">this link</a> to view the request.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createManagerCategories()
    {
        $templates = [

        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createServiceProviderCategories()
    {
        $templates = [

        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createCleanifyRequestCategories()
    {
        $templates = [
            [
                'parent_id' => 8,
                'name' => 'cleanify_request_email',
                'description' => 'Email sent to Cleanify when the resident makes a request.',
                'tag_map' => [
                    'salutation' => 'form.title',
                    'firstName' => 'form.first_name',
                    'lastName' => 'form.last_name',
                    'address' => 'form.address',
                    'zip' => 'form.zip',
                    'city' => 'form.city',
                    'email' => 'form.email',
                    'phone' => 'form.phone',
                ],
                'subject' => 'New Cleanify request from: {{salutation}} {{firstName}} {{lastName}}',
                'body' => <<<HTML
<p>New Cleanify request,</p>
<p>Name : {{salutation}} {{firstName}} {{lastName}}.</p>
<p>Phone : {{phone}}.</p>
<p>Email : {{email}}.</p>
<p>Address:</p>
<p>{{address}}, {{city}} {{zip}}.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }
}
