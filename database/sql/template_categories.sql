INSERT INTO `template_categories` (`id`, `parent_id`, `name`, `description`, `subject`, `body`, `tag_map`, `created_at`, `updated_at`, `deleted_at`, `type`, `system`) VALUES
(1, NULL, 'user', '', '', '', NULL, '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(2, NULL, 'tenant', '', '', '', NULL, '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(3, NULL, 'pinboard', '', '', '', NULL, '2019-08-06 04:29:15', '2019-09-28 03:22:17', NULL, 1, 0),
(4, NULL, 'listing', '', '', '', NULL, '2019-08-06 04:29:15', '2019-10-04 02:42:20', NULL, 1, 0),
(5, NULL, 'request', '', '', '', NULL, '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(6, NULL, 'manager', '', '', '', NULL, '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(7, NULL, 'service_provider', '', '', '', NULL, '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(8, NULL, 'cleanify_request', '', '', '', NULL, '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(9, 1, 'new_admin', 'User create admin notification', 'New admin created', '<p>Hello {{name}}</p>\n<p>A new admin account was created:</p>\n<p>User {{subjectName}}</p>\n<p>Email {{subjectEmail}}</p>', '{\"name\":\"user.name\",\"email\":\"user.email\",\"phone\":\"user.phone\",\"title\":\"constant.user.title\",\"subjectName\":\"subject.name\",\"subjectEmail\":\"subject.email\",\"subjectPhone\":\"subject.phone\",\"subjectTitle\":\"constant.subject.title\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 1),
(10, 1, 'reset_password', 'User reset password email', 'Password reset request for your account', '<p>Hello {{title}} {{name}}</p>\n<p>You are receiving this email because we received a password reset request for your account.</p>\n<p>Reset Password {{passwordResetUrl}}</p><p>If you did not request a password reset, no further action is required.</p>', '{\"name\":\"user.name\",\"email\":\"user.email\",\"phone\":\"user.phone\",\"title\":\"constant.user.title\",\"passwordResetUrl\":\"passwordResetUrl\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 1),
(11, 1, 'reset_password_success', 'Password changed successfully', 'Password changed successfully', '<p>You changed your password successfully.</p>\n<p>If you did change password, no further action is required.</p>\n<p>If you did not change password, protect your account.</p>', '{\"name\":\"user.name\",\"email\":\"user.email\",\"phone\":\"user.phone\",\"title\":\"constant.user.title\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 1),
(12, 2, 'tenant_credentials', 'Email sent to tenant, containing the PDF with the credentials and tenancy details.', 'Account created', '<p>Hello {{title}} {{name}}</p>\n<p>Your account was created.</p>\n<p>Here is an pdf with credentials.</p>', '{\"name\":\"user.name\",\"salutation\":\"tenant.title\",\"firstName\":\"tenant.first_name\",\"lastName\":\"tenant.last_name\",\"birthDate\":\"tenant.birthDate\",\"mobilePhone\":\"tenant.mobile_phone\",\"privatePhone\":\"tenant.private_phone\",\"email\":\"tenant.email\",\"phone\":\"tenant.phone\",\"title\":\"constant.tenant.title\",\"tenantCredentials\":\"tenantCredentials\",\"activationUrl\":\"activationUrl\",\"activationCode\":\"tenant.activation_code\"}', '2019-08-06 04:29:15', '2019-09-10 11:42:24', NULL, 1, 1),
(13, 3, 'announcement_pinboard', 'Email sent to tenants when admin publishes a pinned pinboard', 'New Pined pinboard: {{title}}', '<p>Hello {{salutation}} {{name}},</p>\n<p>Title {{title}}.</p>\n<p>{{content}}.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the announcement.</p>', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"title\":\"pinboard.title\",\"content\":\"pinboard.content\",\"execution_start\":\"pinboard.executionStartStr\",\"execution_end\":\"pinboard.executionEndStr\",\"category\":\"pinboard.categoryStr\",\"providers\":\"pinboard.providersStr\",\"buildings\":\"pinboard.buildingsStr\",\"districts\":\"pinboard.quartersStr\",\"autologin\":\"user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-10-01 10:54:15', NULL, 1, 1),
(14, 3, 'pinboard_published', 'Email sent to neighbour tenants when admin publishes a pinboard, or the pinboard is automatically published', 'New pinboard published {{authorSalutation}} {{authorName}}', '<p>Hello {{salutation}} {{name}},</p>\n<p>{{authorSalutation}} {{authorName}} published a new pinboard.</p>\n<p><em>{{content}}</em></p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the published pinboard.</p>', '{\"authorSalutation\":\"pinboard.user.title\",\"authorName\":\"pinboard.user.name\",\"salutation\":\"receiver.title\",\"name\":\"receiver.name\",\"content\":\"pinboard.content\",\"autologin\":\"receiver.autologinUrl\"}', '2019-08-06 04:29:15', '2019-09-28 03:22:17', NULL, 1, 1),
(15, 3, 'new_pinboard', 'Email sent to admins when tenant creates a new pinboard', 'New tenant pinboard', '<p>Hello {{salutation}} {{name}},</p>\n<p>Tenant {{subjectSalutation}} {{subjectName}} added a new pinboard</p>\n<p>{{content}}.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the published pinboard.</p>', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"subjectSalutation\":\"subject.title\",\"subjectName\":\"subject.name\",\"content\":\"pinboard.content\",\"type\":\"pinboard.type\",\"autologin\":\"user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-09-28 03:22:17', NULL, 1, 1),
(16, 3, 'pinboard_liked', 'Email sent to pinboard author when tenant liked the pinboard', '{{likerSalutation}} {{likerName}} liked your pinboard', '<p>Hello {{salutation}} {{name}},</p>\n<p>Tenant {{likerSalutation}} {{likerName}} liked your pinboard:</p>\n<p>{{content}}.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the liked pinboard.</p>', '{\"salutation\":\"pinboard.user.title\",\"name\":\"pinboard.user.name\",\"likerSalutation\":\"user.title\",\"likerName\":\"user.name\",\"content\":\"pinboard.content\",\"autologin\":\"pinboard.user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-09-28 03:22:17', NULL, 1, 1),
(17, 3, 'pinboard_commented', 'Email sent to pinboard author when tenant comments on the pinboard', '{{commenterSalutation}} {{commenterName}} commented on your pinboard', '<p>Hello {{salutation}} {{name}},</p>\n<p>Tenant {{commenterSalutation}} {{commenterName}} commented on your pinboard:</p>\n<p><em>{{comment}}</em>.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the pinboard.</p>', '{\"salutation\":\"pinboard.user.title\",\"name\":\"pinboard.user.name\",\"commenterSalutation\":\"user.title\",\"commenterName\":\"user.name\",\"content\":\"pinboard.content\",\"comment\":\"comment.comment\",\"autologin\":\"pinboard.user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-09-28 03:22:17', NULL, 1, 1),
(18, 3, 'pinboard_new_tenant_in_neighbour', 'Email sent to neighbour tenants when new neighbour moves in the neighbourhood', 'New tenant in the neighbour', '<p>Hello {{salutation}} {{name}},</p>\n<p>You got a new neighbour: {{subjectSalutation}} {{subjectName}}.</p>\n<p><em>{{content}}</em></p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the pinboard.</p>', '{\"subjectSalutation\":\"pinboard.user.title\",\"subjectName\":\"pinboard.user.name\",\"salutation\":\"receiver.title\",\"name\":\"receiver.name\",\"content\":\"pinboard.content\",\"autologin\":\"receiver.autologinUrl\"}', '2019-08-06 04:29:15', '2019-09-28 03:22:17', NULL, 1, 1),
(19, 4, 'listing_liked', 'Email sent to listing author when tenant liked the listing in marketplace', '{{salutation}} {{name}} liked your pinboard', '<p>Hello {{authorSalutation}} {{authorName}},</p>\n<p>Tenant {{salutation}} {{name}} liked your listing:</p>\n<p>{{title}}.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the listing.</p>', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"authorSalutation\":\"listing.user.title\",\"authorName\":\"listing.user.name\",\"title\":\"listing.title\",\"type\":\"listing.type\",\"autologin\":\"listing.user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-10-04 02:42:20', NULL, 1, 0),
(20, 4, 'listing_commented', 'Email sent to listing author when tenant comments on the listing', '{{salutation}} {{name}} commented on your pinboard', '<p>Hello {{authorSalutation}} {{authorName}},</p>\n<p>Tenant {{salutation}} {{name}} commented on  your listing:</p>\n<p><em>{{title}}</em>.</p>\n<p>Comment:</p>\n<p><em>{{comment}}</em>.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the listing.</p>', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"authorSalutation\":\"listing.user.title\",\"authorName\":\"listing.user.name\",\"title\":\"listing.title\",\"type\":\"listing.type\",\"comment\":\"comment.comment\",\"autologin\":\"listing.user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-10-04 02:42:20', NULL, 1, 0),
(21, 5, 'communication_tenant', 'Request Tenant communication templates', 'Hello {{subjectSalutation}} {{subjectName}}', '', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"subjectSalutation\":\"subject.title\",\"subjectName\":\"subject.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"category\":\"request.category.name\",\"unit\":\"request.unit.name\",\"building\":\"request.unit.building.name\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 2, 0),
(22, 5, 'communication_service_chat', 'Request Service providers communication templates', 'Hello {{subjectSalutation}} {{subjectName}}', '', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"subjectSalutation\":\"subject.title\",\"subjectName\":\"subject.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"category\":\"request.category.name\",\"unit\":\"request.unit.name\",\"building\":\"request.unit.building.name\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 2, 0),
(23, 5, 'new_request', 'Email sent to property managers when tenant creates a new request.', 'New Tenant request: {{title}}', '<p>Hello {{salutation}} {{name}},</p>\n<p>Tenant {{subjectSalutation}} {{subjectName}} created a new request</p>\n<p>Unit: {{category}}.</p>\n<p>Category: {{category}}.</p>\n<p>Title: {{title}}.</p>\n<p>{{description}}.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the request.</p>', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"subjectSalutation\":\"subject.title\",\"subjectName\":\"subject.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"category\":\"request.category.name\",\"unit\":\"request.unit.name\",\"building\":\"request.unit.building.name\",\"autologin\":\"user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 1),
(24, 5, 'communication_service_email', 'Notify service provider -> sends email to service provider and others (BCC, CC).', 'New assignment to request: {{title}}', '<p>Hello {{salutation}} {{name}},</p>\n<p>You have been assigned to an new request {{title}</p>\n<p>Category: {{category}}.</p>\n<p>Title: {{title}}.</p>\n<p>{{description}}.</p>', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"subjectSalutation\":\"subject.title\",\"subjectName\":\"subject.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"category\":\"request.category.name\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(25, 5, 'request_comment', 'When any party adds a new comment (tenant, property manager, service partner, admin or super admin) we notify all assigned people', 'New comment for request: {{title}}', '<p>Hello {{salutation}} {{name}},</p>\n<p>A new comment by {{commenterSalutation}} {{commenterName}} was made for request: {{title}}</p>\n<p><em>{{comment}}</em>.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the request.</p>', '{\"salutation\":\"user.title\",\"name\":\"user.name\",\"commenterSalutation\":\"comment.user.title\",\"commenterName\":\"comment.user.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"category\":\"request.category.name\",\"comment\":\"comment.comment\",\"autologin\":\"user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(26, 5, 'request_upload', 'When any party uploads a document/image', '{{uploaderSalutation}} {{uploaderName}} uploaded a new document for request: {{title}}', '<p>Hello {{receiverSalutation}} {{receiverName}},</p>\n<p>{{uploaderSalutation}} {{uploaderName}} uploaded a new document for request: {{title}}.</p>\n<p>Please find the uploaded file in the attachments of this email.</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the request.</p>', '{\"receiverSalutation\":\"receiver.title\",\"receiverName\":\"receiver.name\",\"uploaderSalutation\":\"uploader.title\",\"uploaderName\":\"uploader.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"category\":\"request.category.name\",\"autologin\":\"receiver.autologinUrl\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(27, 5, 'request_admin_change_status', 'When the Property Manager, Admin or Service partner change the status we notify the tenant, each time when status is changed from X to XY', 'Status changed for request: {{title}}', '<p>Hello {{salutation}} {{name}},</p>\n<p>Admin changed status for request {{title}} from {{originalStatus}} to {{status}}</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the request.</p>', '{\"salutation\":\"request.tenant.title\",\"name\":\"request.tenant.user.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"category\":\"request.category.name\",\"status\":\"constant.request.status\",\"originalStatus\":\"constant.originalRequest.status\",\"autologin\":\"request.tenant.user.autologinUrl\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(28, 5, 'request_internal_comment', 'When admin or service partner add a internal comment, we will notify each other.', 'New internal comment for request: {{title}}', '<p>Hello {{receiverSalutation}} {{receiverName}},</p>\n<p>{{senderSalutation}} {{senderName}} added a new private comment for request: {{title}}</p>\n<p><em>{{comment}}.</em></p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the request.</p>', '{\"receiverSalutation\":\"receiver.title\",\"receiverName\":\"receiver.name\",\"senderSalutation\":\"sender.title\",\"senderName\":\"sender.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"category\":\"request.category.name\",\"comment\":\"comment.comment\",\"autologin\":\"receiver.autologinUrl\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(29, 5, 'request_due_date_reminder', 'Send reminder email to property manager / admin 1 day before the due date is reached', 'Request: {{title}} is approaching its due date', '<p>Hello {{salutation}} {{name}},</p>\n<p>Due date for request {{title}} is {{dueDate}}</p>\n<p>Use <a href=\"{{autologin}}\">this link</a> to view the request.</p>', '{\"salutation\":\"receiver.title\",\"name\":\"receiver.name\",\"title\":\"request.title\",\"description\":\"request.description\",\"dueDate\":\"request.due_date\",\"category\":\"request.category.name\",\"autologin\":\"receiver.autologinUrl\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0),
(30, 8, 'cleanify_request_email', 'Email sent to Cleanify when the tenant makes a request.', 'New Cleanify request from: {{salutation}} {{firstName}} {{lastName}}', '<p>New Cleanify request,</p>\n<p>Name : {{salutation}} {{firstName}} {{lastName}}.</p>\n<p>Phone : {{phone}}.</p>\n<p>Email : {{email}}.</p>\n<p>Address:</p>\n<p>{{address}}, {{city}} {{zip}}.</p>', '{\"salutation\":\"form.title\",\"firstName\":\"form.first_name\",\"lastName\":\"form.last_name\",\"address\":\"form.address\",\"zip\":\"form.zip\",\"city\":\"form.city\",\"email\":\"form.email\",\"phone\":\"form.phone\"}', '2019-08-06 04:29:15', '2019-08-06 04:29:15', NULL, 1, 0);