<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace verturin\groupemailer\cron\task;

class send_queue extends \phpbb\cron\task\base
{
	protected $config;
	protected $db;
	protected $user;
	protected $group_helper;
	protected $table_prefix;
	protected $root_path;
	protected $php_ext;
	protected $campaigns_table;
	protected $queue_table;

	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\group\helper $group_helper, $table_prefix, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->group_helper = $group_helper;
		$this->table_prefix = $table_prefix;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->campaigns_table = $table_prefix . 'groupemailer_campaigns';
		$this->queue_table = $table_prefix . 'groupemailer_queue';
	}

	public function run()
	{
		$sql = 'SELECT * FROM ' . $this->campaigns_table . "
			WHERE status = 'running'
			ORDER BY campaign_id ASC";
		$result = $this->db->sql_query($sql);

		$campaigns = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$campaigns[] = $row;
		}
		$this->db->sql_freeresult($result);

		foreach ($campaigns as $campaign)
		{
			$elapsed = time() - (int) $campaign['last_run_time'];

			if ((int) $campaign['last_run_time'] > 0 && $elapsed < ((int) $campaign['rate_interval'] * 60))
			{
				continue;
			}

			$this->send_batch($campaign);
		}
	}

	/**
	 * Envoie un exemplaire de test à une adresse donnée, sans toucher
	 * à la file d'attente ni aux compteurs de la campagne.
	 */
	public function send_test($campaign_id, $email, $username)
	{
		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$campaign = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$campaign)
		{
			return array('sent' => false, 'error' => 'campaign_not_found');
		}

		if (!class_exists('messenger'))
		{
			include($this->root_path . 'includes/functions_messenger.' . $this->php_ext);
		}

		$header = (string) $this->config['groupemailer_header'];
		$footer = (string) $this->config['groupemailer_footer'];
		$from_name = $this->config['groupemailer_from_name'] !== '' ? $this->config['groupemailer_from_name'] : $this->config['sitename'];
		$from_email = $this->config['groupemailer_from_email'] !== '' ? $this->config['groupemailer_from_email'] : $this->config['board_contact'];

		// Jeton fictif : le lien de test est volontairement inopérant,
		// pour ne pas enregistrer de confirmation parasite.
		$row = array('username' => $username, 'confirm_token' => 'TEST0000TEST0000TEST0000');

		try
		{
			$messenger = new \messenger(false);
			$messenger->template('groupemailer_campaign', (string) $this->config['default_lang'], $this->root_path . 'ext/verturin/groupemailer/language/' . (string) $this->config['default_lang'] . '/email');
			$messenger->to($email, $username);
			$messenger->from($from_email, $from_name);
			$messenger->subject($this->user->lang('GROUPEMAILER_TEST_SUBJECT_PREFIX') . ' ' . $campaign['subject']);
			$messenger->assign_vars(array(
				'MESSAGE'	=> $this->build_message($campaign, $row, $header, $footer),
			));

			$ok = $messenger->send(NOTIFY_EMAIL);

			return array('sent' => (bool) $ok, 'error' => $ok ? '' : 'messenger->send() a retourné false');
		}
		catch (\Throwable $e)
		{
			return array('sent' => false, 'error' => get_class($e) . ': ' . $e->getMessage());
		}
	}

	/**
	 * Envoi immédiat d'un lot pour une campagne donnée, déclenché
	 * depuis l'ACP. Ignore l'intervalle de cadence, contrairement au cron.
	 */
	public function force_send($campaign_id)
	{
		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id . "
				AND status = 'running'";
		$result = $this->db->sql_query($sql);
		$campaign = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$campaign)
		{
			return array('sent' => 0, 'errors' => 0, 'last_error' => '', 'not_running' => true);
		}

		$res = $this->send_batch($campaign);
		$res['not_running'] = false;

		return $res;
	}

	protected function send_batch($campaign)
	{
		$sql = 'SELECT * FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign['campaign_id'] . "
				AND status = 'pending'
			ORDER BY queue_id ASC";
		$result = $this->db->sql_query_limit($sql, (int) $campaign['rate_count']);

		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		if (empty($rows))
		{
			$this->maybe_complete($campaign);
			return array('sent' => 0, 'errors' => 0, 'last_error' => '');
		}

		if (!class_exists('messenger'))
		{
			include($this->root_path . 'includes/functions_messenger.' . $this->php_ext);
		}

		$template_lang = (string) $this->config['default_lang'];
		$template_path = $this->root_path . 'ext/verturin/groupemailer/language/' . $template_lang . '/email';

		$from_name = $this->config['groupemailer_from_name'] !== '' ? $this->config['groupemailer_from_name'] : $this->config['sitename'];
		$from_email = $this->config['groupemailer_from_email'] !== '' ? $this->config['groupemailer_from_email'] : $this->config['board_contact'];
		$header = (string) $this->config['groupemailer_header'];
		$footer = (string) $this->config['groupemailer_footer'];

		$sent = 0;
		$errors = 0;
		$last_error = '';

		foreach ($rows as $row)
		{
			$success = true;
			$error_message = '';

			try
			{
				$full_message = $this->build_message($campaign, $row, $header, $footer);

				$messenger = new \messenger(false);
				$messenger->template('groupemailer_campaign', $template_lang, $template_path);
				$messenger->to($row['email'], $row['username']);
				$messenger->from($from_email, $from_name);
				$messenger->subject($campaign['subject']);
				$messenger->assign_vars(array(
					'MESSAGE'	=> $full_message,
				));

				$success = $messenger->send(NOTIFY_EMAIL);

				if (!$success)
				{
					$error_message = 'messenger->send() a retourné false';
				}
			}
			catch (\Throwable $e)
			{
				$success = false;
				$error_message = get_class($e) . ': ' . $e->getMessage();
			}

			if ($success)
			{
				$sql_up = 'UPDATE ' . $this->queue_table . "
					SET status = 'sent', sent_time = " . time() . '
					WHERE queue_id = ' . (int) $row['queue_id'];
				$this->db->sql_query($sql_up);
				$sent++;
			}
			else
			{
				$sql_up = 'UPDATE ' . $this->queue_table . "
					SET status = 'error', error_message = '" . $this->db->sql_escape(substr($error_message, 0, 250)) . "'
					WHERE queue_id = " . (int) $row['queue_id'];
				$this->db->sql_query($sql_up);
				$errors++;
				$last_error = $error_message;
			}
		}

		$counts = array('sent' => 0, 'error' => 0);

		$sql = 'SELECT status, COUNT(*) AS nb
			FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign['campaign_id'] . '
			GROUP BY status';
		$result = $this->db->sql_query($sql);
		while ($row_c = $this->db->sql_fetchrow($result))
		{
			$counts[$row_c['status']] = (int) $row_c['nb'];
		}
		$this->db->sql_freeresult($result);

		$sql_up = 'UPDATE ' . $this->campaigns_table . '
			SET sent_count = ' . (int) $counts['sent'] . ',
				error_count = ' . (int) $counts['error'] . ',
				last_run_time = ' . time() . '
			WHERE campaign_id = ' . (int) $campaign['campaign_id'];
		$this->db->sql_query($sql_up);

		$this->maybe_complete($campaign);

		return array('sent' => $sent, 'errors' => $errors, 'last_error' => $last_error);
	}

	/**
	 * Construit le corps de l'email selon le mode de la campagne :
	 *  - 'full'   : message complet dans l'email
	 *  - 'notice' : notification seule, le message est consultable en ligne
	 *               via le lien personnel (sa consultation vaut confirmation)
	 */
	protected function build_message($campaign, $row, $header, $footer)
	{
		$username = $row['username'];
		$has_link = ((int) $campaign['require_confirm'] && $row['confirm_token'] !== '');
		$link = $has_link ? $this->get_confirm_url($row['confirm_token']) : '';
		$notice_mode = ($campaign['email_mode'] === 'notice' && $has_link);

		$parts = array();

		if ($header !== '')
		{
			$parts[] = str_replace('{USERNAME}', $username, $header);
		}
		else
		{
			$parts[] = $this->user->lang('GROUPEMAILER_MAIL_HELLO', $username);
		}

		if ($notice_mode)
		{
			$parts[] = $this->user->lang('GROUPEMAILER_MAIL_NOTICE_INTRO', $this->config['sitename']);
			$parts[] = $this->user->lang('GROUPEMAILER_MAIL_NOTICE_SUBJECT', $campaign['subject']);
			$parts[] = $this->user->lang('GROUPEMAILER_MAIL_NOTICE_LINK') . "\n" . $link;
		}
		else
		{
			$parts[] = str_replace('{USERNAME}', $username, $campaign['body']);

			if ($has_link)
			{
				$parts[] = $this->user->lang('GROUPEMAILER_MAIL_CONFIRM_TEXT') . "\n" . $link;
			}
		}

		if ($footer !== '')
		{
			$parts[] = str_replace('{USERNAME}', $username, $footer);
		}

		if ((int) $this->config['groupemailer_add_unsubscribe'])
		{
			$parts[] = "--\n" . $this->user->lang('GROUPEMAILER_MAIL_UNSUBSCRIBE') . "\n" . $this->get_prefs_url();
		}

		return implode("\n\n", $parts);
	}

	/**
	 * Lien vers les préférences du compte, où le membre peut lui-même
	 * refuser les emails de l'administration
	 */
	protected function get_prefs_url()
	{
		return generate_board_url() . '/ucp.' . $this->php_ext . '?i=ucp_prefs&mode=personal';
	}

	/**
	 * URL absolue du lien de confirmation.
	 * Construite sans le routeur : injecter le service de routage dans une
	 * tâche cron empêche phpBB de construire la tâche, qui est alors ignorée.
	 */
	protected function get_confirm_url($token)
	{
		$base = generate_board_url();
		$path = '/gm/' . $token;

		if (!empty($this->config['enable_mod_rewrite']))
		{
			return $base . $path;
		}

		return $base . '/app.' . $this->php_ext . $path;
	}

	protected function maybe_complete($campaign)
	{
		$sql = 'SELECT COUNT(*) AS remaining FROM ' . $this->queue_table . "
			WHERE campaign_id = " . (int) $campaign['campaign_id'] . "
				AND status = 'pending'";
		$result = $this->db->sql_query($sql);
		$remaining = (int) $this->db->sql_fetchfield('remaining');
		$this->db->sql_freeresult($result);

		if ($remaining === 0)
		{
			$sql_up = 'UPDATE ' . $this->campaigns_table . "
				SET status = 'completed', completed_time = " . time() . '
				WHERE campaign_id = ' . (int) $campaign['campaign_id'] . "
					AND status = 'running'";
			$this->db->sql_query($sql_up);
		}
	}

	public function is_runnable()
	{
		return true;
	}

	public function should_run()
	{
		$sql = 'SELECT COUNT(*) AS running FROM ' . $this->campaigns_table . "
			WHERE status = 'running'";
		$result = $this->db->sql_query($sql);
		$running = (int) $this->db->sql_fetchfield('running');
		$this->db->sql_freeresult($result);

		return $running > 0;
	}
}
