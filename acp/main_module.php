<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace verturin\groupemailer\acp;

class main_module
{
	public $u_action;
	public $page_title;
	public $tpl_name;

	protected $db;
	protected $config;
	protected $config_text;
	protected $request;
	protected $template;
	protected $user;
	protected $group_helper;
	protected $table_prefix;
	protected $root_path;
	protected $php_ext;
	protected $campaigns_table;
	protected $queue_table;
	protected $bounces_table;

	function main($id, $mode)
	{
		global $phpbb_container, $request, $template, $user, $db, $config;

		$this->db = $db;
		$this->config = $config;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->group_helper = $phpbb_container->get('group_helper');
		$this->table_prefix = $phpbb_container->getParameter('core.table_prefix');
		$this->php_ext = $phpbb_container->getParameter('core.php_ext');
		$this->config_text = $phpbb_container->get('config_text');
		$this->root_path = $phpbb_container->getParameter('core.root_path');
		$this->campaigns_table = $this->table_prefix . 'groupemailer_campaigns';
		$this->queue_table = $this->table_prefix . 'groupemailer_queue';
		$this->bounces_table = $this->table_prefix . 'groupemailer_bounces';

		$user->add_lang_ext('verturin/groupemailer', 'common');

		switch ($mode)
		{
			case 'dashboard':
				$this->page_title = 'ACP_GROUPEMAILER_DASHBOARD';
				$this->tpl_name = 'acp_groupemailer_dashboard';
				$this->dashboard();
				break;

			case 'campaigns':
				$this->page_title = 'ACP_GROUPEMAILER_CAMPAIGNS';
				$this->tpl_name = 'acp_groupemailer_campaigns';
				$this->campaigns($id, $mode);
				break;

			case 'settings':
				$this->page_title = 'ACP_GROUPEMAILER_SETTINGS';
				$this->tpl_name = 'acp_groupemailer_settings';
				$this->settings($id, $mode);
				break;

			case 'diag':
				$this->page_title = 'ACP_GROUPEMAILER_DIAG';
				$this->tpl_name = 'acp_groupemailer_diag';
				$this->diagnostics();
				break;

			case 'backup':
				$this->page_title = 'ACP_GROUPEMAILER_BACKUP';
				$this->tpl_name = 'acp_groupemailer_backup';
				$this->backup();
				break;

			case 'unsubscribes':
				$this->page_title = 'ACP_GROUPEMAILER_UNSUBS';
				$this->tpl_name = 'acp_groupemailer_unsubs';
				$this->unsubscribes();
				break;

			case 'history':
				$this->page_title = 'ACP_GROUPEMAILER_HISTORY';
				$this->tpl_name = 'acp_groupemailer_history';
				$this->history($id, $mode);
				break;
		}
	}

	/**
	 * Gestion des campagnes : liste, création, édition, actions
	 */
	protected function campaigns($id, $mode)
	{
		$action = $this->request->variable('action', '');
		$campaign_id = $this->request->variable('campaign_id', 0);

		// Les actions qui modifient l'état sont protégées par un jeton de lien,
		// mécanisme standard de phpBB pour les liens d'action en GET.
		$guarded = array('start', 'pause', 'resume', 'cancel', 'relance', 'duplicate', 'unschedule', 'resubscribe', 'resend_unsub', 'reset_bounce', 'retry_errors', 'send_now', 'resend_all', 'resend_unconfirmed', 'resend_one', 'send_test');

		if (in_array($action, $guarded, true) && !check_link_hash($this->request->variable('hash', ''), 'gm_' . $action))
		{
			trigger_error('FORM_INVALID' . adm_back_link($this->u_action), E_USER_WARNING);
		}

		switch ($action)
		{
			case 'add':
			case 'edit':
				$this->campaign_form($action, $campaign_id);
				return;

			case 'save':
				$this->campaign_save($campaign_id);
				return;

			case 'start':
				$this->campaign_start($campaign_id);
				return;

			case 'pause':
				$this->campaign_set_status($campaign_id, 'paused');
				trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_PAUSED') . adm_back_link($this->u_action));
				return;

			case 'resume':
				$this->campaign_set_status($campaign_id, 'running');
				trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_RESUMED') . adm_back_link($this->u_action));
				return;

			case 'details':
				$this->page_title = 'ACP_GROUPEMAILER_CAMPAIGNS';
				$this->tpl_name = 'acp_groupemailer_details';

				// La moindre anomalie est rapportée à l'écran plutôt que de
				// provoquer une page d'erreur muette du serveur.
				try
				{
					$this->campaign_details($campaign_id);
				}
				catch (\Throwable $e)
				{
					if (strpos($e->getMessage(), 'STOP:') === 0)
					{
						throw $e;
					}

					trigger_error(
						'Group Mailer — suivi de campagne : ' . get_class($e) . ' : ' . $e->getMessage()
						. '<br>Fichier : ' . basename($e->getFile()) . ' ligne ' . $e->getLine()
						. adm_back_link($this->u_action),
						E_USER_WARNING
					);
				}
				return;

			case 'send_test':
				$this->campaign_send_test($campaign_id);
				return;

			case 'send_now':
				$this->campaign_send_now($campaign_id);
				return;

			case 'resend_all':
				$this->campaign_resend($campaign_id, 'all', 0);
				return;

			case 'resend_unconfirmed':
				$this->campaign_resend($campaign_id, 'unconfirmed', 0);
				return;

			case 'resend_one':
				$this->campaign_resend($campaign_id, 'one', $this->request->variable('queue_id', 0));
				return;

			case 'relance':
				$this->campaign_relance($campaign_id);
				return;

			case 'reset_bounce':
				$this->reset_bounce($this->request->variable('user_id', 0), $campaign_id);
				return;

			case 'retry_errors':
				$this->campaign_retry_errors($campaign_id);
				return;

			case 'cancel':
				$this->campaign_cancel($campaign_id);
				return;

			case 'duplicate':
				$this->campaign_duplicate($campaign_id);
				return;

			case 'unschedule':
				$this->campaign_unschedule($campaign_id);
				return;

			case 'delete':
				$this->campaign_delete($campaign_id);
				return;

			default:
				$this->campaign_list();
				return;
		}
	}

	/**
	 * Affiche la liste des campagnes avec leurs statistiques
	 */
	protected function campaign_list()
	{
		// Nombre de confirmations de lecture par campagne
		$confirmed = array();
		$sql = 'SELECT campaign_id, COUNT(*) AS nb
			FROM ' . $this->queue_table . '
			WHERE confirmed_time > 0
			GROUP BY campaign_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$confirmed[(int) $row['campaign_id']] = (int) $row['nb'];
		}
		$this->db->sql_freeresult($result);

		$sort_key = $this->request->variable('sk', 'd');
		$sort_dir = $this->request->variable('sd', 'd') === 'a' ? 'ASC' : 'DESC';

		$sort_map = array(
			'd' => 'created_time',
			't' => 'title',
			's' => 'status',
			'r' => 'total_recipients',
			'e' => 'sent_count',
			'x' => 'error_count',
		);
		$order_by = isset($sort_map[$sort_key]) ? $sort_map[$sort_key] : $sort_map['d'];

		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			ORDER BY ' . $order_by . ' ' . $sort_dir;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$cid = (int) $row['campaign_id'];
			$nb_confirmed = isset($confirmed[$cid]) ? $confirmed[$cid] : 0;
			$nb_sent = (int) $row['sent_count'];

			$this->template->assign_block_vars('campaigns', array(
				'CONFIRMED_COUNT'	=> $nb_confirmed,
				'UNCONFIRMED_COUNT'	=> max(0, $nb_sent - $nb_confirmed),
				'READ_RATE'			=> $nb_sent > 0 ? round($nb_confirmed * 100 / $nb_sent) : 0,
				'S_REQUIRE_CONFIRM'	=> ((bool) $row['require_confirm'] && $nb_sent > 0),
				'S_IS_RELANCE'		=> ((int) $row['parent_campaign_id'] > 0),
				'S_CAN_RELANCE'		=> ((bool) $row['require_confirm'] && ($nb_sent - $nb_confirmed) > 0),
				'U_RELANCE'	=> $this->u_action . "&amp;action=relance&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_relance'),
				'CAMPAIGN_ID'		=> $row['campaign_id'],
				'TITLE'				=> $row['title'],
				'SUBJECT'			=> $row['subject'],
				'STATUS'			=> $row['status'],
				'STATUS_LANG'		=> $this->user->lang('GROUPEMAILER_STATUS_' . strtoupper($row['status'])),
				'STATUS_STYLE'		=> $this->status_style($row['status']),
				'RATE'				=> $this->user->lang('GROUPEMAILER_RATE_VALUE', (int) $row['rate_count'], (int) $row['rate_interval']),
				'TOTAL_RECIPIENTS'	=> (int) $row['total_recipients'],
				'SENT_COUNT'		=> (int) $row['sent_count'],
				'ERROR_COUNT'		=> (int) $row['error_count'],
				'CREATED_TIME'		=> $row['created_time'] ? $this->user->format_date($row['created_time']) : '-',

				'S_DRAFT'		=> ($row['status'] === 'draft'),
				'S_RUNNING'		=> ($row['status'] === 'running'),
				'S_PAUSED'		=> ($row['status'] === 'paused'),
				'S_COMPLETED'	=> ($row['status'] === 'completed'),
				'S_SCHEDULED'	=> ($row['status'] === 'scheduled'),
				'SCHEDULED_TIME'	=> (int) $row['scheduled_time'] > 0 ? $this->user->format_date((int) $row['scheduled_time']) : '',
				'S_CANCELLED'	=> ($row['status'] === 'cancelled'),
				'S_CAN_CANCEL'	=> in_array($row['status'], array('running', 'paused'), true),
				'S_CAN_DELETE'	=> true,
				'S_HAS_ERRORS'	=> ((int) $row['error_count'] > 0),

				'U_EDIT'	=> $this->u_action . "&amp;action=edit&amp;campaign_id={$row['campaign_id']}",
				'U_START'	=> $this->u_action . "&amp;action=start&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_start'),
				'U_PAUSE'	=> $this->u_action . "&amp;action=pause&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_pause'),
				'U_RESUME'	=> $this->u_action . "&amp;action=resume&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_resume'),
				'U_TEST'	=> $this->u_action . "&amp;action=send_test&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_send_test'),
				'U_DELETE'	=> $this->u_action . "&amp;action=delete&amp;campaign_id={$row['campaign_id']}",
				'U_CANCEL'	=> $this->u_action . "&amp;action=cancel&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_cancel'),
				'U_RETRY'	=> $this->u_action . "&amp;action=retry_errors&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_retry_errors'),
				'U_DUPLICATE'	=> $this->u_action . "&amp;action=duplicate&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_duplicate'),
				'U_UNSCHEDULE'	=> $this->u_action . "&amp;action=unschedule&amp;campaign_id={$row['campaign_id']}&amp;hash=" . generate_link_hash('gm_unschedule'),
				'U_DETAILS'	=> $this->u_action . "&amp;action=details&amp;campaign_id={$row['campaign_id']}",
			));
		}
		$this->db->sql_freeresult($result);

		$this->assign_sort_urls($this->u_action, $sort_key, $sort_dir, array('d', 't', 's', 'r', 'e', 'x'));

		$this->template->assign_vars(array(
			'U_ADD_CAMPAIGN'	=> $this->u_action . '&amp;action=add',
		));
	}

	/**
	 * Affiche le formulaire de création / édition d'une campagne
	 */
	protected function campaign_form($action, $campaign_id)
	{
		$campaign = array(
			'campaign_id'	=> 0,
			'title'			=> '',
			'subject'		=> '',
			'body'			=> '',
			'target_groups'	=> '',
			'rate_count'		=> (int) $this->config['groupemailer_default_rate_count'],
			'rate_interval'		=> (int) $this->config['groupemailer_default_rate_interval'],
			'require_confirm'	=> 0,
			'parent_campaign_id'	=> 0,
			'exclude_inactive'	=> 0,
			'inactive_days'		=> (int) $this->config['groupemailer_default_inactive_days'],
			'respect_massemail'	=> 1,
			'exclude_deactivated'	=> 1,
			'email_mode'		=> 'notice',
			'scheduled_time'	=> 0,
		);

		if ($action === 'edit' && $campaign_id)
		{
			$sql = 'SELECT * FROM ' . $this->campaigns_table . '
				WHERE campaign_id = ' . (int) $campaign_id;
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$row)
			{
				trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$campaign = $row;
		}

		$selected_groups = $campaign['target_groups'] ? explode(',', $campaign['target_groups']) : array();

		$sql = 'SELECT group_id, group_name, group_type
			FROM ' . GROUPS_TABLE . '
			ORDER BY group_type DESC, group_name ASC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('groups', array(
				'GROUP_ID'		=> $row['group_id'],
				'GROUP_NAME'	=> $this->group_helper->get_name($row['group_name']),
				'S_SELECTED'	=> in_array($row['group_id'], $selected_groups),
			));
		}
		$this->db->sql_freeresult($result);

		add_form_key('acp_groupemailer');

		$this->template->assign_vars(array(
			'S_EDIT'			=> ($action === 'edit'),
			'CAMPAIGN_ID'		=> $campaign['campaign_id'],
			'TITLE'				=> $campaign['title'],
			'SUBJECT'			=> $campaign['subject'],
			'BODY'				=> $campaign['body'],
			'RATE_COUNT'		=> (int) $campaign['rate_count'],
			'RATE_INTERVAL'		=> (int) $campaign['rate_interval'],
			'S_DRAFT_ONLY'		=> (!$campaign_id || $campaign['status'] === 'draft'),
			'S_REQUIRE_CONFIRM'	=> (bool) $campaign['require_confirm'],
			'S_IS_RELANCE'		=> ((int) $campaign['parent_campaign_id'] > 0),
			'S_EXCLUDE_INACTIVE'	=> (bool) $campaign['exclude_inactive'],
			'INACTIVE_DAYS'			=> (int) $campaign['inactive_days'],
			'S_RESPECT_MASSEMAIL'	=> (bool) $campaign['respect_massemail'],
			'S_EXCLUDE_DEACTIVATED'	=> (bool) $campaign['exclude_deactivated'],
			'SCHEDULED_DATE'	=> (int) $campaign['scheduled_time'] > 0 ? date('Y-m-d\\TH:i', (int) $campaign['scheduled_time']) : '',
			'S_MODE_NOTICE'		=> ($campaign['email_mode'] === 'notice'),
			'S_MODE_FULL_TRACK'	=> ($campaign['email_mode'] !== 'notice' && (int) $campaign['require_confirm']),
			'S_MODE_FULL'		=> ($campaign['email_mode'] !== 'notice' && !(int) $campaign['require_confirm']),

			'U_ACTION'	=> $this->u_action . '&amp;action=save',
			'U_BACK'	=> $this->u_action,
		));

		$this->tpl_name = 'acp_groupemailer_campaign_form';
		$this->page_title = 'ACP_GROUPEMAILER_CAMPAIGNS';
	}

	/**
	 * Enregistre (création ou mise à jour) une campagne à l'état brouillon
	 */
	protected function campaign_save($campaign_id)
	{
		$title = $this->request->variable('title', '', true);
		$subject = $this->request->variable('subject', '', true);
		$body = $this->request->variable('body', '', true);
		$groups = $this->request->variable('groups', array(0));
		$rate_count = $this->request->variable('rate_count', 10);
		$rate_interval = $this->request->variable('rate_interval', 10);
		// Un seul choix pilote le contenu de l'email et le suivi de lecture,
		// ce qui évite toute combinaison contradictoire.
		switch ($this->request->variable('send_mode', 'notice'))
		{
			case 'full_track':
				$email_mode = 'full';
				$require_confirm = 1;
				break;

			case 'full':
				$email_mode = 'full';
				$require_confirm = 0;
				break;

			default:
				$email_mode = 'notice';
				$require_confirm = 1;
				break;
		}

		$is_relance = false;

		if ($campaign_id)
		{
			$sql = 'SELECT parent_campaign_id FROM ' . $this->campaigns_table . '
				WHERE campaign_id = ' . (int) $campaign_id;
			$result = $this->db->sql_query($sql);
			$is_relance = ((int) $this->db->sql_fetchfield('parent_campaign_id') > 0);
			$this->db->sql_freeresult($result);
		}

		if ($title === '' || $subject === '' || $body === '' || (empty($groups) && !$is_relance))
		{
			trigger_error($this->user->lang('GROUPEMAILER_FORM_INCOMPLETE') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql_ary = array(
			'title'			=> $title,
			'subject'		=> $subject,
			'body'			=> $body,
			'target_groups'	=> implode(',', array_map('intval', $groups)),
			'rate_count'	=> max(1, (int) $rate_count),
			'rate_interval'	=> max(1, (int) $rate_interval),
			'require_confirm'	=> $require_confirm ? 1 : 0,
			'exclude_inactive'	=> $this->request->variable('exclude_inactive', 0) ? 1 : 0,
			'inactive_days'		=> max(1, $this->request->variable('inactive_days', 180)),
			'respect_massemail'	=> $this->request->variable('respect_massemail', 0) ? 1 : 0,
			'exclude_deactivated'	=> $this->request->variable('exclude_deactivated', 0) ? 1 : 0,
			'email_mode'		=> $email_mode,
			'scheduled_time'	=> $this->parse_schedule($this->request->variable('scheduled_date', '')),
		);

		if ($campaign_id)
		{
			$sql = 'UPDATE ' . $this->campaigns_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE campaign_id = ' . (int) $campaign_id . "
					AND status = 'draft'";
			$this->db->sql_query($sql);
		}
		else
		{
			$sql_ary += array(
				'status'		=> 'draft',
				'created_time'	=> time(),
				'created_by'	=> $this->user->data['user_id'],
			);
			$sql = 'INSERT INTO ' . $this->campaigns_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
			$this->db->sql_query($sql);
		}

		trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_SAVED') . adm_back_link($this->u_action));
	}

	/**
	 * Démarre une campagne : construit la file d'attente puis passe le statut à "running"
	 */
	protected function campaign_start($campaign_id)
	{
		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$campaign = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$campaign || $campaign['status'] !== 'draft')
		{
			trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$parent_id = (int) $campaign['parent_campaign_id'];
		$recipients = array();
		$excluded = 0;
		$ex_deactivated = $ex_inactive = $ex_massemail = 0;
		$ex_bounced = 0;
		$ex_confirmed_elsewhere = 0;
		$ex_left_group = 0;

		if ($parent_id > 0)
		{
			// Remonte toute la chaîne des campagnes d'origine : un membre ayant
			// confirmé via un lien plus ancien ne doit plus être relancé, même
			// si sa ligne dans la campagne parente est restée non confirmée.
			$ancestors = array();
			$pid = $parent_id;
			$guard = 0;

			while ($pid > 0 && $guard < 20)
			{
				$ancestors[] = (int) $pid;

				$sql = 'SELECT parent_campaign_id FROM ' . $this->campaigns_table . '
					WHERE campaign_id = ' . (int) $pid;
				$result = $this->db->sql_query($sql);
				$pid = (int) $this->db->sql_fetchfield('parent_campaign_id');
				$this->db->sql_freeresult($result);

				$guard++;
			}

			$already_confirmed = array();

			$sql = 'SELECT DISTINCT user_id
				FROM ' . $this->queue_table . '
				WHERE ' . $this->db->sql_in_set('campaign_id', $ancestors) . '
					AND confirmed_time > 0';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$already_confirmed[(int) $row['user_id']] = true;
			}
			$this->db->sql_freeresult($result);

			// Les membres ayant quitté les groupes visés ne sont pas relancés,
			// mais restent dans le tableau de suivi pour ne pas fausser les
			// statistiques de la campagne d'origine.
			$still_member = null;
			$origin_groups = array_filter(array_map('intval', explode(',', (string) $campaign['target_groups'])));

			if ($origin_groups)
			{
				$still_member = array();

				$sql = 'SELECT DISTINCT user_id
					FROM ' . USER_GROUP_TABLE . '
					WHERE ' . $this->db->sql_in_set('group_id', $origin_groups) . '
						AND user_pending = 0';
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$still_member[(int) $row['user_id']] = true;
				}
				$this->db->sql_freeresult($result);
			}

			// Relance : uniquement les destinataires de la campagne d'origine
			// qui ont bien reçu le mail mais ne l'ont pas confirmé.
			$sql = 'SELECT user_id, username, email, user_lang
				FROM ' . $this->queue_table . '
				WHERE campaign_id = ' . $parent_id . "
					AND status = 'sent'
					AND confirmed_time = 0";
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				if (isset($already_confirmed[(int) $row['user_id']]))
				{
					$ex_confirmed_elsewhere++;
					continue;
				}

				// Sorti des groupes visés : consigné sans être renvoyé
				$left = ($still_member !== null && !isset($still_member[(int) $row['user_id']]));

				if ($left)
				{
					$ex_left_group++;
				}

				$recipients[] = array(
					'campaign_id'	=> (int) $campaign_id,
					'user_id'		=> (int) $row['user_id'],
					'username'		=> $row['username'],
					'email'			=> $row['email'],
					'status'		=> $left ? 'left_group' : 'pending',
					'confirm_token'	=> $this->generate_token(),
					'user_lang'		=> (string) $row['user_lang'],
				);
			}
			$this->db->sql_freeresult($result);

			$sql = 'SELECT target_groups_names FROM ' . $this->campaigns_table . '
				WHERE campaign_id = ' . $parent_id;
			$result = $this->db->sql_query($sql);
			$parent_groups = (string) $this->db->sql_fetchfield('target_groups_names');
			$this->db->sql_freeresult($result);

			$sql_ary_names = array(
				'target_groups_names'	=> $this->user->lang('GROUPEMAILER_RELANCE_OF', $parent_groups),
			);
			$sql = 'UPDATE ' . $this->campaigns_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary_names) . '
				WHERE campaign_id = ' . (int) $campaign_id;
			$this->db->sql_query($sql);
		}
		else
		{
			$group_ids = array_filter(array_map('intval', explode(',', $campaign['target_groups'])));

			if (empty($group_ids))
			{
				trigger_error($this->user->lang('GROUPEMAILER_FORM_INCOMPLETE') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$sql = 'SELECT group_id, group_name
				FROM ' . GROUPS_TABLE . '
				WHERE group_id IN (' . implode(',', $group_ids) . ')';
			$result = $this->db->sql_query($sql);
			$group_names = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$group_names[] = $this->group_helper->get_name($row['group_name']);
			}
			$this->db->sql_freeresult($result);

			$sql_ary_names = array(
				'target_groups_names'	=> implode(', ', $group_names),
			);
			$sql = 'UPDATE ' . $this->campaigns_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary_names) . '
				WHERE campaign_id = ' . (int) $campaign_id;
			$this->db->sql_query($sql);

			$sql = 'SELECT DISTINCT u.user_id, u.username, u.user_email, u.user_type, u.user_lang,
					u.user_lastvisit, u.user_regdate, u.user_allow_massemail
				FROM ' . USERS_TABLE . ' u, ' . USER_GROUP_TABLE . " ug
				WHERE ug.group_id IN (" . implode(',', $group_ids) . ')
					AND ug.user_id = u.user_id
					AND ug.user_pending = 0
					AND u.user_type <> ' . USER_IGNORE . "
					AND u.user_email <> ''";
			$result = $this->db->sql_query($sql);

			$exclude_deactivated = (bool) $campaign['exclude_deactivated'];
			$exclude_inactive = (bool) $campaign['exclude_inactive'];
			$respect_massemail = (bool) $campaign['respect_massemail'];
			$threshold = time() - ((int) $campaign['inactive_days'] * 86400);

			$ex_deactivated = $ex_inactive = $ex_massemail = 0;
			$ex_bounced = 0;
			$bounced = $this->bounced_users();

			while ($row = $this->db->sql_fetchrow($result))
			{
				// Compte désactivé par un administrateur (non activé / désactivé)
				if ((int) $row['user_type'] === USER_INACTIVE)
				{
					if ($exclude_deactivated)
					{
						$ex_deactivated++;
						continue;
					}
				}

				// Compte dormant : ni visite ni inscription récente
				if ($exclude_inactive
					&& (int) $row['user_lastvisit'] < $threshold
					&& (int) $row['user_regdate'] < $threshold)
				{
					$ex_inactive++;
					continue;
				}

				// Membre ayant refusé les emails de masse dans son profil
				if ($respect_massemail && !(int) $row['user_allow_massemail'])
				{
					$ex_massemail++;
					continue;
				}

				// Adresse ayant échoué de façon répétée sur les campagnes précédentes
				if (isset($bounced[(int) $row['user_id']]))
				{
					$ex_bounced++;
					continue;
				}

				$recipients[] = array(
					'campaign_id'	=> (int) $campaign_id,
					'user_id'		=> (int) $row['user_id'],
					'username'		=> $row['username'],
					'email'			=> $row['user_email'],
					'status'		=> 'pending',
					'confirm_token'	=> $this->generate_token(),
					'user_lang'		=> (string) $row['user_lang'],
				);
			}
			$this->db->sql_freeresult($result);

			$excluded = $ex_deactivated + $ex_inactive + $ex_massemail + $ex_bounced;

			$sql_ary_ex = array(
				'excluded_count'		=> (int) $excluded,
				'excluded_deactivated'	=> (int) $ex_deactivated,
				'excluded_inactive'		=> (int) $ex_inactive,
				'excluded_massemail'	=> (int) $ex_massemail,
			);
			$sql = 'UPDATE ' . $this->campaigns_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary_ex) . '
				WHERE campaign_id = ' . (int) $campaign_id;
			$this->db->sql_query($sql);
		}

		if (empty($recipients))
		{
			$msg = $parent_id > 0 ? 'GROUPEMAILER_NO_UNCONFIRMED' : 'GROUPEMAILER_NO_RECIPIENTS';
			trigger_error($this->user->lang($msg) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->db->sql_multi_insert($this->queue_table, $recipients);

		// Une date future place la campagne en attente : la file est déjà
		// constituée, le cron se contente de lancer l'envoi le moment venu.
		$scheduled = (int) $campaign['scheduled_time'];
		$is_scheduled = ($scheduled > time());

		$sql_ary = array(
			'status'			=> $is_scheduled ? 'scheduled' : 'running',
			'started_time'		=> time(),
			'total_recipients'	=> max(0, count($recipients) - $ex_left_group),
		);
		$sql = 'UPDATE ' . $this->campaigns_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$this->db->sql_query($sql);

		// Les membres sortis des groupes figurent dans la file sans être
		// envoyés : ils ne comptent donc pas comme destinataires.
		$nb_to_send = max(0, count($recipients) - $ex_left_group);

		$msg_start = $is_scheduled
			? $this->user->lang('GROUPEMAILER_CAMPAIGN_SCHEDULED', $nb_to_send, $this->user->format_date($scheduled))
			: $this->user->lang('GROUPEMAILER_CAMPAIGN_STARTED', $nb_to_send);

		if (!empty($ex_confirmed_elsewhere))
		{
			$msg_start .= '<br>' . $this->user->lang('GROUPEMAILER_EXCLUDED_CONFIRMED', (int) $ex_confirmed_elsewhere);
		}

		if (!empty($ex_left_group))
		{
			$msg_start .= '<br>' . $this->user->lang('GROUPEMAILER_EXCLUDED_LEFT_GROUP', (int) $ex_left_group);
		}

		if (!empty($excluded))
		{
			$msg_start .= '<br>' . $this->user->lang('GROUPEMAILER_EXCLUDED_INFO', (int) $excluded);
			$msg_start .= '<br>' . $this->user->lang(
				'GROUPEMAILER_EXCLUDED_BREAKDOWN',
				(int) $ex_deactivated,
				(int) $ex_inactive,
				(int) $ex_massemail
			);

			if ($ex_bounced)
			{
				$msg_start .= '<br>' . $this->user->lang('GROUPEMAILER_EXCLUDED_BOUNCED', (int) $ex_bounced);
			}
		}

		trigger_error($msg_start . adm_back_link($this->u_action));
	}

	protected function campaign_set_status($campaign_id, $status)
	{
		$sql = 'UPDATE ' . $this->campaigns_table . "
			SET status = '" . $this->db->sql_escape($status) . "'
			WHERE campaign_id = " . (int) $campaign_id . "
				AND status IN ('running', 'paused')";
		$this->db->sql_query($sql);
	}

	/**
	 * Tableau de suivi d'une campagne : qui a reçu, qui a confirmé,
	 * avec renvoi possible à tous, aux non-confirmés, ou à un seul membre
	 */
	protected function campaign_details($campaign_id)
	{
		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$campaign = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$campaign)
		{
			trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$nb_pending = $nb_sent = $nb_error = $nb_confirmed = $nb_unsub = 0;

		// Les compteurs portent sur toute la campagne, pas sur la page affichée
		$sql = 'SELECT status, confirmed_time, unsubscribed_time FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			switch ($row['status'])
			{
				case 'pending':	$nb_pending++;	break;
				case 'sent':	$nb_sent++;		break;
				case 'error':	$nb_error++;	break;
			}

			if ((int) $row['confirmed_time'] > 0)
			{
				$nb_confirmed++;
			}

			if ((int) $row['unsubscribed_time'] > 0)
			{
				$nb_unsub++;
			}
		}
		$this->db->sql_freeresult($result);

		$total_rows = $nb_pending + $nb_sent + $nb_error;

		$start = max(0, $this->request->variable('start', 0));
		$per_page = 50;

		$sort_key = $this->request->variable('sk', 'u');
		$sort_dir = $this->request->variable('sd', 'a') === 'd' ? 'DESC' : 'ASC';

		$sort_map = array(
			'd' => 'sent_time',
			'u' => 'username',
			's' => 'status',
			'c' => 'confirmed_time',
		);
		$order_by = isset($sort_map[$sort_key]) ? $sort_map[$sort_key] : $sort_map['u'];

		$bounced = $this->bounced_users();

		$sql = 'SELECT * FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign_id . '
			ORDER BY ' . $order_by . ' ' . $sort_dir;
		$result = $this->db->sql_query_limit($sql, $per_page, $start);

		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		$colours = $this->user_colours(array_column($rows, 'user_id'));

		foreach ($rows as $row)
		{
			$confirmed = ((int) $row['confirmed_time'] > 0);
			$uid = (int) $row['user_id'];

			$this->template->assign_block_vars('recipients', array(
				'USERNAME'		=> $this->username_html($uid, $row['username'], isset($colours[$uid]) ? $colours[$uid] : ''),
				'EMAIL'			=> $row['email'],
				'STATUS_LANG'	=> $this->user->lang('GROUPEMAILER_STATUS_' . strtoupper($row['status'])),
				'STATUS_STYLE'	=> $this->status_style($row['status']),
				'SENT_TIME'		=> (int) $row['sent_time'] > 0 ? $this->user->format_date((int) $row['sent_time']) : '-',
				'ERROR_MESSAGE'	=> $row['error_message'],
				'CONFIRMED_TIME'	=> $confirmed ? $this->user->format_date((int) $row['confirmed_time']) : '',
				'S_UNSUBSCRIBED'	=> ((int) $row['unsubscribed_time'] > 0),
				'UNSUBSCRIBED_TIME'	=> (int) $row['unsubscribed_time'] > 0 ? $this->user->format_date((int) $row['unsubscribed_time']) : '',
				'S_LEFT_GROUP'	=> ($row['status'] === 'left_group'),
				'S_BOUNCED'		=> isset($bounced[(int) $row['user_id']]),
				'FAIL_COUNT'	=> isset($bounced[(int) $row['user_id']]) ? (int) $bounced[(int) $row['user_id']]['fail_count'] : 0,
				'U_RESET_BOUNCE'	=> $this->u_action . "&amp;action=reset_bounce&amp;campaign_id={$campaign_id}&amp;user_id=" . (int) $row['user_id'] . '&amp;hash=' . generate_link_hash('gm_reset_bounce'),

				'S_PENDING'		=> in_array($row['status'], array('pending', 'left_group'), true),
				'S_ERROR'		=> ($row['status'] === 'error'),
				'S_CONFIRMED'	=> $confirmed,

				'U_RESEND_ONE'	=> $this->u_action . "&amp;action=resend_one&amp;campaign_id={$campaign_id}&amp;queue_id={$row['queue_id']}&amp;hash=" . generate_link_hash('gm_resend_one'),
			));
		}

		$this->template->assign_vars(array
		(
			'CAMPAIGN_ID'		=> (int) $campaign_id,
			'CAMPAIGN_TITLE'	=> $campaign['title'],
			'CAMPAIGN_SUBJECT'	=> $campaign['subject'],
			'CAMPAIGN_BODY'		=> $campaign['body'],
			'CAMPAIGN_GROUPS'	=> $campaign['target_groups_names'],
			'STATUS_LANG'		=> $this->user->lang('GROUPEMAILER_STATUS_' . strtoupper($campaign['status'])),

			'NB_TOTAL'		=> $total_rows,
			'NB_PENDING'	=> $nb_pending,
			'NB_SENT'		=> $nb_sent,
			'NB_ERROR'		=> $nb_error,
			'NB_CONFIRMED'	=> $nb_confirmed,
			'NB_UNCONFIRMED'	=> max(0, $nb_sent - $nb_confirmed),
			'NB_UNSUBSCRIBED'	=> $nb_unsub,
			'NB_BOUNCED'		=> count($bounced),
			'BOUNCE_THRESHOLD'	=> max(1, (int) $this->config['groupemailer_bounce_threshold']),

			'S_REQUIRE_CONFIRM'	=> (bool) $campaign['require_confirm'],

			'U_SEND_NOW'			=> $this->u_action . "&amp;action=send_now&amp;campaign_id={$campaign_id}&amp;hash=" . generate_link_hash('gm_send_now'),
			'S_RUNNING'				=> ($campaign['status'] === 'running'),
			'U_RESEND_ALL'			=> $this->u_action . "&amp;action=resend_all&amp;campaign_id={$campaign_id}&amp;hash=" . generate_link_hash('gm_resend_all'),
			'U_RESEND_UNCONFIRMED'	=> $this->u_action . "&amp;action=resend_unconfirmed&amp;campaign_id={$campaign_id}&amp;hash=" . generate_link_hash('gm_resend_unconfirmed'),
			'U_BACK'	=> $this->u_action,
		));

		// Liens de tri et navigation entre les pages, calculés sur l'adresse
		// de cette page de suivi afin de conserver la campagne consultée.
		$action_url = $this->u_action . '&amp;action=details&amp;campaign_id=' . (int) $campaign_id;

		$this->assign_sort_urls($action_url, $sort_key, $sort_dir);
		$this->build_pagination(
			$action_url . '&amp;sk=' . $sort_key . '&amp;sd=' . ($sort_dir === 'ASC' ? 'a' : 'd'),
			$total_rows,
			$per_page,
			$start
		);
	}

	/**
	 * Envoie un exemplaire de test à l'adresse de l'administrateur connecté
	 */
	protected function campaign_send_test($campaign_id)
	{
		global $phpbb_container;

		$email = (string) $this->user->data['user_email'];

		if ($email === '')
		{
			trigger_error($this->user->lang('GROUPEMAILER_TEST_NO_EMAIL') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$task = $phpbb_container->get('verturin.groupemailer.cron.task.send_queue');
		$res = $task->send_test($campaign_id, $email, $this->user->data['username']);

		if (empty($res['sent']))
		{
			trigger_error($this->user->lang('GROUPEMAILER_TEST_FAILED', $res['error']) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		trigger_error($this->user->lang('GROUPEMAILER_TEST_SENT', $email) . adm_back_link($this->u_action));
	}

	/**
	 * Déclenche immédiatement l'envoi d'un lot, sans attendre le cron.
	 * Utile pour tester une campagne ou sur un forum peu fréquenté.
	 */
	protected function campaign_send_now($campaign_id)
	{
		global $phpbb_container;

		$task = $phpbb_container->get('verturin.groupemailer.cron.task.send_queue');
		$res = $task->force_send($campaign_id);

		$back = adm_back_link($this->u_action . '&amp;action=details&amp;campaign_id=' . (int) $campaign_id);

		if (!empty($res['not_running']))
		{
			trigger_error($this->user->lang('GROUPEMAILER_SEND_NOW_NOT_RUNNING') . $back, E_USER_WARNING);
		}

		$msg = $this->user->lang('GROUPEMAILER_SEND_NOW_DONE', (int) $res['sent'], (int) $res['errors']);

		if (!empty($res['last_error']))
		{
			$msg .= '<br>' . $this->user->lang('GROUPEMAILER_SEND_NOW_ERROR', $res['last_error']);
			trigger_error($msg . $back, E_USER_WARNING);
		}

		trigger_error($msg . $back);
	}

	/**
	 * Remet des destinataires en file d'attente pour un nouvel envoi.
	 * $scope : all | unconfirmed | one
	 */
	protected function campaign_resend($campaign_id, $scope, $queue_id)
	{
		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$campaign = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$campaign || $campaign['status'] === 'draft')
		{
			trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		switch ($scope)
		{
			case 'one':
				$where = 'queue_id = ' . (int) $queue_id;
				break;

			case 'unconfirmed':
				$where = "status = 'sent' AND confirmed_time = 0";
				break;

			default:
				$where = '1 = 1';
				break;
		}

		$sql = 'SELECT queue_id, confirm_token FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign_id . '
				AND ' . $where;
		$result = $this->db->sql_query($sql);

		$ids = array();
		$missing_token = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$ids[] = (int) $row['queue_id'];

			if ((int) $campaign['require_confirm'] && $row['confirm_token'] === '')
			{
				$missing_token[] = (int) $row['queue_id'];
			}
		}
		$this->db->sql_freeresult($result);

		if (empty($ids))
		{
			trigger_error($this->user->lang('GROUPEMAILER_RESEND_NOBODY') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		// Un jeton est créé pour les destinataires qui n'en avaient pas encore
		// (campagne passée en confirmation après son premier envoi)
		foreach ($missing_token as $qid)
		{
			$sql = 'UPDATE ' . $this->queue_table . "
				SET confirm_token = '" . $this->db->sql_escape($this->generate_token()) . "'
				WHERE queue_id = " . $qid;
			$this->db->sql_query($sql);
		}

		$sql = 'UPDATE ' . $this->queue_table . "
			SET status = 'pending', error_message = '', sent_time = 0
			WHERE campaign_id = " . (int) $campaign_id . '
				AND ' . $this->db->sql_in_set('queue_id', $ids);
		$this->db->sql_query($sql);

		$this->refresh_counters($campaign_id);

		$sql_ary = array(
			'status'		=> 'running',
			'last_run_time'	=> 0,
		);
		$sql = 'UPDATE ' . $this->campaigns_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$this->db->sql_query($sql);

		trigger_error($this->user->lang('GROUPEMAILER_RESEND_DONE', count($ids)) . adm_back_link($this->u_action . '&amp;action=details&amp;campaign_id=' . (int) $campaign_id));
	}

	/**
	 * Recalcule les compteurs d'une campagne à partir de la file d'attente
	 * (fiable même après plusieurs renvois successifs)
	 */
	protected function refresh_counters($campaign_id)
	{
		$counts = array('sent' => 0, 'error' => 0);

		$sql = 'SELECT status, COUNT(*) AS nb
			FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign_id . '
			GROUP BY status';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$counts[$row['status']] = (int) $row['nb'];
		}
		$this->db->sql_freeresult($result);

		$sql_ary = array(
			'sent_count'	=> (int) $counts['sent'],
			'error_count'	=> (int) $counts['error'],
		);
		$sql = 'UPDATE ' . $this->campaigns_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Couleurs des pastilles de statut, appliquées en ligne car les feuilles
	 * de style d'extension ne sont pas chargées dans toutes les pages de l'ACP
	 */
	protected function status_style($status)
	{
		$map = array(
			'draft'		=> 'background:#e8e8e8;color:#555;',
			'running'	=> 'background:#d9edf7;color:#31708f;',
			'paused'	=> 'background:#fcf8e3;color:#8a6d3b;',
			'scheduled'	=> 'background:#e8e4f3;color:#5b4a8a;',
			'completed'	=> 'background:#dff0d8;color:#3c763d;',
			'cancelled'	=> 'background:#eee;color:#777;text-decoration:line-through;',
			'left_group'	=> 'background:#fdf0e3;color:#8a5a2b;',
			'sent'		=> 'background:#dff0d8;color:#3c763d;',
			'pending'	=> 'background:#e8e8e8;color:#555;',
			'error'		=> 'background:#f2dede;color:#a94442;',
		);

		$style = isset($map[$status]) ? $map[$status] : $map['draft'];

		return $style . 'padding:2px 8px;border-radius:10px;display:inline-block;white-space:nowrap;';
	}

	/**
	 * Jeton unique et non devinable identifiant le couple (campagne, destinataire)
	 */
	protected function generate_token()
	{
		return bin2hex(random_bytes(12));
	}

	/**
	 * Crée un brouillon de relance ciblant les destinataires
	 * qui n'ont pas confirmé la lecture de la campagne d'origine
	 */
	protected function campaign_relance($campaign_id)
	{
		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$campaign = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$campaign || !(int) $campaign['require_confirm'])
		{
			trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign_id . "
				AND status = 'sent'
				AND confirmed_time = 0";
		$result = $this->db->sql_query($sql);
		$nb = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		if (!$nb)
		{
			trigger_error($this->user->lang('GROUPEMAILER_NO_UNCONFIRMED') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql_ary = array(
			'title'				=> $this->user->lang('GROUPEMAILER_RELANCE_TITLE', $campaign['title']),
			'subject'			=> $campaign['subject'],
			'body'				=> $campaign['body'],
			'target_groups'		=> $campaign['target_groups'],
			'rate_count'		=> (int) $campaign['rate_count'],
			'rate_interval'		=> (int) $campaign['rate_interval'],
			'require_confirm'	=> 1,
			'parent_campaign_id'	=> (int) $campaign_id,
			'status'			=> 'draft',
			'created_time'		=> time(),
			'created_by'		=> $this->user->data['user_id'],
		);
		$sql = 'INSERT INTO ' . $this->campaigns_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);

		trigger_error($this->user->lang('GROUPEMAILER_RELANCE_CREATED', $nb) . adm_back_link($this->u_action));
	}

	protected function campaign_retry_errors($campaign_id)
	{
		$sql = 'UPDATE ' . $this->queue_table . "
			SET status = 'pending', error_message = ''
			WHERE campaign_id = " . (int) $campaign_id . "
				AND status = 'error'";
		$this->db->sql_query($sql);

		$sql_ary = array(
			'status'		=> 'running',
			'last_run_time'	=> 0,
			'error_count'	=> 0,
		);
		$sql = 'UPDATE ' . $this->campaigns_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . "
			WHERE campaign_id = " . (int) $campaign_id . "
				AND status IN ('completed', 'running')";
		$this->db->sql_query($sql);

		trigger_error($this->user->lang('GROUPEMAILER_RETRY_DONE') . adm_back_link($this->u_action));
	}

	/**
	 * Arrêt définitif : la campagne est close et les destinataires encore
	 * en attente sont écartés. Ce qui a déjà été envoyé reste dans l'historique.
	 */
	protected function campaign_cancel($campaign_id)
	{
		$sql = 'SELECT status FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$status = $this->db->sql_fetchfield('status');
		$this->db->sql_freeresult($result);

		if (!in_array($status, array('running', 'paused'), true))
		{
			trigger_error($this->user->lang('GROUPEMAILER_CANCEL_IMPOSSIBLE') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign_id . "
				AND status = 'pending'";
		$result = $this->db->sql_query($sql);
		$nb_pending = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		if (confirm_box(true))
		{
			$sql = 'UPDATE ' . $this->queue_table . "
				SET status = 'cancelled'
				WHERE campaign_id = " . (int) $campaign_id . "
					AND status = 'pending'";
			$this->db->sql_query($sql);

			$sql_ary = array(
				'status'			=> 'cancelled',
				'completed_time'	=> time(),
			);
			$sql = 'UPDATE ' . $this->campaigns_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE campaign_id = ' . (int) $campaign_id;
			$this->db->sql_query($sql);

			$this->refresh_counters($campaign_id);

			trigger_error($this->user->lang('GROUPEMAILER_CANCEL_DONE', $nb_pending) . adm_back_link($this->u_action));
		}
		else
		{
			confirm_box(false, $this->user->lang('GROUPEMAILER_CONFIRM_CANCEL', $nb_pending), build_hidden_fields(array(
				'i'				=> $this->request->variable('i', ''),
				'mode'			=> $this->request->variable('mode', ''),
				'action'		=> 'cancel',
				'campaign_id'	=> $campaign_id,
				'hash'			=> $this->request->variable('hash', ''),
			)));
		}
	}

	/**
	 * Suppression d'une campagne : la campagne, sa file d'attente et les
	 * entrées d'historique correspondantes sont retirées définitivement.
	 * La confirmation indique précisément ce qui sera perdu.
	 */
	protected function campaign_delete($campaign_id)
	{
		$sql = 'SELECT title, status FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$campaign = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$campaign)
		{
			trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign_id . "
				AND status IN ('sent', 'error')";
		$result = $this->db->sql_query($sql);
		$nb_done = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		// Une relance encore à l'état de brouillon calcule ses destinataires
		// à partir de cette campagne : la supprimer la rendrait inutilisable.
		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->campaigns_table . '
			WHERE parent_campaign_id = ' . (int) $campaign_id . "
				AND status = 'draft'";
		$result = $this->db->sql_query($sql);
		$nb_children = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		if (confirm_box(true))
		{
			$sql = 'DELETE FROM ' . $this->campaigns_table . '
				WHERE campaign_id = ' . (int) $campaign_id;
			$this->db->sql_query($sql);

			$sql = 'DELETE FROM ' . $this->queue_table . '
				WHERE campaign_id = ' . (int) $campaign_id;
			$this->db->sql_query($sql);

			trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_DELETED') . adm_back_link($this->u_action));
		}
		else
		{
			$msg = $nb_done > 0
				? $this->user->lang('GROUPEMAILER_CONFIRM_DELETE_SENT', $campaign['title'], $nb_done)
				: $this->user->lang('GROUPEMAILER_CONFIRM_DELETE', $campaign['title']);

			if ($nb_children > 0)
			{
				$msg .= '<br><br>' . $this->user->lang('GROUPEMAILER_CONFIRM_DELETE_CHILDREN', $nb_children);
			}

			confirm_box(false, $msg, build_hidden_fields(array(
				'i'				=> $this->request->variable('i', ''),
				'mode'			=> $this->request->variable('mode', ''),
				'action'		=> 'delete',
				'campaign_id'	=> $campaign_id,
			)));
		}
	}

	/**
	 * Page de diagnostic : montre exactement l'état réel de l'extension
	 * (schéma, réglages, file d'attente, tâche cron, configuration email)
	 */
	protected function diagnostics()
	{
		global $phpbb_container;

		$line = function($label, $value, $state) {
			$this->template->assign_block_vars('diag', array(
				'LABEL'	=> $label,
				'VALUE'	=> $value,
				'S_OK'		=> ($state === 'ok'),
				'S_WARN'	=> ($state === 'warn'),
				'S_FAIL'	=> ($state === 'fail'),
			));
		};

		// --- Schéma de la base ---
		$expected = array(
			$this->campaigns_table => array('require_confirm', 'parent_campaign_id', 'exclude_inactive', 'inactive_days', 'respect_massemail', 'exclude_deactivated', 'email_mode'),
			$this->queue_table => array('confirm_token', 'confirmed_time', 'user_lang', 'unsubscribed_time'),
		);

		try
		{
			$tools = $phpbb_container->get('dbal.tools');
			$missing = array();

			foreach ($expected as $table => $cols)
			{
				foreach ($cols as $col)
				{
					if (!$tools->sql_column_exists($table, $col))
					{
						$missing[] = $col;
					}
				}
			}

			if (!$tools->sql_table_exists($this->bounces_table))
			{
				$missing[] = $this->user->lang('GROUPEMAILER_DIAG_TABLE_BOUNCES');
			}

			$line(
				$this->user->lang('GROUPEMAILER_DIAG_SCHEMA'),
				empty($missing) ? $this->user->lang('GROUPEMAILER_DIAG_SCHEMA_OK') : $this->user->lang('GROUPEMAILER_DIAG_SCHEMA_MISSING', implode(', ', $missing)),
				empty($missing) ? 'ok' : 'fail'
			);
		}
		catch (\Throwable $e)
		{
			$line($this->user->lang('GROUPEMAILER_DIAG_SCHEMA'), $e->getMessage(), 'warn');
		}

		// --- Tâche cron : le conteneur peut-il la construire ? ---
		$task = null;

		try
		{
			$task = $phpbb_container->get('verturin.groupemailer.cron.task.send_queue');
			$line($this->user->lang('GROUPEMAILER_DIAG_SERVICE'), $this->user->lang('GROUPEMAILER_DIAG_SERVICE_OK'), 'ok');
		}
		catch (\Throwable $e)
		{
			$line($this->user->lang('GROUPEMAILER_DIAG_SERVICE'), get_class($e) . ': ' . $e->getMessage(), 'fail');
		}

		if ($task)
		{
			$name = method_exists($task, 'get_name') ? (string) $task->get_name() : '';
			$line(
				$this->user->lang('GROUPEMAILER_DIAG_TASK_NAME'),
				$name !== '' ? $name : $this->user->lang('GROUPEMAILER_DIAG_TASK_NAME_EMPTY'),
				$name !== '' ? 'ok' : 'fail'
			);

			try
			{
				$ready = $task->is_runnable() && $task->should_run();
				$line(
					$this->user->lang('GROUPEMAILER_DIAG_TASK_READY'),
					$ready ? $this->user->lang('GROUPEMAILER_DIAG_YES') : $this->user->lang('GROUPEMAILER_DIAG_TASK_NOT_READY'),
					$ready ? 'ok' : 'warn'
				);
			}
			catch (\Throwable $e)
			{
				$line($this->user->lang('GROUPEMAILER_DIAG_TASK_READY'), get_class($e) . ': ' . $e->getMessage(), 'fail');
			}
		}

		// --- La tâche est-elle visible par le gestionnaire de cron de phpBB ? ---
		try
		{
			$manager = $phpbb_container->get('cron.manager');
			$found = $manager->find_task('verturin.groupemailer.cron.task.send_queue');
			$line(
				$this->user->lang('GROUPEMAILER_DIAG_CRON_FOUND'),
				$found ? $this->user->lang('GROUPEMAILER_DIAG_YES') : $this->user->lang('GROUPEMAILER_DIAG_CRON_NOT_FOUND'),
				$found ? 'ok' : 'fail'
			);
		}
		catch (\Throwable $e)
		{
			$line($this->user->lang('GROUPEMAILER_DIAG_CRON_FOUND'), get_class($e) . ': ' . $e->getMessage(), 'fail');
		}

		// --- Configuration phpBB du cron et de l'email ---
		$sys_cron = !empty($this->config['use_system_cron']);
		$line(
			$this->user->lang('GROUPEMAILER_DIAG_SYSTEM_CRON'),
			$sys_cron ? $this->user->lang('GROUPEMAILER_DIAG_SYSTEM_CRON_ON') : $this->user->lang('GROUPEMAILER_DIAG_SYSTEM_CRON_OFF'),
			$sys_cron ? 'fail' : 'ok'
		);

		$mail_on = !empty($this->config['email_enable']);
		$line(
			$this->user->lang('GROUPEMAILER_DIAG_EMAIL_ENABLE'),
			$mail_on ? $this->user->lang('GROUPEMAILER_DIAG_YES') : $this->user->lang('GROUPEMAILER_DIAG_EMAIL_DISABLED'),
			$mail_on ? 'ok' : 'fail'
		);

		$line(
			$this->user->lang('GROUPEMAILER_DIAG_MAIL_METHOD'),
			!empty($this->config['smtp_delivery'])
				? $this->user->lang('GROUPEMAILER_DIAG_MAIL_SMTP', (string) $this->config['smtp_host'])
				: $this->user->lang('GROUPEMAILER_DIAG_MAIL_PHP'),
			'ok'
		);

		$notify_id = (int) $this->config['groupemailer_unsub_notify_id'];
		$notify_name = '';

		if ($notify_id)
		{
			$sql = 'SELECT username, user_email FROM ' . USERS_TABLE . '
				WHERE user_id = ' . $notify_id;
			$result = $this->db->sql_query($sql);
			$notify_row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($notify_row)
			{
				$modes = array();

				if (!empty($this->config['groupemailer_unsub_notify_email']))
				{
					$modes[] = $this->user->lang('GROUPEMAILER_UNSUB_NOTIFY_BY_EMAIL');
				}

				if (!empty($this->config['groupemailer_unsub_notify_pm']))
				{
					$modes[] = $this->user->lang('GROUPEMAILER_UNSUB_NOTIFY_BY_PM');
				}

				$notify_name = $notify_row['username'] . ' <' . $notify_row['user_email'] . '> — '
					. ($modes ? implode(', ', $modes) : $this->user->lang('GROUPEMAILER_DIAG_UNSUB_NO_MODE'));
			}
		}

		$line(
			$this->user->lang('GROUPEMAILER_DIAG_UNSUB_NOTIFY'),
			$notify_name !== '' ? $notify_name : $this->user->lang('GROUPEMAILER_DIAG_UNSUB_NOBODY'),
			$notify_name !== '' ? 'ok' : 'warn'
		);

		$line(
			$this->user->lang('GROUPEMAILER_DIAG_LIST_UNSUB'),
			!empty($this->config['groupemailer_list_unsubscribe'])
				? $this->user->lang('GROUPEMAILER_DIAG_LIST_UNSUB_ON')
				: $this->user->lang('GROUPEMAILER_DIAG_LIST_UNSUB_OFF'),
			'ok'
		);

		$line(
			$this->user->lang('GROUPEMAILER_DIAG_SENDER'),
			($this->config['groupemailer_from_email'] !== '' ? $this->config['groupemailer_from_email'] : (string) $this->config['board_contact']),
			'ok'
		);

		// --- Contenu réel de la file d'attente ---
		$states = array();
		$sql = 'SELECT status, COUNT(*) AS nb FROM ' . $this->queue_table . ' GROUP BY status';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$states[] = $row['status'] . ' = ' . (int) $row['nb'];
		}
		$this->db->sql_freeresult($result);

		$line(
			$this->user->lang('GROUPEMAILER_DIAG_QUEUE'),
			empty($states) ? $this->user->lang('GROUPEMAILER_DIAG_QUEUE_EMPTY') : implode(' | ', $states),
			'ok'
		);

		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->campaigns_table . " WHERE status = 'running'";
		$result = $this->db->sql_query($sql);
		$nb_running = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		$line($this->user->lang('GROUPEMAILER_DIAG_RUNNING'), (string) $nb_running, $nb_running > 0 ? 'ok' : 'warn');

		$base = generate_board_url();
		$sample = !empty($this->config['enable_mod_rewrite'])
			? $base . '/groupemailer/confirm/EXEMPLE'
			: $base . '/app.php/groupemailer/confirm/EXEMPLE';
		$line($this->user->lang('GROUPEMAILER_DIAG_LINK'), $sample, 'ok');

		$this->template->assign_vars(array(
			'GROUPEMAILER_VERSION'	=> '2.33.1',
			'U_CRON_DIRECT'	=> generate_board_url() . '/app.php/cron/verturin.groupemailer.cron.task.send_queue',
		));
	}

	/**
	 * Historique détaillé : chaque email réellement envoyé, avec titre, contenu, groupe(s) et destinataire
	 */
	protected function history($id, $mode)
	{
		$start = max(0, $this->request->variable('start', 0));
		$per_page = 50;

		$sort_key = $this->request->variable('sk', 'd');
		$sort_dir = $this->request->variable('sd', 'd') === 'a' ? 'ASC' : 'DESC';

		$sort_map = array(
			'd' => 'q.queue_id',
			'u' => 'q.username',
			's' => 'q.status',
			'c' => 'q.confirmed_time',
			't' => 'c.title',
			'o' => 'c.subject',
			'g' => 'c.target_groups_names',
		);
		$order_by = isset($sort_map[$sort_key]) ? $sort_map[$sort_key] : $sort_map['d'];

		$sql = 'SELECT COUNT(*) AS total FROM ' . $this->queue_table . "
			WHERE status IN ('sent', 'error')";
		$result = $this->db->sql_query($sql);
		$total = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->queue_table . '
			WHERE confirmed_time > 0';
		$result = $this->db->sql_query($sql);
		$total_confirmed = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		$bounced = $this->bounced_users();

		$sql = 'SELECT q.queue_id, q.user_id, q.username, q.email, q.status, q.sent_time, q.error_message,
				q.confirmed_time, q.unsubscribed_time, c.require_confirm,
				c.title, c.subject, c.body, c.target_groups_names, c.total_recipients
			FROM ' . $this->queue_table . ' q, ' . $this->campaigns_table . " c
			WHERE q.campaign_id = c.campaign_id
				AND q.status IN ('sent', 'error')
			ORDER BY " . $order_by . ' ' . $sort_dir;
		$result = $this->db->sql_query_limit($sql, $per_page, $start);

		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		$colours = $this->user_colours(array_column($rows, 'user_id'));

		foreach ($rows as $row)
		{
			$uid = (int) $row['user_id'];

			$this->template->assign_block_vars('history', array(
				'CAMPAIGN_TITLE'	=> $row['title'],
				'SUBJECT'			=> $row['subject'],
				'BODY'				=> $row['body'],
				'GROUPS'			=> $row['target_groups_names'],
				'TOTAL_RECIPIENTS'	=> (int) $row['total_recipients'],
				'RECIPIENT'			=> $this->username_html($uid, $row['username'], isset($colours[$uid]) ? $colours[$uid] : ''),
				'EMAIL'				=> $row['email'],
				'SENT_TIME'			=> $row['sent_time'] ? $this->user->format_date($row['sent_time']) : '-',
				'STATUS_LANG'		=> $this->user->lang('GROUPEMAILER_STATUS_' . strtoupper($row['status'])),
				'STATUS_STYLE'		=> $this->status_style($row['status']),
				'ERROR_MESSAGE'		=> $row['error_message'],
				'S_ERROR'			=> ($row['status'] === 'error'),
				'S_REQUIRE_CONFIRM'	=> (bool) $row['require_confirm'],
				'S_CONFIRMED'		=> ((int) $row['confirmed_time'] > 0),
				'CONFIRMED_TIME'	=> (int) $row['confirmed_time'] > 0 ? $this->user->format_date((int) $row['confirmed_time']) : '',
				'S_UNSUBSCRIBED'	=> ((int) $row['unsubscribed_time'] > 0),
				'UNSUBSCRIBED_TIME'	=> (int) $row['unsubscribed_time'] > 0 ? $this->user->format_date((int) $row['unsubscribed_time']) : '',
				'S_LEFT_GROUP'	=> ($row['status'] === 'left_group'),
				'S_BOUNCED'		=> isset($bounced[(int) $row['user_id']]),
				'FAIL_COUNT'	=> isset($bounced[(int) $row['user_id']]) ? (int) $bounced[(int) $row['user_id']]['fail_count'] : 0,
				'U_RESET_BOUNCE'	=> $this->u_action . '&amp;action=reset_bounce&amp;user_id=' . (int) $row['user_id'] . '&amp;hash=' . generate_link_hash('gm_reset_bounce'),
			));
		}

		$base = $this->u_action . '&amp;sk=' . $sort_key . '&amp;sd=' . ($sort_dir === 'ASC' ? 'a' : 'd');
		$this->build_pagination($base, $total, $per_page, $start);
		$this->assign_sort_urls($this->u_action, $sort_key, $sort_dir, array('d', 'u', 's', 'c', 't', 'o', 'g'));

		$this->template->assign_vars(array(
			'GROUPEMAILER_TOTAL_SENT'		=> $total,
			'GROUPEMAILER_TOTAL_CONFIRMED'	=> $total_confirmed,
		));
	}

	/**
	 * Couleur de groupe des membres concernés, en une seule requête.
	 * La couleur courante est reprise du profil plutôt que figée à l'envoi :
	 * un changement de groupe se reflète ainsi dans les tableaux.
	 */
	protected function user_colours(array $user_ids)
	{
		$ids = array_filter(array_map('intval', array_unique($user_ids)));

		if (empty($ids))
		{
			return array();
		}

		$colours = array();

		$sql = 'SELECT user_id, user_colour
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_id', $ids);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$colours[(int) $row['user_id']] = (string) $row['user_colour'];
		}
		$this->db->sql_freeresult($result);

		return $colours;
	}

	/**
	 * Pseudonyme mis en forme par phpBB : couleur du groupe et lien vers
	 * le profil, en tenant compte des comptes supprimés
	 */
	protected function username_html($user_id, $username, $colour)
	{
		// get_username_string() appartient à functions_content.php, qui n'est
		// pas chargé d'office dans l'ACP contrairement au forum.
		if (!function_exists('get_username_string'))
		{
			$file = $this->root_path . 'includes/functions_content.' . $this->php_ext;

			if (file_exists($file))
			{
				include_once($file);
			}
		}

		if (function_exists('get_username_string'))
		{
			return get_username_string('full', (int) $user_id, (string) $username, (string) $colour);
		}

		// Repli : couleur et lien reconstruits à l'identique
		$url = append_sid(generate_board_url() . '/memberlist.' . $this->php_ext, 'mode=viewprofile&amp;u=' . (int) $user_id);
		$style = $colour !== '' ? ' style="color:#' . $colour . ';"' : '';

		return '<a href="' . $url . '"' . $style . '>' . $username . '</a>';
	}

	/**
	 * Liens de tri des colonnes : un clic sur la colonne active inverse le sens
	 */
	protected function assign_sort_urls($base, $sort_key, $sort_dir, $keys = array('d', 'u', 's', 'c'))
	{
		$next = ($sort_dir === 'ASC') ? 'd' : 'a';

		foreach ($keys as $k)
		{
			$dir = ($k === $sort_key) ? $next : 'd';

			$this->template->assign_var(
				'U_SORT_' . strtoupper($k),
				$base . '&amp;sk=' . $k . '&amp;sd=' . $dir
			);
		}

		$this->template->assign_vars(array(
			'S_SORT_KEY'	=> $sort_key,
			'S_SORT_ASC'	=> ($sort_dir === 'ASC'),
		));
	}

	/**
	 * Barre de navigation entre les pages, au format standard de phpBB
	 */
	protected function build_pagination($base_url, $total, $per_page, $start)
	{
		global $phpbb_container;

		try
		{
			$pagination = $phpbb_container->get('pagination');
			$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total, $per_page, $start);
		}
		catch (\Throwable $e)
		{
			// Rien : la liste reste utilisable sans barre de navigation
		}

		$this->template->assign_vars(array(
			'TOTAL_ROWS'	=> $total,
			'S_PAGINATED'	=> ($total > $per_page),
		));
	}

	/**
	 * Membres dont les envois échouent de façon répétée, au-delà du seuil réglé
	 */
	protected function bounced_users()
	{
		$threshold = max(1, (int) $this->config['groupemailer_bounce_threshold']);
		$list = array();

		$sql = 'SELECT user_id, email, username, fail_count, last_fail_time, last_error
			FROM ' . $this->bounces_table . '
			WHERE fail_count >= ' . $threshold;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$list[(int) $row['user_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $list;
	}

	/**
	 * Réarme une adresse écartée, pour la réintégrer aux prochains envois
	 */
	protected function reset_bounce($user_id, $campaign_id)
	{
		if (!$user_id)
		{
			trigger_error($this->user->lang('GROUPEMAILER_BOUNCE_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'DELETE FROM ' . $this->bounces_table . '
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);

		$back = $campaign_id
			? $this->u_action . '&amp;action=details&amp;campaign_id=' . (int) $campaign_id
			: $this->u_action;

		trigger_error($this->user->lang('GROUPEMAILER_BOUNCE_RESET_DONE') . adm_back_link($back));
	}

	/**
	 * Convertit la date saisie dans le formulaire en horodatage.
	 * Le fuseau de l'administrateur est appliqué, afin que l'heure indiquée
	 * soit bien celle qu'il voit dans l'ACP.
	 */
	protected function parse_schedule($value)
	{
		$value = trim((string) $value);

		if ($value === '')
		{
			return 0;
		}

		try
		{
			$tz = $this->user->create_datetime()->getTimezone();
			$date = new \DateTime($value, $tz);
			$time = (int) $date->format('U');
		}
		catch (\Throwable $e)
		{
			return 0;
		}

		// Une date déjà passée n'a pas de sens : l'envoi démarre alors
		// au prochain passage du cron, ce qui est le comportement attendu.
		return $time > 0 ? $time : 0;
	}

	/**
	 * Duplique une campagne : le message et tous ses réglages sont repris
	 * dans un nouveau brouillon, sans destinataires ni compteurs.
	 */
	protected function campaign_duplicate($campaign_id)
	{
		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$campaign = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$campaign)
		{
			trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql_ary = array(
			'title'				=> $this->user->lang('GROUPEMAILER_COPY_TITLE', $campaign['title']),
			'subject'			=> $campaign['subject'],
			'body'				=> $campaign['body'],
			'target_groups'		=> $campaign['target_groups'],
			'rate_count'		=> (int) $campaign['rate_count'],
			'rate_interval'		=> (int) $campaign['rate_interval'],
			'require_confirm'	=> (int) $campaign['require_confirm'],
			'email_mode'		=> $campaign['email_mode'],
			'exclude_inactive'	=> (int) $campaign['exclude_inactive'],
			'inactive_days'		=> (int) $campaign['inactive_days'],
			'respect_massemail'	=> (int) $campaign['respect_massemail'],
			'exclude_deactivated'	=> (int) $campaign['exclude_deactivated'],

			// Rien n'est repris de l'exécution : ni destinataires, ni statuts,
			// ni rattachement à une campagne d'origine.
			'parent_campaign_id'	=> 0,
			'status'			=> 'draft',
			'created_time'		=> time(),
			'created_by'		=> (int) $this->user->data['user_id'],
		);

		$sql = 'INSERT INTO ' . $this->campaigns_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);

		trigger_error($this->user->lang('GROUPEMAILER_DUPLICATE_DONE') . adm_back_link($this->u_action));
	}

	/**
	 * Annule une programmation : la campagne redevient un brouillon ordinaire
	 * et sa file d'attente est vidée.
	 */
	protected function campaign_unschedule($campaign_id)
	{
		$sql = 'SELECT status FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$status = $this->db->sql_fetchfield('status');
		$this->db->sql_freeresult($result);

		if ($status !== 'scheduled')
		{
			trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'DELETE FROM ' . $this->queue_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$this->db->sql_query($sql);

		$sql_ary = array(
			'status'			=> 'draft',
			'scheduled_time'	=> 0,
			'total_recipients'	=> 0,
			'sent_count'		=> 0,
			'error_count'		=> 0,
		);
		$sql = 'UPDATE ' . $this->campaigns_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$this->db->sql_query($sql);

		trigger_error($this->user->lang('GROUPEMAILER_UNSCHEDULE_DONE') . adm_back_link($this->u_action));
	}

	/**
	 * Tableau de bord : vue d'ensemble des envois et comparaison des campagnes
	 */
	protected function dashboard()
	{
		$stats = array(
			'campaigns'	=> 0,
			'sent'		=> 0,
			'errors'	=> 0,
			'confirmed'	=> 0,
			'unsub'		=> 0,
		);

		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->campaigns_table;
		$result = $this->db->sql_query($sql);
		$stats['campaigns'] = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT status, COUNT(*) AS nb
			FROM ' . $this->queue_table . '
			GROUP BY status';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['status'] === 'sent')
			{
				$stats['sent'] = (int) $row['nb'];
			}
			elseif ($row['status'] === 'error')
			{
				$stats['errors'] = (int) $row['nb'];
			}
		}
		$this->db->sql_freeresult($result);

		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->queue_table . '
			WHERE confirmed_time > 0';
		$result = $this->db->sql_query($sql);
		$stats['confirmed'] = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT COUNT(*) AS nb FROM ' . $this->queue_table . '
			WHERE unsubscribed_time > 0';
		$result = $this->db->sql_query($sql);
		$stats['unsub'] = (int) $this->db->sql_fetchfield('nb');
		$this->db->sql_freeresult($result);

		$bounced = $this->bounced_users();

		// Confirmations par campagne, pour le comparatif
		$confirmed_by = array();
		$sql = 'SELECT campaign_id, COUNT(*) AS nb
			FROM ' . $this->queue_table . '
			WHERE confirmed_time > 0
			GROUP BY campaign_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$confirmed_by[(int) $row['campaign_id']] = (int) $row['nb'];
		}
		$this->db->sql_freeresult($result);

		$unsub_by = array();
		$sql = 'SELECT campaign_id, COUNT(*) AS nb
			FROM ' . $this->queue_table . '
			WHERE unsubscribed_time > 0
			GROUP BY campaign_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$unsub_by[(int) $row['campaign_id']] = (int) $row['nb'];
		}
		$this->db->sql_freeresult($result);

		$rates = array();

		$sql = 'SELECT campaign_id, title, status, require_confirm, sent_count, error_count, created_time
			FROM ' . $this->campaigns_table . "
			WHERE status <> 'draft'
			ORDER BY created_time DESC";
		$result = $this->db->sql_query_limit($sql, 20);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$cid = (int) $row['campaign_id'];
			$sent = (int) $row['sent_count'];
			$conf = isset($confirmed_by[$cid]) ? $confirmed_by[$cid] : 0;
			$rate = ($sent > 0) ? (int) round($conf * 100 / $sent) : 0;

			if ((int) $row['require_confirm'] && $sent > 0)
			{
				$rates[] = $rate;
			}

			$this->template->assign_block_vars('campaigns', array(
				'CAMPAIGN_ID'	=> $cid,
				'TITLE'			=> $row['title'],
				'STATUS_LANG'	=> $this->user->lang('GROUPEMAILER_STATUS_' . strtoupper($row['status'])),
				'STATUS_STYLE'	=> $this->status_style($row['status']),
				'SENT'			=> $sent,
				'ERRORS'		=> (int) $row['error_count'],
				'CONFIRMED'		=> $conf,
				'RATE'			=> $rate,
				'UNSUB'			=> isset($unsub_by[$cid]) ? $unsub_by[$cid] : 0,
				'S_TRACKED'		=> ((bool) $row['require_confirm'] && $sent > 0),
				'CREATED_TIME'	=> $row['created_time'] ? $this->user->format_date((int) $row['created_time']) : '-',
				'U_DETAILS'		=> $this->u_action_campaigns() . '&amp;action=details&amp;campaign_id=' . $cid,
			));
		}
		$this->db->sql_freeresult($result);

		$avg = $rates ? (int) round(array_sum($rates) / count($rates)) : 0;

		$this->template->assign_vars(array(
			'NB_CAMPAIGNS'	=> $stats['campaigns'],
			'NB_SENT'		=> $stats['sent'],
			'NB_ERRORS'		=> $stats['errors'],
			'NB_CONFIRMED'	=> $stats['confirmed'],
			'NB_UNSUB'		=> $stats['unsub'],
			'NB_BOUNCED'	=> count($bounced),
			'GLOBAL_RATE'	=> $stats['sent'] > 0 ? (int) round($stats['confirmed'] * 100 / $stats['sent']) : 0,
			'AVERAGE_RATE'	=> $avg,
			'S_HAS_RATES'	=> !empty($rates),
		));
	}

	/**
	 * Adresse de la page des campagnes, depuis n'importe quel mode
	 */
	protected function u_action_campaigns()
	{
		return str_replace('mode=dashboard', 'mode=campaigns', $this->u_action);
	}

	/**
	 * Administrateurs susceptibles de recevoir les notifications :
	 * fondateurs et membres du groupe Administrateurs.
	 */
	protected function assign_admin_list($selected)
	{
		$sql = 'SELECT DISTINCT u.user_id, u.username, u.user_email
			FROM ' . USERS_TABLE . ' u
			LEFT JOIN ' . USER_GROUP_TABLE . ' ug ON (ug.user_id = u.user_id)
			LEFT JOIN ' . GROUPS_TABLE . " g ON (g.group_id = ug.group_id)
				WHERE u.user_type = " . USER_FOUNDER . "
					OR (g.group_name = 'ADMINISTRATORS' AND ug.user_pending = 0)
			ORDER BY u.username ASC";
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('admins', array(
				'USER_ID'		=> (int) $row['user_id'],
				'USERNAME'		=> $row['username'],
				'S_NO_EMAIL'	=> ($row['user_email'] === ''),
				'S_SELECTED'	=> ((int) $row['user_id'] === (int) $selected),
			));
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Membres désabonnés à la suite d'un envoi : qui, quand, et depuis quelle
	 * campagne. Le réabonnement se fait sur simple demande du membre.
	 */
	protected function unsubscribes()
	{
		$sub_action = $this->request->variable('action', '');

		if ($sub_action === 'resubscribe')
		{
			$this->resubscribe_user($this->request->variable('user_id', 0));
			return;
		}

		if ($sub_action === 'resend_unsub')
		{
			$this->resend_unsub_confirmation($this->request->variable('queue_id', 0));
			return;
		}

		$start = max(0, $this->request->variable('start', 0));
		$per_page = 50;

		$sort_key = $this->request->variable('sk', 'd');
		$sort_dir = $this->request->variable('sd', 'd') === 'a' ? 'ASC' : 'DESC';

		$sort_map = array(
			'd' => 'q.unsubscribed_time',
			'u' => 'q.username',
			't' => 'c.title',
		);
		$order_by = isset($sort_map[$sort_key]) ? $sort_map[$sort_key] : $sort_map['d'];

		// Un membre peut s'être désabonné depuis plusieurs campagnes : seul
		// son désabonnement le plus récent est listé.
		$sql = 'SELECT COUNT(DISTINCT user_id) AS total
			FROM ' . $this->queue_table . '
			WHERE unsubscribed_time > 0';
		$result = $this->db->sql_query($sql);
		$total = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT q.queue_id, q.user_id, q.username, q.email, q.unsubscribed_time, q.unsub_resent_time, c.title, c.campaign_id
			FROM ' . $this->queue_table . ' q, ' . $this->campaigns_table . ' c
			WHERE q.campaign_id = c.campaign_id
				AND q.unsubscribed_time > 0
			ORDER BY ' . $order_by . ' ' . $sort_dir;
		$result = $this->db->sql_query_limit($sql, $per_page * 4, $start);

		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$uid = (int) $row['user_id'];

			// Une seule ligne par membre, celle du désabonnement le plus récent
			if (!isset($rows[$uid]) || (int) $row['unsubscribed_time'] > (int) $rows[$uid]['unsubscribed_time'])
			{
				$rows[$uid] = $row;
			}
		}
		$this->db->sql_freeresult($result);

		$rows = array_slice($rows, 0, $per_page, true);
		$colours = $this->user_colours(array_keys($rows));

		// Les membres réabonnés depuis leur profil apparaissent différemment
		$allow = array();

		if ($rows)
		{
			$sql = 'SELECT user_id, user_allow_massemail
				FROM ' . USERS_TABLE . '
				WHERE ' . $this->db->sql_in_set('user_id', array_keys($rows));
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$allow[(int) $row['user_id']] = (int) $row['user_allow_massemail'];
			}
			$this->db->sql_freeresult($result);
		}

		foreach ($rows as $uid => $row)
		{
			$this->template->assign_block_vars('unsubs', array(
				'USERNAME'		=> $this->username_html($uid, $row['username'], isset($colours[$uid]) ? $colours[$uid] : ''),
				'EMAIL'			=> $row['email'],
				'CAMPAIGN'		=> $row['title'],
				'UNSUB_TIME'	=> $this->user->format_date((int) $row['unsubscribed_time']),
				'S_RESUBSCRIBED'	=> !empty($allow[$uid]),
				'U_DETAILS'		=> $this->u_action_campaigns() . '&amp;action=details&amp;campaign_id=' . (int) $row['campaign_id'],
				'U_RESUBSCRIBE'	=> $this->u_action . '&amp;action=resubscribe&amp;user_id=' . $uid . '&amp;hash=' . generate_link_hash('gm_resubscribe'),
				'U_RESEND'		=> $this->u_action . '&amp;action=resend_unsub&amp;queue_id=' . (int) $row['queue_id'] . '&amp;hash=' . generate_link_hash('gm_resend_unsub'),
				'RESENT_TIME'	=> (int) $row['unsub_resent_time'] > 0 ? $this->user->format_date((int) $row['unsub_resent_time']) : '',
			));
		}

		$base = $this->u_action . '&amp;sk=' . $sort_key . '&amp;sd=' . ($sort_dir === 'ASC' ? 'a' : 'd');
		$this->build_pagination($base, $total, $per_page, $start);
		$this->assign_sort_urls($this->u_action, $sort_key, $sort_dir, array('d', 'u', 't'));

		$this->template->assign_vars(array(
			'NB_UNSUBS'	=> $total,
		));
	}

	/**
	 * Renvoie l'accusé de désabonnement à un membre, et mémorise la date
	 * de ce renvoi.
	 */
	protected function resend_unsub_confirmation($queue_id)
	{
		global $phpbb_container;

		if (!$queue_id)
		{
			trigger_error($this->user->lang('GROUPEMAILER_BOUNCE_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'SELECT q.queue_id, q.user_id, q.username, q.email, q.user_lang, q.unsubscribed_time, c.title
			FROM ' . $this->queue_table . ' q, ' . $this->campaigns_table . '  c
			WHERE q.campaign_id = c.campaign_id
				AND q.queue_id = ' . (int) $queue_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row || !(int) $row['unsubscribed_time'])
		{
			trigger_error($this->user->lang('GROUPEMAILER_BOUNCE_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$task = $phpbb_container->get('verturin.groupemailer.cron.task.send_queue');

		// L'accusé reprend la date du désabonnement, non celle du renvoi :
		// c'est bien la première qui fait foi pour le membre. La copie à
		// l'administrateur suit, comme lors du désabonnement initial.
		$sent = $task->send_unsub_notifications($row, (int) $row['unsubscribed_time'], true);

		if (!$sent)
		{
			trigger_error($this->user->lang('GROUPEMAILER_UNSUB_RESEND_FAILED') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$now = time();

		$sql = 'UPDATE ' . $this->queue_table . '
			SET unsub_resent_time = ' . $now . '
			WHERE queue_id = ' . (int) $queue_id;
		$this->db->sql_query($sql);

		trigger_error($this->user->lang('GROUPEMAILER_UNSUB_RESEND_DONE', $row['email'], $this->user->format_date($now)) . adm_back_link($this->u_action));
	}

	/**
	 * Réabonne un membre depuis l'ACP, à sa demande
	 */
	protected function resubscribe_user($user_id)
	{
		if (!$user_id)
		{
			trigger_error($this->user->lang('GROUPEMAILER_BOUNCE_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET user_allow_massemail = 1
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);

		trigger_error($this->user->lang('GROUPEMAILER_RESUB_ADMIN_DONE') . adm_back_link($this->u_action));
	}

	/**
	 * Sauvegarde et restauration des campagnes.
	 * L'export produit un fichier JSON contenant les campagnes et, au choix,
	 * la file d'attente correspondante (destinataires, statuts d'envoi et
	 * confirmations de lecture). La restauration recrée les campagnes sous
	 * de nouveaux identifiants, sans jamais écraser l'existant.
	 */
	protected function backup()
	{
		$action = $this->request->variable('action', '');

		if ($action === 'export')
		{
			$this->backup_export();
			return;
		}

		if ($action === 'import')
		{
			$this->backup_import();
			return;
		}

		// Nombre d'entrées de file par campagne, pour information
		$queue_counts = array();
		$sql = 'SELECT campaign_id, COUNT(*) AS nb
			FROM ' . $this->queue_table . '
			GROUP BY campaign_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$queue_counts[(int) $row['campaign_id']] = (int) $row['nb'];
		}
		$this->db->sql_freeresult($result);

		$nb_campaigns = 0;
		$nb_queue = array_sum($queue_counts);

		$sql = 'SELECT campaign_id, title, subject, status, created_time, total_recipients
			FROM ' . $this->campaigns_table . '
			ORDER BY created_time DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$cid = (int) $row['campaign_id'];
			$nb_campaigns++;

			$this->template->assign_block_vars('campaigns', array(
				'CAMPAIGN_ID'	=> $cid,
				'TITLE'			=> $row['title'],
				'SUBJECT'		=> $row['subject'],
				'STATUS_LANG'	=> $this->user->lang('GROUPEMAILER_STATUS_' . strtoupper($row['status'])),
				'STATUS_STYLE'	=> $this->status_style($row['status']),
				'CREATED_TIME'	=> $row['created_time'] ? $this->user->format_date((int) $row['created_time']) : '-',
				'NB_QUEUE'		=> isset($queue_counts[$cid]) ? $queue_counts[$cid] : 0,
			));
		}
		$this->db->sql_freeresult($result);

		add_form_key('acp_groupemailer_backup');

		$this->template->assign_vars(array(
			'NB_CAMPAIGNS'	=> $nb_campaigns,
			'NB_QUEUE'		=> $nb_queue,
			'U_ACTION'		=> $this->u_action,
		));
	}

	/**
	 * Produit le fichier de sauvegarde et le transmet au navigateur
	 */
	protected function backup_export()
	{
		if (!check_form_key('acp_groupemailer_backup'))
		{
			trigger_error('FORM_INVALID' . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$with_queue = (bool) $this->request->variable('with_queue', 1);

		// Sélection éventuelle : une liste vide exporte toutes les campagnes
		$selected = array_filter(array_map('intval', $this->request->variable('campaigns', array(0))));

		$data = array(
			'format'	=> 'groupemailer-backup',
			'version'	=> 1,
			'created'	=> time(),
			'with_queue'	=> $with_queue,
			'campaigns'	=> array(),
		);

		$sql = 'SELECT * FROM ' . $this->campaigns_table
			. ($selected ? ' WHERE ' . $this->db->sql_in_set('campaign_id', $selected) : '')
			. ' ORDER BY campaign_id ASC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$data['campaigns'][] = array(
				'campaign'	=> $row,
				'queue'		=> array(),
			);
		}
		$this->db->sql_freeresult($result);

		if (empty($data['campaigns']))
		{
			trigger_error($this->user->lang('GROUPEMAILER_BACKUP_NOTHING') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		if ($with_queue)
		{
			$index = array();

			foreach ($data['campaigns'] as $k => $entry)
			{
				$index[(int) $entry['campaign']['campaign_id']] = $k;
			}

			$sql = 'SELECT * FROM ' . $this->queue_table . ' ORDER BY queue_id ASC';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$cid = (int) $row['campaign_id'];

				if (isset($index[$cid]))
				{
					$data['campaigns'][$index[$cid]]['queue'][] = $row;
				}
			}
			$this->db->sql_freeresult($result);
		}

		$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$filename = 'groupemailer-' . date('Ymd-His') . '.json';

		garbage_collection();

		header('Content-Type: application/json; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . strlen($json));

		echo $json;

		exit_handler();
	}

	/**
	 * Restaure un fichier de sauvegarde. Les campagnes sont recréées avec de
	 * nouveaux identifiants : rien n'est écrasé, et un import répété produit
	 * des doublons plutôt que de détruire des données.
	 */
	protected function backup_import()
	{
		if (!check_form_key('acp_groupemailer_backup'))
		{
			trigger_error('FORM_INVALID' . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$file = $this->request->file('backup_file');

		if (empty($file['tmp_name']) || !empty($file['error']))
		{
			trigger_error($this->user->lang('GROUPEMAILER_IMPORT_NO_FILE') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$raw = @file_get_contents($file['tmp_name']);
		$data = $raw !== false ? json_decode($raw, true) : null;

		if (!is_array($data) || !isset($data['format']) || $data['format'] !== 'groupemailer-backup' || !isset($data['campaigns']) || !is_array($data['campaigns']))
		{
			trigger_error($this->user->lang('GROUPEMAILER_IMPORT_BAD_FILE') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$campaign_cols = $this->table_columns($this->campaigns_table);
		$queue_cols = $this->table_columns($this->queue_table);

		$nb_campaigns = 0;
		$nb_queue = 0;
		$id_map = array();
		$parents = array();

		foreach ($data['campaigns'] as $entry)
		{
			if (!isset($entry['campaign']) || !is_array($entry['campaign']))
			{
				continue;
			}

			$old_id = (int) $entry['campaign']['campaign_id'];
			$row = $this->filter_columns($entry['campaign'], $campaign_cols);
			unset($row['campaign_id']);

			// Le rattachement des relances est rétabli après coup, une fois
			// tous les nouveaux identifiants connus.
			$parent = isset($row['parent_campaign_id']) ? (int) $row['parent_campaign_id'] : 0;
			$row['parent_campaign_id'] = 0;

			$sql = 'INSERT INTO ' . $this->campaigns_table . ' ' . $this->db->sql_build_array('INSERT', $row);
			$this->db->sql_query($sql);
			$new_id = (int) $this->db->sql_nextid();

			$id_map[$old_id] = $new_id;
			$nb_campaigns++;

			if ($parent)
			{
				$parents[$new_id] = $parent;
			}

			if (!empty($entry['queue']) && is_array($entry['queue']))
			{
				$batch = array();

				foreach ($entry['queue'] as $q)
				{
					if (!is_array($q))
					{
						continue;
					}

					$qrow = $this->filter_columns($q, $queue_cols);
					unset($qrow['queue_id']);
					$qrow['campaign_id'] = $new_id;
					$batch[] = $qrow;
				}

				if ($batch)
				{
					$this->db->sql_multi_insert($this->queue_table, $batch);
					$nb_queue += count($batch);
				}
			}
		}

		foreach ($parents as $new_id => $old_parent)
		{
			if (isset($id_map[$old_parent]))
			{
				$sql = 'UPDATE ' . $this->campaigns_table . '
					SET parent_campaign_id = ' . (int) $id_map[$old_parent] . '
					WHERE campaign_id = ' . (int) $new_id;
				$this->db->sql_query($sql);
			}
		}

		if (!$nb_campaigns)
		{
			trigger_error($this->user->lang('GROUPEMAILER_IMPORT_EMPTY') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		trigger_error($this->user->lang('GROUPEMAILER_IMPORT_DONE', $nb_campaigns, $nb_queue) . adm_back_link($this->u_action));
	}

	/**
	 * Colonnes réellement présentes dans une table, afin qu'une sauvegarde
	 * issue d'une autre version reste exploitable
	 */
	protected function table_columns($table)
	{
		$sql = 'SELECT * FROM ' . $table;
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			return array_keys($row);
		}

		global $phpbb_container;

		try
		{
			$tools = $phpbb_container->get('dbal.tools');

			return array_keys($tools->sql_list_columns($table));
		}
		catch (\Throwable $e)
		{
			return array();
		}
	}

	/**
	 * Ne retient que les colonnes connues de la table visée
	 */
	protected function filter_columns($row, $columns)
	{
		if (empty($columns))
		{
			return $row;
		}

		return array_intersect_key($row, array_flip($columns));
	}

	/**
	 * Réglages par défaut (cadence proposée à la création d'une campagne)
	 */
	protected function settings($id, $mode)
	{
		if ($this->request->is_set_post('submit'))
		{
			$this->config->set('groupemailer_default_rate_count', max(1, $this->request->variable('groupemailer_default_rate_count', 10)));
			$this->config->set('groupemailer_default_rate_interval', max(1, $this->request->variable('groupemailer_default_rate_interval', 10)));
			$this->config->set('groupemailer_from_name', $this->request->variable('groupemailer_from_name', '', true));
			$this->config->set('groupemailer_from_email', $this->request->variable('groupemailer_from_email', '', true));
			$this->config_text->set_array(array(
				'groupemailer_header'	=> $this->request->variable('groupemailer_header', '', true),
				'groupemailer_footer'	=> $this->request->variable('groupemailer_footer', '', true),
				'groupemailer_unsub_text'	=> $this->request->variable('groupemailer_unsub_text', '', true),
			));
			$this->config->set('groupemailer_default_inactive_days', max(1, $this->request->variable('groupemailer_default_inactive_days', 180)));
			$this->config->set('groupemailer_add_unsubscribe', $this->request->variable('groupemailer_add_unsubscribe', 0) ? 1 : 0);
			$this->config->set('groupemailer_list_unsubscribe', $this->request->variable('groupemailer_list_unsubscribe', 0) ? 1 : 0);
			$this->config->set('groupemailer_bounce_threshold', max(1, $this->request->variable('groupemailer_bounce_threshold', 3)));
			$this->config->set('groupemailer_unsub_confirm', $this->request->variable('groupemailer_unsub_confirm', 0) ? 1 : 0);
			$this->config->set('groupemailer_unsub_notify_id', $this->request->variable('groupemailer_unsub_notify_id', 0));
			$this->config->set('groupemailer_unsub_notify_email', $this->request->variable('groupemailer_unsub_notify_email', 0) ? 1 : 0);
			$this->config->set('groupemailer_unsub_notify_pm', $this->request->variable('groupemailer_unsub_notify_pm', 0) ? 1 : 0);

			trigger_error($this->user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		$this->assign_admin_list((int) $this->config['groupemailer_unsub_notify_id']);

		add_form_key('acp_groupemailer');

		$this->template->assign_vars(array(
			'GROUPEMAILER_DEFAULT_RATE_COUNT'		=> (int) $this->config['groupemailer_default_rate_count'],
			'GROUPEMAILER_DEFAULT_RATE_INTERVAL'	=> (int) $this->config['groupemailer_default_rate_interval'],
			'GROUPEMAILER_FROM_NAME'		=> $this->config['groupemailer_from_name'] !== '' ? $this->config['groupemailer_from_name'] : $this->config['sitename'],
			'GROUPEMAILER_FROM_EMAIL'		=> $this->config['groupemailer_from_email'] !== '' ? $this->config['groupemailer_from_email'] : $this->config['board_contact'],
			'GROUPEMAILER_HEADER'			=> $this->config_text->get('groupemailer_header'),
			'GROUPEMAILER_FOOTER'			=> $this->config_text->get('groupemailer_footer'),
			'GROUPEMAILER_UNSUB_TEXT'		=> $this->config_text->get('groupemailer_unsub_text'),
			'GROUPEMAILER_UNSUB_DEFAULT'	=> $this->user->lang('GROUPEMAILER_UNSUB_MAIL_DEFAULT'),
			'GROUPEMAILER_DEFAULT_INACTIVE_DAYS'	=> (int) $this->config['groupemailer_default_inactive_days'],
			'S_ADD_UNSUBSCRIBE'	=> (bool) $this->config['groupemailer_add_unsubscribe'],
			'S_LIST_UNSUBSCRIBE'	=> (bool) $this->config['groupemailer_list_unsubscribe'],
			'GROUPEMAILER_BOUNCE_THRESHOLD'	=> max(1, (int) $this->config['groupemailer_bounce_threshold']),
			'S_UNSUB_CONFIRM'		=> (bool) $this->config['groupemailer_unsub_confirm'],
			'S_UNSUB_NOTIFY_EMAIL'	=> (bool) $this->config['groupemailer_unsub_notify_email'],
			'S_UNSUB_NOTIFY_PM'		=> (bool) $this->config['groupemailer_unsub_notify_pm'],
			'U_ACTION'	=> $this->u_action,
		));
	}
}
