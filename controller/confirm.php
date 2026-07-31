<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace verturin\groupemailer\controller;

class confirm
{
	protected $config;
	protected $db;
	protected $template;
	protected $user;
	protected $helper;
	protected $campaigns_table;
	protected $queue_table;

	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, $table_prefix)
	{
		$this->config = $config;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->campaigns_table = $table_prefix . 'groupemailer_campaigns';
		$this->queue_table = $table_prefix . 'groupemailer_queue';
	}

	/**
	 * Affiche le message en ligne à partir du lien unique reçu par email.
	 * La consultation de la page vaut confirmation de lecture :
	 * aucune action supplémentaire n'est demandée au destinataire.
	 */
	public function handle($token)
	{
		$this->user->add_lang_ext('verturin/groupemailer', 'common');

		$sql = 'SELECT q.queue_id, q.username, q.confirmed_time, q.sent_time,
				c.title, c.subject, c.body, c.require_confirm
			FROM ' . $this->queue_table . ' q, ' . $this->campaigns_table . " c
			WHERE q.campaign_id = c.campaign_id
				AND q.confirm_token = '" . $this->db->sql_escape($token) . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			$this->template->assign_vars(array(
				'S_INVALID'	=> true,
			));

			return $this->helper->render('groupemailer_confirm.html', $this->user->lang('GROUPEMAILER_CONFIRM_PAGE_TITLE'));
		}

		$already = ((int) $row['confirmed_time'] > 0);

		if (!$already)
		{
			$sql = 'UPDATE ' . $this->queue_table . '
				SET confirmed_time = ' . time() . '
				WHERE queue_id = ' . (int) $row['queue_id'];
			$this->db->sql_query($sql);
		}

		// Contenu identique à celui de l'email, sans le bloc du lien
		$header = (string) $this->config['groupemailer_header'];
		$footer = (string) $this->config['groupemailer_footer'];

		$message = ($header !== '' ? $header . "\n\n" : '')
			. $row['body']
			. ($footer !== '' ? "\n\n" . $footer : '');

		$message = str_replace('{USERNAME}', $row['username'], $message);

		$this->template->assign_vars(array(
			'S_INVALID'		=> false,
			'S_ALREADY'		=> $already,
			'USERNAME'		=> $row['username'],
			'SUBJECT'		=> $row['subject'],
			'SITENAME'		=> $this->config['sitename'],
			'SENT_DATE'		=> (int) $row['sent_time'] > 0 ? $this->user->format_date((int) $row['sent_time']) : '',
			'MESSAGE_BODY'	=> $message,
			'CONFIRM_TIME'	=> $this->user->format_date($already ? (int) $row['confirmed_time'] : time()),
		));

		return $this->helper->render('groupemailer_confirm.html', $row['subject']);
	}
}
