<?php
return [
    'user' => [
        'add_admin' => 'Aggiungi Amministratore',
        'edit_admin' => 'Modifica Amministratore',                                
        "saved" => "Utente salvato con successo",
        "deleted" => "Utente cancellato",
        "not_found" => "Utente non trovato",
        "profile_image" => "Immagine del profilo",
        "profile_text" => "Testo del profilo",
        "logo" => "Logo",
        "circle_logo" => "Logo del cerchio",
        "favicon_icon" => "Icona Favicon",
        "resident_logo" => "Logo residente",                
        "notification_saved" => "Impostazione della notifica salvata",        
        "request_category_saved" => "Categoria della richiesta di servizio salvata",
        "request_category_deleted" => "Categoria della richiesta di servizio cancellata",                
        'errors' => [
            'not_found' => "Utente non trovato",            
            'image_upload' => "Errore caricamento immagine utente : ",
            'incorrect_password' => "Password utente non corretta",
            'email_missing' => "manca l'email",
            'email_already_exists' => "Questa email [:email] esiste già, Seleziona un'altra email",
            'email_not_exists' => "Questa [:email] email non esiste",            
            'deleted' => "Errore cancellato dall'utente: ",
        ],        
    ],
    'resident' => [
        "view" => "Vista",
        'name' => 'Residente',
        "view_title" => "Visualizza residente",
        "edit_title" => "Modifica Residente",
        "download_credentials" => "Scarica le credenziali",
        "send_credentials" => "Mandare le credenziali",
        "credentials_sent" => "Invio delle credenziali",
        "credentials_send_fail" => "File delle credenziali non trovato. Prova ad aggiornare la password residente per rigenerarla",
        "credentials_download_failed" => "File delle credenziali non trovato. Prova ad aggiornare la password residente per rigenerarla",
        "add" => "Aggiungi Residente",
        "saved" => "Residente salvato",
        "deleted" => "Residente cancellato",
        "status_changed" => "Stato cambiato",
        "password_reset" => "Reset della password residente con successo",
        "update" => "Aggiornamento",
        "birth_date" => "Data di nascita",
        'nation' => 'Nazione',
        "mobile_phone" => "Telefono cellulare",
        "work_phone" => "Telefono di lavoro",        
        "private_phone" => "Telefono personale",
        "created_date" => "Data di creazione",
        "pinboard" => "Bacheca",
        "listings" => "Prodotti",
        "company" => "Nome dell'azienda",
        'building' => [
            'name' => 'Bâtiment',
        ],
        'unit' => [
            'name' => 'Unità',
        ],
        'search_building' => 'Ricerca edificio',
        'search_unit' => 'Unità di ricerca',
        'errors' => [
            'not_found' => "Residente non trovato",
            'incorrect_email' => "Indirizzo e-mail errato",
            'create' => "Residente crea errore: ",
            'update' => "Errore nell'aggiornamento del residente: ",
            'deleted' => "Residente Cancella errore: ",
            'not_allowed_change_status' => "Non è consentito modificare lo stato.",
        ],        
        "personal_details_card" => "Dati personali",
        "account_info_card" => "Accesso utente",
        "contact_info_card" => "Dati di contatto",
        "contract" => [
            'title' => "Contratto",
            "rent_end" => "Fine affitto",
            "rent_start" => "Inizio affitto",
            'rent_type' => 'Tipo di affitto',            
            'rent_duration' => "Durata dell'affitto",
            'rent_durations' => [
                'unlimited' => 'Illimitato',
                'limited' => 'Limitato',
            ],
            'contract_pdf' => "Contratto PDF",
            'deposit_amount' => 'Importo del deposito',
            'type_of_deposit' => 'Tipo di deposito',
            'deposit_types' => [
                'bank_deposit' => 'Deposito bancario',                
                'bank_guarantee' => 'Garanzia bancaria',
                'insurance' => 'Assicurazione',
                'other' => 'Altro',
            ],
            'deposit_status' => [
                'label' => 'Stato del deposito',
                'yes' => 'Sì',
                'no' => 'No',
            ],
            'contract_id' => 'ID contratto',
            'rent_status' => [
                'active' => 'Attivo',
                'inactive' => 'Inattivo',
            ],
            'add' => 'Aggiungere un nuovo contratto',
            'pdf_only_desc' => 'Si prega di notare che solo i file PDF possono essere caricati',
            'saved' => 'Contratto salvato',
            'status_count' => [
                'total' => 'Totale contratti',
                'active' => 'Contratti attivi',
                'inactive' => 'Contratti inattivi',
            ],
        ],        
        'status' => [
            "label" => "Situazione",
            "active" => "Attivo",
            "not_active" => "Non attivo",
            'total' => 'Totale',
        ],
        'credentials_pdf' => [
            'resident_credentials' => 'Credenziali di residenza',
            'code' => 'Codice di sblocco personale',
            'telephone' => 'Telefono',
            'your_email' => 'Il tuo indirizzo e-mail',
            'email' => 'messaggio di posta elettronica',            
            'welcome' => 'Benvenuti nel portale dei residenti della',
            'content_1' => 'Siamo lieti di informarvi che è stato creato un account per voi nel totale dei residenti e vi invieremo il codice di attivazione.',
            'offer' => 'Cosa offre l\'applicazione?',
            'offers' => '
                <li>Con il dossier del residente digitale si ha accesso a tutti i documenti rilevanti, come il contratto di locazione, il regolamento interno o altri documenti relativi alla proprietà.</li>
                <li>Il sistema di ticketing vi permette di elaborare le vostre richieste in modo semplice e senza complicazioni - potete comunicare le vostre preoccupazioni all\'amministrazione in qualsiasi momento e da qualsiasi luogo . </li>
                <li>Puoi vendere o prestare oggetti al tuo quartiere sulla piazza del mercato e sull\'area di prestito . </li>
                <li>Condividi le notizie con i tuoi vicini pubblicando un contributo. La bacheca è utilizzata anche dall\'amministrazione per la comunicazione, in modo che tutti siano sempre aggiornati .</li>
                <li>Ulteriori Micro-Apps all\'interno dell\'applicazione stabiliscono nuovi standard nella qualità della vita, per cui è possibile utilizzare comodamente diversi servizi.</li>
            ',
            'register' => 'Prima registrazione e attivazione del tuo account',
            'content_2' => 'Per registrarsi, clicca sul link sottostante e accedi con il tuo indirizzo e-mail e codice di attivazione personale. Una volta effettuato l\'accesso, è possibile definire la propria password e utilizzarla per l\'accesso.',
            'link_application' => 'Collegamento all\'applicazione',
            'content_3' => 'Vi aspettiamo a bordo!',
            'content_4' => 'Se avete bisogno di assistenza per la registrazione, allora siamo a vostra disposizione.',
            'your_sincerely' => 'La vostra sinceramente',
            'your_administration' => 'la sua amministrazione'
        ],
        'type' => [
            'label' => 'Tipo',
            'tenant' => 'Affittuario',
            'owner' => 'Proprietario',
        ],
    ],
    'building' => [
        "title" => "Edifici",
        "edit_title" => "Modifica Edificio",
        "add" => "Aggiungi edificio",
        "cancel" => "Annulla",
        "deleted" => "Edificio cancellato con successo",
        "units" => "Unità",
        "saved" => "Edificio salvato",
        "floors" => "Pavimenti",
        'under_floor' => 'Sotto il pavimento',
        'ground_floor' => 'Piano terra',
        "basement" => "Nel seminterrato",
        "attic" => "In soffitta",
        "floor_nr" => "Numero di piani",
        "internal_building_id" => "Id edificio interno",        
        "address_search" => "Inserire l'indirizzo",
        "not_found" => "Edificio non trovato",
        'media_category' => [
            "house_rules" => "Le regole della casa",
            "operating_instructions" => "Istruzioni per l'uso",
            'care_instructions' => 'Istruzioni per la cura',
            'other' => 'Altro',
        ],
        "files" => "I file",
        "add_files" => "Aggiungere file",
        "providers" => "Fornitori di servizi",        
        "select_media_category" => "Categoria di supporti selezionati",
        "quarter" => "Quartiere",
        "managers" => "Manager",
        "house_num" => "Casa Nr...",
        "assign_managers" => "Assegnare i manager",
        "unassign_manager" => "Disassegnare",
        "managers_assigned" => "Dirigenti assegnati",
        "occupied_units" => "Unità ossessionate",
        "free_units" => "Unità libere",        
        'document' => [
            "uploaded" => "Documento caricato",
            "deleted" => "Documento cancellato",
        ],
        'service' => [
            "deleted" => "Servizio rimosso da questo edificio",
        ],
        'errors' => [
            'not_found' => "Edificio non trovato",
            'manager_not_found' => "Property manager non trovato",
            'deleted' => "Edificio cancellato errore: ",
            'manager_assigned' => "I gestori di proprietà assegnano all'errore dell'edificio: ",
            'provider_deleted' => "Il fornitore del servizio ha cancellato l'errore: ",
        ],
        'delete_building_modal' => [
            "title" => "Cancellare l'edificio o gli edifici",
            "description_unit" => "Le unità vengono assegnate alla proprietà selezionata. Se si desidera cancellare anche le unità, attivare l'opzione sottostante",
            "description_request" => "Le richieste vengono assegnate alla proprietà selezionata. Se si desidera cancellare anche la richiesta, attivare l'opzione sottostante",
            "description_both" => "Le unità e le richieste vengono assegnate alla proprietà selezionata. Se anche voi volete cancellarle, attivate le opzioni sottostanti",
            "delete_units" => "Elimina unità",
            "dont_delete_units" => "Non cancellare l'unità (o le unità)",
            "delete_requests" => "Cancellare la richiesta (o le richieste)",
            "dont_delete_requests" => "Non cancellare una o più richieste di cancellazione",
        ],
        'assigned_buildings' => "Edifici assegnati",
        'warning_bar' => [
            'title' => 'Problema trovato',
            'message' => "Sembra che tu non abbia selezionato fortimo membri come destinatari di e-mail per le categorie di richiesta. Si prega di selezionarli dal cassetto delle impostazioni disponibile nella scheda Richieste."
        ]
    ],
    'unit' => [
        "title" => "Unità",
        "not_found" => "Unità non trovata",
        "add" => "Aggiungi unità",
        'edit' => 'Modifica unità',
        "name" => "Numero di unità",
        'auto_create_question' => 'Vuoi creare l\'unità automaticamente?',
        'auto_create_description' => "Con questa opzione, sarete in grado di inserire il numero di unità nel piano e tali unità saranno create automaticamente al salvataggio dell'edificio.",
        "deleted" => "Unità cancellata",
        "saved" => "Unità salvata",
        "floor" => "Piano",
        'floor_title' => [
            'under_ground_floor' => "UG",
            'ground_floor' => "EG",
            'upper_ground_floor' => "OG",
            'top_floor' => "Mansarda",
        ],
        'rooms' => 'Camere',
        "sq_meter" => "Misuratore di mq",
        "room_no" => "Numero di camere",        
        "building" => "Edificio",
        "basement" => "Nel seminterrato",
        "attic" => "In soffitta",        
        "assigned_resident" => "Residente assegnato",
        "resident_assigned" => "Residente assegnato",
        "resident_unassigned" => "Residente non assegnato",
        'assignment' => 'Residenti assegnati',
        'type' => [
            "label" => "Tipo",
            "apartment" => "Appartamento",
            "business" => "Spazio commerciale",
            'hobby_room' => 'Camera hobby',
            'storeroom' => 'Sgabuzzino',
            'underground_parking_space' => 'Parcheggio sotterraneo',
            'outdoor_parking' => 'Parcheggio esterno',
            'motorbike_pitch' => 'Passo moto'
        ],        
        'errors' => [
            'not_found' => "Unità non trovata",
            'create' => "Unità crea errore: ",
            'update' => "Errore di aggiornamento dell'unità: ",
            'resident_assign' => "Errore nell'assegnazione dei residenti: ",
            'resident_not_assign' => "Residente non assegnato a questa unità",
            'resident_not_found' => "Residente non trovato",
            'deleted' => "Unità cancellata errore: ",
        ],
    ],    
    'pinboard' => [
        "title" => "Bacheca",
        "title_label" => "Titolo",
        "content" => "Contenuto",
        "preview" => "Anteprima",
        "add" => "Aggiungere Pinboard",        
        "saved" => "Cartellone salvato",
        'view_incresead' => "Le viste sono aumentate con successo",        
        "deleted" => "Lavagna luminosa cancellata",
        "edit_title" => "Modifica bacheca",
        "likes" => "Gli piace",
        "views" => "Viste",
        "published_at" => "Pubblicato",
        "publish" => "Pubblicare",
        "unpublish" => "Non pubblicare",
        "buildings" => "Edifici",
        "announcement" => "Annuncio a",
        "notify_email" => "Notifica e-mail",
        'notify_email_description' => "Con questa opzione, sarà possibile abilitare la notifica via e-mail",
        "announcement_to" => "Annuncio",
        "comments" => "Commenti",
        "images" => "Foto e documenti",
        'attachments' => 'Allegati',
        'category_default_image_label' => 'Vuoi usare questa immagine?',
        'placeholders' => [            
            "search_provider" => "Fornitore di ricerca",
        ],
        'type' => [
            "label" => "Tipo",
            'post' => "Messaggio",
            "article" => "Articolo",
            "new_neighbour" => "Nuovo vicino",
            "announcement" => "Annuncio",
        ],
        'sub_type' => [
            'label' => 'Sottotipo',
            'important' => 'Importante',
            'critical' => 'Critico',
            'maintenance' => 'Manutenzione',
        ],
        'errors' => [
            'not_found' => "Pinboard non trovato",
            'quarter_not_found' => "Quartiere non trovato",
            'building_not_found' => "Edificio non trovato",
            'provider_not_found' => "Fornitore di servizi non trovato",
            'deleted' => "Pinboard cancellato errore: ",
        ],
        'status' => [
            "label" => "Situazione",
            "new" => "Nuovo",
            "published" => "Pubblicato",
            "unpublished" => "Inedito",
            "not_approved" => "Non approvato",
        ],
        'visibility' => [
            "label" => "Visibilità",
            "address" => "Indirizzo",
            "quarter" => "Quartiere",
            "all" => "Tutti",
        ],
        "assign_type" => "Tipo",        
        'execution_period' => [
            'label' => 'Un giorno o più giorni',
            'single_day' => 'Giorno singolo',
            'many_day' => 'Più giorni',
        ],
        'specify_time_question' => 'Vuoi specificare l\'ora?',
        'specify_time_description' => "Con questa opzione, sarete in grado di specificare l'ora dell'annuncio",
        'execution_interval' => [
            "label" => "Intervallo di esecuzione",
            "date" => "Data di esecuzione",
            "end" => "Fine dell'esecuzione",
            "start" => "Inizio esecuzione",
            'from' => 'Da',
            "separator" => "A",
        ],
        'category' => [
            "label" => "Categoria",
            "general" => "Generale",
            "maintenance" => "Manutenzione",
            "electricity" => "Elettricità",
            "heating" => "Riscaldamento",
            "sanitary" => "Sanitario",
        ],
    ],
    'service' => [
        "title" => "Servizi",
        'view' => 'Vista',
        'view_title' => 'Visualizza servizio',
        "add_title" => "Aggiungi servizio",
        "edit_title" => "Modifica servizio",
        "saved" => "Servizio salvato",
        "deleted" => "Servizio cancellato",
        "category" => [
            "label" => "Categoria",
            "electrician" => "Elettricista",
            "heating_company" => "Azienda di riscaldamento",
            "lift" => "Sollevare",
            "sanitary" => "Sanitario",
            "key_service" => "Servizio chiave",
            "caretaker" => "Custode",
            "real_estate_service" => "Servizio immobiliare",
        ],
        "contact_details" => "Dati di contatto",
        "user_credentials" => "Credenziali utente",
        "company_details" => "Dettagli dell'azienda",
        "assign_type" => "Tipo",       
        'placeholders' => [
            "category" => "Selezionare la categoria",
        ],
        'errors' => [
            'not_found' => "Fornitore di servizi non trovato",
            'create' => "Il fornitore di servizi crea un errore: ",
            'update' => "Errore aggiornato del fornitore di servizi: ",
            'deleted' => "Errore cancellato dal fornitore di servizi: ",
            'quarter_not_found' => "Quartiere non trovato",
            'building_not_found' => "Edificio non trovato",
            'building_already_assign' => "Edificio già assegnato per tutto il trimestre",
        ],
    ],
    'quarter' => [
        "title" => "Quartieri",
        "add" => "Aggiungi trimestre",
        "edit" => "Modifica trimestre",
        "saved" => "Quartiere salvato",
        "deleted" => "Quartiere cancellato",        
        "required" => "Questo campo è obbligatorio",
        "buildings" => "Edifici",
        'count_of_buildings' => 'Conteggio degli edifici',
        'buildings_count' => 'Conteggio edifici',
        'total_units_count' => 'Totale unità di misura',
        'occupied_units_count' => 'Le unità occupate contano',
        'active_residents_count' => 'Nombre de résidents actifs',
        'assignment' => "Assegnazione di manager/amministratore",
        'errors' => [
            'not_found' => "Quartiere non trovato",
            'deleted' => "Errore al quarto eliminato: ",
        ],
    ],
    'request' => [        
        "deleted" => "Richiesta supprimée",
        "title" => "Richieste",
        "created" => "Creato",
        "saved" => "Requête sauvegardée",
        "prop_title" => "Titolo",
        "category" => "Categoria",
        "edit_title" => "Modifica Richiesta",
        "add_title" => "Aggiungi Richiesta",
        'mass_edit' => [
            'label' => 'Modifica di massa',
            'options' => [
                'service_provider' => 'Fornitore di servizi',
                'property_manager' => 'Proprietà manager',
                'change_status' => 'Cambia stato'
            ],
            'service_provider' => [
                'modal' => [
                    'heading_title' => 'Assegnare la voce ai fornitori di servizi',
                    'content_label' => 'È possibile selezionare i fornitori di servizi qui',
                    'footer_button' => 'Assegnare i fornitori di servizi',
                    'switcher_label' => 'Informare i fornitori di servizi',
                    'switcher_desc' => 'È possibile informare i fornitori di servizi interessati',
                ]
            ],
            'property_manager' => [
                'modal' => [
                    'heading_title' => 'Assegnare i gestori di immobili alla rubrica',
                    'content_label' => 'È possibile selezionare il gestore(i) di proprietà qui',
                    'footer_button' => 'Assegnare i gestori di proprietà',
                ]
            ],
            'change_status' => [
                'modal' => [
                    'heading_title' => 'Cambiare l\'intestazione di stato',
                    'content_label' => 'È possibile modificare lo stato qui',
                    'footer_button' => 'Cambia stato',
                ]
            ],
        ],
        "due_date" => "Scadenza",
        "solved_date" => "Data di fabbricazione",
        "closed_date" => "Data di chiusura",
        "service" => "Servizio",
        "created_by" => "Creato da",
        "is_public" => "Pubblico",
        'public_title' => 'Rendere pubblica la richiesta',
        'public_desc' => "È possibile contrassegnare questa richiesta come pubblica e renderla visibile ad altre persone nell'edificio o nel quartiere.",
        'visibility_title' => "Per chi rendere visibile?",
        'visibility_desc' => "Indicare se i residenti possono vedere all'interno di un edificio o anche all'interno del quartiere.",
        'send_notification_title' => 'Informare i residenti',
        'send_notification_desc' => "È possibile informare i residenti interessati via e-mail su questa richiesta pubblica.",
        "comments" => "Commenti",
        "assigned_to" => "Assegnato a",
        "assign_providers" => "Assegnare i fornitori",
        "assign_managers" => "Assegnare i manager",
        'assigned_service_providers' => 'Fornitori di servizi assegnati',
        'assigned_property_managers' => 'Gestori di immobili assegnati',
        "notify" => "Avvisare",
        "public_legend" => "Imposta questa opzione per rendere la richiesta visibile a tutti i vicini residenti",
        "conversation" => "Conversazione",
        'conversation_created' => "Commento alla conversazione creato",
        'internal_notice_saved' => "Avviso interno salvato",
        'internal_notice_updated' => "Avviso interno aggiornato",
        'internal_notice_deleted' => "Soppressione dell'avviso interno",
        "open_conversation" => "Aprite",
        "other_recipients" => "Altri destinatari",
        "recipients" => "Destinatari",
        "images" => "Foto e documenti",
        "no_images_message" => "Nessun file caricato",
        "request_details" => "Richiedi dettagli",
        "internal_notices" => "Avvisi interni",
        "status_changed" => "Stato cambiato",
        "priority_changed" => "La priorità è cambiata",
        'assignment' => 'Manager/Servizi',
        'active_reminder_switcher' => 'Promemoria',
        'days_left' => "Quanti giorni prima dovrebbe essere inviata l'e-mail?",
        'send_person' => 'Quale persona deve essere informata?',
        'sort' => 'In scadenza il',
        'reset_sort' => 'Ripristina ordinamento',
        'creation_date' => 'Data di creazione',
        'category_list' => [
            'general' => 'Preoccupazioni generali',
            'malfunction' => 'Guasto',
            'deficiency' => 'Carenza (limitata nel tempo)'
        ],
        'sub_category' => [
            'surrounding' => 'Circostante',
            'real_estate' => 'Immobili',
            'flat' => 'Appartamento',
        ],
        'media' => [
            "added" => "Documento ajouté",
            "removed" => "Supporti rimossi",
            "deleted" => "Media cancellati",
            "delete" => "Cancellare",
        ],
        'priority' => [
            "label" => "Priorità",
            "urgent" => "E' urgente",
            "low" => "Basso",
            "normal" => "Normale",
        ],
        'internal_priority' => [
            "label" => "Priorità interna",
            "urgent" => "E' urgente",
            "low" => "Basso",
            "normal" => "Normale",
        ],
        'defect_location' => [
            "label" => "Posizione del difetto",
            "apartment" => "Appartamento",
            "building" => "Edificio",
            "environment" => "Ambiente"
        ],
        'qualification' => [
            "label" => "Qualificazione",
            "none" => "Nessuna",
            "optical" => "Ottico",
            "sia" => "Sia",
            "2_year_warranty" => "2 anni di garanzia",
            "cost_consequences" => "Conseguenze dei costi",
        ],
        'location' => [
            'house_entrance' => 'Ingresso Casa',
            'staircase' => 'Scala',
            'elevator' => 'Ascensore',
            'car_park' => 'Parcheggio sotterraneo',
            'washing' => 'Lavaggio/asciugatura',
            'heating' => 'Tecnologia/riscaldamento',
            'electro' => 'Tecnologia/Elettro',
            'facade' => 'Facciata',
            'roof' => 'Tetto',
            'other' => 'Altro'
        ],
        'room' => [
            'bath' => 'Bagno/WC',
            'shower' => 'Doccia/WC',
            'entrance' => 'Ingresso',
            'passage' => 'Passaggio',
            'basement' => 'Seminterrato',
            'kitchen' => 'Cucina',
            'storeroom' => 'Reduite',
            'habitation' => 'Abitazione',
            'room1' => 'Camera 1',
            'room2' => 'Camera 2',
            'room3' => 'Camera 3',
            'room4' => 'Camera 4',
            'all' => 'Tutti',
            'other' => 'Altro'
        ],
        'capture_phase' => [
            'other' => 'Altro',
            'construction' => 'Fase di costruzione',
            'shell' => 'Accettazione Shell',
            'preliminary' => 'Accettazione Preliminare',
            'work' => 'Accettazione del lavoro',
            'surrender' => 'Arrendersi',
            'inspection' => 'Accettazione'
        ],
        'payer' => [
            'landlord' => 'Padrone di casa',
            'resident' => 'Residente',
            'resident/landlord' => 'Residente/Padrone di casa'
        ],
        'status' => [
            "label" => "Situazione",
            "received" => "Ricevuto",
            "assigned" => "Assegnato",
            "in_processing" => "In lavorazione",
            "reactivated" => "Riattivato",
            "done" => "Fatto",
            "archived" => "Archiviato",
            "solved" => "Risolte",
            "pending" => "Pendenti"
        ],
        'category_options' => [
            "disturbance" => "Perturbazione",
            "defect" => "Difetto",
            "other" => "Altro",
            'room' => 'Camera',
            'range' => 'Gamma',
            'component' => 'Componente',
            'capture_phase' => 'Fase di cattura',
            'cost' => 'Costo Impatto',
            'keywords' => 'Parole chiave',
        ],
        'placeholders' => [
            "category" => "Selezionare la categoria",
            "qualification" => "Selezionare la qualifica",
            "status" => "Selezionare lo stato",
            "due_date" => "Scegli la data di scadenza",
            "resident" => "Ricerca di un residente",
            "visibility" => "Selezionare la visibilità",
            "person" => "Cerca una persona",
        ],
        'mail' => [
            "body" => "Corpo",
            "subject" => "Oggetto",
            "to" => "A",            
            "notify" => "Invia e-mail",            
            "provider" => "Fornitore",
            "manager" => "Manager",
            "cancel" => "Annulla",
            "send" => "Invia",
            "cc" => "CC",
            "bcc" => "BCC",
            "success" => "Messaggio di notifica inviato con successo",            
        ],
        'user_type' => [
            "label" => "Tipo",
            "provider" => "Servizio",
            "manager" => "Manager",
            'user' => 'Administrator',
        ],
        'visibility' => [
            "label" => "Visibilità",
            "resident" => "Privato",
            "quarter" => "Quartiere",
            "building" => "Edificio",
        ],
        'errors' => [
            'not_found' => 'Richiesta di servizio non trovata',
            'not_allowed_change_status' => "Non sei autorizzato a cambiare stato",
            'provider_not_found' => 'Fornitore di servizi non trovato',
            'tag_not_found' => 'Etichetta non trovata',
            'user_not_found' => 'Utente non trovato',
            'conversation_not_found' => "Conversazione non trovata",
            'statistics_error' => "richiesta errore di statistica: ",
            'internal_notice_not_found' => "Avviso interno non trovato",
            'deleted' => "Errore cancellato nella richiesta di assistenza: ",
        ],                
        'actions' => 'Azioni',
        'download_pdf' => [
            'title' => 'Scarica il PDF',
            'entrepreneur_signature'=> 'Signature entrepreneur',
            'customer_signature'=> 'Firma del cliente',
            'service_request' => 'Service Request',
            'contact_details' => 'Contact Details',
            'contact_text' => "Questi sono i dati di contatto dell'attuale residente/proprietario dell'unità abitativa.",
        ],
        'go_to_building' => 'vai al palazzo'
    ],
    'request_category' => [
        "title" => "Richiedi categorie",
        "add" => "Aggiungi categoria",
        "cancel" => "Annulla",
        "required" => "Questo campo è obbligatorio",
        "parent" => "Categoria genitore",
        'errors' => [
            'not_found' => "Richiesta di servizio Categoria non trovata",
            'parent_not_found' => "Richiesta di servizio genitori Categoria non trovata",
            'multiple_level_not_found' => "Non sono ammesse categorie nidificate a più livelli",
            'used_by_request' => "Categoria di richiesta di servizio che viene utilizzata da una richiesta di servizio",
        ]
    ],
    'property_manager' => [
        "title" => "Gestori immobiliari",
        "add" => "Aggiungi Property Manager",
        "saved" => "Gestionnaire immobilier sauvé",
        "deleted" => "Gestionnaire immobilier supprimé",
        "edit_title" => "Modifica Property Manager",
        "profession" => "Professione",
        "slogan" => "Slogan",
        "linkedin_url" => "URL di linkedin",
        "xing_url" => "URL Xing",
        "details_card" => "Dettagli",
        'delete_with_reassign_modal' => [
            "title" => "Cancellare e riassegnare gli edifici",
            "description" => "Il gestore di proprietà selezionato è collegato alle proprietà. È possibile assegnare le proprietà ad un'altra persona. Per fare questo, selezionare un gestore di proprietà dall'elenco",
            "search_title" => "Cerca Property Manager",
        ],
        "delete_without_reassign" => "Cancellare",
        "profile_card" => "Profilo utente",
        "social_card" => "Social Media",
        "assign_type" => "Tipo",        
        'errors' => [
            'not_found' => "Property Manager non trovato",
            'create' => "Property Manager crea un errore: ",
            'update' => "Errore aggiornato Property Manager: ",
            'quarter_not_found' => "Quartiere non trovato",
            'building_not_found' => "Edificio non trovato",
            'building_already_assign' => "Edificio già assegnato per tutto il trimestre",
            'building_assign_deleted_property_manager' => "Non è possibile assegnare edifici a un gestore di proprietà cancellato",
            'deleted' => "Errore cancellato dal Property Manager: ",
        ],
    ],
    'house_owner' => [
        "title" => "Proprietari",
        "add" => "Aggiungi proprietario di casa",
        "saved" => "Proprietario salvato",
        "deleted" => "Proprietario Soppresso",
        "edit_title" => "Proprietario della casa editrice",
        "first_name" => "Nome",
        "last_name" => "Cognome",
        "profession" => "Professione",
        "slogan" => "Slogan",
        "linkedin_url" => "URL di linkedin",
        "xing_url" => "URL Xing",
        "password" => "La password",
        "confirm_password" => "Confermare la password",
        "building_card" => "Assegnare gli edifici",
        "details_card" => "Dettagli",
        "no_buildings" => "Non ci sono edifici assegnati",
        "add_buildings" => "Aggiungere edifici",
        "buildings_search" => "Ricerca di edifici",
        "quarters" => "Quartieri",
        'delete_with_reassign_modal' => [
            "title" => "Cancellare e riassegnare gli edifici",
            "description" => "Il proprietario della casa selezionata è legato alle proprietà. È possibile assegnare le proprietà ad un'altra persona. Per fare questo, selezionare un proprietario di casa dalla lista.",
            "search_title" => "Ricerca Proprietario di casa",
        ],
        "delete_without_reassign" => "Cancellare",
        "profile_card" => "Profilo utente",
        "social_card" => "Social Media",
        "assign_type" => "Tipo",
        "building_already_assigned" => "L'edificio e' gia' all'interno di un quarto di dollaro.",
        'errors' => [
            'not_found' => "Proprietario della casa non trovato",
            'create' => "Il proprietario della casa crea un errore: ",
            'update' => "Errore del proprietario della casa aggiornato: ",
            'quarter_not_found' => "Quartiere non trovato",
            'building_not_found' => "Edificio non trovato",
            'building_already_assign' => "Edificio già assegnato per tutto il trimestre",
            'building_assign_deleted_house_owner' => "Non è possibile assegnare gli edifici a un proprietario di casa cancellato.",
            'deleted' => "Il proprietario della casa ha cancellato l'errore: ",
        ],
    ],
    'listing' => [
        "title" => "Prodotti",
        "add" => "Aggiungi prodotto",
        "edit_title" => "Modifica prodotto",                
        "listing_title" => "Titolo",
        "published_at" => "Pubblicato",
        "publish" => "Pubblicare",
        "unpublish" => "Non pubblicare",
        "likes" => "Gli piace",
        "saved" => "Prodotto salvato",
        "deleted" => "Prodotto cancellato",
        "comments" => "Commenti",
        "contact" => "Contatto",
        "price" => "Prezzo",
        'comment_created' => "Commento creato con successo",
        'errors' => [
            'not_found' => "Prodotto non trovato",
            'deleted' => "Errore prodotto cancellato: ",
        ],
        'type' => [
            'label' => 'Tipo',
            'sell' => 'Vendere',
            'lend' => 'Prestito',
            'service' => 'Servizio',
            'giveaway' => 'Dare via',
        ],
        'status' => [
            'label' => 'Stato',
            'published' => 'Pubblicato',
            'unpublished' => 'Inedito',
        ],
        'visibility' => [
            'label' => 'Visibilità',
            'address' => 'Indirizzo',
            'quarter' => 'Quartiere',
            'all' => 'Tutti',
        ],
    ],
    'template' => [
        'saved' => 'Modello salvato',
        'deleted' => 'Modello cancellato',
        'add' => 'Aggiungi',
        'title' => 'Modelli',
        'subject' => 'Oggetto',
        'body' => 'Corpo',
        'category' => 'Categoria',
        'tags' => 'Tags',
        'placeholders' => [
            'category' => 'Scegli la categoria',
        ],
        'errors' => [
            'not_found' => "Modello non trovato",
        ],
    ],
    'cleanify' => [
        "page_title" => "Pulire la richiesta",        
        "address" => "Indirizzo",
        "save" => "Invia richiesta",
        "success" => "Pulire la richiesta inviata con successo",
        "terms_and_conditions" => "Accettare i termini e le condizioni",
        "terms_text" => "Termini testo qui, testo lungo"
    ],
    'editor' => [
        'bold' => 'Audacieux',
        'underline' => 'Souligner',
        'italic' => 'Italique',
        'forecolor' => 'Couleur',
        'bgcolor' => 'Couleur de fond',
        'strikethrough' => 'biffé',
        'eraser' => 'Gomme',
        'source' => 'Codeview',
        'quote' => 'Citation',
        'fontfamily' => 'Famille de polices',
        'fontsize' => 'Taille de police',
        'head' => 'Tête',
        'orderlist' => 'Liste ordonnée',
        'unorderlist' => 'Liste non ordonnée',
        'alignleft' => 'Aligner à gauche',
        'aligncenter' => 'Aligner le centre',
        'alignright' => 'Aligner à droite',
        'link' => 'Insérer un lien',
        'link_target' => 'Mode ouvert',
        'text' => 'Texte',
        'submit' => 'Soumettre',
        'cancel' => 'Annuler',
        'unlink' => 'Supprimer le lien',
        'table' => 'Table',
        'emotion' => 'Émotions',
        'img' => 'Image',
        'upload_img' => 'Télécharger',
        'link_img' => 'Lien',
        'video' => 'Vidéo',
        'width' => 'largeur',
        'height' => 'apogée',
        'location' => 'Lieu',
        'loading' => 'Chargement',
        'searchlocation' => 'perquisition',
        'dynamic_map' => 'Dynamique',
        'clear_location' => 'Clair',
        'lang_dynamic_one_location' => 'Un seul endroit sur la carte dynamique',
        'insertcode' => 'Insérer le code',
        'undo' => 'Annuler',
        'redo' => 'Refaire',
        'fullscreen' => 'Screnn complet',
        'open_link' => 'lien ouvert',
        'upload_place_txt' => 'téléchargement__',
        'upload_timeout_place_txt' => 'upload_timeout__',
        'upload_error_place_txt' => 'upload_error__',
        'title' => 'Denominazione',
        'in_format' => 'In formato',
        'rows' => 'Righe',
        'cols' => 'Colonne',
        'color' => [
            'dark_red' => 'Rosso scuro',
            'violet' => 'Viola',
            'red' => 'Rosso',
            'fresh_pink' => 'Rosa fresco',
            'navy_blue' => 'Blu marino',
            'blue' => 'Blu',
            'blue_lake' => 'Lago blu',
            'blue_green' => 'Blu verde',
            'green' => 'Verde',
            'olive' => 'L\'oliva',
            'light_green' => 'Verde chiaro',
            'orange' => 'Arancione',
            'gray' => 'Grigio',
            'silver' => 'Argento',
            'black' => 'Nero',
            'white' => 'Bianco',
        ]
    ],
];
