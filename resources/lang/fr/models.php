<?php
return [
    'user' => [
        'add_admin' => 'Ajouter un administrateur',
        'edit_admin' => 'Modifier l\'administrateur',                
        "saved" => "Utilisateur enregistré avec succès",
        "deleted" => "Utilisateur supprimé",
        "not_found" => "Utilisateur non trouvé",
        "profile_image" => "Image du profil",
        "profile_text" => "Texte du profil",
        "logo" => "Logo",
        'circle_logo' => "Logo du cercle",
        'favicon_icon' => "Icône Favicon",
        "resident_logo" => "Logo du résident",                
        "notification_saved" => "Réglage de la notification sauvegardé",        
        "request_category_saved" => "Catégorie de demande de service sauvegardée",
        "request_category_deleted" => "Catégorie de demande de service supprimée",                
        'errors' => [
            'not_found' => "Utilisateur introuvable",            
            'image_upload' => "Erreur de téléchargement de l'image de l'utilisateur :",
            'incorrect_password' => "Mot de passe utilisateur incorrect",
            'email_missing' => "email est manquant",
            'email_already_exists' => "Ce [:email] email existe déjà, Sélectionner un autre email",
            'email_not_exists' => "Ce [:email] email n'existe pas",            
            'deleted' => "Erreur de suppression par l'utilisateur : ",
        ],        
    ],
    'resident' => [
        "view" => "Vue",
        "view_title" => "Voir résident",
        "edit_title" => "Modifier Résident",
        "download_credentials" => "Télécharger les informations d'identification",
        "send_credentials" => "Envoyez les papiers d'identité",
        "credentials_sent" => "Lettres de créance envoyées",
        "credentials_send_fail" => "Fichier d'authentification introuvable. Essayez de mettre à jour le mot de passe résident pour le régénérer",
        "credentials_download_failed" => "Fichier d'authentification introuvable. Essayez de mettre à jour le mot de passe résident pour le régénérer",
        "add" => "Ajouter Résident",
        "saved" => "Résident sauvé",
        "deleted" => "Résident supprimé",
        "status_changed" => "Le statut a changé",
        "password_reset" => "Réinitialisation réussie du mot de passe résident",
        "update" => "Mise à jour",
        "birth_date" => "Date de naissance",
        'nation' => 'Nation',
        "mobile_phone" => "Téléphone portable",
        "work_phone" => "Téléphone au travail",        
        "private_phone" => "Téléphone personnel",
        "created_date" => "Date de création",
        "pinboard" => "Panneau d'affichage",
        "listings" => "Produits",
        "company" => "Nom de l'entreprise",
        'building' => [
            'name' => 'Edificio',
        ],
        'unit' => [
            'name' => 'Unité',
        ],
        'search_building' => 'Rechercher un bâtiment',
        'search_unit' => 'Unité de recherche',
        'errors' => [
            'not_found' => "Résident introuvable",
            'incorrect_email' => "Adresse e-mail incorrecte",
            'create' => "Le résident crée l'erreur : ",
            'update' => "Erreur de mise à jour résidente : ",
            'deleted' => "Résident Supprimer erreur : ",
            'not_allowed_change_status' => "Vous n'êtes pas autorisé à changer de statut.",
        ],        
        "personal_details_card" => "Données personnelles",
        "account_info_card" => "Connexion de l'utilisateur",
        "contact_info_card" => "Coordonnées de contact",
        "contract" => [
            "title" => "Contrat",
            "rent_end" => "Fin du loyer",
            "rent_start" => "Début du loyer",
            'rent_type' => 'Type de loyer',            
            'rent_duration' => 'Durée du loyer',
            'rent_durations' => [
                'unlimited' => 'Illimité',
                'limited' => 'Limitée',
            ],
            'contract_pdf' => 'Contrat PDF',
            'deposit_amount' => 'Montant du dépôt',
            'type_of_deposit' => 'Type de dépôt',
            'deposit_types' => [
                'bank_deposit' => 'Dépôt bancaire',                
                'bank_guarantee' => 'Garantie bancaire',
                'insurance' => 'Assurance',
                'other' => 'Autre',
            ],
            'deposit_status' => [
                'label' => 'Statut des dépôts',
                'yes' => 'Oui',
                'no' => 'Non',
            ],
            'contract_id' => 'ID du contrat',
            'rent_status' => [
                'active' => 'Actif',
                'inactive' => 'Inactif',
            ],
            'add' => 'Ajouter un nouveau contrat',
            'pdf_only_desc' => 'Veuillez noter que seuls les fichiers PDF peuvent être téléchargés.',
            'saved' => 'Contrat sauvegardé',
        ],       
        'status' => [
            "label" => "Statut",
            "active" => "Actif",
            "not_active" => "Non actif",
            'total' => 'Total',
        ],
        'credentials_pdf' => [
            'resident_credentials' => 'Titres de compétences des résidents',
            'code' => 'Code de déverrouillage personnel',
            'telephone' => 'Téléphone',
            'your_email' => 'Votre adresse e-mail',
            'email' => 'courriel',            
            'welcome' => 'Bienvenue sur le portail des résidents de l\'Institut de la santé publique et des populations des',
            'content_1' => 'Nous avons le plaisir de vous informer qu\'un compte a été créé pour vous dans le total du résident et de vous envoyer le code d\'activation.',
            'offer' => 'Qu\'offre l\'application ?',
            'offers' => '
                <li>Avec le dossier de résident numérique, vous avez accès à tous les documents pertinents, tels que le contrat de location, le règlement intérieur ou d\'autres documents relatifs à la propriété.</li>
                <li>Le système de billetterie vous permet de traiter vos demandes facilement et sans complications - vous pouvez communiquer vos préoccupations à l\'administration à tout moment et de n\'importe quel endroit.</li>
                <li>Vous pouvez vendre ou prêter des objets à votre quartier sur la place du marché et dans la zone de prêt.</li>
                <li>Partagez les nouvelles avec vos voisins en publiant une contribution. Le tableau d\'affichage est également utilisé par l\'administration pour la communication, de sorte que tout le monde est toujours à jour.</li>
                <li>D\'autres Micro - Apps au sein de l\'application fixent de nouveaux standards de qualité de vie, grâce auxquels divers services peuvent être utilisés de manière pratique.</li>
            ',
            'register' => 'Première inscription et activation de votre compte',
            'content_2' => 'Pour vous inscrire, cliquez sur le lien ci-dessous et connectez-vous avec votre adresse e-mail et votre code d\'activation personnel. Une fois connecté, vous pouvez définir votre propre mot de passe et l\'utiliser pour vous connecter.',
            'link_application' => 'Lien vers l\'application',
            'content_3' => 'Au plaisir de vous accueillir à bord !',
            'content_4' => 'Si vous avez besoin d\'aide pour l\'enregistrement, nous sommes à votre disposition.',
            'your_sincerely' => 'Sincèrement vôtre',
            'your_administration' => 'votre administration'
        ],
        'type' => [
            'label' => 'Type',
            'resident' => 'Résident',
            'owner' => 'Propriétaire',
        ],
    ],
    'building' => [
        "title" => "Bâtiments",
        "edit_title" => "Éditer le bâtiment",
        "add" => "Ajouter un bâtiment",
        "cancel" => "Annuler",
        "deleted" => "Bâtiment supprimé avec succès",
        "units" => "Unités",
        "saved" => "Bâtiment sauvé",
        "floors" => "Planchers",
        'under_floor' => 'Sous le plancher',
        "basement" => "Sous-sol",
        "attic" => "Grenier aménageable",
        "floor_nr" => "Nombre d'étages",
        "internal_building_id" => "Numéro d'identification interne de l'immeuble",       
        "address_search" => "Veuillez entrer l'adresse",
        "not_found" => "Bâtiment non trouvé",
        'media_category' => [
            "house_rules" => "Règlement intérieur",
            "operating_instructions" => "Notice d'utilisation",
            'care_instructions' => 'Conseils d\'entretien',
            'other' => 'Autre',
        ],
        "files" => "Fichiers",
        "add_files" => "Ajouter des fichiers",
        "add_companies" => "Ajouter des entreprises",
        "companies" => "Sociétés de services",        
        "select_media_category" => "Sélectionner une catégorie de médias",
        "quarter" => "Trimestre",
        "managers" => "Gestionnaires",
        "house_num" => "Maison Nr.",
        "assign_managers" => "Affecter des gestionnaires",
        "unassign_manager" => "Désassigner",
        "managers_assigned" => "Gestionnaires affectés",
        "occupied_units" => "Unités Ocuppied",
        "free_units" => "Unités libres",        
        'document' => [
            "uploaded" => "Document téléchargé",
            "deleted" => "Document supprimé",
        ],
        'service' => [
            "deleted" => "Service enlevé de ce bâtiment",
        ],
        'errors' => [
            'not_found' => "Bâtiment non trouvé",
            'manager_not_found' => "Gestionnaire immobilier introuvable",
            'deleted' => "Erreur de suppression du bâtiment : ",
            'manager_assigned' => "Les gestionnaires immobiliers attribuent à l'erreur de construction : ",
            'provider_deleted' => "Erreur supprimée par le fournisseur de services : ",
        ],
        'delete_building_modal' => [
            "title" => "Supprimer le(s) bâtiment(s)",
            "description_unit" => "Les unités sont affectées à la propriété sélectionnée. Si vous souhaitez également supprimer les unités, veuillez activer l'option ci-dessous..",
            "description_request" => "Les demandes sont affectées à la propriété sélectionnée. Si vous souhaitez également supprimer une demande, veuillez activer l'option ci-dessous..",
            "description_both" => "Les unités et les requêtes sont affectées à la propriété sélectionnée. Si vous souhaitez également les supprimer, veuillez activer les options ci-dessous..",
            "delete_units" => "Supprimer unité(s)",
            "dont_delete_units" => "Ne supprimez pas d'unité(s)",
            "delete_requests" => "Supprimer demande(s)",
            "dont_delete_requests" => "Ne supprimez pas de demande(s)",
        ],
        'assigned_buildings' => "Bâtiments affectés",
    ],
    'unit' => [
        "title" => "Unités",
        "not_found" => "Unité non trouvée",
        "add" => "Ajouter une unité",
        'edit' => 'Modifier l\'unité',
        "name" => "Numéro d'unité",
        'auto_create_question' => 'Voulez-vous créer l\'unité automatiquement ?',
        'auto_create_description' => "Avec cette option, vous pourrez entrer le nombre d'unités dans l'étage et ces unités seront créées automatiquement lors de l'enregistrement du bâtiment.",
        "deleted" => "Unité supprimée",
        "saved" => "Unité sauvegardée",
        "floor" => "L'étage",
        'floor_title' => [
            'under_ground_floor' => "UG",
            'ground_floor' => "EG",
            'upper_ground_floor' => "OG",
            'top_floor' => "Grenier aménageable",
        ],
        'rooms' => 'Chambres',
        "sq_meter" => "Mètre carré",
        "room_no" => "Nombre de pièces",
        "building" => "Bâtiment",
        "basement" => "Sous-sol",
        "attic" => "Grenier aménageable",        
        "assigned_resident" => "Résident affecté",
        "resident_assigned" => "Résident affecté",
        "resident_unassigned" => "Résident non affecté",
        'assignment' => 'Résidents assignés',
        'type' => [
            "label" => "Type",
            "apartment" => "Appartement",
            "business" => "Espace commercial",
            'hobby_room' => 'Pièce de loisirs',
            'storeroom' => 'Local de stockage',
            'underground_parking_space' => 'Place de parking souterrain',
            'outdoor_parking' => 'Parking extérieur',
            'motorbike_pitch' => 'Emplacement moto'
        ],        
        'errors' => [
            'not_found' => "Unité non trouvée",
            'create' => "L'unité crée l'erreur : ",
            'update' => "Erreur de mise à jour de l'unité : ",
            'resident_assign' => "Erreur d'affectation des résidents : ",
            'resident_not_assign' => "Résident non affecté à cette unité",
            'resident_not_found' => "Résident introuvable",
            'deleted' => "Erreur d'effacement de l'unité : ",
        ],
    ],    
    'pinboard' => [
        "title" => "Panneau d'affichage",
        "title_label" => "Titre",
        "content" => "Contenu",
        "preview" => "Aperçu",
        "add" => "Ajouter Panneau d'affichage",        
        "saved" => "Panneau d'affichage sauvegardé",
        'view_incresead' => "Le nombre de points de vue a augmenté avec succès",        
        "deleted" => "Panneau d'affichage effacé",
        "edit_title" => "Modifier le tableau d'affichage",
        "likes" => "Aime",
        "views" => "Vues",
        "published_at" => "Publié",
        "publish" => "Publier",
        "unpublish" => "Non publié",
        "buildings" => "Bâtiments",
        "announcement" => "Annonce à l'intention de",
        "notify_email" => "Prévenez par courriel",
        'notify_email_description' => "Avec cette option, vous serez en mesure d'activer la notification par e-mail",
        "announcement_to" => "Annonce",
        "comments" => "Commentaires",
        "images" => "Photos et documents",
        'attachments' => 'Pièces jointes',
        'category_default_image_label' => 'Voulez-vous utiliser cette image?',
        'placeholders' => [            
            "search_provider" => "Fournisseur de recherche",
        ],
        'type' => [
            "label" => "Type",
            "post" => "Poster",
            "article" => "Article",
            "new_neighbour" => "Nouveau voisin",
            "announcement" => "Annonce",
        ],
        'sub_type' => [
            'label' => 'Sous-type',
            'important' => 'Important',
            'critical' => 'Critique',
            'maintenance' => 'Maintenance',
        ],
        'errors' => [
            'not_found' => "Panneau d'affichage non trouvé",
            'quarter_not_found' => "Trimestre non trouvé",
            'building_not_found' => "Bâtiment non trouvé",
            'provider_not_found' => "Fournisseur de services introuvable",
            'deleted' => "Erreur de suppression du tableau d'affichage : ",
        ],
        'status' => [
            "label" => "Statut",
            "new" => "Nouveau",
            "published" => "Publié",
            "unpublished" => "Non publié",
            "not_approved" => "Non approuvé",
        ],
        'visibility' => [
            "label" => "Visibilité",
            "address" => "Adresse",
            "quarter" => "Trimestre",
            "all" => "Tous",
        ],
        "assign_type" => "Type",
        'execution_period' => [
            'label' => 'Un jour ou plusieurs jours',
            'single_day' => 'Une seule journée',
            'many_day' => 'Plusieurs jours',
        ],
        'specify_time_question' => 'Voulez-vous spécifier l\'heure ?',
        'specify_time_description' => "Avec cette option, vous pourrez spécifier l'heure de l'annonce",
        'execution_interval' => [
            "label" => "Intervalle d'exécution",
            "date" => "Date d'exécution",
            "end" => "Fin de l'exécution",
            "start" => "Début d'exécution",
            'from' => 'De',
            "separator" => "A",
        ],
        'category' => [
            "label" => "Catégorie",
            "general" => "Général",
            "maintenance" => "Maintenance",
            "electricity" => "Électricité",
            "heating" => "Chauffage",
            "sanitary" => "Sanitaire",
        ],
    ],
    'service' => [
        "title" => "Services",
        "add_title" => "Ajouter un service",
        "edit_title" => "Modifier le service",
        "saved" => "Servizio salvato",
        "deleted" => "Servizio cancellato",
        "category" => [
            "label" => "Catégorie",
            "electrician" => "Électricien",
            "heating_company" => "Société de chauffage",
            "lift" => "Ascenseur",
            "sanitary" => "Sanitaire",
            "key_service" => "Service clé",
            "caretaker" => "Concierge",
            "real_estate_service" => "Service immobilier",
        ],
        "contact_details" => "Coordonnées de contact",
        "user_credentials" => "Informations d'identification de l'utilisateur",
        "company_details" => "Coordonnées de l'entreprise",
        "assign_type" => "Type",        
        'placeholders' => [
            "category" => "Sélectionnez une catégorie",
        ],
        'errors' => [
            'not_found' => "Fournisseur de services introuvable",
            'create' => "Le fournisseur de services crée l'erreur : ",
            'update' => "Erreur de mise à jour du fournisseur de services : ",
            'deleted' => "Erreur supprimée par le fournisseur de services : ",
            'quarter_not_found' => "Trimestre non trouvé",
            'building_not_found' => "Bâtiment non trouvé",
            'building_already_assign' => "Immeuble déjà assigné jusqu'au quart",
        ],
    ],
    'quarter' => [
        "title" => "Trimestres",
        "add" => "Ajouter un trimestre",
        "edit" => "Modifier le trimestre",
        "saved" => "Trimestre économisé",
        "deleted" => "Trimestre supprimé",        
        "required" => "Ce champ est obligatoire",
        "buildings" => "Bâtiments",
        'count_of_buildings' => 'Nombre de bâtiments',
        'buildings_count' => 'Nombre de bâtiments',
        'total_units_count' => "Nombre total d'unités",
        'occupied_units_count' => 'Nombre de logements occupés',
        'active_residents_count' => 'Nombre de résidents actifs',
        'assignment' => "Affectation des gestionnaires/administrateurs",
        'errors' => [
            'not_found' => "Trimestre non trouvé",
            'deleted' => "Erreur d'effacement trimestriel : ",
        ],
    ],
    'request' => [
        "audits" => "Audits",
        "deleted" => "Richiesta supprimée",
        "title" => "Demandes",
        "created" => "Créé",
        "saved" => "Requête sauvegardée",
        "prop_title" => "Titre",
        "category" => "Catégorie",
        "edit_title" => "Demande de modification",
        "add_title" => "Ajouter une demande",
        "due_date" => "Date d'échéance",
        "closed_date" => "Date de fermeture",
        "service" => "Service",
        "created_by" => "Créé par",
        "is_public" => "Public",
        'public_title' => 'Rendre la demande publique',
        'public_desc' => "Vous pouvez marquer cette demande comme publique et la rendre visible aux autres personnes dans le bâtiment ou le quartier.",
        'visibility_title' => "Pour qui rendre visible ?",
        'visibility_desc' => "Indiquez si les résidents peuvent voir à l'intérieur d'un bâtiment ou même dans le quartier.",
        'send_notification_title' => 'Aviser les résidents',
        'send_notification_desc' => "Vous pouvez informer les résidents concernés par courriel de cette demande publique.",
        "comments" => "Commentaires",
        "assigned_to" => "Affecté à",
        "assign_providers" => "Affecter fournisseurs",
        "assign_managers" => "Affectez des gestionnaires",
        'assigned_service_providers' => "Fournisseurs de services assignés",
        'assigned_property_managers' => "Gestionnaires immobiliers désignés",
        "notify" => "Avertissez",
        "public_legend" => "Définissez cette option pour rendre la demande visible à tous les voisins résidents.",
        "conversation" => "Conversation",
        'conversation_created' => "Création d'un commentaire de conversation",
        'internal_notice_saved' => "Avis interne sauvegardé",
        'internal_notice_updated' => "Avis interne mis à jour",
        'internal_notice_deleted' => "Avis interne supprimé",
        "open_conversation" => "Ouvrez",
        "other_recipients" => "Autres bénéficiaires",
        "recipients" => "Destinataires",
        "images" => "Photos et documents",
        "no_images_message" => "Aucun fichier téléchargé",
        "request_details" => "Demande de détails",
        "internal_notices" => "Avis internes",
        "status_changed" => "Le statut a changé",
        "priority_changed" => "Priorité modifiée",
        'assignment' => 'Affectation de gestionnaires/fournisseurs de services',        
        'active_reminder_switcher' => 'Rappel',
        'days_left' => "Combien de jours avant l'envoi de l'email ?",
        'send_person' => 'Quelle personne doit être notifiée ?',
        'sort' => 'Trier',
        'reset_sort' => 'Réinitialiser le tri',
        'creation_date' => 'Date de création',        
        'media' => [
            "added" => "Document ajouté",
            "removed" => "Suppression du support",
            "deleted" => "Médias supprimés",
            "delete" => "Supprimer",
        ],
        'priority' => [
            "label" => "Priorité",
            "urgent" => "Urgent",
            "low" => "Faible",
            "normal" => "Normal",
        ],
        'internal_priority' => [
            "label" => "Priorité interne",
            "urgent" => "Urgent",
            "low" => "Faible",
            "normal" => "Normal",
        ],
        'defect_location' => [
            "label" => "Localisation des défauts",
            "apartment" => "Appartement",
            "building" => "Bâtiment",
            "environment" => "Environnement",
        ],
        'qualification' => [
            "label" => "Qualification",
            "none" => "Aucune",
            "optical" => "Optique",
            "sia" => "Sia",
            "2_year_warranty" => "Garantie de 2 ans",
            "cost_consequences" => "Conséquences financières",
        ],
        'location' => [
            'house_entrance' => 'Entrée de maison',
            'staircase' => 'Escalier',
            'elevator' => 'Ascenseur',
            'car_park' => 'Parking souterrain',
            'washing' => 'Lavage/séchage',
            'heating' => 'Technologie/Chauffage',
            'electro' => 'Technologie/Électrotechnique',
            'facade' => 'Façade',
            'roof' => 'Toit',
            'other' => 'Autre'
        ],
        'room' => [
            'bath' => 'Salle de bains/WC',
            'shower' => 'Douche/WC',
            'entrance' => 'L\'entrée',
            'passage' => 'Passage',
            'basement' => 'Sous-sol',
            'kitchen' => 'Cuisine',
            'storeroom' => 'Reduite',
            'habitation' => 'Habitation',
            'room1' => 'Chambre 1',
            'room2' => 'Chambre 2',
            'room3' => 'Chambre 3',
            'room4' => 'Chambre 4',
            'all' => 'Tous',
            'other' => 'Autre'
        ],
        'capture_phase' => [
            'other' => 'Autre',
            'construction' => 'Phase de construction',
            'shell' => 'Acceptation de la coquille',
            'preliminary' => 'Acceptation préliminaire',
            'work' => 'Acceptation des travaux',
            'surrender' => 'Rendez-vous',
            'inspection' => 'Acceptation'
        ],
        'payer' => [
            'landlord' => 'Propriétaire',
            'resident' => 'Résident',
            'resident/landlord' => 'Résident/Propriétaire'
        ],
        'status' => [
            "label" => "Statut",
            "received" => "Reçu",
            "assigned" => "Affecté",
            "in_processing" => "En cours de traitement",
            "reactivated" => "Réactivé",
            "done" => "C'est fait",
            "archived" => "Archivé",
            "solved" => "Résolues",
            "pending" => "En attente"
        ],
        'category_options' => [
            "disturbance" => "Perturbation",
            "defect" => "Défaut",
            "other" => "Autre",
            'room' => 'Chambre',
            'range' => 'Gamme',
            'component' => 'Composante',
            'acquisition' => 'Phase d\'acquisition',
            'cost' => 'Incidence sur les coûts',
            'keywords' => 'Mots-clés',
        ],
        'placeholders' => [
            "category" => "Sélectionnez une catégorie",
            "qualification" => "Sélectionnez Compétence",
            "status" => "Sélectionnez le statut",
            "due_date" => "Choisir la date d'échéance",
            "resident" => "Rechercher un résident",           
            "visibility" => "Sélectionner la visibilité",
            "person" => "Rechercher une personne",
        ],
        'mail' => [
            "body" => "Corps",
            "subject" => "Sujet",
            "to" => "A",            
            "notify" => "Envoyer un courriel",            
            "provider" => "Prestataire",
            "manager" => "Directeur",
            "cancel" => "Annuler",
            "send" => "Envoyer",
            "cc" => "CC",
            "bcc" => "BCC",
            "success" => "Envoi réussi du mail de notification",            
        ],
        'user_type' => [
            "label" => "Type",
            "provider" => "Service",
            "manager" => "Directeur",
            'user' => 'Administrator',
        ],
        'visibility' => [
            "label" => "Visibilité",
            "resident" => "Privé",
            "quarter" => "Trimestre",
            "building" => "Bâtiment",
        ],
        'errors' => [
            'not_found' => 'Demande de service introuvable',
            'not_allowed_change_status' => "Vous n'êtes pas autorisé à changer de statut.",
            'provider_not_found' => 'Fournisseur de services introuvable',
            'tag_not_found' => 'Étiquette non trouvée',
            'user_not_found' => 'Utilisateur introuvable',
            'conversation_not_found' => "Conversation non trouvée",
            'statistics_error' => "demander des statistiques d'erreur : ",
            'internal_notice_not_found' => "Avis interne non trouvé",
            'deleted' => "Erreur de demande de service supprimée : ",
        ],                
        'actions' => 'Actions',
        'download_pdf' => [
            'title' => 'Télécharger le PDF',
            'entrepreneur_signature'=> 'Signature entrepreneur',
            'customer_signature'=> 'Signature du client',
            'service_request' => 'Service Request',
            'contact_details' => 'Contact Details',
            'contact_text' => 'Voici les coordonnées du résident/propriétaire actuel du logement.',
        ]
    ],
    'request_category' => [
        "title" => "Catégories de demandes",
        "add" => "Ajouter catégorie",
        "cancel" => "Annuler",
        "required" => "Ce champ est obligatoire",
        "parent" => "Catégorie de parents",
        'errors' => [
            'not_found' => "Catégorie de demande de service introuvable",
            'parent_not_found' => "Catégorie de demande de service parent non trouvée",
            'multiple_level_not_found' => "Les catégories imbriquées à plusieurs niveaux ne sont pas autorisées",
            'used_by_request' => "Catégorie de demande de service utilisée par une demande de service",
        ]
    ],
    'property_manager' => [
        "title" => "Gestionnaires immobiliers",
        "add" => "Ajouter un gestionnaire immobilier",
        "saved" => "Gestionnaire immobilier sauvé",
        "deleted" => "Gestionnaire immobilier supprimé",
        "edit_title" => "Modifier le gestionnaire immobilier",        
        "profession" => "Profession",
        "slogan" => "Slogan",
        "linkedin_url" => "URL Linkedin",
        "xing_url" => "Xing URL",
        "details_card" => "Détails",
        'delete_with_reassign_modal' => [
            "title" => "Supprimer et réaffecter des bâtiments",
            "description" => "Le gestionnaire immobilier sélectionné est lié aux biens immobiliers. Vous pouvez affecter les propriétés à une autre personne. Pour ce faire, sélectionnez un gestionnaire immobilier dans la liste..",
            "search_title" => "Rechercher un gestionnaire immobilier",
        ],
        "delete_without_reassign" => "Supprimer",
        "profile_card" => "Profil de l'utilisateur",
        "social_card" => "Médias sociaux",
        "assign_type" => "Type",        
        'errors' => [
            'not_found' => "Gestionnaire immobilier introuvable",
            'create' => "Le gestionnaire immobilier crée l'erreur : ",
            'update' => "Erreur de mise à jour du gestionnaire immobilier : ",
            'quarter_not_found' => "Trimestre non trouvé",
            'building_not_found' => "Bâtiment non trouvé",
            'building_already_assign' => "Immeuble déjà assigné jusqu'au quart",
            'building_assign_deleted_property_manager' => "Vous ne pouvez pas affecter de bâtiments à un gestionnaire immobilier supprimé.",
            'deleted' => "Le gestionnaire immobilier a supprimé l'erreur : ",
        ],
    ],
    'house_owner' => [
        "title" => "Propriétaires de maison",
        "add" => "Ajouter un propriétaire",
        "saved" => "Propriétaire de maison sauvé",
        "deleted" => "Propriétaire de la maison supprimée",
        "edit_title" => "Modifier le propriétaire de la maison",
        "first_name" => "Prénom",
        "last_name" => "Nom de famille",
        "profession" => "Profession",
        "slogan" => "Slogan",
        "linkedin_url" => "URL Linkedin",
        "xing_url" => "Xing URL",
        "password" => "Mot de passe",
        "confirm_password" => "Confirmez le mot de passe",
        "building_card" => "Affecter des bâtiments",
        "details_card" => "Détails",
        "no_buildings" => "Il n'y a pas de bâtiments assignés",
        "add_buildings" => "Ajouter des bâtiments",
        "buildings_search" => "Recherche de bâtiments",
        "quarters" => "Trimestres",
        'delete_with_reassign_modal' => [
            "title" => "Supprimer et réaffecter des bâtiments",
            "description" => "Le propriétaire de la maison sélectionnée est lié aux propriétés. Vous pouvez affecter les propriétés à une autre personne. Pour ce faire, sélectionnez un propriétaire dans la liste.",
            "search_title" => "Recherche Propriétaire de maison",
        ],
        "delete_without_reassign" => "Supprimer",
        "profile_card" => "Profil de l'utilisateur",
        "social_card" => "Médias sociaux",
        "assign_type" => "Type",
        "building_already_assigned" => "Le bâtiment est déjà à l'intérieur sur un quart",
        'errors' => [
            'not_found' => "Propriétaire de maison non trouvé",
            'create' => "Le propriétaire de la maison crée l'erreur : ",
            'update' => "Erreur de mise à jour du propriétaire de la maison : ",
            'quarter_not_found' => "Trimestre non trouvé",
            'building_not_found' => "Bâtiment non trouvé",
            'building_already_assign' => "Immeuble déjà assigné jusqu'au quart",
            'building_assign_deleted_house_owner' => "Vous ne pouvez pas affecter de bâtiments à un propriétaire de maison supprimé.",
            'deleted' => "Le propriétaire de la maison a supprimé l'erreur : ",
        ],
    ],
    'listing' => [
        "title" => "Produits",
        "add" => "Ajouter un produit",
        "edit_title" => "Modifier le produit",                
        "listing_title" => "Titre",
        "published_at" => "Publié",
        "publish" => "Publier",
        "unpublish" => "Non publié",
        "likes" => "Aime",
        "saved" => "Prodotto salvato",
        "deleted" => "Prodotto cancellato",
        "comments" => "Commentaires",
        "contact" => "Contact",
        "price" => "Prix",
        'comment_created' => "Commentaire créé avec succès",
        'errors' => [
            'not_found' => "Produit non trouvé",
            'deleted' => "Errore prodotto cancellato: ",
        ],
        'type' => [
            'label' => 'Type',
            'sell' => 'Vendre',
            'lend' => 'Prêter',
            'service' => 'Service après-vente',
            'giveaway' => 'Donner',
        ],
        'status' => [
            'label' => 'Statut',
            'published' => 'Publié',
            'unpublished' => 'Non publié',
        ],
        'visibility' => [
            'label' => 'Visibilité',
            'address' => 'Adresse',
            'quarter' => 'Trimestre',
            'all' => 'Tous',
        ],
    ],
    'template' => [
        'saved' => 'Ajouter',
        'deleted' => 'Gabarit supprimé',
        'add' => 'Ajouter',
        'title' => 'Modèles',
        'subject' => 'Sujet',
        'body' => 'Corps',
        'category' => 'Catégorie',
        'tags' => 'Étiquettes',
        'placeholders' => [
            'category' => 'Choisir une catégorie',
        ],
        'errors' => [
            'not_found' => "Modèle non trouvé",
        ]
    ],
    'cleanify' => [
        "page_title" => "Nettoyer la demande",        
        "address" => "Adresse",
        "save" => "Envoyer demande",
        "success" => "Nettoyer la demande envoyée avec succès",
        "terms_and_conditions" => "Accepter les conditions générales de vente",
        "terms_text" => "Texte des termes ici, texte descriptif"
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
        'title' => 'Intitulé',
        'in_format' => 'En format',
        'rows' => 'Lignes',
        'cols' => 'Colonnes',
        'color' => [
            'dark_red' => 'Rouge foncé',
            'violet' => 'Violet',
            'red' => 'Rouge',
            'fresh_pink' => 'Rose frais',
            'navy_blue' => 'Bleu marine',
            'blue' => 'Bleu',
            'blue_lake' => ' Blue Lake',
            'blue_green' => 'Vert bleu',
            'green' => 'Vert',
            'olive' => 'Olive',
            'light_green' => 'Vert clair',
            'orange' => 'Orange',
            'gray' => 'Gris',
            'silver' => 'Argent',
            'black' => 'Noir',
            'white' => 'Blanc',
        ]
    ],
];