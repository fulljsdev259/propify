<?php
return [
    'common' => 
    [
      'audit' => 
      [
        'type' => [
          'post' => 'Messaggio',
          'product' => 'Prodotto',
          'request' => 'Richiesta'
        ],
        'filter' => [
          'type' => [
          'post' => 'Messaggio',
          'product' => 'Prodotto',
          'request' => 'Richiesta'
          ],
          'post' => [
            'created' => 'Creato',
            'updated' => 'Aggiornamenti',
            'provider_assigned' => 'Fornitore assegnato',
            'user_assigned' => 'Utente assegnato',
            'media_uploaded' => 'Media caricati',
            'media_deleted' => 'Supporti cancellati'
          ],
          'product' => [],
          'request' => [
            'created' => 'Creato',
            'updated' => 'Aggiornamenti',
            'provider_assigned' => 'Fornitore assegnato',
            'user_assigned' => 'Utente assegnato',
            'media_uploaded' => 'Media caricati',
            'media_deleted' => 'Supporti cancellati'
          ]
        ],
        'content' => [
          'withId' => [
            'post' => [
              'created' => '{userName} ha aperto questo {auditable_type} su {auditable_type} #{auditable_id}.',
              'updated' => [
                  'status' => 'Lo stato è cambiato da "{old}" al "{new}".',
                  'published_at' => 'Messaggio pubblicato su {new}.'
                ]              
              ],
              'product' => [
                'created' => '{userName} ha aperto questo {auditable_type}.',
                'updated' => [
                  'title' => 'Il titolo è cambiato da "{old}" al "{new}".',
                  'status' => 'Lo stato è cambiato da "{old}" al "{new}".',
                  'due_date' => 'La data di scadenza è cambiata da "{old}" al "{new}".',
                  'priority' => 'La categoria è cambiata da "{old}" al "{new}".',
                  'category_id' => 'La categoria è cambiata da "{old}" al "{new}".',
                  'qualification' => 'La qualifica è cambiata da "{old}" al "{new}".',
                  'visibility' => 'La visibilità è cambiata da "{old}" al "{new}".',   
                ],
                'provider_assigned' => '{providerName} è stato assegnato come fornitore.',
                'user_assigned' => '{userName} è stato assegnato come manager.',
                'media_uploaded' => 'Media caricati',
                'media_deleted' => 'Supporti cancellati',
              ],
              'request' => [
                'created' => '{userName} ha aperto questo {auditable_type}.',
                'updated' => [
                  'title' => 'Il titolo è cambiato da "{old}" al "{new}".',
                  'status' => 'Lo stato è cambiato da "{old}" al "{new}".',
                  'due_date' => 'La data di scadenza è cambiata da "{old}" al "{new}".',
                  'priority' => 'La categoria è cambiata da "{old}" al "{new}".',
                  'category_id' => 'La categoria è cambiata da "{old}" al "{new}".',
                  'qualification' => 'La qualifica è cambiata da "{old}" al "{new}".',
                  'visibility' => 'La visibilità è cambiata da "{old}" al "{new}".',   
                ],
                'provider_assigned' => '{providerName} è stato assegnato come fornitore.',
                'provider_unassigned' => 'Fornitore di servizi {providerName} non è stato assegnato.',
                'manager_assigned' => '{propertyManagerFirstName} {propertyManagerLastName} è stato assegnato come manager.',
                'manager_unassigned' => 'Manager {propertyManagerFirstName} {propertyManagerLastName} non è stato assegnato.',
                'user_assigned' => '{userName} è stato assegnato come manager.',
                'media_uploaded' => 'Media caricati',
                'media_deleted' => 'Supporti cancellati',
              ]
          ],
          'withNoId' => [
            'post' => [
              'created' => '{userName} ha aperto questo {auditable_type} su {auditable_type} #{auditable_id}.',
              'updated' => [
                  'status' => 'Lo stato è cambiato da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'published_at' => 'Messaggio pubblicato su {new} su {auditable_type} #{auditable_id}.'
                ]              
              ],
              'product' => [
                'created' => '{userName} ha aperto questo {auditable_type} su {auditable_type} #{auditable_id}.',
                'updated' => [
                  'title' => 'Il titolo è cambiato da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'status' => 'Lo stato è cambiato da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'due_date' => 'La data di scadenza è cambiata da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'priority' => 'La categoria è cambiata da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'category_id' => 'La categoria è cambiata da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'qualification' => 'La qualifica è cambiata da "{old}" al "{new}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'visibility' => 'La visibilità è cambiata da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',  
                ],
                'provider_assigned' => '{providerName} è stato assegnato come fornitore su {auditable_type} #{auditable_id}.',
                'user_assigned' => '{userName} è stato assegnato come manager su {auditable_type} #{auditable_id}.',
                'media_uploaded' => 'Media caricati su {auditable_type} #{auditable_id}.',
                'media_deleted' => 'Supporti cancellati su {auditable_type} #{auditable_id}.',
              ],
              'request' => [
                'created' => '{userName} opened this {auditable_type} su {auditable_type} #{auditable_id}.',
                'updated' => [
                  'title' => 'Il titolo è cambiato da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'status' => 'Lo stato è cambiato da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'due_date' => 'La data di scadenza è cambiata da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'priority' => 'La categoria è cambiata da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'category_id' => 'La categoria è cambiata da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'qualification' => 'La qualifica è cambiata da "{old}" al "{new}" al "{new}" su {auditable_type} #{auditable_id}.',
                  'visibility' => 'La visibilità è cambiata da "{old}" al "{new}" su {auditable_type} #{auditable_id}.',  
                ],
                'provider_assigned' => '{providerName} è stato assegnato come fornitore su {auditable_type} #{auditable_id}.',
                'provider_unassigned' => 'Fornitore di servizi {providerName} non è stato assegnato su {auditable_type} #{auditable_id}.',
                'manager_assigned' => '{propertyManagerFirstName} {propertyManagerLastName} è stato assegnato come manager su {auditable_type} #{auditable_id}.',
                'manager_unassigned' => 'Manager {propertyManagerFirstName} {propertyManagerLastName} non è stato assegnato su {auditable_type} #{auditable_id}.',
                'user_assigned' => '{userName} è stato assegnato come manager su {auditable_type} #{auditable_id}.',
                'media_uploaded' => 'Media caricati su {auditable_type} #{auditable_id}.',
                'media_deleted' => 'Supporti cancellati su {auditable_type} #{auditable_id}.',
              ]
          ]
        ],

      ],
      'commentsList' => 
      [
        'loading' => 'Caricamento...',
        'loadMore' => 
        [
          'simple' => 'Carico {count} ',
          'detailed' => 'Carico {count} altri commenti',
        ],
        'emptyPlaceholder' => 
        [
          'title' => "Non ci sono ancora messaggi...",
          'description' => 'Avviare la messaggistica utilizzando il modulo sottostante e premere Invio.',
        ],
      ],
      'comment' => 
      [
        'updateShortcut' => "o l'uso {shortcut} scorciatoia",
        'updateOrCancel' => '{update} o premere {esc} al {cancel}',
        'update' => 'attualizzazione',
        'esc' => 'ESC',
        'cancel' => 'stornare',
        'addChildComment' => 'Commento',
        'loadMore' => 'Carica 1 commento in più | Carica {count} più commenti',
        'deletedCommentPlaceholder' => 'Questo commento è stato cancellato.',
      ],
      'addComment' => 
      [
        'placeholder' => 'Scrivi un commento...',
        'tooltipTemplates' => 'Scegliere un modello',
        'loadingTemplates' => 'Caricamento dei modelli...',
        'saveShortcut' => "o l'uso {shortcut} scorciatoia",
        'emptyTemplatesPlaceholder' => 'Nessun modello disponibile.',
      ],
      'media' => 
      [
        'buttons' => 
        [
          'selectFiles' => 
          [
            'withDrop' => 'Rilasciare i file o fare clic per selezionare...',
            'withoutDrop' => 'Fare clic per selezionare...',
          ],
          'upload' => 'Carica',
        ],
        'dropActive' => 
        [
          'title' => 'Lascia qui i tuoi file...',
          'description' => 'Sono ammessi solo i file con una certa estensione.',
        ],
        'messages' => 
        [
          'preview' => 'Questo file non può essere visualizzato in anteprima.',
          'uploading' => 'Caricamento...',
          'uploaded' => 'I file multimediali sono stati caricati con successo.',
          'size' => 'Ops! Alcuni file avevano dimensioni maggiori del massimo consentito di {bytes}.',
          'extensions' => "Ops! Alcuni file hanno avuto un'estensione non consentita. Saltare...",
        ],
      ],
    ],
    'tenant' => 
    [
      'weatherWidget' => 
      [
        'minTemp' => 'min',
        'maxTemp' => 'massimo',
        'wind' => 'eolico',
        'cloudiness' => 'nebulosità',
        'humidity' => 'umidità',
        'pressure' => 'pressione',
      ],
      'postAdd' => 
      [
        'visibility' => 
        [
          'address' => 'Indirizzo',
          'quarter' => 'Quartiere',
          'district' => 'Quartiere',
          'all' => 'Tutti',
        ],
      ],
    ],
    'admin' => 
    [
    ],
];