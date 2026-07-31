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

	'GROUPEMAILER_REQUIRE_CONFIRM'	=> 'Read confirmation',
	'GROUPEMAILER_REQUIRE_CONFIRM_LABEL'	=> 'Ask recipients to confirm they received the message',
	'GROUPEMAILER_REQUIRE_CONFIRM_EXPLAIN'	=> 'A unique personal link is added at the end of each email. By clicking it, the recipient confirms they received and read the message (no forum login required). You can then send a reminder only to those who did not confirm.',
	'GROUPEMAILER_CONFIRMED'		=> 'Confirmed',
	'GROUPEMAILER_CONFIRMED_YES'	=> 'Confirmed',
	'GROUPEMAILER_CONFIRMED_NO'	=> 'Not confirmed',
	'GROUPEMAILER_TOTAL_CONFIRMED_LABEL'	=> 'Read confirmations',

	'GROUPEMAILER_RELANCE'			=> 'Remind non-readers',
	'GROUPEMAILER_RELANCE_TITLE'	=> '%s (reminder)',
	'GROUPEMAILER_RELANCE_OF'		=> 'Reminder — %s',
	'GROUPEMAILER_RELANCE_CREATED'	=> 'Reminder draft created: %d recipient(s) did not confirm reading. Review the message then click "Start".',
	'GROUPEMAILER_RELANCE_TARGET_EXPLAIN'	=> 'Recipients are computed automatically on start: only members who received the original campaign without confirming.',
	'GROUPEMAILER_NO_UNCONFIRMED'	=> 'No recipient to remind: everyone who received the message has already confirmed.',

	'GROUPEMAILER_MAIL_CONFIRM_TEXT'	=> 'You can read this message online using your personal link below. Opening it automatically confirms you received it, no further action is needed:',
	'GROUPEMAILER_CONFIRM_PAGE_TITLE'	=> 'Board message',
	'GROUPEMAILER_CONFIRM_OK_TITLE'	=> 'Thank you!',
	'GROUPEMAILER_CONFIRM_OK'		=> 'Hello %s, your read confirmation has been recorded. You will not receive a reminder for this message.',
	'GROUPEMAILER_CONFIRM_ALREADY_TITLE'	=> 'Already confirmed',
	'GROUPEMAILER_CONFIRM_ALREADY'	=> 'You already confirmed reading this message on %s. No further action is needed.',
	'GROUPEMAILER_CONFIRM_INVALID_TITLE'	=> 'Invalid link',
	'GROUPEMAILER_CONFIRM_INVALID'	=> 'This confirmation link is invalid or has expired. If you believe this is a mistake, please contact the board administrator.',

	'GROUPEMAILER_DETAILS'			=> 'Tracking',
	'GROUPEMAILER_DETAILS_LEGEND'	=> 'Campaign tracking',
	'GROUPEMAILER_DETAILS_SUMMARY'	=> 'Summary',
	'GROUPEMAILER_DETAILS_TABLE'	=> 'Per-recipient detail',
	'GROUPEMAILER_STATUS_PENDING'	=> 'Pending',
	'GROUPEMAILER_ALREADY_QUEUED'	=> 'Already queued',
	'GROUPEMAILER_NO_RECIPIENTS_YET'	=> 'No recipient in this campaign queue.',
	'GROUPEMAILER_BACK_TO_LIST'	=> 'Back to campaign list',

	'GROUPEMAILER_RESEND_LEGEND'	=> 'Resend the message',
	'GROUPEMAILER_RESEND_EXPLAIN'	=> 'Resending puts the selected recipients back in the queue: the message will go out progressively at the campaign rate, on the next cron run.',
	'GROUPEMAILER_RESEND_ALL'		=> 'Resend to all recipients',
	'GROUPEMAILER_RESEND_UNCONFIRMED'	=> 'Resend to unconfirmed only',
	'GROUPEMAILER_RESEND_ONE'		=> 'Resend',
	'GROUPEMAILER_RESEND_DONE'		=> '%d recipient(s) put back in the queue. Sending will resume on the next cron run.',
	'GROUPEMAILER_RESEND_NOBODY'	=> 'No recipient matches this resend.',

	'GROUPEMAILER_EXCLUDE_INACTIVE'	=> 'Inactive accounts',
	'GROUPEMAILER_EXCLUDE_INACTIVE_LABEL'	=> 'Do not send to inactive accounts',
	'GROUPEMAILER_INACTIVE_DAYS'	=> 'Inactivity threshold (days)',
	'GROUPEMAILER_EXCLUDE_INACTIVE_EXPLAIN'	=> 'Excludes members who have not logged in for more than X days. Recent registrations are kept even without a first visit. Non-activated accounts and bots are always excluded anyway.',
	'GROUPEMAILER_RESPECT_MASSEMAIL'		=> 'Mass emails',
	'GROUPEMAILER_RESPECT_MASSEMAIL_LABEL'	=> 'Respect the mass email opt-out',
	'GROUPEMAILER_RESPECT_MASSEMAIL_EXPLAIN'	=> 'Excludes members who unchecked "Receive mass emails" in their profile. This is the behaviour of phpBB\'s native tool; only disable it for a genuinely essential message (security, board closure...).',
	'GROUPEMAILER_EXCLUDED_INFO'	=> '%d account(s) excluded by the campaign filters.',
	'GROUPEMAILER_DEFAULT_INACTIVE_DAYS'	=> 'Default inactivity threshold (days)',
	'GROUPEMAILER_DEFAULT_INACTIVE_DAYS_EXPLAIN'	=> 'Value proposed when creating a campaign if inactive account exclusion is enabled.',

	'GROUPEMAILER_EXCLUDE_DEACTIVATED'	=> 'Deactivated accounts',
	'GROUPEMAILER_EXCLUDE_DEACTIVATED_LABEL'	=> 'Do not send to accounts deactivated by an administrator',
	'GROUPEMAILER_EXCLUDE_DEACTIVATED_EXPLAIN'	=> 'Accounts flagged "Inactive" in phpBB: deactivated by an administrator or never activated after registration. Only uncheck this for a message inviting them to reactivate. Bots and the anonymous account remain excluded in all cases.',
	'GROUPEMAILER_EXCLUDED_BREAKDOWN'	=> 'Breakdown: %1$d deactivated account(s), %2$d dormant account(s), %3$d mass email opt-out(s).',

	'GROUPEMAILER_EXCLUDED_CONFIRMED'	=> '%d member(s) excluded from this reminder: they had confirmed reading via the link of an earlier message.',

	'GROUPEMAILER_READ_RECORDED'	=> 'Hello %1$s — receipt of this message was recorded on %2$s. You will not receive a reminder about it.',
	'GROUPEMAILER_READ_ALREADY'	=> 'Receipt of this message had already been recorded on %s.',

	'GROUPEMAILER_EMAIL_MODE'		=> 'Email content',
	'GROUPEMAILER_EMAIL_MODE_NOTICE'	=> 'Notification only — the message is read online (recommended)',
	'GROUPEMAILER_EMAIL_MODE_FULL'	=> 'Full message directly in the email',
	'GROUPEMAILER_EMAIL_MODE_EXPLAIN'	=> 'In notification mode the email simply announces that a message is waiting and contains only the personal reading link, so read tracking is reliable: the content can only be read by opening the page. "Full message" mode sends the whole text by email; if it already contains links, many members will read the message without ever opening the reading link and will not appear as readers. Notification mode always enables read confirmation.',

	'GROUPEMAILER_MAIL_HELLO'		=> 'Hello %s,',
	'GROUPEMAILER_MAIL_NOTICE_INTRO'	=> 'You are receiving this email because you are a member of the %s board.',
	'GROUPEMAILER_MAIL_NOTICE_SUBJECT'	=> 'A message has been sent to you: "%s"',
	'GROUPEMAILER_MAIL_NOTICE_LINK'	=> 'To read it, click your personal link below. Opening it automatically confirms receipt of the message, no further action is needed:',
	'GROUPEMAILER_MAIL_UNSUBSCRIBE'	=> 'To stop receiving this type of email, log in to your account and go to "User Control Panel" → "Board preferences" to turn off emails from the administration yourself:',

	'GROUPEMAILER_ADD_UNSUBSCRIBE'	=> 'Unsubscribe notice',
	'GROUPEMAILER_ADD_UNSUBSCRIBE_LABEL'	=> 'Automatically add the unsubscribe notice to every email',
	'GROUPEMAILER_ADD_UNSUBSCRIBE_EXPLAIN'	=> 'Appends instructions for opting out of this type of email, with a link to the account preferences. Members stay in control from their own profile: keep this enabled, it is expected good practice for any bulk mailing.',

	'GROUPEMAILER_SEND_NOW'			=> 'Send a batch now',
	'GROUPEMAILER_SEND_NOW_LEGEND'	=> 'Immediate sending',
	'GROUPEMAILER_SEND_NOW_EXPLAIN'	=> 'Sends the next batch right away, without waiting for the cron or respecting the rate interval. Useful to test a campaign or to move sending along on a quiet board. Take care not to click repeatedly beyond your host\'s hourly limit.',
	'GROUPEMAILER_SEND_NOW_DONE'	=> 'Immediate sending finished: %1$d message(s) sent, %2$d error(s).',
	'GROUPEMAILER_SEND_NOW_ERROR'	=> 'Last error encountered: %s',
	'GROUPEMAILER_SEND_NOW_NOT_RUNNING'	=> 'This campaign is not sending: start or resume it before forcing a batch.',

	'ACP_GROUPEMAILER_DIAG'		=> 'Diagnostics',
	'GROUPEMAILER_DIAG_LEGEND'	=> 'Actual state of the extension',
	'GROUPEMAILER_DIAG_EXPLAIN'	=> 'This page queries the database and phpBB service container directly. It helps identify why sending does not start.',
	'GROUPEMAILER_DIAG_CHECK'	=> 'Check',
	'GROUPEMAILER_DIAG_RESULT'	=> 'Result',
	'GROUPEMAILER_DIAG_YES'		=> 'Yes',

	'GROUPEMAILER_DIAG_SCHEMA'	=> 'Database columns',
	'GROUPEMAILER_DIAG_SCHEMA_OK'	=> 'All expected columns are present: migrations have run correctly.',
	'GROUPEMAILER_DIAG_SCHEMA_MISSING'	=> 'Missing columns: %s. Not all migrations have run: disable then re-enable the extension (without "Delete data").',

	'GROUPEMAILER_DIAG_SERVICE'	=> 'Sending task service',
	'GROUPEMAILER_DIAG_SERVICE_OK'	=> 'The service is built correctly by phpBB.',

	'GROUPEMAILER_DIAG_TASK_NAME'	=> 'Cron task name',
	'GROUPEMAILER_DIAG_TASK_NAME_EMPTY'	=> 'Empty name: the "set_name" call is missing from config/services.yml, so phpBB cannot find the task.',

	'GROUPEMAILER_DIAG_TASK_READY'	=> 'Task ready to run',
	'GROUPEMAILER_DIAG_TASK_NOT_READY'	=> 'No: no campaign is currently sending.',

	'GROUPEMAILER_DIAG_CRON_FOUND'	=> 'Task recognised by the cron manager',
	'GROUPEMAILER_DIAG_CRON_NOT_FOUND'	=> 'Not found: phpBB does not see this task, automatic sending cannot trigger.',

	'GROUPEMAILER_DIAG_SYSTEM_CRON'	=> 'phpBB "system cron" setting',
	'GROUPEMAILER_DIAG_SYSTEM_CRON_ON'	=> 'Enabled: phpBB no longer triggers any task on board visits. Disable it in ACP → General → Server settings, or set up a scheduled task on the host.',
	'GROUPEMAILER_DIAG_SYSTEM_CRON_OFF'	=> 'Disabled: tasks trigger while browsing the board, which is the expected behaviour.',

	'GROUPEMAILER_DIAG_EMAIL_ENABLE'	=> 'Email sending enabled in phpBB',
	'GROUPEMAILER_DIAG_EMAIL_DISABLED'	=> 'Disabled: no email can be sent. Enable it in ACP → General → Email settings.',

	'GROUPEMAILER_DIAG_SENDER'	=> 'Sender address in use',
	'GROUPEMAILER_DIAG_QUEUE'	=> 'Queue (all campaigns)',
	'GROUPEMAILER_DIAG_QUEUE_EMPTY'	=> 'Empty',
	'GROUPEMAILER_DIAG_RUNNING'	=> 'Campaigns currently sending',

	'GROUPEMAILER_DIAG_MANUAL'	=> 'Manual cron trigger',
	'GROUPEMAILER_DIAG_MANUAL_EXPLAIN'	=> 'Opening this address in a browser runs the task directly. An almost blank page (a white dot) is normal; any error shown reveals the cause.',
	'GROUPEMAILER_DIAG_LINK'	=> 'Generated reading link',

	'GROUPEMAILER_SEND_MODE'		=> 'Email content and tracking',
	'GROUPEMAILER_SEND_MODE_NOTICE'	=> 'Notification only, with read tracking — the message is read online (recommended)',
	'GROUPEMAILER_SEND_MODE_FULL_TRACK'	=> 'Full message in the email, with read tracking',
	'GROUPEMAILER_SEND_MODE_FULL'	=> 'Full message in the email, without read tracking',
	'GROUPEMAILER_SEND_MODE_EXPLAIN'	=> 'In notification mode the email simply announces that a message is waiting and contains only the personal link: since the content can only be read by opening the page, tracking is reliable. This mode necessarily implies read tracking, as the link is the only way to reach the message. With full message and tracking, a reading link is appended to the email; beware that if your text already contains links, many members will read the message without ever opening it and will count as non-readers. Without tracking, no link is added and reminding non-readers is not available.',

	'GROUPEMAILER_TEST'			=> 'Test',
	'GROUPEMAILER_TEST_SUBJECT_PREFIX'	=> '[TEST]',
	'GROUPEMAILER_TEST_SENT'	=> 'Test copy sent to %s. Neither the queue nor the campaign counters were changed, and the test reading link is intentionally inactive.',
	'GROUPEMAILER_TEST_FAILED'	=> 'Test sending failed: %s',
	'GROUPEMAILER_TEST_NO_EMAIL'	=> 'No email address is associated with your account: cannot send a test.',

	'GROUPEMAILER_READ_FROM'		=> 'Message from %s',
	'GROUPEMAILER_READ_SENT_ON'	=> 'sent on %s',
	'GROUPEMAILER_READ_GO_BOARD'	=> 'Go to the board',

	'GROUPEMAILER_STATUS_CANCELLED'	=> 'Stopped',
	'GROUPEMAILER_CANCEL'		=> 'Stop permanently',
	'GROUPEMAILER_CONFIRM_CANCEL'	=> 'Permanently stop this campaign? The %d recipient(s) still pending will not receive the message. Messages already sent are kept in the history. This cannot be undone: for a temporary interruption, use "Pause" instead.',
	'GROUPEMAILER_CANCEL_DONE'	=> 'Campaign permanently stopped. %d pending recipient(s) were dropped.',
	'GROUPEMAILER_CANCEL_IMPOSSIBLE'	=> 'Only a sending or paused campaign can be stopped.',
));
