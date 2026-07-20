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
	protected $request;
	protected $template;
	protected $user;
	protected $group_helper;
	protected $table_prefix;
	protected $campaigns_table;
	protected $queue_table;

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
		$this->campaigns_table = $this->table_prefix . 'groupemailer_campaigns';
		$this->queue_table = $this->table_prefix . 'groupemailer_queue';

		$user->add_lang_ext('verturin/groupemailer', 'common');

		switch ($mode)
		{
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

			case 'retry_errors':
				$this->campaign_retry_errors($campaign_id);
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
		$sql = 'SELECT * FROM ' . $this->campaigns_table . '
			ORDER BY created_time DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('campaigns', array(
				'CAMPAIGN_ID'		=> $row['campaign_id'],
				'TITLE'				=> $row['title'],
				'SUBJECT'			=> $row['subject'],
				'STATUS'			=> $row['status'],
				'STATUS_LANG'		=> $this->user->lang('GROUPEMAILER_STATUS_' . strtoupper($row['status'])),
				'RATE'				=> $this->user->lang('GROUPEMAILER_RATE_VALUE', (int) $row['rate_count'], (int) $row['rate_interval']),
				'TOTAL_RECIPIENTS'	=> (int) $row['total_recipients'],
				'SENT_COUNT'		=> (int) $row['sent_count'],
				'ERROR_COUNT'		=> (int) $row['error_count'],
				'CREATED_TIME'		=> $row['created_time'] ? $this->user->format_date($row['created_time']) : '-',

				'S_DRAFT'		=> ($row['status'] === 'draft'),
				'S_RUNNING'		=> ($row['status'] === 'running'),
				'S_PAUSED'		=> ($row['status'] === 'paused'),
				'S_COMPLETED'	=> ($row['status'] === 'completed'),
				'S_HAS_ERRORS'	=> ((int) $row['error_count'] > 0),

				'U_EDIT'	=> $this->u_action . "&amp;action=edit&amp;campaign_id={$row['campaign_id']}",
				'U_START'	=> $this->u_action . "&amp;action=start&amp;campaign_id={$row['campaign_id']}",
				'U_PAUSE'	=> $this->u_action . "&amp;action=pause&amp;campaign_id={$row['campaign_id']}",
				'U_RESUME'	=> $this->u_action . "&amp;action=resume&amp;campaign_id={$row['campaign_id']}",
				'U_DELETE'	=> $this->u_action . "&amp;action=delete&amp;campaign_id={$row['campaign_id']}",
				'U_RETRY'	=> $this->u_action . "&amp;action=retry_errors&amp;campaign_id={$row['campaign_id']}",
			));
		}
		$this->db->sql_freeresult($result);

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
			'rate_count'	=> (int) $this->config['groupemailer_default_rate_count'],
			'rate_interval'	=> (int) $this->config['groupemailer_default_rate_interval'],
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

		if ($title === '' || $subject === '' || $body === '' || empty($groups))
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

		$sql = 'SELECT DISTINCT u.user_id, u.username, u.user_email
			FROM ' . USERS_TABLE . ' u, ' . USER_GROUP_TABLE . " ug
			WHERE ug.group_id IN (" . implode(',', $group_ids) . ')
				AND ug.user_id = u.user_id
				AND ug.user_pending = 0
				AND u.user_type IN (' . USER_NORMAL . ', ' . USER_FOUNDER . ")
				AND u.user_email <> ''";
		$result = $this->db->sql_query($sql);

		$recipients = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$recipients[] = array(
				'campaign_id'	=> (int) $campaign_id,
				'user_id'		=> (int) $row['user_id'],
				'username'		=> $row['username'],
				'email'			=> $row['user_email'],
				'status'		=> 'pending',
			);
		}
		$this->db->sql_freeresult($result);

		if (empty($recipients))
		{
			trigger_error($this->user->lang('GROUPEMAILER_NO_RECIPIENTS') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->db->sql_multi_insert($this->queue_table, $recipients);

		$sql_ary = array(
			'status'			=> 'running',
			'started_time'		=> time(),
			'total_recipients'	=> count($recipients),
		);
		$sql = 'UPDATE ' . $this->campaigns_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$this->db->sql_query($sql);

		trigger_error($this->user->lang('GROUPEMAILER_CAMPAIGN_STARTED', count($recipients)) . adm_back_link($this->u_action));
	}

	protected function campaign_set_status($campaign_id, $status)
	{
		$sql = 'UPDATE ' . $this->campaigns_table . "
			SET status = '" . $this->db->sql_escape($status) . "'
			WHERE campaign_id = " . (int) $campaign_id . "
				AND status IN ('running', 'paused')";
		$this->db->sql_query($sql);
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

	protected function campaign_delete($campaign_id)
	{
		$sql = 'SELECT status FROM ' . $this->campaigns_table . '
			WHERE campaign_id = ' . (int) $campaign_id;
		$result = $this->db->sql_query($sql);
		$status = $this->db->sql_fetchfield('status');
		$this->db->sql_freeresult($result);

		if ($status !== 'draft')
		{
			trigger_error($this->user->lang('GROUPEMAILER_DELETE_LOCKED') . adm_back_link($this->u_action), E_USER_WARNING);
		}

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
			confirm_box(false, $this->user->lang('GROUPEMAILER_CONFIRM_DELETE'), build_hidden_fields(array(
				'i'				=> $this->request->variable('i', ''),
				'mode'			=> $this->request->variable('mode', ''),
				'action'		=> 'delete',
				'campaign_id'	=> $campaign_id,
			)));
		}
	}

	/**
	 * Historique détaillé : chaque email réellement envoyé, avec titre, contenu, groupe(s) et destinataire
	 */
	protected function history($id, $mode)
	{
		$sql = 'SELECT COUNT(*) AS total FROM ' . $this->queue_table . "
			WHERE status IN ('sent', 'error')";
		$result = $this->db->sql_query($sql);
		$total = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT q.queue_id, q.username, q.email, q.status, q.sent_time, q.error_message,
				c.title, c.subject, c.body, c.target_groups_names, c.total_recipients
			FROM ' . $this->queue_table . ' q, ' . $this->campaigns_table . " c
			WHERE q.campaign_id = c.campaign_id
				AND q.status IN ('sent', 'error')
			ORDER BY q.queue_id DESC";
		$result = $this->db->sql_query_limit($sql, 500);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('history', array(
				'CAMPAIGN_TITLE'	=> $row['title'],
				'SUBJECT'			=> $row['subject'],
				'BODY'				=> $row['body'],
				'GROUPS'			=> $row['target_groups_names'],
				'TOTAL_RECIPIENTS'	=> (int) $row['total_recipients'],
				'RECIPIENT'			=> $row['username'],
				'EMAIL'				=> $row['email'],
				'SENT_TIME'			=> $row['sent_time'] ? $this->user->format_date($row['sent_time']) : '-',
				'STATUS_LANG'		=> $this->user->lang('GROUPEMAILER_STATUS_' . strtoupper($row['status'])),
				'ERROR_MESSAGE'		=> $row['error_message'],
				'S_ERROR'			=> ($row['status'] === 'error'),
			));
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'GROUPEMAILER_TOTAL_SENT'	=> $total,
		));
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
			$this->config->set('groupemailer_header', $this->request->variable('groupemailer_header', '', true));
			$this->config->set('groupemailer_footer', $this->request->variable('groupemailer_footer', '', true));

			trigger_error($this->user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		add_form_key('acp_groupemailer');

		$this->template->assign_vars(array(
			'GROUPEMAILER_DEFAULT_RATE_COUNT'		=> (int) $this->config['groupemailer_default_rate_count'],
			'GROUPEMAILER_DEFAULT_RATE_INTERVAL'	=> (int) $this->config['groupemailer_default_rate_interval'],
			'GROUPEMAILER_FROM_NAME'		=> $this->config['groupemailer_from_name'] !== '' ? $this->config['groupemailer_from_name'] : $this->config['sitename'],
			'GROUPEMAILER_FROM_EMAIL'		=> $this->config['groupemailer_from_email'] !== '' ? $this->config['groupemailer_from_email'] : $this->config['board_contact'],
			'GROUPEMAILER_HEADER'			=> $this->config['groupemailer_header'],
			'GROUPEMAILER_FOOTER'			=> $this->config['groupemailer_footer'],
			'U_ACTION'	=> $this->u_action,
		));
	}
}
