<?php
return [
    'common' => [
        'audit' => [
            'type' => [
                'pinboard' => 'Pinnwand',
                'listing' => 'Markplatz',
                'request' => 'Anfragen'
            ],
            'filter' => [
                'type' => [
                    'pinboard' => 'Pinnwand',
                    'listing' => 'Markplatz',
                    'request' => 'Anfragen'
                ],
                'pinboard' => [
                    'created' => 'Erstellt',
                    'updated' => 'Aktualisiert',
                    'provider_assigned' => 'Dienstleister wurde zugewiesen',
                    'user_assigned' => 'Benutzer wurde zugewiesen',
                    'media_uploaded' => 'Mediendateien hinaufgeladen',
                    'media_deleted' => 'Mediendateien gelöscht'
                ],
                'listing' => [

                ],
                'request' => [
                    'created' => 'Erstellt',
                    'updated' => 'Aktualisiert',
                    'provider_assigned' => 'Dienstleister zugewiesen',
                    'user_assigned' => 'Benutzer Dienstleister',
                    'media_uploaded' => 'Medien hinaufgeladen',
                    'media_deleted' => 'Medien gelöscht'
                ]
            ],
            'content' => [
                'withId' => [
                    'pinboard' => [
                        'created' => '{userName} hat diesen Beitrag erstellt.',
                        'updated' => [
                            'status' => 'Der Status wurde von "{old}" zu "{new}" geändert.',
                            'published_at' => 'Beitrag wurde am {new} veröffentlicht.'
                        ]
                    ],
                    'listing' => [
                        'created' => '{userName} dieses Inserat erstellt: {auditable_type}',
                        'updated' => [
                            'title' => 'Der Titel wurde von "{old}" zu "{new}" geändert.',
                            'status' => 'Der Status wurde von "{old}" zu "{new}" geändert.',
                            'due_date' => 'Das Erledigungsdatum wurde von "{old}" zu "{new}" geändert.',
                            'priority' => 'Die Priorität wurde von "{old}" zu "{new}" geändert.',
                            'internal_priority' => 'Die interne Priorität wurde von "{old}" zu "{new}" geändert.',
                            'category_id' => 'Die Kategorie wurde von "{old}" zu "{new}" geändert.',
                            'qualification' => 'Die Qualifikation wurde von "{old}" zu "{new}" geändert.',
                            'visibility' => 'Die Sichtbarkeit wurde von "{old}" zu "{new}" geändert.',
                        ],
                        'provider_assigned' => '{providerName} wurde als Dienstleistern zugewiesen.',
                        'user_assigned' => '{userName} wurde als zuständige Person zugewiesen.',
                        'media_uploaded' => 'Mediendateien hinaufgeladen',
                        'media_deleted' => 'Mediendateien gelöscht',
                    ],
                    'request' => [
                        'created' => '{userName} hat diese Anfrage erstellt.',
                        'updated' => [
                            'title' => 'Der Titel wurde von "{old}" zu "{new}" geändert.',
                            'status' => 'Der Status wurde von "{old}" zu "{new}" geändert.',
                            'due_date' => 'Das Erledigungsdatum wurde von "{old}" zu "{new}" geändert.',
                            'priority' => 'Die Priorität wurde von "{old}" zu "{new}" geändert.',
                            'internal_priority' => 'Die interne Priorität wurde von "{old}" zu "{new}" geändert.',
                            'category_id' => 'Die Kategorie wurde von "{old}" zu "{new}" geändert.',
                            'qualification' => 'Die Qualifikation wurde von "{old}" zu "{new}" geändert.',
                            'visibility' => 'Die Sichtbarkeit wurde von "{old}" zu "{new}" geändert.',
                        ],
                        'provider_assigned' => '{providerName} wurde als Dienstleister zugewiesen.',
                        'provider_unassigned' => 'Dienstleisterin {providerName} wurde nicht beauftragt.',
                        'manager_assigned' => '{propertyManagerFirstName} {propertyManagerLastName} wurde als Managerin zugewiesen.',
                        'manager_unassigned' => 'Managerin {propertyManagerFirstName} {propertyManagerLastName} wurde nicht zugewiesen.',
                        'user_assigned' => '{userName} wurde dieser Anfrage hinzugefügt.',
                        'media_uploaded' => 'Mediendateien hinaufgeladen',
                        'media_deleted' => 'Mediendateien gelöscht',
                    ]
                ],
                'withNoId' => [
                    'pinboard' => [
                        'created' => '{userName} hat folgende Anfrage erstellt: {auditable_type} im {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'status' => 'Der Status wurde von "{old}" zu "{new}" im {auditable_type} #{auditable_id} geändert.',
                            'published_at' => 'Post published im {new} im {auditable_type} #{auditable_id}.'
                        ]
                    ],
                    'listing' => [
                        'created' => '{userName} hat dieses Inserat erstellt.',
                        'updated' => [
                            'title' => 'Der Titel wurde von "{old}" zu "{new}" geändert im {auditable_type} #{auditable_id}.',
                            'status' => 'Der Status wurde von "{old}" zu "{new}" geändert im {auditable_type} #{auditable_id}.',
                            'due_date' => 'Das Erledigungsdatum wurde von "{old}" zu "{new}" geändert im {auditable_type} #{auditable_id}.',
                            'priority' => 'Die Priorität wurde von "{old}" zu "{new}" geändert im {auditable_type} #{auditable_id}.',
                            'category_id' => 'Die Kategorie wurde von "{old}" zu "{new}" geändert im {auditable_type} #{auditable_id}.',
                            'qualification' => 'Die Qualifikation wurde von "{old}" zu "{new}" geändert im {auditable_type} #{auditable_id}.',
                            'visibility' => 'Die Sichtbarkeit wurde von "{old}" zu "{new}" geändert im {auditable_type} #{auditable_id}.',
                        ],
                        'provider_assigned' => '{providerName} wurde als Dienstleister zugewiesen.',
                        'user_assigned' => '{userName} wurde als zuständige Person zugewiesen.',
                        'media_uploaded' => 'Mediendateien hinaufgeladen',
                        'media_deleted' => 'Mediendateie gelöscht',
                    ],
                    'request' => [
                        'created' => '{userName} hat diese Anfrage erstellt.',
                        'updated' => [
                            'title' => 'Der Titel wurde von "{old}" zu "{new}" geändert.',
                            'status' => 'Der Status wurde von "{old}" zu "{new}" geändert.',
                            'due_date' => 'Das Erledigungsdatum wurde von "{old}" zu "{new}" geändert.',
                            'priority' => 'Die Priorität wurde von "{old}" zu "{new}" geändert.',
                            'category_id' => 'Die Kategorie wurde von "{old}" zu "{new}" geändert.',
                            'qualification' => 'Die Qualifikation wurde von "{old}" zu "{new}" geändert.',
                            'visibility' => 'Die Sichtbarkeit wurde von "{old}" zu "{new}" geändert.',
                        ],
                        'provider_assigned' => '{providerName} wurde als Dienstleister zugewiesen.',
                        //'provider_unassigned' => 'Dienstleisterin {providerName} wurde nicht beauftragt im {auditable_type} #{auditable_id}.',
                        'provider_unassigned' => '{providerName} wurde als Dienstleister entfernt.',
                        'manager_assigned' => '{propertyManagerFirstName} {propertyManagerLastName} wurde als zuständige Person zugewiesen.',
                        'manager_unassigned' => '{propertyManagerFirstName} {propertyManagerLastName} als zusätinge Person wurde entfernt.',
                        'user_assigned' => '{userName} wurde als zuständige Person zugewiesen.',
                        'media_uploaded' => 'Mediendateien hinaufgeladen',
                        'media_deleted' => 'Mediendateien gelöscht',
                    ]
                ]
            ],
        ],
        'commentsList' => [                        
            'loadMore' => 'Lade {count} weitere Kommentare',
            'emptyPlaceholder' => [
                'title' => 'Bislang wurden keine Kommentare geteilt...',
                'description' => 'Verfassen Sie den ersten Kommentar.',
            ],
        ],
        'internalnoticesList' => [            
            'loadMore' => 'Lade {count} weitere interne Notizen',            
            'emptyPlaceholder' => [
                'title' => 'Es existieren noch keine interne Notizen.',
                'description' => 'Fügen Sie einen internen Notiz hinzu.',
            ],
        ],
        'serviceproviderconversationsList' => [            
            'loadMore' => 'Lade {count} weitere Nachrichten',
            'emptyPlaceholder' => [
                'title' => 'Es gibt noch keine Nachrichten.',
                'description' => 'Verfassen Sie die erste Nachricht.',
            ],
        ],
        'residentconversationsList' => [
            'loadMore' => 'Laden von {count} mehr residuelle Gespräche.',
            'emptyPlaceholder' => [
                'title' => 'Es gibt kein Gespräch mit dem Bewohner.',
                'description' => 'Fügen Sie mit dem untenstehenden Formular eine Nachricht an den Bewohner hinzu und drücken Sie die Eingabetaste.',
            ],
        ],
        'listingcommentsList' => [
            'loadMore' => 'Lade {count} weitere Anfragen',
            'emptyPlaceholder' => [
                'title' => 'Es gibt keine Anfragen',
                'description' => 'Übermitteln Sie die erste Anfrage.',
            ],
        ],
        'pinboardcommentsList' => [
	        'loadMore' => 'Lade {count} weitere Kommentare.',
	        'emptyPlaceholder' => [
		        'title' => 'Es gibt keine Kommentare',
		        'description' => 'Verfassen Sie den ersten Kommentar',
	        ],
        ],
        'comment' => [
            'updateShortcut' => 'oder Verwendung {shortcut} Abkürzung',
            'updateOrCancel' => '{update} oder drücke {esc} um {cancel}',
            'update' => 'bearbeiten',
            'esc' => 'ESC',
            'cancel' => 'abzubrechen',
            'addChildComment' => 'Kommentar',
            'loadMore' => 'Lade 1 weiteren Kommentar | Lade {count} weitere Kommentare',
            'deletedCommentPlaceholder' => 'Der Kommentar wurde gelöscht.',
        ],
        'addComment' => [
            'placeholder' => 'Schreiben Sie einen Kommentar...',
            'tooltipTemplates' => 'Wählen Sie eine Vorlage',
            'loadingTemplates' => 'Vorlagen werden geladen...',
            'saveShortcut' => 'oder verwende {shortcut} Abkürzung',
            'emptyTemplatesPlaceholder' => 'Keine Vorlagen vorhanden',
        ],
        'media' => [
            'buttons' => [
                'selectFiles' => [
                    'withDrop' => 'Schieben oder wählen Sie die Dateien...',
                    'withoutDrop' => 'Datei wählen...',
                ],
                'upload' => 'Hinauflauden',
            ],
            'dropActive' => [
                'title' => 'Schieben Sie die Dateien hier hinein..',
                'description' => 'Nur die Dateien mit eines bestimmten Typs sind erlaubt...',
            ],
            'messages' => [
                'preview' => 'Es kann keine Vorschau angezeigt werden.',
                'uploading' => 'Hinaufladen...',
                'uploaded' => 'Mediendateien wurden erfolgreich hochgeladen.',
                'size' => 'Hoppla! Einige Dateien sind grösser als die maximal zulässige Anzahl von {bytes}.',
                'extensions' => 'Hoppla! Einige Datei-Typen wurden ausgewählt, die nicht erlaubt sind. Überspringen...',
            ],
        ],
    ],
    'resident' => [
        'weatherWidget' => [
            'minTemp' => 'min',
            'maxTemp' => 'max',
            'wind' => 'Wind',
            'cloudiness' => 'Bewölkung',
            'humidity' => 'Luftfeuchte',
            'pressure' => 'Druck',
        ],
        'pinboardAdd' => [
            'visibility' => [
                'address' => 'Nachbarn',
                'quarter' => 'Überbauung',
                'all' => 'Alle',
            ],
        ],
    ],
    'admin' => [

    ],
];