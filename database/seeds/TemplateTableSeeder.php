<?php

use App\Models\Template;
use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;

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
        \Illuminate\Support\Facades\DB::statement('INSERT INTO `templates` (`id`, `category_id`, `type`, `name`, `subject`, `body`, `default`, `system`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 9, 1, \'User - new_admin\', \'New admin created\', \'<p>Hello {{name}}</p>\n<p>A new admin account was created:</p>\n<p>User {{subjectName}}</p>\n<p>Email {{subjectEmail}}</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-08-06 04:29:15\', NULL),
(2, 10, 1, \'User - reset_password\', \'Passwort zurücksetzen | {{real_estate_name}} Mieterportal\', \'<p>Hallo {{name}}</p><p><br></p><p><span style=\"color: rgb(68, 68, 68);\">Wir haben die Anfrage zur Änderung deines Passworts erhalten. Bitte klicken Sie den unten stehenden Button, um ein neues Passwort festzulegen.</span></p><p><br></p><p>{{passwordResetUrl}}</p><p><br></p><p>Sie haben 24&nbsp;Stunden Zeit, um ein neues Passwort auszuwählen.</p><p><br></p><p>Sollten Sie keinen Passwort-Reset angefordert haben, so ist keine weitere Aktion erforderlich.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-08-28 17:18:54\', NULL),
(3, 11, 1, \'User - reset_password_success\', \'Password changed successfully\', \'<p>You changed your password successfully.</p>\n<p>If you did change password, no further action is required.</p>\n<p>If you did not change password, protect your account.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-08-06 04:29:15\', NULL),
(4, 12, 1, \'Tenant - tenant_credentials\', \'Willkommen im Mieterportal der {{settingsCompany}}\', \'<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"wrapper\">\n            <tbody><tr>\n                <td bgcolor=\"#ffffff\" height=\"42\"></td>\n            </tr>\n\n            <tr>\n              <td bgcolor=\"#ffffff\" align=\"center\" style=\"color: #000000; font-size: 18px; font-weight: 400; line-height: 25px;\">\n                  <h3 style=\"padding: 0; margin: 0\">\nErste Anmeldung und Aktivierung Ihres Accounts</h3>\n              </td>\n            </tr>\n<tr>\n                <td bgcolor=\"#ffffff\" height=\"43\"></td>\n            </tr>\n\n<tr><td bgcolor=\"#ffffff\" align=\"center\"> {{salutation}}<br></td></tr>\n\n            <tr>\n                <td bgcolor=\"#ffffff\" height=\"37\"></td>\n            </tr>\n            \n           \n            <tr>\n                <td bgcolor=\"#ffffff\" align=\"center\" style=\"color: #000000; font-size: 18px; font-weight: 400; line-height: 25px;\">\n                  <p style=\"padding: 0; margin: 0\">\n                     Um sich anzumelden, klicken Sie auf den unten stehenden Link und loggen Sie sich mit Ihrer E-Mail-Adresse und dem persönlichen Freischaltcode ein. Einmal eingeloggt können Sie Ihr eigenes Passwort definieren und dieses fortan für die Anmeldung nutzen.\n                  </p>\n                </td>\n            </tr>\n            <tr>\n                <td bgcolor=\"#ffffff\" height=\"46\"></td>\n            </tr>\n            <tr>\n                <td bgcolor=\"#ffffff\" align=\"center\">\n                    <table cellspacing=\"0\" cellpadding=\"0\">\n                        <tbody><tr>\n                            <td align=\"center\" width=\"300\" height=\"40\" bgcolor=\"{{primaryColor}}\" style=\"-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block;padding: 0\">\n                                {{activationUrl}}\n                            </td>\n                        </tr>\n                    </tbody></table>\n                </td>\n            </tr>\n           \n            \n            \n            <tr>\n                <td bgcolor=\"#ffffff\" height=\"47\"></td>\n            </tr>\n          <tr>\n            <td bgcolor=\"#ffffff\" align=\"center\" style=\"color: #000000; font-size: 18px; font-weight: 400; line-height: 25px;position: relative\">\n                <table bgcolor=\"#f3f5f7\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"400\" class=\"wrapper\">\n                    <tbody><tr>\n                        <td height=\"10\"></td>\n                    </tr>\n                    <tr>\n                        <td align=\"center\">\n                            <p style=\"padding: 0; margin: 0\">Dies ist lhr personlicher Freischaltcode:</p>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td height=\"20\"></td>\n                    </tr>\n                    <tr>\n                        <td align=\"center\" style=\"letter-spacing:10px\">\n                            <h4 style=\"padding: 0; margin: 0\"> {{activationCode}}</h4>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td height=\"10\"></td>\n                    </tr>\n                </tbody></table>\n            </td>\n          </tr>\n          <tr>\n              <td height=\"40\" bgcolor=\"#ffffff\"></td>\n          </tr>\n          <tr bgcolor=\"#ffffff\">\n                <td bgcolor=\"#ffffff\" align=\"center\" style=\"color: #000000; font-size: 18px; font-weight: 400; line-height: 25px;position: relative\">\n                    <h4 style=\"color: black; font-weight: normal;padding: 0;margin: 0\">Willkomen an Board!</h4>\n                </td>\n            </tr>\n            <tr>\n                <td height=\"10\" bgcolor=\"#ffffff\" style=\"position: relative; z-index: 999\"></td>\n            </tr>\n          </tbody></table>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-10-01 05:46:57\', NULL),
(5, 13, 1, \'Pinboard - announcement_pinboard\', \'Neue Ankündigung erstellt – Mieterportal\', \'<p>Sehr geehrte/r {{salutation}} {{name}}</p><p><br></p><p>Für die Liegenschaft&nbsp;{{buildings}} wurde soeben eine neue Nachricht zwecks... hinterlassen.</p><p><br></p><p>Titel:</p><p>{{title}}</p><p><br></p><p>Inhalt:</p><p>{{content}}</p><p><br></p><p>Datum:</p><p>{{execution_start}} {{execution_end}}</p><p><br></p><p>Auszuführender Betrieb:</p><p>{{providers}}</p><p><br></p><p>Use <a href=\"http://dev.propify.ch/admin/templates/%7B%7Bautologin%7D%7D\" target=\"_blank\">this link</a> to view the announcement.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-10-01 10:54:15\', NULL),
(6, 14, 1, \'Pinboard - pinboard_published\', \'New pinboard published {{authorSalutation}} {{authorName}}\', \'<p>Hello {{salutation}} {{name}},</p>\n<p>{{authorSalutation}} {{authorName}} published a new pinboard.</p>\n<p><em>{{content}}</em></p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the published pinboard.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-09-28 03:22:17\', NULL),
(7, 15, 1, \'Pinboard - new_pinboard\', \'New tenant pinboard\', \'<p>Hello {{salutation}} {{name}},</p>\n<p>Tenant {{subjectSalutation}} {{subjectName}} added a new pinboard</p>\n<p>{{content}}.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the published pinboard.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-09-28 03:22:17\', NULL),
(8, 16, 1, \'Pinboard - pinboard_liked\', \'{{likerSalutation}} {{likerName}} liked your pinboard\', \'<p>Hello {{salutation}} {{name}},</p>\n<p>Tenant {{likerSalutation}} {{likerName}} liked your pinboard:</p>\n<p>{{content}}.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the liked pinboard.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-09-28 03:22:17\', NULL),
(9, 17, 1, \'Pinboard - pinboard_commented\', \'{{commenterSalutation}} {{commenterName}} commented on your pinboard\', \'<p>Hello {{salutation}} {{name}},</p>\n<p>Tenant {{commenterSalutation}} {{commenterName}} commented on your pinboard:</p>\n<p><em>{{comment}}</em>.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the pinboard.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-09-28 03:22:17\', NULL),
(10, 18, 1, \'Pinboard - pinboard_new_tenant_in_neighbour\', \'New tenant in the neighbour\', \'<p>Hello {{salutation}} {{name}},</p>\n<p>You got a new neighbour: {{subjectSalutation}} {{subjectName}}.</p>\n<p><em>{{content}}</em></p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the pinboard.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-09-28 03:22:17\', NULL),
(11, 19, 1, \'Listing - listing_liked\', \'{{salutation}} {{name}} liked your pinboard\', \'<p>Hello {{authorSalutation}} {{authorName}},</p>\n<p>Tenant {{salutation}} {{name}} liked your listing:</p>\n<p>{{title}}.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the listing.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-10-05 10:17:06\', NULL),
(12, 20, 1, \'Listing - listing_commented\', \'{{salutation}} {{name}} commented on your pinboard\', \'<p>Hello {{authorSalutation}} {{authorName}},</p>\n<p>Tenant {{salutation}} {{name}} commented on  your listing:</p>\n<p><em>{{title}}</em>.</p>\n<p>Comment:</p>\n<p><em>{{comment}}</em>.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the listing.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-10-05 10:17:07\', NULL),
(13, 23, 1, \'Request - new_request\', \'Neue Anfrage eingegangen | Mieterportal\', \'<p>Hallo {{name}}</p><p><br></p><p>Soeben hat der Mieter/die Mieterin {{name}} eine neue Anfrage erstellt.</p><p><br></p><p>Unit: {{building}}</p><p>Unit: {{unit}}</p><p><br></p><p>Typ der Anfrage: {{category}}</p><p>Betreff: {{title}}</p><p>Beschreibung: {{description}}.</p><p><br></p><p>Mit Klick auf diesen <a href=\"{{autologin}}\" target=\"_blank\">Link</a> werden Sie automatisch im Mieterportal eingeloggt und können weitere Aktionen online vornehmen.</p><p><br></p><p><br></p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-08-25 17:28:12\', NULL),
(14, 24, 1, \'Request - communication_service_email\', \'New assignment to request: {{title}}\', \'<p>Hello {{salutation}} {{name}},</p>\n<p>You have been assigned to an new request {{title}</p>\n<p>Category: {{category}}.</p>\n<p>Title: {{title}}.</p>\n<p>{{description}}.</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-08-06 04:29:15\', NULL),
(15, 25, 1, \'Request - request_comment\', \'Neuer Kommentar in Anfrage {{requestID}} – Mieterportal\', \'<p>Hallo {{name}}</p><p><br></p><p>Soeben hat {{commenterName}} einen neuen Kommentar in einer Anfrage hinterlassen:</p><p><br></p><p>Anfrage-ID: {{requestID}}</p><p>Kategorie: {{category}}</p><p>Anfrage Titel: \"{{title}}\"</p><p><br></p><p>Neuer Kommentar:</p><p><strong><em>{{comment}}</em></strong></p><p><br></p><p>Klicken Sie <a href=\"{{autologin}}\" target=\"_blank\">hier</a>, um zur Anfrage zu gelangen.</p><p><br></p><p>(Dies ist eine automatisierte Nachricht. Bitte antworten Sie nicht auf diese E-Mail antworten sondern hinterlassen Sie Ihren Kommentar im Portal. Mit Klick auf diesen <a href=\"{{autologin}}\" target=\"_blank\">Link</a> werden Sie automatisch in Ihrem Konto eingeloggt.)</p>\', 1, 0, \'2019-08-06 04:29:15\', \'2019-08-11 10:28:07\', NULL),
(16, 26, 1, \'Request - request_upload\', \'{{uploaderSalutation}} {{uploaderName}} uploaded a new document for request: {{title}}\', \'<p>Hello {{receiverSalutation}} {{receiverName}},</p>\n<p>{{uploaderSalutation}} {{uploaderName}} uploaded a new document for request: {{title}}.</p>\n<p>Please find the uploaded file in the attachments of this email.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the request.</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-06 04:29:16\', NULL),
(17, 27, 1, \'Request - request_admin_change_status\', \'Anfrage-Status wurde geändert | Mieterportal\', \'<p>Hallo {{name}}</p><p><br></p><p>Soeben wurde den Status Ihrer Anfrage mit der Nummer {{request_id}} von <strong>{{originalStatus}} </strong>zu <strong>{{status}}</strong> geändert. Weitere Einzelheiten können Sie im Mieterbereich entnehmen.</p><p><br></p><p>Mit einem Klick auf diesen Button, gelangen Sie zum Mieterportal und werden automatisch in Ihrem Konto eingeloggt.</p><p><br></p><p>{{autologin}}</p><p><br></p><pre class=\"ql-syntax\" spellcheck=\"false\">&lt;hr&gt;\n</pre><p><br></p><p>Dies ist eine automatisch generierte Nachricht, bitte nicht darauf antworten. Allfällige Mitteilungen können Sie direkt in Mieterportal im entsprechenden Fall hinterlegen.</p><p><br></p><p>Freundliche Grüsse</p><p>{{real_estate_name}}</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-28 17:48:23\', NULL),
(18, 28, 1, \'Request - request_internal_comment\', \'New internal comment for request: {{title}}\', \'<p>Hello {{receiverSalutation}} {{receiverName}},</p>\n<p>{{senderSalutation}} {{senderName}} added a new private comment for request: {{title}}</p>\n<p><em>{{comment}}.</em></p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the request.</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-06 04:29:16\', NULL),
(19, 29, 1, \'Request - request_due_date_reminder\', \'Erinnerung zur Anfrage ID {{request-id}}\', \'<p>Hallo {{name}}</p><p><br></p><p>Wir möchten Sie darüber informieren, dass der untenstehende Fall bis heute nicht erledigt wurde und bitten Sie das Anliegen schnellstmöglich zu bearbeiten.</p><p><br></p><p><strong>Anfrage-ID:</strong> {{request-id}}</p><p><strong>Status:</strong> {{request-status}}</p><p><strong>Zu erledigen bis:</strong> {{dueDate}}</p><p><br></p><p><strong>Typ:</strong> {{category}}</p><p>Kurzbezeichnung: {{request-id}}</p><p><strong>Details:</strong> {{request-id}}</p><p><br></p><p><strong>Liegenschaft:</strong> {{building_address}}, {{building_zip}} {{building_city}}</p><p><strong>Mieter:</strong> {{tenant}}</p><p><br></p><p>Mit einem Klick auf diesen Button, gelangen Sie zum Mieterportal und werden automatisch eingeloggt.</p><p><br></p><p>{{autologin}}</p><p><br></p><pre class=\"ql-syntax\" spellcheck=\"false\">&lt;hr&gt;\n</pre><p><br></p><p>Dies ist eine automatisch generierte Nachricht, bitte nicht darauf antworten. Allfällige Mitteilungen können Sie direkt in Mieterportal im entsprechenden Fall hinterlegen.</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-28 17:44:34\', NULL),
(20, 30, 1, \'Cleanify_request - cleanify_request_email\', \'New Cleanify request from: {{salutation}} {{firstName}} {{lastName}}\', \'<p>New Cleanify request,</p>\n<p>Name : {{salutation}} {{firstName}} {{lastName}}.</p>\n<p>Phone : {{phone}}.</p>\n<p>Email : {{email}}.</p>\n<p>Address:</p>\n<p>{{address}}, {{city}} {{zip}}.</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-06 04:29:16\', NULL),
(21, 21, 1, \'Empfangsbestätigung\', \'Vielen Dank für die Übermittlung Ihrer Anfrage.\', \'<p>Hallo {{name}}</p><p><br></p><p>Vielen Dank für die Übermittlung Ihrer Anfrage.</p><p><br></p><p><span style=\"color: rgb(51, 51, 51);\">Wir haben Ihr Anliegen umgehend an den zuständigen Bewirtschaftungsmitarbeiter weitergeleitet, welcher sich raschmöglichst darum kümmert. Bitte haben Sie etwas Geduld.</span></p><p><br></p><p>{{signature}}</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-11 10:42:27\', NULL),
(22, 21, 1, \'Verzögerungsmeldung\', \'Bitte haben Sie etwas Geduld.\', \'<p>{{salutation}} {{name}}</p><p><br></p><p>Die Verarbeitung Ihrer Anfrage benötigt etwas mehr Zeit. Bitte haben Sie noch etwas Geduld. Wir benachrichtigen Sie sobald wir Neuigkeiten haben.</p><p><br></p><p>Ich wünsche Ihnen weiterhin einen angenehmen Tag.</p><p><br></p><p>{{signature}}</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-11 11:52:53\', NULL),
(23, 21, 1, \'Weitere Vorlagen erstellen\', \'Stellen Sie Ihre eigenen Vorlagen zusammen.\', \'<p>Stellen Sie Ihre eigenen Vorlagen zusammen.</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-11 11:54:17\', NULL),
(24, 22, 1, \'Neue Anfrage übermitteln\', \'Hello {{subjectSalutation}} {{subjectName}}. How can I help you?\', \'<p>Guten Tag Herr Darko Brezovac</p><p><br></p><p>Anfrage-I</p><p><br></p><p>Wir bitten Sie, die unten angefügten Mängel fristgerecht zu erledigen. Generell gilt die Erledigung eines Mangels erst durch Visum auf zugestelltem Formular als bestätigt. Nach der Mängelerledigung bitten wir Sie uns das visierte Formular umgehend per Mail zu retournieren.</p><p><br></p><p>Besten Dank.</p><p><br></p><p>Freundliche Grüsse</p><p>Fortimo AG</p>\', 1, 0, \'2019-08-06 04:29:16\', \'2019-09-10 08:32:06\', NULL),
(25, 22, 1, \'Service Grreting 2\', \'Hello {{subjectSalutation}} {{subjectName}}. My name is {{name}} How can I help you?\', \'\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-06 04:29:16\', NULL),
(26, 22, 1, \'Service Goodbye 1\', \'Have a nice day {{subjectSalutation}} {{subjectName}}.\', \'\', 1, 0, \'2019-08-06 04:29:16\', \'2019-08-06 04:29:16\', NULL)');

        return;
        $this->setupEmailTemplates();
        $this->setupCommunicationTenantTemplates();
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

    private function setupCommunicationTenantTemplates()
    {
        $templateCategory = (new TemplateCategory)->where('parent_id', '>', 0)
            ->where('type', TemplateCategory::TypeCommunication)
            ->where('name', 'communication_tenant')
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
