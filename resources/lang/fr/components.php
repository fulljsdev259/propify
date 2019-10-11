<?php
return [
    'common' => [
        'audit' => [
            'type' => [
                'pinboard' => 'Panneau d\'affichage',
                'listing' => 'Produit',
                'request' => 'Demande'
            ],
            'filter' => [
                'type' => [
                    'pinboard' => 'Panneau d\'affichage',
                    'listing' => 'Produit',
                    'request' => 'Demande'
                ],
                'pinboard' => [
                    'created' => 'Créé',
                    'updated' => 'Mises à jour',
                    'provider_assigned' => 'Fournisseur assigné',
                    'user_assigned' => "Affecté par l'utilisateur",
                    'media_uploaded' => 'Média téléchargé',
                    'media_deleted' => 'Médias supprimés'
                ],
                'listing' => [

                ],
                'request' => [
                    'created' => 'Créé',
                    'updated' => 'Mises à jour',
                    'provider_assigned' => 'Fournisseur assigné',
                    'user_assigned' => "Affecté par l'utilisateur",
                    'media_uploaded' => 'Média téléchargé',
                    'media_deleted' => 'Médias supprimés'
                ]
            ],
            'content' => [
                'withId' => [
                    'pinboard' => [
                        'created' => '{userName} a ouvert ce {auditable_type} à bord de {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'status' => 'Le statut est passé de "{old}" au "{new}".',
                            'published_at' => 'Panneau d\'affichage publié sur {new}.'
                        ]
                    ],
                    'listing' => [
                        'created' => '{userName} a ouvert ce {auditable_type}.',
                        'updated' => [
                            'title' => 'Le titre est passé de "{old}" au "{new}".',
                            'status' => 'Le statut est passé de "{old}" au "{new}".',
                            'due_date' => "La date d'échéance est passée de '{old}' au '{new}'.",
                            'priority' => 'La priorité est passée de "{old}" au "{new}".',
                            'internal_priority' => 'La priorité interne est passée de "{old}" à "{new}".',
                            'category_id' => 'La catégorie est passée de "{old}" au "{new}".',
                            'qualification' => 'La qualification est passée de "{old}" au "{new}".',
                            'visibility' => 'La visibilité est passée de "{old}" au "{new}".',
                        ],
                        'provider_assigned' => '{providerName} a été affecté en tant que prestataire.',
                        'user_assigned' => '{userName} a été affecté au poste de gestionnaire.',
                        'media_uploaded' => 'Média téléchargé',
                        'media_deleted' => 'Médias supprimés',
                    ],
                    'request' => [
                        'created' => '{userName} a ouvert ce {auditable_type}.',
                        'updated' => [
                            'title' => 'Le titre est passé de "{old}" au "{new}".',
                            'status' => 'Le statut est passé de "{old}" au "{new}".',
                            'due_date' => "La date d'échéance est passée de '{old}' au '{new}'.",
                            'priority' => 'La priorité est passée de "{old}" au "{new}".',
                            'internal_priority' => 'La priorité interne est passée de "{old}" à "{new}".',
                            'category_id' => 'La catégorie est passée de "{old}" au "{new}".',
                            'qualification' => 'La qualification est passée de "{old}" au "{new}".',
                            'visibility' => 'La visibilité est passée de "{old}" au "{new}".',
                        ],
                        'provider_assigned' => '{providerName} a été affecté en tant que prestataire.',
                        'provider_unassigned' => "Prestataire {providerName} n'a pas été affecté.",
                        'manager_assigned' => '{propertyManagerFirstName} {propertyManagerLastName} a été affecté au poste de gestionnaire.',
                        'manager_unassigned' => "Gestionnaire {propertyManagerFirstName} {propertyManagerLastName} n'a pas été affecté.",
                        'user_assigned' => '{userName} a été affecté au poste de gestionnaire.',
                        'media_uploaded' => 'Média téléchargé',
                        'media_deleted' => 'Médias supprimés',
                    ]
                ],
                'withNoId' => [
                    'pinboard' => [
                        'created' => '{userName} a ouvert ce {auditable_type} à bord de {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'status' => 'Le statut est passé de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'published_at' => 'Panneau d\'affichage publié au {new} sur {auditable_type} #{auditable_id}.'
                        ]
                    ],
                    'listing' => [
                        'created' => '{userName} opened this {auditable_type} sur {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'title' => 'Le titre est passé de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'status' => 'Le statut est passé de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'due_date' => "La date d'échéance est passée de '{old}' au '{new}' sur {auditable_type} #{auditable_id}.",
                            'priority' => 'La priorité est passée de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'category_id' => 'La catégorie est passée de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'qualification' => 'La qualification est passée de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'visibility' => 'La visibilité est passée de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                        ],
                        'provider_assigned' => '{providerName} a été affecté en tant que prestataire sur {auditable_type} #{auditable_id}.',
                        'user_assigned' => '{userName} a été affecté au poste de gestionnaire sur {auditable_type} #{auditable_id}.',
                        'media_uploaded' => 'Média téléchargé sur {auditable_type} #{auditable_id}.',
                        'media_deleted' => 'Médias supprimés sur {auditable_type} #{auditable_id}.',
                    ],
                    'request' => [
                        'created' => '{userName} opened this {auditable_type} sur {auditable_type} #{auditable_id}.',
                        'updated' => [
                            'title' => 'Le titre est passé de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'status' => 'Le statut est passé de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'due_date' => "La date d'échéance est passée de '{old}' au '{new}' sur {auditable_type} #{auditable_id}.",
                            'priority' => 'La priorité est passée de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'category_id' => 'La catégorie est passée de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'qualification' => 'La qualification est passée de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                            'visibility' => 'La visibilité est passée de "{old}" au "{new}" sur {auditable_type} #{auditable_id}.',
                        ],
                        'provider_assigned' => '{providerName} a été affecté en tant que prestataire sur {auditable_type} #{auditable_id}.',
                        'provider_unassigned' => "Prestataire {providerName} n'a pas été affecté sur {auditable_type} #{auditable_id}.",
                        'manager_assigned' => '{propertyManagerFirstName} {propertyManagerLastName} a été affecté au poste de gestionnaire sur {auditable_type} #{auditable_id}.',
                        'manager_unassigned' => "Gestionnaire {propertyManagerFirstName} {propertyManagerLastName} n'a pas été affecté sur {auditable_type} #{auditable_id}.",
                        'user_assigned' => '{userName} a été affecté au poste de gestionnaire sur {auditable_type} #{auditable_id}.',
                        'media_uploaded' => 'Média téléchargé sur {auditable_type} #{auditable_id}.',
                        'media_deleted' => 'Médias supprimés sur {auditable_type} #{auditable_id}.',
                    ]
                ]
            ],
        ],
        'commentsList' => [            
            'loadMore' => 'Charge {count} commentaires supplémentaires',
            'emptyPlaceholder' => [
                'title' => "Il n'y a pas encore de messages...",
                'description' => 'Commencez la messagerie en utilisant le formulaire ci-dessous et appuyez sur Entrée.',
            ],
        ],
        'internalnoticesList' => [            
            'loadMore' => "Charger {count} plus d'avis interne",            
            'emptyPlaceholder' => [
                'title' => "Il n'y a pas encore d'avis internes",
                'description' => "Ajoutez une résiliation interne en utilisant le formulaire ci-dessous et appuyez sur Entrée.",
            ],
        ],
        'serviceproviderconversationsList' => [            
            'loadMore' => 'Charger {count} plus conversations avec les fournisseurs de services',
            'emptyPlaceholder' => [
                'title' => "Il n'y a pas encore de conversations avec le fournisseur de services",
                'description' => "Ajoutez la conversation au fournisseur de services en utilisant le formulaire ci-dessous et appuyez sur Entrée.",
            ],
        ],
        'residentconversationsList' => [
            'loadMore' => 'Charger {count} plus conversations avec les locataires',
            'emptyPlaceholder' => [
                'title' => "Il n'y a pas de conversation avec le locataire.",
                'description' => "Ajouter un message au locataire à l'aide du formulaire ci-dessous et appuyez sur Entrée.",
            ],
        ],
        'listingcommentsList' => [
            'loadMore' => 'Charger {count} plus commentaires de listage',
            'emptyPlaceholder' => [
                'title' => "Il n'y a pas de commentaires pour l'inscription",
                'description' => "Renseignez-vous sur l'inscription en envoyant un message à l'aide du formulaire ci-dessous et appuyez sur Entrée.",
            ],
        ],
        'pinboardcommentsList' => [
	        'loadMore' => 'Télécharger {count} plus de commentaires sur le Mur.',
	        'emptyPlaceholder' => [
		        'title' => "Il n'y a aucun commentaire pour Pinnwand.",
		        'description' => "Demandez le tableau d'affichage en envoyant des messages à l'aide du formulaire ci-dessous et en appuyant sur Entrée.",
	        ],
        ],
        'comment' => [
            'updateShortcut' => 'ou utiliser {shortcut} raccourci',
            'updateOrCancel' => '{update} ou appuyez sur {esc} au {cancel}',
            'update' => 'mettre à jour',
            'esc' => 'ESC',
            'cancel' => 'résilier',
            'addChildComment' => 'Comment',
            'loadMore' => 'Load 1 more comment | Load {count} more comments',
            'deletedCommentPlaceholder' => 'Ce commentaire a été supprimé',
        ],
        'addComment' => [
            'placeholder' => 'Tapez un commentaire...',
            'tooltipTemplates' => 'Choisissez un modèle',
            'loadingTemplates' => 'Chargement des modèles...',
            'saveShortcut' => 'ou utiliser {shortcut} raccourci',
            'emptyTemplatesPlaceholder' => 'Aucun modèle disponible.',
        ],
        'media' => [
            'buttons' => [
                'selectFiles' => [
                    'withDrop' => 'Lâcher les fichiers ou cliquer pour sélectionner...',
                    'withoutDrop' => 'Cliquez pour sélectionner...',
                ],
                'upload' => 'Télécharger',
            ],
            'dropActive' => [
                'title' => 'Déposez vos fichiers ici...',
                'description' => 'Seuls les fichiers avec une certaine extension sont autorisés.',
            ],
            'messages' => [
                'preview' => 'Ce fichier ne peut pas être prévisualisé',
                'uploading' => 'Téléchargement...',
                'uploaded' => 'Les fichiers médias ont été téléchargés avec succès.',
                'size' => 'Oups ! Certains fichiers avaient une taille supérieure au maximum autorisé de {bytes}.',
                'extensions' => "Oups ! Certains fichiers ont eu une extension qui n'était pas autorisée. Sauter",
            ],
        ],
    ],
    'resident' => [
        'weatherWidget' => [
            'minTemp' => 'minute',
            'maxTemp' => 'maximum',
            'wind' => 'essouffler',
            'cloudiness' => 'essouffler',
            'humidity' => 'nébulosité',
            'pressure' => 'humidité',
        ],
        'pinboardAdd' => [
            'visibility' => [
                'address' => 'faire pression',
                'quarter' => 'Trimestre',
                'all' => 'Tous',
            ],
        ],
    ],
    'admin' => [

    ],
];