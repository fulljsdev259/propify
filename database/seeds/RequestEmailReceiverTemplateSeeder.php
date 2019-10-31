<?php

use App\Models\Settings;
use Illuminate\Database\Seeder;

class RequestEmailReceiverTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data =  [
            'parent_id' => 5,
            'system' => 1,
            'name' => 'request_email_receiver',
            'description' => 'Email sent to property managers(receptionist) when resident creates a new request.',
            'tag_map' => [
                'salutation' => 'user.title',
                'name' => 'user.name',
                'subjectSalutation' => 'subject.title',
                'subjectName' => 'subject.name',
                'title' => 'request.title',
                'description' => 'request.description',
            ],
            'subject' => 'New Resident request: {{title}}',
            'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Title: {{title}}.</p>
<p>{{description}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the request.</p>
HTML

        ];

        $templateCategory = (new \App\Models\TemplateCategory())->create($data);

        $parentCategory = $templateCategory->parentCategory;
        $template = new \App\Models\Template();
        $template->category_id = $templateCategory->id;
        $template->name = sprintf('%s - %s', ucfirst($parentCategory->name), $templateCategory->name);
        $template->subject = $templateCategory->subject;
        $template->body = $templateCategory->body;

        $template->default = true;
        $template->save();
    }
}
