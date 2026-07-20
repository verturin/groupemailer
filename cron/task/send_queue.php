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
			return;
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

		foreach ($rows as $row)
		{
			$success = true;
			$error_message = '';

			try
			{
				$body = str_replace('{USERNAME}', $row['username'], $campaign['body']);
				$full_message = ($header !== '' ? $header . "\n\n" : '') . $body . ($footer !== '' ? "\n\n" . $footer : '');

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
			}
		}

		$sql_up = 'UPDATE ' . $this->campaigns_table . '
			SET sent_count = sent_count + ' . (int) $sent . ',
				error_count = error_count + ' . (int) $errors . ',
				last_run_time = ' . time() . '
			WHERE campaign_id = ' . (int) $campaign['campaign_id'];
		$this->db->sql_query($sql_up);

		$this->maybe_complete($campaign);
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
				WHERE campaign_id = ' . (int) $campaign['campaign_id'];
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
