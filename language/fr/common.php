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
));
