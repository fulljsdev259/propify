<?php
return [
    'user' => [
        'add_admin' => 'Add Administrator',
        'edit_admin' => 'Edit Administrator',        
        'saved' => 'User saved successfully',
        'deleted' => 'User deleted',
        'not_found' => 'User not found',
        'profile_image' => 'Profile image',
        'profile_text' => 'Profile text',
        'logo' => 'Logo',
        'circle_logo' => 'Circle Logo',
        'favicon_icon' => 'Favicon Icon',
        'resident_logo' => 'Resident Logo',                
        'notification_saved' => 'Notificatin setting saved',        
        'request_category_saved' => 'Service request category saved',
        'request_category_deleted' => 'Service request category deleted',                
        'errors' => [
            'not_found' => "User not found",            
            'image_upload' => "User image upload error :",
            'incorrect_password' => "User password incorrect",
            'email_missing' => "email is missing",
            'email_already_exists' => "This [:email] email already exist, Select other email",
            'email_not_exists' => "This [:email] email not exist",            
            'deleted' => "User deleted error: ",
        ],        
    ],
    'resident' => [
        'view' => 'View',
        'name' => 'Resident',
        'view_title' => 'View resident',
        'edit_title' => 'Edit Resident',
        'download_credentials' => 'Download credentials',
        'send_credentials' => 'Send credentials',
        'credentials_sent' => 'Credentials sent',
        'credentials_send_fail' => 'Credentials file not found. Try updating the resident password to regenerate it',
        'credentials_download_failed' => 'Credentials file not found. Try updating the resident password to regenerate it',
        'add' => 'Add Resident',
        'saved' => 'Resident saved',
        'deleted' => 'Resident deleted',
        'status_changed' => 'Status changed',
        'password_reset' => 'Resident password reset successfully',
        'update' => 'Update',       
        'birth_date' => 'Birth date',
        'nation' => 'Nation',
        'mobile_phone' => 'Mobile phone',
        'work_phone' => 'Work phone',        
        'private_phone' => 'Personal phone',
        'created_date' => 'Created Date',
        'pinboard' => 'Pinboard',
        'listings' => 'Listings',
        'company' => 'Company name',
        'building' => [
            'name' => 'Building',
        ],
        'unit' => [
            'name' => 'Unit',
        ],
        'search_building' => 'Search building',
        'search_unit' => 'Search unit',        
        'errors' => [
            'not_found' => "Resident not found",
            'incorrect_email' => "Incorrect email address",
            'create' => "Resident create error: ",
            'update' => "Resident update error: ",
            'deleted' => "Resident Delete error: ",
            'not_allowed_change_status' => 'You are not allowed to change status.',
            'not_allowed_change_type_has_request_contract' => 'You can\'t change type as this resident has :contracts_count contracts and :requests_count requests',
            'not_allowed_change_type_has_contract' => 'You can\'t change type as this resident has :contracts_count contracts',
            'not_allowed_change_type_has_request' => 'You can\'t change type as this resident has :requests_count requests',
        ],
        'personal_details_card' => 'Personal details',
        'account_info_card' => 'User login',
        'contact_info_card' => 'Contact details',        
        'contract' => [
            'title' => 'Contract',
            'rent_end' => 'Rent end',
            'rent_start' => 'Rent start',
            'rent_type' => 'Rent type',            
            'rent_duration' => 'Rent duration',
            'rent_durations' => [
                'unlimited' => 'Unlimited',
                'limited' => 'Limited',
            ],
            'contract_pdf' => 'Contract PDF',
            'filename' => 'Name',
            'deposit_amount' => 'Deposit amount',
            'type_of_deposit' => 'Type of deposit',
            'deposit_types' => [
                'bank_deposit' => 'Bank Deposit',                
                'bank_guarantee' => 'Bank Guarantee',
                'insurance' => 'Insurance',
                'other' => 'Other',
            ],
            'deposit_status' => [
                'label' => 'Deposit Status',
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'contract_id' => 'Contract ID',
            'rent_status' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
            'add' => 'Add new contract',
            'pdf_only_desc' => 'Please note that only PDF files can be uploaded',
            'saved' => 'Contract saved',
            'status_count' => [
                'total' => 'Total contracts',
                'active' => 'Active contracts',
                'inactive' => 'Inactive contracts',
            ],
        ],      
        'status' => [
            'label' => 'Status',
            'active' => 'Active',
            'not_active' => 'Not active',
            'total' => 'Total',
        ],
        'credentials_pdf' => [
            'resident_credentials' => 'Resident credentials',
            'code' => 'Personal unlock code',
            'telephone' => 'Telephone',
            'your_email' => 'Your e-mail address',
            'email' => 'E-Mail:',            
            'welcome' => 'Welcome to the resident portal of the',
            'content_1' => 'We are pleased to inform you that an account has been set up for you in the resident\'s total. and send you the activation code.',
            'offer' => 'What does the application offer?',
            'offers' => '
                <li>With the digital resident dossier you have access to all relevant documents, such as Tenancy agreement, house rules or other documents relating to the property</li>
                <li>The ticketing system enables you to process your enquiries simply and uncomplicatedly - with the following features Communicate your concerns to the administration at any time and from any place.</li>
                <li>On the market and lending place you can send objects to your neighbourhood. sell or lend.</li>
                <li>Share Pinboard with your neighbours by publishing a contribution. The pinboard is also used by the administration for the communication, so all are always on the Running.</li>
                <li>Further micro apps within the application set new standards in living quality where various services can be conveniently used.</li>
            ',
            'register' => 'First registration and activation of your account',
            'content_2' => 'To register, please click on the link below and log in with your e-mail address and the personal unlock code. Once you have logged in, you can Define your own password and use it from now on for the login.',
            'link_application' => 'Link to the application',
            'content_3' => 'We look forward to welcoming you on board!',
            'content_4' => 'If you should need assistance with the registration, then we are gladly at your disposal.',
            'your_sincerely' => 'Yours sincerely',
            'your_administration' => 'your administration'
        ],
        'type' => [
            'label' => 'Type',
            'tenant' => 'Tenant',
            'owner' => 'Owner',
        ],
    ],
    'building' => [
        'title' => 'Buildings',
        'edit_title' => 'Edit Building',
        'add' => 'Add Building',
        'cancel' => 'Cancel',
        'deleted' => 'Building deleted successfully',
        'units' => 'Units',
        'saved' => 'Building saved',
        'floors' => 'Floors',
        'under_floor' => 'Under floor',
        'ground_floor' => 'Ground floor',
        'basement' => 'Basement',
        'attic' => 'Attic',
        'floor_nr' => 'Number of floors',
        'internal_building_id' => 'Internal Building Id',       
        'address_search' => 'Please enter address',
        'not_found' => 'Building not found',
        'media_category' => [
            'house_rules' => 'House rules',
            'operating_instructions' => 'Operating instructions',
            'care_instructions' => 'Care instructions',
            'other' => 'Other',
        ],
        'files' => 'Files',
        'add_files' => 'Add files',
        'providers' => 'Service providers',        
        'select_media_category' => 'Selected media category',
        'quarter' => 'Quarter',
        'managers' => 'Managers',
        'house_num' => 'House Nr.',
        'assign_managers' => 'Assign managers',
        'unassign_manager' => 'Unassign',
        'managers_assigned' => 'Managers assigned',
        'occupied_units' => 'Ocuppied units',
        'free_units' => 'Free units',       
        'document' => [
            'uploaded' => 'Document uploaded',
            'deleted' => 'Document deleted',
        ],
        'service' => [
            'deleted' => 'Service removed from this building',
        ],
        'errors' => [
            'not_found' => "Building not found",
            'manager_not_found' => "Property manager not found",
            'deleted' => "Building deleted error: ",
            'manager_assigned' => "Property Managers assign to Building error: ",
            'provider_deleted' => "Service Provider deleted error: ",
        ],
        'delete_building_modal' => [
            'title' => 'Delete Building(s)',
            'description_unit' => 'Units are assigned to the selected property. If you want to delete the units as well, please activate the option below.',
            'description_request' => 'Requests are assigned to the selected property. If you also want to delete request as well, please activate the option below.',
            'description_both' => 'Units and requests are assigned to the selected property. If you also want to delete them, please activate the options below.',
            'delete_units' => 'Delete Unit(s)',
            'dont_delete_units' => 'Don\'t Delete Unit(s)',
            'delete_requests' => 'Delete Request(s)',
            'dont_delete_requests' => 'Don\'t Delete Request(s)',
        ],
        'assigned_buildings' => 'Assigned buildings',
        'warning_bar' => [
            'title' => 'Problem Found',
            'message' => "It looks like you've not selected fortimo members as email receivers for request categories. Please select them from settings drawer available at Requests tab"
        ]
    ],
    'unit' => [
        'title' => 'Units',
        'not_found' => 'Unit not found',
        'add' => 'Add Unit',
        'edit' => 'Edit Unit',
        'name' => 'Unit number',
        'auto_create_question' => 'Do you want to create unit automatically?',
        'auto_create_description' => 'With this option, you will be able to input number of units in floor and such units will be created automatically on saving of building',
        'deleted' => 'Unit deleted',
        'saved' => 'Unit saved',
        'floor' => 'Floor',
        'floor_title' => [
            'under_ground_floor' => "UG",
            'ground_floor' => "EG",
            'upper_ground_floor' => "OG",
            'top_floor' => "Attic",
        ],
        'rooms' => 'Rooms',
        'sq_meter' => 'Sq Meter',
        'room_no' => 'Number of rooms',
        'building' => 'Building',
        'basement' => 'Basement',
        'attic' => 'Attic',        
        'assigned_resident' => 'Assigned resident',
        'resident_assigned' => 'Resident assigned',
        'resident_unassigned' => 'Resident unassigned',
        'assignment' => 'Assigned residents',
        'type' => [
            'label' => 'Type',
            'apartment' => 'Apartment',
            'business' => 'Commercial space',
            'hobby_room' => 'Hobby room',
            'storeroom' => 'Storeroom',
            'underground_parking_space' => 'Underground parking space',
            'outdoor_parking' => 'Outdoor parking',
            'motorbike_pitch' => 'Motorbike pitch'
        ],
        'errors' => [
            'not_found' => "Unit not found",
            'create' => "Unit create error: ",
            'update' => "Unit update error: ",
            'resident_assign' => "Resident assign error: ",
            'resident_not_assign' => "Resident not assigned to this unit",
            'resident_not_found' => "Resident not found",
            'deleted' => "Unit deleted error: ",
        ],
    ],   
    'pinboard' => [
        'title' => 'Pinboard',
        'title_label' => 'Title',
        'content' => 'Content',
        'preview' => 'Preview',
        'add' => 'Add Pinboard',        
        'saved' => 'Pinboard saved',
        'view_incresead' => "Views increased successfully",        
        'deleted' => 'Pinboard deleted',
        'edit_title' => 'Edit Pinboard',
        'likes' => 'Likes',
        'views' => 'Views',
        'published_at' => 'Published',
        'publish' => 'Publish',
        'unpublish' => 'Unpublish',
        'buildings' => 'Buildings',
        'announcement' => 'Announcement',
        'notify_email' => 'Notify email',
        'notify_email_description' => 'With this option, you will be able to enable email notifiation',
        'announcement_to' => 'Announcement to',
        'comments' => 'Comments',
        'images' => 'Photos and documents',
        'attachments' => 'Attachments',
        'category_default_image_label' => 'Do want to use this image?',
        'placeholders' => [            
            'search_provider' => 'Search provider',
        ],
        'type' => [
            'label' => 'Type',
            'post' => 'Post',
            'article' => 'Article',
            'new_neighbour' => 'New neighbour',
            'announcement' => 'Announcement',
        ],
        'sub_type' => [
            'label' => 'Subtype',
            'important' => 'Important',
            'critical' => 'Critical',
            'maintenance' => 'Maintenance',
        ],
        'errors' => [
            'not_found' => "Pinboard not found",
            'quarter_not_found' => "Quarter not found",
            'building_not_found' => "Building not found",
            'provider_not_found' => "Service provider not found",
            'deleted' => "Pinboard deleted error: ",
        ],
        'status' => [
            'label' => 'Status',
            'new' => 'New',
            'published' => 'Published',
            'unpublished' => 'Unpublished',
            'not_approved' => 'Not approved',
        ],
        'visibility' => [
            'label' => 'Visibility',
            'address' => 'Address',
            'quarter' => 'Quarter',
            'all' => 'All',
        ],
        'assign_type' => 'Type',        
        'execution_period' => [
            'label' => 'One day or multiple days',
            'single_day' => 'Single day',
            'many_day' => 'Multiple days',
        ],
        'specify_time_question' => 'Do you want to specify time?',
        'specify_time_description' => 'With this option, you will be able to specify time of announcement',
        'execution_interval' => [
            'label' => 'Execution interval',
            'date' => 'Execution Date',
            'end' => 'Execution End',
            'start' => 'Execution Start',
            'from' => 'From',
            'separator' => 'To',
        ],
        'category' => [
            'label' => 'Category',
            'general' => 'General',
            'maintenance' => 'Maintenance',
            'electricity' => 'Electricity',
            'heating' => 'Heating',
            'sanitary' => 'Sanitary',
        ],
    ],
    'service' => [
        'title' => 'Services',
        'view' => 'View',
        'view_title' => 'View Service',
        'add_title' => 'Add Service',
        'edit_title' => 'Edit Service',
        'saved' => 'Service saved',
        'deleted' => 'Service deleted',
        'category' => [
            'label' => 'Category',
            'electrician' => 'Electrician',
            'heating_company' => 'Heating company',
            'lift' => 'Lift',
            'sanitary' => 'Sanitary',
            'key_service' => 'Key service',
            'caretaker' => 'Caretaker',
            'real_estate_service' => 'Real estate service',
        ],
        'contact_details' => 'Contact details',
        'user_credentials' => 'User credentials',
        'company_details' => 'Company details',
        'assign_type' => 'Type',        
        'placeholders' => [
            'category' => 'Select category',
        ],
        'errors' => [
            'not_found' => "Service Provider not found",
            'create' => "Service Provider create error: ",
            'update' => "Service Provider updated error: ",
            'deleted' => "Service Provider deleted error: ",
            'quarter_not_found' => "Quarter not found",
            'building_not_found' => "Building not found",
            'building_already_assign' => "Building already assigned through quarter",
        ],
    ],
    'quarter' => [
        'title' => 'Quarters',
        'add' => 'Add Quarter',
        "edit" => "Edit Quarter",
        'saved' => 'Quarter saved',
        'deleted' => 'Quarter deleted',        
        'required' => 'This field is required',
        'buildings' => 'Buildings',
        'count_of_buildings' => 'Allowed buildings',
        'buildings_count' => 'Count of buildings',
        'total_units_count' => 'Count of units',
        'occupied_units_count' => 'Count of occupied units',
        'active_residents_count' => 'Count of active residents',
        'assignment' => 'Assignment of managers/administrator',
        'errors' => [
            'not_found' => "Quarter not found",
            'deleted' => "Quarter deleted error: ",
        ],
    ],
    'request' => [        
        'deleted' => 'Request deleted',
        'title' => 'Requests',
        'created' => 'Created',
        'saved' => 'Request saved',
        'prop_title' => 'Title',
        'category' => 'Category',
        'edit_title' => 'Edit Request',
        'add_title' => 'Add Request',
        'mass_edit' => [
            'label' => 'Mass Edit',
            'options' => [
                'service_provider' => 'Service provider',
                'property_manager' => 'Property manager',
                'change_status' => 'Change status'
            ],
            'service_provider' => [
                'modal' => [
                    'heading_title' => 'Assign service providers heading',
                    'content_label' => 'You can select service provider(s) here',
                    'footer_button' => 'Assign service providers',
                    'switcher_label' => 'Notify service providers',
                    'switcher_desc' => 'You can inform the concerned service providers',
                ]
            ],
            'property_manager' => [
                'modal' => [
                    'heading_title' => 'Assign property managers heading',
                    'content_label' => 'You can select property manager(s) here',
                    'footer_button' => 'Assign property managers',
                ]
            ],
            'change_status' => [
                'modal' => [
                    'heading_title' => 'Change status heading',
                    'content_label' => 'You can change status here',
                    'footer_button' => 'Change status',
                ]
            ],
        ],
        'due_date' => 'Due date',
        'solved_date' => 'Completed date',
        'closed_date' => 'Closed date',
        'service' => 'Service',
        'created_by' => 'Created by',
        'is_public' => 'Public',
        'public_title' => 'Make request public',
        'public_desc' => 'You can mark this request as public and make it visible to other persons in the building or quarter.',
        'visibility_title' => 'For whom to make visible?',
        'visibility_desc' => 'Indicate whether residents can see within a building or even within the quarter.',
        'send_notification_title' => 'Notify residents',
        'send_notification_desc' => 'You can inform the concerned residents via email about this public request.',
        'comments' => 'Comments',
        'assigned_to' => 'Assigned to',
        'assign_providers' => 'Assign providers',
        'assign_managers' => 'Assign managers',
        'assigned_service_providers' => 'Assigned service providers',
        'assigned_property_managers' => 'Assigned property managers',
        'notify' => 'Notify',
        'public_legend' => 'Set this option to make the request visible to all resident neighbours',
        'conversation' => 'Conversation',
        'conversation_created' => "Conversation comment created",
        'internal_notice_saved' => "Internal Notice saved",
        'internal_notice_updated' => "Internal Notice updated",
        'internal_notice_deleted' => "Internal Notice deleted",
        'open_conversation' => 'Open',
        'other_recipients' => 'Other recipients',
        'recipients' => 'Recipients',
        'images' => 'Photos and documents',
        'no_images_message' => 'No files uploaded',
        'request_details' => 'Request details',
        'internal_notices' => 'Internal notices',
        'status_changed' => 'Status changed',
        'priority_changed' => 'Priority changed',
        'assignment' => 'Managers/service providers',        
        'active_reminder_switcher' => 'Reminder',
        'days_left' => 'How many days before should the email be sent?',
        'send_person' => 'Which person should be notified?',
        'sort' => 'Sort',
        'reset_sort' => 'Reset Sort',
        'creation_date' => 'Creation Date',
        'category_list' => [
            'general' => 'General concerns',
            'malfunction' => 'Malfunction',
            'deficiency' => 'Deficiency'
        ],
        'sub_category' => [
            'surrounding' => 'Surrounding',
            'real_estate' => 'Real Estate',
            'flat' => 'Flat',
        ],
        'media'  => [
            'added' => 'Document added',
            'removed' => 'Document removed',
            'deleted' => 'Document deleted',
            'delete' => 'Delete',
        ],
        'priority' => [
            'label' => 'Priority',
            'urgent' => 'Urgent',
            'low' => 'Low',
            'normal' => 'Normal',
        ],
        'internal_priority' => [
            'label' => 'Internal priority',
            'urgent' => 'Urgent',
            'low' => 'Low',
            'normal' => 'Normal',
        ],
        'defect_location' => [
            'label' => 'Defect location',
            'apartment' => 'Apartment',
            'building' => 'Building',
            'environment' => 'Environment',
        ],
        'qualification' => [
            'label' => 'Qualification',
            'none' => 'None',
            'optical' => 'Optical',
            'sia' => 'Sia',
            '2_year_warranty' => '2 Year Warranty',
            'cost_consequences' => 'Cost consequences',
        ],
        'location' => [
            'house_entrance' => 'House Entrance',
            'staircase' => 'Staircase',
            'elevator' => 'Elevator',
            'car_park' => 'Underground Car park',
            'washing' => 'Washing/Drying',
            'heating' => 'Technology/Heating',
            'electro' => 'Technology/Electro',
            'facade' => 'Facade',
            'roof' => 'Roof',
            'other' => 'Other'
        ],
        'room' => [
            'bath' => 'Bathroom/WC',
            'shower' => 'Shower/WC',
            'entrance' => 'Entrance',
            'passage' => 'Passage',
            'basement' => 'Basement',
            'kitchen' => 'Kitchen',
            'storeroom' => 'Reduite',
            'habitation' => 'Habitation',
            'room1' => 'Room 1',
            'room2' => 'Room 2',
            'room3' => 'Room 3',
            'room4' => 'Room 4',
            'all' => 'All',
            'other' => 'Other'
        ],
        'capture_phase' => [
            'other' => 'Other',
            'construction' => 'Construction phase',
            'shell' => 'Shell Acceptance',
            'preliminary' => 'Preliminary Acceptance',
            'work' => 'Acceptance of Work',
            'surrender' => 'Surrender',
            'inspection' => 'Acceptance'
        ],
        'payer' => [
            'landlord' => 'Landlord',
            'resident' => 'Resident',
            'resident/landlord' => 'Resident/Landlord'
        ],
        'status' => [
            'label' => 'Status',
            'received' => 'Received',
            'assigned' => 'Assigned',
            'in_processing' => 'In processing',
            'reactivated' => 'Reactivated',
            'done' => 'Done',
            'archived' => 'Archived',
            'solved' => 'Solved',
            'pending' => 'Pending'
        ],
        'category_options' => [
            'disturbance' => 'Disturbance',
            'defect' => 'Defect',
            'other' => 'Other',
            'room' => 'Room',
            'range' => 'Range',
            'component' => 'Component',
            'capture_phase' => 'Capture Phase',
            'cost' => 'Cost Impact',
            'keywords' => 'Keywords',
        ],
        'placeholders' => [
            'category' => 'Select category',            
            'qualification' => 'Select qualification',
            'status' => 'Select status',
            'due_date' => 'Pick due date',
            'resident' => 'Search for a resident',            
            'visibility' => 'Select visibility',
            'person' => 'Search for a person',
        ],
        'mail' => [
            'body' => 'Body',
            'subject' => 'Subject',
            'to' => 'To',            
            'notify' => 'Send Email',            
            'provider' => 'Provider',
            'manager' => 'Manager',
            'cancel' => 'Cancel',
            'send' => 'Send',
            'cc' => 'CC',
            'bcc' => 'BCC',
            'success' => 'Notification mail sent successfully',            
        ],
        'user_type' => [
            'label' => 'Type',
            'provider' => 'Service',
            'manager' => 'Manager',
            'user' => 'Administrator',
        ],
        'visibility' => [
            'label' => 'Visibility',
            'resident' => 'Private',
            'quarter' => 'Quarter',
            'building' => 'Building',
        ],
        'errors' => [
            'not_found' => 'Service Request not found',
            'not_allowed_change_status' => 'You are not allowed to change status.',
            'provider_not_found' => 'Service Provider not found',
            'tag_not_found' => 'Tag not found',
            'user_not_found' => 'User not found',
            'conversation_not_found' => "Conversation not found",
            'statistics_error' => "Request statistics error: ",
            'internal_notice_not_found' => "Internal Notice not found",
            'deleted' => "Service Request deleted error: ",
        ],                
        'actions' => 'Actions',
        'download_pdf' => [
            'title' => 'Download PDF',
            'entrepreneur_signature'=> 'Signature entrepreneur',
            'customer_signature'=> 'Customer Signature',
            'service_request' => 'Service Request',
            'contact_details' => 'Contact Details',
            'contact_text' => 'These are the contact details of the current resident/owner of the housing unit.',
        ],
        'go_to_building' => 'go to building'
    ],
    'request_category' => [
        'title' => 'Request categories',
        'add' => 'Add category',
        'cancel' => 'Cancel',
        'required' => 'This field is required',
        'parent' => 'Parent category',
        'errors' => [
            'not_found' => "Service Request Category not found",
            'parent_not_found' => "Parent Service Request Category not found",
            'multiple_level_not_found' => "Multiple level nested categories are not allowed",
            'used_by_request' => "Service Request Category it is used by a Service Request"
        ]
    ],
    'property_manager' => [
        'title' => 'Property managers',
        'add' => 'Add Property Manager',
        'saved' => 'Property manager saved',
        'deleted' => 'Property manager deleted',
        'edit_title' => 'Edit Property Manager',        
        'profession' => 'Profession',
        'slogan' => 'Slogan',
        'linkedin_url' => 'Linkedin URL',
        'xing_url' => 'Xing URL',
        'details_card' => 'Details',                
        'delete_with_reassign_modal' => [
            'title' => 'Delete & reassign buildings',
            'description' => 'The selected property manager is linked to properties. You can assign the properties to another person. To do this, select a property manager from the list.',
            'search_title' => 'Search Property Manager',
        ],
        'delete_without_reassign' => 'Delete',
        'profile_card' => 'User Profile',
        'social_card' => 'Social Media',
        'assign_type' => 'Type',        
        'errors' => [
            'not_found' => "Property Manager not found",
            'create' => "Property Manager create error: ",
            'update' => "Property Manager updated error: ",
            'quarter_not_found' => "Quarter not found",
            'building_not_found' => "Building not found",
            'building_already_assign' => "Building already assigned through quarter",
            'building_assign_deleted_property_manager' => "You cannot assign buildings to an deleted Property Manager",
            'deleted' => "Property Manager deleted error: ",
        ],
    ],
    'house_owner' => [
        'title' => 'House Owners',
        'add' => 'Add House Owner',
        'saved' => 'House Owner Saved',
        'deleted' => 'House Owner Deleted',
        'edit_title' => 'Edit House Owner',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'profession' => 'Profession',
        'slogan' => 'Slogan',
        'linkedin_url' => 'Linkedin URL',
        'xing_url' => 'Xing URL',
        'password' => 'Password',
        'confirm_password' => 'Confirm password',
        'building_card' => 'Assign buildings',
        'details_card' => 'Details',
        'no_buildings' => 'There are no buildings assigned',
        'add_buildings' => 'Add buildings',
        'buildings_search' => 'Search for buildings',
        'quarters' => 'Quarters',
        'delete_with_reassign_modal' => [
            'title' => 'Delete & reassign buildings',
            'description' => 'The selected house owner is linked to properties. You can assign the properties to another person. To do this, select a house owner from the list.',
            'search_title' => 'Search House Owner',
        ],
        'delete_without_reassign' => 'Delete',
        'profile_card' => 'User Profile',
        'social_card' => 'Social Media',
        'assign_type' => 'Type',
        'building_already_assigned' => 'Building is already inside on a quarter',
        'errors' => [
            'not_found' => "House Owner not found",
            'create' => "House Owner create error: ",
            'update' => "House Owner updated error: ",
            'quarter_not_found' => "Quarter not found",
            'building_not_found' => "Building not found",
            'building_already_assign' => "Building already assigned through quarter",
            'building_assign_deleted_house_owner' => "You cannot assign buildings to an deleted House Owner",
            'deleted' => "House Owner deleted error: ",
        ],
    ],
    'listing' => [
        'title' => 'Listings',
        'add' => 'Add Listing',
        'edit_title' => 'Edit Listing',                
        'listing_title' => 'Title',
        'published_at' => 'Published',
        'publish' => 'Publish',
        'unpublish' => 'Unpublish',
        'likes' => 'Likes',
        'saved' => 'Listing saved',
        'deleted' => 'Listing deleted',
        'comments' => 'Comments',
        'contact' => 'Contact',
        'price' => 'Price',
        'comment_created' => "Comment successfully created",
        'errors' => [
            'not_found' => "Listing not found",
            'deleted' => "Listing deleted error: ",
        ],
        'type' => [
            'label' => 'Type',
            'sell' => 'Sell',
            'lend' => 'Lend',
            'service' => 'Service',
            'giveaway' => 'Give away',
        ],
        'status' => [
            'label' => 'Status',
            'published' => 'Published',
            'unpublished' => 'Unpublished',
        ],
        'visibility' => [
            'label' => 'Visibility',
            'address' => 'Address',
            'quarter' => 'Quarter',
            'all' => 'All',
        ],
    ],
    'template' => [
        'saved' => 'Template saved',
        'deleted' => 'Template deleted',
        'add' => 'Add',
        'title' => 'Templates',
        'subject' => 'Subject',
        'body' => 'Body',
        'category' => 'Category',
        'tags' => 'Tags',
        'placeholders' => [
            'category' => 'Choose category',
        ],
        'errors' => [
            'not_found' => "Template not found",
        ]
    ],
    'cleanify' => [
        'page_title' => 'Cleanify request',        
        'address' => 'Address',
        'save' => 'Send request',
        'success' => 'Cleanify request sent successfully',
        'terms_and_conditions' => 'Accept Terms & Conditions',
        'terms_text' => 'Terms text here, long text',
    ],
    'editor' => [
        'bold' => 'Bold',
        'underline' => 'Underline',
        'italic' => 'Italic',
        'forecolor' => 'Color',
        'bgcolor' => 'Backcolor',
        'strikethrough' => 'Strikethrough',
        'eraser' => 'Eraser',
        'source' => 'Codeview',
        'quote' => 'Quote',
        'fontfamily' => 'Font family',
        'fontsize' => 'Font size',
        'head' => 'Head',
        'orderlist' => 'Ordered list',
        'unorderlist' => 'Unordered list',
        'alignleft' => 'Align left',
        'aligncenter' => 'Align center',
        'alignright' => 'Align right',
        'link' => 'Insert link',
        'link_target' => 'Open mode',
        'text' => 'Text',
        'submit' => 'Submit',
        'cancel' => 'Cancel',
        'unlink' => 'Unlink',
        'table' => 'Table',
        'emotion' => 'Emotions',
        'img' => 'Image',
        'upload_img' => 'Upload',
        'link_img' => 'Link',
        'video' => 'Video',
        'width' => 'width',
        'height' => 'height',
        'location' => 'Location',
        'loading' => 'Loading',
        'searchlocation' => 'search',
        'dynamic_map' => 'Dynamic',
        'clear_location' => 'Clear',
        'lang_dynamic_one_location' => 'Only one location in dynamic map',
        'insertcode' => 'Insert Code',
        'undo' => 'Undo',
        'redo' => 'Redo',
        'fullscreen' => 'Full screnn',
        'open_link' => 'open link',
        'upload_place_txt' => 'uploading__',
        'upload_timeout_place_txt' => 'upload_timeout__',
        'upload_error_place_txt' => 'upload_error__',
        'title' => 'Title',
        'in_format' => 'In format',
        'rows' => 'Rows',
        'cols' => 'Columns',
        'color' => [
            'dark_red' => 'Dark red',
            'violet' => 'Violet',
            'red' => 'Red',
            'fresh_pink' => 'Fresh pink',
            'navy_blue' => 'Navy blue',
            'blue' => 'Blue',
            'blue_lake' => 'Blue Lake',
            'blue_green' => 'Blue green',
            'green' => 'Green',
            'olive' => 'Olive',
            'light_green' => 'Light green',
            'orange' => 'Orange',
            'gray' => 'Gray',
            'silver' => 'Silver',
            'black' => 'Black',
            'white' => 'White',
        ]
    ],
];
