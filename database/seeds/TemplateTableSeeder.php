<?php

use App\Models\Template;
use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Class TemplateTableSeeder
 */
class TemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::unprepared(file_get_contents(database_path('sql' . DIRECTORY_SEPARATOR . 'templates.sql')));
        Schema::enableForeignKeyConstraints();
        return;

        $this->setupEmailTemplates();
        $this->setupCommunicationResidentTemplates();
        $this->setupCommunicationServiceTemplates();
    }

    private function setupEmailTemplates()
    {
        $templateCategories = (new TemplateCategory)->where('parent_id', '>', 0)
            ->where('type', TemplateCategory::TypeEmail)
            ->with('parentCategory')
            ->get();

        foreach ($templateCategories as $key => $templateCategory) {

            $parentCategory = $templateCategory->parentCategory;
            $template = new Template();
            $template->category_id = $templateCategory->id;
            $template->name = sprintf('%s - %s', ucfirst($parentCategory->name), $templateCategory->name);
            $template->subject = $templateCategory->subject;
            $template->body = $templateCategory->body;

            $template->default = true;
            $template->save();
        }
    }

    private function setupCommunicationResidentTemplates()
    {
        $templateCategory = (new TemplateCategory)->where('parent_id', '>', 0)
            ->where('type', TemplateCategory::TypeCommunication)
            ->where('name', 'communication_resident')
            ->with('parentCategory')
            ->first();

        $templatData = [
            [
                'name' => 'Grreting 1',
                'subject' => 'Hello {{subjectSalutation}} {{subjectName}}. How can I help you?',
            ],
            [
                'name' => 'Grreting 2',
                'subject' => 'Hello {{subjectSalutation}} {{subjectName}}. My name is {{name}} How can I help you?',
            ],
            [
                'name' => 'Goodbye 1',
                'subject' => 'Have a nice day {{subjectSalutation}} {{subjectName}}.',
            ],
        ];

        foreach ($templatData as $data) {
            $template = new Template();
            $template->category_id = $templateCategory->id;
            $template->name = $data['name'];
            $template->subject = $data['subject'];
            $template->body = '';

            $template->default = true;
            $template->save();
        }
    }

    /**
     *
     */
    private function setupCommunicationServiceTemplates()
    {
        $templateCategory = (new TemplateCategory)->where('parent_id', '>', 0)
            ->where('type', TemplateCategory::TypeCommunication)
            ->where('name', 'communication_service_chat')
            ->with('parentCategory')
            ->first();

        $templatData = [
            [
                'name' => 'Service Grreting 1',
                'subject' => 'Hello {{subjectSalutation}} {{subjectName}}. How can I help you?',
            ],
            [
                'name' => 'Service Grreting 2',
                'subject' => 'Hello {{subjectSalutation}} {{subjectName}}. My name is {{name}} How can I help you?',
            ],
            [
                'name' => 'Service Goodbye 1',
                'subject' => 'Have a nice day {{subjectSalutation}} {{subjectName}}.',
            ],
        ];

        foreach ($templatData as $data) {
            $template = new Template();
            $template->category_id = $templateCategory->id;
            $template->name = $data['name'];
            $template->subject = $data['subject'];
            $template->body = '';

            $template->default = true;
            $template->save();
        }
    }
}
