<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_GROUPEMAILER_TITLE'		=> 'Group Mailer',
	'ACP_GROUPEMAILER_CAMPAIGNS'	=> 'Campagnes',
	'ACP_GROUPEMAILER_SETTINGS'	=> 'Réglages',
	'ACP_GROUPEMAILER_HISTORY'		=> 'Historique',

	'GROUPEMAILER_CAMPAIGN_LIST'		=> 'Campagnes d\'envoi',
	'GROUPEMAILER_ADD_CAMPAIGN'		=> 'Créer une campagne',
	'GROUPEMAILER_EDIT_CAMPAIGN'		=> 'Modifier la campagne',
	'GROUPEMAILER_NO_CAMPAIGNS'		=> 'Aucune campagne n\'a encore été créée.',

	'GROUPEMAILER_TITLE'			=> 'Titre (usage interne)',
	'GROUPEMAILER_TITLE_EXPLAIN'	=> 'Ce titre n\'est visible que dans l\'ACP, pas par les destinataires.',
	'GROUPEMAILER_SUBJECT'			=> 'Objet du message',
	'GROUPEMAILER_BODY'			=> 'Contenu du message',
	'GROUPEMAILER_BODY_EXPLAIN'	=> 'Utilisez {USERNAME} pour insérer automatiquement le pseudo du destinataire.',
	'GROUPEMAILER_TARGET_GROUPS'	=> 'Groupes destinataires',
	'GROUPEMAILER_TARGET_GROUPS_EXPLAIN'	=> 'Sélectionnez un ou plusieurs groupes. Pour envoyer à tous les membres, sélectionnez le groupe « Utilisateurs enregistrés ».',
	'GROUPEMAILER_RATE_COUNT'		=> 'Nombre de mails par envoi',
	'GROUPEMAILER_RATE_INTERVAL'	=> 'Intervalle entre chaque envoi (minutes)',
	'GROUPEMAILER_RATE_VALUE'		=> '%1$d mails / %2$d min',

	'GROUPEMAILER_STATUS'			=> 'Statut',
	'GROUPEMAILER_STATUS_DRAFT'	=> 'Brouillon',
	'GROUPEMAILER_STATUS_RUNNING'	=> 'En cours d\'envoi',
	'GROUPEMAILER_STATUS_PAUSED'	=> 'En pause',
	'GROUPEMAILER_STATUS_COMPLETED'	=> 'Terminée',
	'GROUPEMAILER_STATUS_ERROR'	=> 'Erreur',
	'GROUPEMAILER_STATUS_SENT'	=> 'Envoyé',

	'GROUPEMAILER_RECIPIENTS'		=> 'Destinataires',
	'GROUPEMAILER_SENT'			=> 'Envoyés',
	'GROUPEMAILER_ERRORS'			=> 'Erreurs',
	'GROUPEMAILER_CREATED'			=> 'Créée le',
	'GROUPEMAILER_ACTIONS'			=> 'Actions',

	'GROUPEMAILER_START'			=> 'Démarrer',
	'GROUPEMAILER_PAUSE'			=> 'Mettre en pause',
	'GROUPEMAILER_RESUME'			=> 'Reprendre',
	'GROUPEMAILER_EDIT'			=> 'Modifier',
	'GROUPEMAILER_DELETE'			=> 'Supprimer',

	'GROUPEMAILER_CAMPAIGN_SAVED'		=> 'Campagne enregistrée avec succès.',
	'GROUPEMAILER_CAMPAIGN_DELETED'	=> 'Campagne supprimée.',
	'GROUPEMAILER_CAMPAIGN_PAUSED'		=> 'Campagne mise en pause. Les envois reprendront quand vous cliquerez sur « Reprendre ».',
	'GROUPEMAILER_CAMPAIGN_RESUMED'	=> 'Campagne relancée, l\'envoi reprendra automatiquement.',
	'GROUPEMAILER_CAMPAIGN_NOT_FOUND'	=> 'Cette campagne est introuvable ou ne peut plus être modifiée.',
	'GROUPEMAILER_FORM_INCOMPLETE'		=> 'Merci de renseigner le titre, l\'objet, le message et au moins un groupe destinataire.',
	'GROUPEMAILER_NO_RECIPIENTS'		=> 'Aucun destinataire trouvé pour les groupes sélectionnés (adresse email manquante ou groupe vide).',
	'GROUPEMAILER_CAMPAIGN_STARTED'	=> 'Campagne démarrée : %d destinataire(s) placé(s) en file d\'attente. L\'envoi se fera progressivement selon la cadence définie.',

	'GROUPEMAILER_SETTINGS_EXPLAIN'	=> 'Ces valeurs sont proposées par défaut lors de la création d\'une nouvelle campagne ; chaque campagne peut ensuite définir sa propre cadence.',
	'GROUPEMAILER_DEFAULT_RATE_COUNT'		=> 'Nombre de mails par défaut',
	'GROUPEMAILER_DEFAULT_RATE_INTERVAL'	=> 'Intervalle par défaut (minutes)',
	'GROUPEMAILER_RATE_WARNING'		=> 'Attention : vérifiez la limite d\'envoi horaire imposée par votre hébergeur (ex. 180 mails/heure chez certains hébergeurs mutualisés) et réglez la cadence en conséquence.',

	'GROUPEMAILER_RECIPIENT'		=> 'Destinataire',
	'GROUPEMAILER_SENT_DATE'		=> 'Date d\'envoi',
	'GROUPEMAILER_NO_HISTORY'		=> 'Aucun email n\'a encore été envoyé.',
	'GROUPEMAILER_RETRY_ERRORS'	=> 'Relancer les erreurs',
	'GROUPEMAILER_RETRY_DONE'		=> 'Les envois en erreur ont été remis en file d\'attente, ils repartiront au prochain passage du cron.',

	'GROUPEMAILER_SENDER_LEGEND'	=> 'Expéditeur et mise en forme',
	'GROUPEMAILER_SENDER_EXPLAIN'	=> 'L\'adresse d\'expéditeur doit impérativement être une adresse réellement créée sur le domaine du forum, par exemple contact@monforum.com. Si elle appartient à un autre domaine, ou si le serveur retombe sur son adresse par défaut faute de réglage, les messages seront classés en indésirable puis écartés en silence par les grandes messageries — le journal de l\'hébergeur les affichera pourtant comme « acceptés ». Laissez vide pour reprendre le nom du site et l\'adresse de contact du forum, à condition que celle-ci remplisse la même exigence.',
	'GROUPEMAILER_FROM_NAME'		=> 'Nom de l\'expéditeur',
	'GROUPEMAILER_FROM_EMAIL'		=> 'Adresse email de l\'expéditeur',
	'GROUPEMAILER_HEADER'			=> 'En-tête (ajouté avant chaque message)',
	'GROUPEMAILER_HEADER_EXPLAIN'	=> 'Texte optionnel inséré au début de chaque email envoyé, avant le contenu de la campagne.',
	'GROUPEMAILER_FOOTER'			=> 'Pied de page (ajouté après chaque message)',
	'GROUPEMAILER_FOOTER_EXPLAIN'	=> 'Texte optionnel inséré à la fin de chaque email envoyé, après le contenu de la campagne (ex. mentions légales, lien de désabonnement).',
	'GROUPEMAILER_TOTAL_SENT_LABEL'	=> 'Nombre total d\'emails envoyés',

	'GROUPEMAILER_CONFIRMED'		=> 'Confirmés',
	'GROUPEMAILER_CONFIRMED_YES'	=> 'Confirmé',
	'GROUPEMAILER_CONFIRMED_NO'	=> 'Non confirmé',
	'GROUPEMAILER_TOTAL_CONFIRMED_LABEL'	=> 'Confirmations de lecture',

	'GROUPEMAILER_RELANCE'			=> 'Relancer les non-lecteurs',
	'GROUPEMAILER_RELANCE_TITLE'	=> '%s (relance)',
	'GROUPEMAILER_RELANCE_OF'		=> 'Relance — %s',
	'GROUPEMAILER_RELANCE_CREATED'	=> 'Brouillon de relance créé : %d destinataire(s) n\'ont pas confirmé la lecture. Vérifiez le message puis cliquez sur « Démarrer ».',
	'GROUPEMAILER_RELANCE_TARGET_EXPLAIN'	=> 'Destinataires calculés automatiquement au démarrage : uniquement les membres ayant reçu la campagne d\'origine sans confirmer la lecture.',
	'GROUPEMAILER_NO_UNCONFIRMED'	=> 'Aucun destinataire à relancer : tous ceux qui ont reçu le message ont déjà confirmé la lecture.',

	'GROUPEMAILER_MAIL_CONFIRM_TEXT'	=> 'Vous pouvez consulter ce message en ligne via le lien personnel ci-dessous. Son ouverture confirme automatiquement que vous l\'avez bien reçu, aucune autre action n\'est nécessaire :',
	'GROUPEMAILER_CONFIRM_PAGE_TITLE'	=> 'Message du forum',
	'GROUPEMAILER_CONFIRM_INVALID_TITLE'	=> 'Lien invalide',
	'GROUPEMAILER_CONFIRM_INVALID'	=> 'Ce lien de confirmation est invalide ou a expiré. Si vous pensez qu\'il s\'agit d\'une erreur, contactez l\'administrateur du forum.',

	'GROUPEMAILER_DETAILS'			=> 'Suivi',
	'GROUPEMAILER_DETAILS_LEGEND'	=> 'Suivi de la campagne',
	'GROUPEMAILER_DETAILS_SUMMARY'	=> 'Récapitulatif',
	'GROUPEMAILER_DETAILS_TABLE'	=> 'Détail par destinataire',
	'GROUPEMAILER_STATUS_PENDING'	=> 'En attente',
	'GROUPEMAILER_ALREADY_QUEUED'	=> 'Déjà en file d\'attente',
	'GROUPEMAILER_NO_RECIPIENTS_YET'	=> 'Aucun destinataire dans la file d\'attente de cette campagne.',
	'GROUPEMAILER_BACK_TO_LIST'	=> 'Retour à la liste des campagnes',

	'GROUPEMAILER_RESEND_LEGEND'	=> 'Renvoyer le message',
	'GROUPEMAILER_RESEND_EXPLAIN'	=> 'Le renvoi remet les destinataires concernés en file d\'attente : le message repart progressivement selon la cadence de la campagne, au prochain passage du cron.',
	'GROUPEMAILER_RESEND_ALL'		=> 'Renvoyer à tous les destinataires',
	'GROUPEMAILER_RESEND_UNCONFIRMED'	=> 'Renvoyer aux non-confirmés',
	'GROUPEMAILER_RESEND_ONE'		=> 'Renvoyer',
	'GROUPEMAILER_RESEND_DONE'		=> '%d destinataire(s) remis en file d\'attente. L\'envoi reprendra au prochain passage du cron.',
	'GROUPEMAILER_RESEND_NOBODY'	=> 'Aucun destinataire ne correspond à ce renvoi.',

	'GROUPEMAILER_EXCLUDE_INACTIVE'	=> 'Comptes inactifs',
	'GROUPEMAILER_EXCLUDE_INACTIVE_LABEL'	=> 'Ne pas envoyer aux comptes inactifs',
	'GROUPEMAILER_INACTIVE_DAYS'	=> 'Seuil d\'inactivité (jours)',
	'GROUPEMAILER_EXCLUDE_INACTIVE_EXPLAIN'	=> 'Exclut les membres qui ne se sont pas connectés depuis plus de X jours. Les inscriptions récentes sont conservées même sans première visite. Les comptes non activés et les robots sont de toute façon toujours exclus.',
	'GROUPEMAILER_RESPECT_MASSEMAIL'		=> 'Emails de masse',
	'GROUPEMAILER_RESPECT_MASSEMAIL_LABEL'	=> 'Respecter le refus des emails de masse',
	'GROUPEMAILER_RESPECT_MASSEMAIL_EXPLAIN'	=> 'Exclut les membres ayant décoché « Recevoir les emails de masse » dans leur profil. C\'est le comportement de l\'outil natif de phpBB ; à ne désactiver que pour un message réellement indispensable (sécurité, fermeture du forum...).',
	'GROUPEMAILER_EXCLUDED_INFO'	=> '%d compte(s) exclu(s) par les filtres de la campagne.',
	'GROUPEMAILER_DEFAULT_INACTIVE_DAYS'	=> 'Seuil d\'inactivité par défaut (jours)',
	'GROUPEMAILER_DEFAULT_INACTIVE_DAYS_EXPLAIN'	=> 'Valeur proposée à la création d\'une campagne lorsque l\'exclusion des comptes inactifs est activée.',

	'GROUPEMAILER_EXCLUDE_DEACTIVATED'	=> 'Comptes désactivés',
	'GROUPEMAILER_EXCLUDE_DEACTIVATED_LABEL'	=> 'Ne pas envoyer aux comptes désactivés par un administrateur',
	'GROUPEMAILER_EXCLUDE_DEACTIVATED_EXPLAIN'	=> 'Comptes marqués « Inactif » dans phpBB : désactivés par un administrateur ou jamais activés après inscription. À décocher uniquement pour un message les invitant à réactiver leur compte. Les robots et le compte anonyme restent exclus dans tous les cas.',
	'GROUPEMAILER_EXCLUDED_BREAKDOWN'	=> 'Détail : %1$d compte(s) désactivé(s), %2$d compte(s) dormant(s), %3$d refus des emails de masse.',

	'GROUPEMAILER_EXCLUDED_CONFIRMED'	=> '%d membre(s) écarté(s) de cette relance : ils avaient confirmé la lecture via le lien d\'un message précédent.',

	'GROUPEMAILER_READ_RECORDED'	=> 'Bonjour %1$s — la bonne réception de ce message a été enregistrée le %2$s. Vous ne recevrez pas de rappel à son sujet.',
	'GROUPEMAILER_READ_ALREADY'	=> 'La bonne réception de ce message avait déjà été enregistrée le %s.',


	'GROUPEMAILER_MAIL_HELLO'		=> 'Bonjour %s,',
	'GROUPEMAILER_MAIL_NOTICE_INTRO'	=> 'Vous recevez cet email parce que vous êtes membre du forum %s.',
	'GROUPEMAILER_MAIL_NOTICE_SUBJECT'	=> 'Un message vous a été adressé : « %s »',
	'GROUPEMAILER_MAIL_NOTICE_LINK'	=> 'Pour le consulter, cliquez sur le lien personnel ci-dessous. Son ouverture confirme automatiquement la bonne réception du message, aucune autre action n\'est nécessaire :',
	'GROUPEMAILER_MAIL_UNSUBSCRIBE'	=> 'Pour ne plus recevoir ce type d\'email, connectez-vous à votre compte puis rendez-vous dans « Panneau de l\'utilisateur » → « Préférences » afin de désactiver vous-même la réception des emails de l\'administration :',

	'GROUPEMAILER_ADD_UNSUBSCRIBE'	=> 'Mention de désabonnement',
	'GROUPEMAILER_ADD_UNSUBSCRIBE_LABEL'	=> 'Ajouter automatiquement la mention de désabonnement à chaque email',
	'GROUPEMAILER_ADD_UNSUBSCRIBE_EXPLAIN'	=> 'Ajoute en fin de message la marche à suivre pour ne plus recevoir ce type d\'email, accompagnée du lien vers les préférences du compte. Le membre reste maître de son choix depuis son profil : à conserver activé, c\'est une bonne pratique attendue pour tout envoi groupé.',

	'GROUPEMAILER_SEND_NOW'			=> 'Envoyer un lot maintenant',
	'GROUPEMAILER_SEND_NOW_LEGEND'	=> 'Envoi immédiat',
	'GROUPEMAILER_SEND_NOW_EXPLAIN'	=> 'Envoie tout de suite le prochain lot, sans attendre le passage du cron ni respecter l\'intervalle de cadence. Pratique pour tester une campagne ou pour faire avancer un envoi sur un forum peu fréquenté. Attention à ne pas enchaîner les clics au-delà de la limite horaire de votre hébergeur.',
	'GROUPEMAILER_SEND_NOW_DONE'	=> 'Envoi immédiat terminé : %1$d message(s) envoyé(s), %2$d erreur(s).',
	'GROUPEMAILER_SEND_NOW_ERROR'	=> 'Dernière erreur rencontrée : %s',
	'GROUPEMAILER_SEND_NOW_NOT_RUNNING'	=> 'Cette campagne n\'est pas en cours d\'envoi : démarrez-la ou reprenez-la avant de forcer un envoi.',

	'ACP_GROUPEMAILER_DIAG'		=> 'Diagnostic',
	'GROUPEMAILER_DIAG_LEGEND'	=> 'État réel de l\'extension',
	'GROUPEMAILER_DIAG_EXPLAIN'	=> 'Cette page interroge directement la base de données et le conteneur de services de phpBB. Elle sert à identifier pourquoi un envoi ne démarre pas.',
	'GROUPEMAILER_DIAG_CHECK'	=> 'Vérification',
	'GROUPEMAILER_DIAG_RESULT'	=> 'Résultat',
	'GROUPEMAILER_DIAG_YES'		=> 'Oui',

	'GROUPEMAILER_DIAG_SCHEMA'	=> 'Colonnes en base',
	'GROUPEMAILER_DIAG_SCHEMA_OK'	=> 'Toutes les colonnes attendues sont présentes : les migrations ont bien été exécutées.',
	'GROUPEMAILER_DIAG_SCHEMA_MISSING'	=> 'Colonnes manquantes : %s. Les migrations n\'ont pas toutes été exécutées : désactivez puis réactivez l\'extension (sans « Supprimer les données »).',

	'GROUPEMAILER_DIAG_SERVICE'	=> 'Service de la tâche d\'envoi',
	'GROUPEMAILER_DIAG_SERVICE_OK'	=> 'Le service est correctement construit par phpBB.',

	'GROUPEMAILER_DIAG_TASK_NAME'	=> 'Nom de la tâche cron',
	'GROUPEMAILER_DIAG_TASK_NAME_EMPTY'	=> 'Nom vide : la déclaration « set_name » manque dans config/services.yml, la tâche est donc introuvable pour phpBB.',

	'GROUPEMAILER_DIAG_TASK_READY'	=> 'Tâche prête à s\'exécuter',
	'GROUPEMAILER_DIAG_TASK_NOT_READY'	=> 'Non : aucune campagne n\'est actuellement en cours d\'envoi.',

	'GROUPEMAILER_DIAG_CRON_FOUND'	=> 'Tâche reconnue par le gestionnaire de cron',
	'GROUPEMAILER_DIAG_CRON_NOT_FOUND'	=> 'Introuvable : phpBB ne voit pas cette tâche, l\'envoi automatique ne peut pas se déclencher.',

	'GROUPEMAILER_DIAG_SYSTEM_CRON'	=> 'Réglage phpBB « cron système »',
	'GROUPEMAILER_DIAG_SYSTEM_CRON_ON'	=> 'Activé : phpBB ne déclenche plus aucune tâche lors des visites du forum. Désactivez-le dans ACP → Général → Réglages du serveur, ou créez une tâche planifiée côté hébergeur.',
	'GROUPEMAILER_DIAG_SYSTEM_CRON_OFF'	=> 'Désactivé : les tâches se déclenchent à la navigation sur le forum, c\'est le fonctionnement attendu.',

	'GROUPEMAILER_DIAG_EMAIL_ENABLE'	=> 'Envoi d\'emails activé dans phpBB',
	'GROUPEMAILER_DIAG_EMAIL_DISABLED'	=> 'Désactivé : aucun email ne peut partir. Activez-le dans ACP → Général → Réglages des emails.',

	'GROUPEMAILER_DIAG_SENDER'	=> 'Adresse d\'expéditeur utilisée',
	'GROUPEMAILER_DIAG_QUEUE'	=> 'File d\'attente (toutes campagnes)',
	'GROUPEMAILER_DIAG_QUEUE_EMPTY'	=> 'Vide',
	'GROUPEMAILER_DIAG_RUNNING'	=> 'Campagnes en cours d\'envoi',

	'GROUPEMAILER_DIAG_MANUAL'	=> 'Déclenchement manuel du cron',
	'GROUPEMAILER_DIAG_MANUAL_EXPLAIN'	=> 'Ouvrir cette adresse dans le navigateur exécute la tâche directement. Une page presque vide (un point blanc) est le comportement normal ; toute erreur affichée indique la cause du blocage.',
	'GROUPEMAILER_DIAG_LINK'	=> 'Lien de consultation généré',

	'GROUPEMAILER_SEND_MODE'		=> 'Contenu de l\'email et suivi',
	'GROUPEMAILER_SEND_MODE_NOTICE'	=> 'Notification seule, avec suivi de lecture — le message est consultable en ligne (recommandé)',
	'GROUPEMAILER_SEND_MODE_FULL_TRACK'	=> 'Message complet dans l\'email, avec suivi de lecture',
	'GROUPEMAILER_SEND_MODE_FULL'	=> 'Message complet dans l\'email, sans suivi de lecture',
	'GROUPEMAILER_SEND_MODE_EXPLAIN'	=> 'En mode notification, l\'email annonce simplement qu\'un message attend le membre et ne contient que son lien personnel : le contenu ne pouvant être lu qu\'en ouvrant la page, le suivi est fiable. Ce mode implique nécessairement le suivi de lecture, puisque le lien est le seul accès au message. Avec le message complet et suivi, un lien de consultation est ajouté à la fin de l\'email ; attention, si votre texte contient déjà des liens, beaucoup de membres liront le message sans jamais ouvrir ce lien et seront comptés comme non-lecteurs. Sans suivi, aucun lien n\'est ajouté et la relance des non-lecteurs n\'est pas disponible.',

	'GROUPEMAILER_TEST'			=> 'Tester',
	'GROUPEMAILER_TEST_SUBJECT_PREFIX'	=> '[TEST]',
	'GROUPEMAILER_TEST_SENT'	=> 'Exemplaire de test envoyé à %s. Ni la file d\'attente ni les compteurs de la campagne n\'ont été modifiés, et le lien de consultation du test est volontairement inopérant.',
	'GROUPEMAILER_TEST_FAILED'	=> 'L\'envoi de test a échoué : %s',
	'GROUPEMAILER_TEST_NO_EMAIL'	=> 'Aucune adresse email n\'est associée à votre compte : impossible d\'envoyer un test.',

	'GROUPEMAILER_READ_FROM'		=> 'Message du forum %s',
	'GROUPEMAILER_READ_SENT_ON'	=> 'envoyé le %s',
	'GROUPEMAILER_READ_GO_BOARD'	=> 'Aller sur le forum',

	'GROUPEMAILER_STATUS_CANCELLED'	=> 'Arrêtée',
	'GROUPEMAILER_CANCEL'		=> 'Arrêter définitivement',
	'GROUPEMAILER_CONFIRM_CANCEL'	=> 'Arrêter définitivement cette campagne ? Les %d destinataire(s) encore en attente ne recevront pas le message. Les envois déjà effectués sont conservés dans l\'historique. Cette action est irréversible : pour une interruption temporaire, utilisez plutôt « Mettre en pause ».',
	'GROUPEMAILER_CANCEL_DONE'	=> 'Campagne arrêtée définitivement. %d destinataire(s) en attente ont été écarté(s).',
	'GROUPEMAILER_CANCEL_IMPOSSIBLE'	=> 'Seule une campagne en cours d\'envoi ou en pause peut être arrêtée.',
	'GROUPEMAILER_CONFIRM_DELETE'	=> 'Supprimer définitivement la campagne « %s » ? Aucun message n\'a été envoyé, rien ne sera perdu.',
	'GROUPEMAILER_CONFIRM_DELETE_SENT'	=> 'Supprimer définitivement la campagne « %1$s » ? Les %2$d envoi(s) déjà effectué(s) disparaîtront également de l\'historique et du suivi, ainsi que les confirmations de lecture associées. Cette action est irréversible.',
	'GROUPEMAILER_CONFIRM_DELETE_CHILDREN'	=> 'Attention : %d relance(s) encore à l\'état de brouillon calculent leurs destinataires à partir de cette campagne. Leur suppression les rendrait inutilisables.',
	'GROUPEMAILER_TOTAL_ROWS'	=> '%d entrée(s) au total',
	'GROUPEMAILER_LIST_UNSUBSCRIBE'	=> 'En-tête List-Unsubscribe',
	'GROUPEMAILER_LIST_UNSUBSCRIBE_LABEL'	=> 'Ajouter l\'en-tête technique de désabonnement',
	'GROUPEMAILER_LIST_UNSUBSCRIBE_EXPLAIN'	=> 'Ajoute l\'en-tête « List-Unsubscribe » attendu par Gmail et Yahoo des expéditeurs en nombre : le destinataire dispose alors d\'un lien de désabonnement directement dans l\'interface de sa messagerie, ce qui améliore le placement en boîte de réception. Il pointe vers le lien de désabonnement personnel du membre. À conserver activé, sauf si vous constatez un problème de remise propre à votre hébergeur.',
	'GROUPEMAILER_DIAG_MAIL_METHOD'	=> 'Méthode d\'envoi de phpBB',
	'GROUPEMAILER_DIAG_MAIL_PHP'	=> 'Fonction mail() de PHP',
	'GROUPEMAILER_DIAG_MAIL_SMTP'	=> 'SMTP via %s',
	'GROUPEMAILER_DIAG_LIST_UNSUB'	=> 'En-tête List-Unsubscribe',
	'GROUPEMAILER_DIAG_LIST_UNSUB_ON'	=> 'Ajouté aux emails. Si les messages n\'arrivent plus, désactivez-le dans les Réglages.',
	'GROUPEMAILER_DIAG_LIST_UNSUB_OFF'	=> 'Non ajouté.',
	'ACP_GROUPEMAILER_BACKUP'	=> 'Sauvegarde / Restauration',
	'GROUPEMAILER_BACKUP_LEGEND'	=> 'Sauvegarde',
	'GROUPEMAILER_BACKUP_EXPLAIN'	=> 'Enregistre vos campagnes dans un fichier que vous conservez sur votre poste. À faire avant toute suppression de campagne, toute mise à jour de l\'extension ou toute migration de forum.',
	'GROUPEMAILER_BACKUP_CONTENT'	=> 'Contenu actuel',
	'GROUPEMAILER_BACKUP_COUNTS'	=> '%1$d campagne(s), %2$d entrée(s) dans la file d\'attente',
	'GROUPEMAILER_BACKUP_FULL'	=> 'Sauvegarde complète',
	'GROUPEMAILER_BACKUP_LIGHT'	=> 'Campagnes seules',
	'GROUPEMAILER_BACKUP_CHOICE_EXPLAIN'	=> 'La sauvegarde complète inclut les destinataires, les statuts d\'envoi et les confirmations de lecture : c\'est celle à privilégier. L\'option « Campagnes seules » ne conserve que les messages et leurs réglages, pour réutiliser des modèles sur un autre forum sans y transférer de données personnelles.',

	'GROUPEMAILER_RESTORE_LEGEND'	=> 'Restauration',
	'GROUPEMAILER_RESTORE_EXPLAIN'	=> 'Les campagnes du fichier sont recréées sous de nouveaux identifiants : rien n\'est écrasé. Restaurer deux fois le même fichier crée donc des doublons plutôt que d\'effacer quoi que ce soit. Les campagnes restaurées conservent leur statut : une campagne qui était en cours d\'envoi reprendra son envoi, pensez à la mettre en pause si ce n\'est pas souhaité.',
	'GROUPEMAILER_RESTORE_FILE'	=> 'Fichier de sauvegarde',
	'GROUPEMAILER_RESTORE_SUBMIT'	=> 'Restaurer',
	'GROUPEMAILER_IMPORT_NO_FILE'	=> 'Aucun fichier n\'a été transmis, ou son envoi a échoué.',
	'GROUPEMAILER_IMPORT_BAD_FILE'	=> 'Ce fichier n\'est pas une sauvegarde Group Mailer exploitable.',
	'GROUPEMAILER_IMPORT_EMPTY'	=> 'Ce fichier ne contient aucune campagne restaurable.',
	'GROUPEMAILER_IMPORT_DONE'	=> 'Restauration terminée : %1$d campagne(s) et %2$d entrée(s) de file d\'attente ajoutées.',
	'GROUPEMAILER_UNSUBSCRIBED'	=> 'Désabonné',
	'GROUPEMAILER_UNSUB_LINK'	=> 'Ne plus recevoir ces emails',
	'GROUPEMAILER_RESUB_LINK'	=> 'Recevoir de nouveau ces emails',
	'GROUPEMAILER_UNSUB_PAGE_TITLE'	=> 'Préférences d\'emails',
	'GROUPEMAILER_UNSUB_TITLE'	=> 'Vous êtes désabonné',
	'GROUPEMAILER_UNSUB_TEXT'	=> 'Bonjour %1$s, vous ne recevrez plus les emails d\'information du forum %2$s. Votre compte reste actif et vous pouvez continuer à consulter le forum normalement.',
	'GROUPEMAILER_UNSUB_UNDO'	=> 'Vous avez cliqué par erreur ?',
	'GROUPEMAILER_RESUB_TITLE'	=> 'Vous êtes réabonné',
	'GROUPEMAILER_RESUB_TEXT'	=> 'Bonjour %1$s, vous recevrez de nouveau les emails d\'information du forum %2$s.',
	'GROUPEMAILER_RESUB_UNDO'	=> 'Vous préférez finalement ne plus les recevoir ?',
	'GROUPEMAILER_READ_UNSUB_INTRO'	=> 'Vous ne souhaitez plus recevoir ce type de message ?',
	'GROUPEMAILER_READ_ALREADY_UNSUB'	=> 'Vous êtes désabonné des emails d\'information du forum.',
	'GROUPEMAILER_MAIL_UNSUBSCRIBE_LINK'	=> 'Pour ne plus recevoir ce type d\'email, il vous suffit d\'ouvrir le lien ci-dessous. Aucune connexion n\'est nécessaire, et la modification est immédiate :',
	'GROUPEMAILER_BOUNCED'		=> '%d échec(s) consécutif(s)',
	'GROUPEMAILER_BOUNCED_SUMMARY'	=> '%1$d adresse(s) écartée(s) des prochains envois après au moins %2$d échec(s) consécutif(s).',
	'GROUPEMAILER_BOUNCE_RESET'	=> 'Réintégrer',
	'GROUPEMAILER_BOUNCE_RESET_DONE'	=> 'Adresse réintégrée : elle sera de nouveau destinataire des prochaines campagnes.',
	'GROUPEMAILER_BOUNCE_NOT_FOUND'	=> 'Destinataire introuvable.',
	'GROUPEMAILER_EXCLUDED_BOUNCED'	=> '%d adresse(s) écartée(s) pour échecs répétés lors des campagnes précédentes.',
	'GROUPEMAILER_BOUNCE_THRESHOLD'	=> 'Seuil d\'échecs consécutifs',
	'GROUPEMAILER_BOUNCE_THRESHOLD_EXPLAIN'	=> 'Au-delà de ce nombre d\'échecs consécutifs, une adresse est automatiquement écartée des campagnes suivantes : inutile de continuer à écrire à une boîte supprimée, et ces échecs répétés dégradent la réputation d\'expéditeur du forum. Le compteur revient à zéro dès qu\'un envoi aboutit, et la page de suivi permet de réintégrer une adresse manuellement.',
	'GROUPEMAILER_DIAG_TABLE_BOUNCES'	=> 'table des adresses en échec',
	'GROUPEMAILER_BACKUP_SELECT'	=> 'Campagnes à sauvegarder',
	'GROUPEMAILER_BACKUP_SELECT_EXPLAIN'	=> 'Cochez les campagnes voulues. Si vous n\'en cochez aucune, la sauvegarde portera sur l\'ensemble.',
	'GROUPEMAILER_BACKUP_ALL'	=> 'Tout cocher / tout décocher',
	'GROUPEMAILER_BACKUP_FORMAT'	=> 'Contenu du fichier',
	'GROUPEMAILER_BACKUP_SUBMIT'	=> 'Télécharger la sauvegarde',
	'GROUPEMAILER_BACKUP_NOTHING'	=> 'Aucune campagne ne correspond à la sélection.',
	'ACP_GROUPEMAILER_DASHBOARD'	=> 'Tableau de bord',
	'GROUPEMAILER_DASH_OVERVIEW'	=> 'Vue d\'ensemble',
	'GROUPEMAILER_DASH_VOLUME'	=> 'Volume',
	'GROUPEMAILER_DASH_READING'	=> 'Lecture',
	'GROUPEMAILER_DASH_ATTENTION'	=> 'Points de vigilance',
	'GROUPEMAILER_DASH_GLOBAL_RATE'	=> 'Taux global',
	'GROUPEMAILER_DASH_AVERAGE_RATE'	=> 'Moyenne par campagne',
	'GROUPEMAILER_DASH_RATE_EXPLAIN'	=> 'Le taux global rapporte l\'ensemble des confirmations à l\'ensemble des envois ; la moyenne par campagne traite chaque campagne à égalité, quel que soit son nombre de destinataires. Un écart marqué entre les deux signale que vos grandes campagnes sont moins lues que les petites, ou l\'inverse.',
	'GROUPEMAILER_DASH_BOUNCED'	=> 'Adresses écartées pour échecs répétés',
	'GROUPEMAILER_DASH_COMPARE'	=> 'Comparatif des campagnes',
	'GROUPEMAILER_DASH_COMPARE_EXPLAIN'	=> 'Vingt dernières campagnes lancées. Les campagnes sans suivi de lecture n\'ont pas de taux : elles n\'apportent aucune information de lecture.',
	'GROUPEMAILER_DASH_RATE'	=> 'Taux de lecture',

	'GROUPEMAILER_DUPLICATE'	=> 'Dupliquer',
	'GROUPEMAILER_COPY_TITLE'	=> '%s (copie)',
	'GROUPEMAILER_DUPLICATE_DONE'	=> 'Campagne dupliquée : le message et ses réglages ont été repris dans un nouveau brouillon, sans destinataires ni compteurs.',

	'GROUPEMAILER_SCHEDULE'		=> 'Démarrage programmé',
	'GROUPEMAILER_SCHEDULE_EXPLAIN'	=> 'Laissez vide pour un envoi immédiat au clic sur « Démarrer ». Avec une date future, « Démarrer » constitue la liste des destinataires et met la campagne en attente : l\'envoi commencera de lui-même à l\'heure indiquée, au premier passage du cron qui suit. Les destinataires étant figés dès la programmation, un membre inscrit entre-temps ne recevra pas le message.',
	'GROUPEMAILER_STATUS_SCHEDULED'	=> 'Programmée',
	'GROUPEMAILER_CAMPAIGN_SCHEDULED'	=> 'Campagne programmée : %1$d destinataire(s) en attente, envoi prévu le %2$s.',
	'GROUPEMAILER_UNSCHEDULE'	=> 'Annuler la programmation',
	'GROUPEMAILER_UNSCHEDULE_DONE'	=> 'Programmation annulée : la campagne redevient un brouillon et sa file d\'attente a été vidée.',
));
