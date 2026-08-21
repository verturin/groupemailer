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
	protected $config_text;
	protected $mailer;
	protected $db;
	protected $template;
	protected $user;
	protected $helper;
	protected $root_path;
	protected $php_ext;
	protected $campaigns_table;
	protected $queue_table;

	public function __construct(\phpbb\config\config $config, \phpbb\config\db_text $config_text, \verturin\groupemailer\cron\task\send_queue $mailer, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, $table_prefix, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->mailer = $mailer;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->campaigns_table = $table_prefix . 'groupemailer_campaigns';
		$this->queue_table = $table_prefix . 'groupemailer_queue';
	}

	/**
	 * Adresse publique de l'extension, en tenant compte de la réécriture d'URL
	 */
	protected function build_url($prefix, $token)
	{
		$base = generate_board_url();
		$path = '/' . $prefix . '/' . $token;

		if (!empty($this->config['enable_mod_rewrite']))
		{
			return $base . $path;
		}

		return $base . '/app.' . $this->php_ext . $path;
	}

	/**
	 * Désabonnement en un clic depuis le lien reçu par email.
	 * Agit sur la préférence native de phpBB « Recevoir les emails de masse »,
	 * que l'extension respecte déjà lors du choix des destinataires.
	 */
	public function unsubscribe($token)
	{
		return $this->set_subscription($token, false);
	}

	/**
	 * Réabonnement, proposé juste après un désabonnement en cas d'erreur
	 */
	public function resubscribe($token)
	{
		return $this->set_subscription($token, true);
	}

	protected function set_subscription($token, $allow)
	{
		$this->user->add_lang_ext('verturin/groupemailer', 'common');

		$sql = 'SELECT q.queue_id, q.user_id, q.username, q.email, q.user_lang, c.title
			FROM ' . $this->queue_table . ' q, ' . $this->campaigns_table . " c
			WHERE q.campaign_id = c.campaign_id
				AND q.confirm_token = '" . $this->db->sql_escape($token) . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			$this->template->assign_vars(array('S_INVALID' => true));

			return $this->helper->render('groupemailer_unsub.html', $this->user->lang('GROUPEMAILER_UNSUB_PAGE_TITLE'));
		}

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET user_allow_massemail = ' . ($allow ? 1 : 0) . '
			WHERE user_id = ' . (int) $row['user_id'];
		$this->db->sql_query($sql);

		$now = time();

		$sql = 'UPDATE ' . $this->queue_table . '
			SET unsubscribed_time = ' . ($allow ? 0 : $now) . '
			WHERE queue_id = ' . (int) $row['queue_id'];
		$this->db->sql_query($sql);

		if (!$allow)
		{
			// Le désabonnement est déjà enregistré : un échec d'envoi
			// des accusés ne doit jamais le remettre en cause.
			try
			{
				$this->mailer->send_unsub_notifications($row, $now);
			}
			catch (\Throwable $e)
			{
			}
		}

		$this->template->assign_vars(array(
			'S_INVALID'			=> false,
			'S_RESUBSCRIBED'	=> (bool) $allow,
			'USERNAME'			=> $row['username'],
			'SITENAME'			=> $this->config['sitename'],
			'U_UNSUBSCRIBE'		=> $this->build_url('gmu', $token),
			'U_RESUBSCRIBE'		=> $this->build_url('gmr', $token),
		));

		return $this->helper->render('groupemailer_unsub.html', $this->user->lang('GROUPEMAILER_UNSUB_PAGE_TITLE'));
	}

	/**
	 * Affiche le message en ligne à partir du lien unique reçu par email.
	 * La consultation de la page vaut confirmation de lecture :
	 * aucune action supplémentaire n'est demandée au destinataire.
	 */
	public function handle($token)
	{
		$this->user->add_lang_ext('verturin/groupemailer', 'common');

		$sql = 'SELECT q.queue_id, q.user_id, q.username, q.confirmed_time, q.sent_time, q.unsubscribed_time,
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
		$header = (string) $this->config_text->get('groupemailer_header');
		$footer = (string) $this->config_text->get('groupemailer_footer');

		$message = ($header !== '' ? $header . "\n\n" : '')
			. $row['body']
			. ($footer !== '' ? "\n\n" . $footer : '');

		$message = str_replace('{USERNAME}', $row['username'], $message);

		// Le texte est d'abord échappé, puis les adresses web y sont rendues
		// cliquables par la fonction native de phpBB : aucune balise saisie
		// dans le message ne peut donc être interprétée.
		$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

		// make_clickable() de phpBB est privilégiée ; si le fichier n'est pas
		// chargeable dans ce contexte, un repli interne assure le rendu.
		if (!function_exists('make_clickable'))
		{
			$file = $this->root_path . 'includes/functions_content.' . $this->php_ext;

			if (file_exists($file))
			{
				include($file);
			}
		}

		if (function_exists('make_clickable'))
		{
			$message = make_clickable($message, generate_board_url());
		}
		else
		{
			$message = preg_replace(
				'#(^|[\n ])(https?://[^\s<]+)#i',
				'$1<a class="postlink" href="$2" rel="noopener noreferrer">$2</a>',
				$message
			);
			$message = preg_replace(
				'#(^|[\n ])(www\.[^\s<]+)#i',
				'$1<a class="postlink" href="http://$2" rel="noopener noreferrer">$2</a>',
				$message
			);
		}

		$this->template->assign_vars(array(
			'S_INVALID'		=> false,
			'S_ALREADY'		=> $already,
			'USERNAME'		=> $row['username'],
			'SUBJECT'		=> $row['subject'],
			'SITENAME'		=> $this->config['sitename'],
			'SENT_DATE'		=> (int) $row['sent_time'] > 0 ? $this->user->format_date((int) $row['sent_time']) : '',
			'S_UNSUBSCRIBED'	=> ((int) $row['unsubscribed_time'] > 0),
			'U_UNSUBSCRIBE'	=> $this->build_url('gmu', $token),
			'U_RESUBSCRIBE'	=> $this->build_url('gmr', $token),
			'MESSAGE_BODY'	=> $message,
			'CONFIRM_TIME'	=> $this->user->format_date($already ? (int) $row['confirmed_time'] : time()),
		));

		return $this->helper->render('groupemailer_confirm.html', $row['subject']);
	}
}
