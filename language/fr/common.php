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

	'GROUPEMAILER_CONFIRM_DELETE'		=> 'Voulez-vous vraiment supprimer cette campagne ainsi que sa file d\'attente ?',
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
	'GROUPEMAILER_DELETE_LOCKED'	=> 'Cette campagne a déjà été envoyée (ou est en cours d\'envoi) : elle ne peut plus être supprimée, afin de conserver l\'historique des envois.',
	'GROUPEMAILER_RETRY_ERRORS'	=> 'Relancer les erreurs',
	'GROUPEMAILER_ERROR_MESSAGE'	=> 'Détail de l\'erreur',
	'GROUPEMAILER_RETRY_DONE'		=> 'Les envois en erreur ont été remis en file d\'attente, ils repartiront au prochain passage du cron.',

	'GROUPEMAILER_SENDER_LEGEND'	=> 'Expéditeur et mise en forme',
	'GROUPEMAILER_SENDER_EXPLAIN'	=> 'Laissez vide pour utiliser le nom du site et l\'adresse de contact configurés dans les réglages généraux du forum.',
	'GROUPEMAILER_FROM_NAME'		=> 'Nom de l\'expéditeur',
	'GROUPEMAILER_FROM_EMAIL'		=> 'Adresse email de l\'expéditeur',
	'GROUPEMAILER_HEADER'			=> 'En-tête (ajouté avant chaque message)',
	'GROUPEMAILER_HEADER_EXPLAIN'	=> 'Texte optionnel inséré au début de chaque email envoyé, avant le contenu de la campagne.',
	'GROUPEMAILER_FOOTER'			=> 'Pied de page (ajouté après chaque message)',
	'GROUPEMAILER_FOOTER_EXPLAIN'	=> 'Texte optionnel inséré à la fin de chaque email envoyé, après le contenu de la campagne (ex. mentions légales, lien de désabonnement).',
	'GROUPEMAILER_TOTAL_SENT_LABEL'	=> 'Nombre total d\'emails envoyés',

	'GROUPEMAILER_REQUIRE_CONFIRM'	=> 'Confirmation de lecture',
	'GROUPEMAILER_REQUIRE_CONFIRM_LABEL'	=> 'Demander au destinataire de confirmer la réception du message',
	'GROUPEMAILER_REQUIRE_CONFIRM_EXPLAIN'	=> 'Un lien unique et personnel est ajouté à la fin de chaque email. En cliquant dessus, le destinataire confirme avoir reçu et lu le message (aucune connexion au forum requise). Vous pourrez ensuite relancer uniquement ceux qui n\'ont pas confirmé.',
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
	'GROUPEMAILER_CONFIRM_OK_TITLE'	=> 'Merci !',
	'GROUPEMAILER_CONFIRM_OK'		=> 'Bonjour %s, votre confirmation de lecture a bien été enregistrée. Vous ne recevrez pas de rappel pour ce message.',
	'GROUPEMAILER_CONFIRM_ALREADY_TITLE'	=> 'Déjà confirmé',
	'GROUPEMAILER_CONFIRM_ALREADY'	=> 'Vous aviez déjà confirmé la lecture de ce message le %s. Aucune action supplémentaire n\'est nécessaire.',
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

	'GROUPEMAILER_EMAIL_MODE'		=> 'Contenu de l\'email',
	'GROUPEMAILER_EMAIL_MODE_NOTICE'	=> 'Notification seule — le message est consultable en ligne (recommandé)',
	'GROUPEMAILER_EMAIL_MODE_FULL'	=> 'Message complet directement dans l\'email',
	'GROUPEMAILER_EMAIL_MODE_EXPLAIN'	=> 'En mode notification, l\'email annonce simplement qu\'un message attend le membre et ne contient que le lien personnel de consultation : le suivi de lecture est alors fiable, puisque le contenu ne peut être lu qu\'en ouvrant la page. Le mode « message complet » envoie tout le texte par email ; si celui-ci contient déjà des liens, beaucoup de membres liront le message sans jamais ouvrir le lien de consultation, et n\'apparaîtront donc pas comme lecteurs. Le mode notification active obligatoirement la confirmation de lecture.',

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
));
