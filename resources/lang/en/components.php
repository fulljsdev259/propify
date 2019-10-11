<?php
return [
    'common' => [
        'audit' => [
            'type' => [
                'pinboard' => 'Pinboard',
                'listing' => 'Listing',
                'request' => 'Request'
            ],
            'filter' => [
                'type' => [
                    'pinboard' => 'Pinboard',
                    'listing' => 'Listing',
                    'request' => 'Request'
                ],
                'pinboard' => [
                    'created' => 'Created',
                    'updated' => 'Updates',
                    'provider_assigned' => 'Provider assigned',
                    'user_assigned' => 'User assigned',
                    'media_uploaded' => 'Media uploaded',
                    'media_deleted' => 'Media deleted'
                ],
                'listing' => [

                ],
                'request' => [
                    'created' => 'Created',
                    'updated' => 'Updates',
                    'provider_assigned' => 'Provider assigned',
                    'user_assigned' => 'User assigned',
                    'media_uploaded' => 'Media uploaded',
                    'media_deleted' => 'Media deleted'
                ]
            ],
            'content' => [
                'withId' => [
                    'pinboard' => [
                        'created' => '{userName} opened this {auditable_type} on {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'status' => 'The status changed from "{old}" to "{new}".',
                            'published_at' => 'Pinboard published on {new}.'
                        ]
                    ],
                    'listing' => [
                        'created' => '{userName} opened this {auditable_type}.',
                        'updated' => [
                            'title' => 'The title changed from "{old}" to "{new}".',
                            'status' => 'The status changed from "{old}" to "{new}".',
                            'due_date' => 'The due date changed from "{old}" to "{new}".',
                            'priority' => 'The priority changed from "{old}" to "{new}".',
                            'internal_priority' => 'The internal priority has been changed from "{old}" to "{new}".',
                            'category_id' => 'The category changed from "{old}" to "{new}".',
                            'qualification' => 'The qualification changed from "{old}" to "{new}".',
                            'visibility' => 'The visibility changed from "{old}" to "{new}".',
                        ],
                        'provider_assigned' => '{providerName} has been assigned as provider.',
                        'user_assigned' => '{userName} has been assigned as manager.',
                        'media_uploaded' => 'Media uploaded',
                        'media_deleted' => 'Media deleted',
                    ],
                    'request' => [
                        'created' => '{userName} opened this {auditable_type}.',
                        'updated' => [
                            'title' => 'The title changed from "{old}" to "{new}".',
                            'status' => 'The status changed from "{old}" to "{new}".',
                            'due_date' => 'The due date changed from "{old}" to "{new}".',
                            'priority' => 'The priority changed from "{old}" to "{new}".',
                            'internal_priority' => 'The internal priority has been changed from "{old}" to "{new}".',
                            'category_id' => 'The category changed from "{old}" to "{new}".',
                            'qualification' => 'The qualification changed from "{old}" to "{new}".',
                            'visibility' => 'The visibility changed from "{old}" to "{new}".',
                        ],
                        'provider_assigned' => '{providerName} has been assigned as provider.',
                        'provider_unassigned' => 'Provider {providerName} has been unassigned.',
                        'manager_assigned' => '{propertyManagerFirstName} {propertyManagerLastName} has been assigned as manager.',
                        'manager_unassigned' => 'Manager {propertyManagerFirstName} {propertyManagerLastName} has been unassigned.',
                        'user_assigned' => '{userName} has been assigned as manager.',
                        'media_uploaded' => 'Media uploaded',
                        'media_deleted' => 'Media deleted',
                    ]
                ],
                'withNoId' => [
                    'pinboard' => [
                        'created' => '{userName} opened this {auditable_type} on {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'status' => 'The status changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'published_at' => 'Pinboard published on {new} on {auditable_type} #{auditable_id}.'
                        ]
                    ],
                    'listing' => [
                        'created' => '{userName} opened this {auditable_type} on {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'title' => 'The title changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'status' => 'The status changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'due_date' => 'The due date changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'priority' => 'The priority changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'category_id' => 'The category changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'qualification' => 'The qualification changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'visibility' => 'The visibility changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                        ],
                        'provider_assigned' => '{providerName} has been assigned as provider on {auditable_type} #{auditable_id}.',
                        'user_assigned' => '{userName} has been assigned as manager on {auditable_type} #{auditable_id}.',
                        'media_uploaded' => 'Media uploaded on {auditable_type} #{auditable_id}.',
                        'media_deleted' => 'Media deleted on {auditable_type} #{auditable_id}.',
                    ],
                    'request' => [
                        'created' => '{userName} opened this {auditable_type} on {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'title' => 'The title changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'status' => 'The status changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'due_date' => 'The due date changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'priority' => 'The priority changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'category_id' => 'The category changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'qualification' => 'The qualification changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                            'visibility' => 'The visibility changed from "{old}" to "{new}" on {auditable_type} #{auditable_id}.',
                        ],
                        'provider_assigned' => '{providerName} has been assigned as service provider on {auditable_type} #{auditable_id}.',
                        'provider_unassigned' => 'Service provider {providerName} has been unassigned on {auditable_type} #{auditable_id}.',
                        'manager_assigned' => '{propertyManagerFirstName} {propertyManagerLastName} has been assigned as manager on {auditable_type} #{auditable_id}.',
                        'manager_unassigned' => 'Manager {propertyManagerFirstName} {propertyManagerLastName} has been unassigned on {auditable_type} #{auditable_id}.',
                        'user_assigned' => '{userName} has been assigned as manager on {auditable_type} #{auditable_id}.',
                        'media_uploaded' => 'Media uploaded on {auditable_type} #{auditable_id}.',
                        'media_deleted' => 'Media deleted on {auditable_type} #{auditable_id}.',
                    ]
                ]
            ],
        ],
        'commentsList' => [            
            'loadMore' =>  'Load {count} more comments',            
            'emptyPlaceholder' => [
                'title' => 'There are no messages yet...',
                'description' => 'Start messaging by using the below form and press enter.',
            ],
        ],
        'internalnoticesList' => [            
            'loadMore' => 'Load {count} more internal notice',            
            'emptyPlaceholder' => [
                'title' => 'There are no internal notices yet...',
                'description' => 'Add internal notice by using the below form and press enter.',
            ],
        ],
        'serviceproviderconversationsList' => [            
            'loadMore' => 'Load {count} more service provider conversations',
            'emptyPlaceholder' => [
                'title' => 'There are no conversations with service provider yet...',
                'description' => 'Add conversation to service provider by using the below form and press enter.',
            ],
        ],
        'residentconversationsList' => [
            'loadMore' => 'Load {count} more resident conversations',
            'emptyPlaceholder' => [
                'title' => 'There are no conversation made with resident.',
                'description' => 'Add message to resident using the below form and press enter.',
            ],
        ],
        'listingcommentsList' => [
            'loadMore' => 'Load {count} more listing comments',
            'emptyPlaceholder' => [
                'title' => 'There are no comments for listing',
                'description' => 'Ask about listing by sending messaging using the below form and press enter.',
            ],
        ],
        'pinboardcommentsList' => [
	        'loadMore' => 'Load {count} more pinboard comments',
	        'emptyPlaceholder' => [
		        'title' => 'There are no comments for pinboard',
		        'description' => 'Ask about pinboard by sending messaging using the below form and press enter.',
	        ],
        ],
        'comment' => [
            'updateShortcut' => 'or use {shortcut} shortcut',
            'updateOrCancel' => '{update} or press {esc} to {cancel}',
            'update' => 'update',
            'esc' => 'ESC',
            'cancel' => 'cancel',
            'addChildComment' => 'Comment',
            'loadMore' => 'Load 1 more comment | Load {count} more comments',
            'deletedCommentPlaceholder' => 'This comment was deleted.',
        ],
        'addComment' => [
            'placeholder' => 'Type a comment...',
            'tooltipTemplates' => 'Choose a template',
            'loadingTemplates' => 'Loading templates...',
            'saveShortcut' => 'or use {shortcut} shortcut',
            'emptyTemplatesPlaceholder' => 'No templates available.',
        ],
        'media' => [
            'buttons' => [
                'selectFiles' => [
                    'withDrop' => 'Drop files or click to select...',
                    'withoutDrop' => 'Click to select...',
                ],
                'upload' => 'Upload',
            ],
            'dropActive' => [
                'title' => 'Drop your files here...',
                'description' => 'Only the files with a certain extension are allowed.',
            ],
            'messages' => [
                'preview' => 'This file cannot be previewed.',
                'uploading' => 'Uploading...',
                'uploaded' => 'Media files have been succesfully uploaded.',
                'size' => 'Oops! Some files had the size bigger than the maximum allowed of {bytes}.',
                'extensions' => 'Oops! Some files have had an extension that was not allowed. Skipping...',
            ],
        ],
    ],
    'resident' => [
        'weatherWidget' => [
            'minTemp' => 'min',
            'maxTemp' => 'max',
            'wind' => 'wind',
            'cloudiness' => 'cloudiness',
            'humidity' => 'humidity',
            'pressure' => 'pressure',
        ],
        'pinboardAdd' => [
            'visibility' => [
                'address' => 'Address',
                'quarter' => 'Quarter',
                'all' => 'All',
            ],
        ],
    ],
    'admin' => [

    ],
];