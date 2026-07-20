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
	'ACP_GROUPEMAILER_CAMPAIGNS'	=> 'Campaigns',
	'ACP_GROUPEMAILER_SETTINGS'	=> 'Settings',
	'ACP_GROUPEMAILER_HISTORY'		=> 'History',

	'GROUPEMAILER_CAMPAIGN_LIST'		=> 'Mail campaigns',
	'GROUPEMAILER_ADD_CAMPAIGN'		=> 'Create a campaign',
	'GROUPEMAILER_EDIT_CAMPAIGN'		=> 'Edit campaign',
	'GROUPEMAILER_NO_CAMPAIGNS'		=> 'No campaign has been created yet.',

	'GROUPEMAILER_TITLE'			=> 'Title (internal use)',
	'GROUPEMAILER_TITLE_EXPLAIN'	=> 'This title is only visible in the ACP, not by recipients.',
	'GROUPEMAILER_SUBJECT'			=> 'Message subject',
	'GROUPEMAILER_BODY'			=> 'Message body',
	'GROUPEMAILER_BODY_EXPLAIN'	=> 'Use {USERNAME} to automatically insert the recipient\'s username.',
	'GROUPEMAILER_TARGET_GROUPS'	=> 'Target groups',
	'GROUPEMAILER_TARGET_GROUPS_EXPLAIN'	=> 'Select one or more groups. To send to all members, select the "Registered users" group.',
	'GROUPEMAILER_RATE_COUNT'		=> 'Emails per batch',
	'GROUPEMAILER_RATE_INTERVAL'	=> 'Interval between batches (minutes)',
	'GROUPEMAILER_RATE_VALUE'		=> '%1$d emails / %2$d min',

	'GROUPEMAILER_STATUS'			=> 'Status',
	'GROUPEMAILER_STATUS_DRAFT'	=> 'Draft',
	'GROUPEMAILER_STATUS_RUNNING'	=> 'Sending',
	'GROUPEMAILER_STATUS_PAUSED'	=> 'Paused',
	'GROUPEMAILER_STATUS_COMPLETED'	=> 'Completed',
	'GROUPEMAILER_STATUS_ERROR'	=> 'Error',
	'GROUPEMAILER_STATUS_SENT'	=> 'Sent',

	'GROUPEMAILER_RECIPIENTS'		=> 'Recipients',
	'GROUPEMAILER_SENT'			=> 'Sent',
	'GROUPEMAILER_ERRORS'			=> 'Errors',
	'GROUPEMAILER_CREATED'			=> 'Created on',
	'GROUPEMAILER_ACTIONS'			=> 'Actions',

	'GROUPEMAILER_START'			=> 'Start',
	'GROUPEMAILER_PAUSE'			=> 'Pause',
	'GROUPEMAILER_RESUME'			=> 'Resume',
	'GROUPEMAILER_EDIT'			=> 'Edit',
	'GROUPEMAILER_DELETE'			=> 'Delete',

	'GROUPEMAILER_CONFIRM_DELETE'		=> 'Do you really want to delete this campaign and its queue?',
	'GROUPEMAILER_CAMPAIGN_SAVED'		=> 'Campaign saved successfully.',
	'GROUPEMAILER_CAMPAIGN_DELETED'	=> 'Campaign deleted.',
	'GROUPEMAILER_CAMPAIGN_PAUSED'		=> 'Campaign paused. Sending will resume when you click "Resume".',
	'GROUPEMAILER_CAMPAIGN_RESUMED'	=> 'Campaign resumed, sending will continue automatically.',
	'GROUPEMAILER_CAMPAIGN_NOT_FOUND'	=> 'This campaign could not be found or can no longer be edited.',
	'GROUPEMAILER_FORM_INCOMPLETE'		=> 'Please fill in the title, subject, message and select at least one target group.',
	'GROUPEMAILER_NO_RECIPIENTS'		=> 'No recipients found for the selected groups (missing email address or empty group).',
	'GROUPEMAILER_CAMPAIGN_STARTED'	=> 'Campaign started: %d recipient(s) queued. Emails will be sent progressively according to the configured rate.',

	'GROUPEMAILER_SETTINGS_EXPLAIN'	=> 'These values are proposed by default when creating a new campaign; each campaign can then define its own rate.',
	'GROUPEMAILER_DEFAULT_RATE_COUNT'		=> 'Default emails per batch',
	'GROUPEMAILER_DEFAULT_RATE_INTERVAL'	=> 'Default interval (minutes)',
	'GROUPEMAILER_RATE_WARNING'		=> 'Warning: check the hourly sending limit imposed by your hosting provider (e.g. 180 emails/hour on some shared hosting) and set the rate accordingly.',

	'GROUPEMAILER_RECIPIENT'		=> 'Recipient',
	'GROUPEMAILER_SENT_DATE'		=> 'Sent date',
	'GROUPEMAILER_NO_HISTORY'		=> 'No email has been sent yet.',
	'GROUPEMAILER_DELETE_LOCKED'	=> 'This campaign has already been sent (or is currently sending): it can no longer be deleted, in order to preserve the sending history.',
	'GROUPEMAILER_RETRY_ERRORS'	=> 'Retry errors',
	'GROUPEMAILER_ERROR_MESSAGE'	=> 'Error detail',
	'GROUPEMAILER_RETRY_DONE'		=> 'Failed emails have been queued again, they will be sent on the next cron run.',

	'GROUPEMAILER_SENDER_LEGEND'	=> 'Sender and formatting',
	'GROUPEMAILER_SENDER_EXPLAIN'	=> 'Leave empty to use the site name and contact address configured in the board\'s general settings.',
	'GROUPEMAILER_FROM_NAME'		=> 'Sender name',
	'GROUPEMAILER_FROM_EMAIL'		=> 'Sender email address',
	'GROUPEMAILER_HEADER'			=> 'Header (added before each message)',
	'GROUPEMAILER_HEADER_EXPLAIN'	=> 'Optional text inserted at the start of every email sent, before the campaign content.',
	'GROUPEMAILER_FOOTER'			=> 'Footer (added after each message)',
	'GROUPEMAILER_FOOTER_EXPLAIN'	=> 'Optional text inserted at the end of every email sent, after the campaign content (e.g. legal notice, unsubscribe link).',
	'GROUPEMAILER_TOTAL_SENT_LABEL'	=> 'Total emails sent',
));
