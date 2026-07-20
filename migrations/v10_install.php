<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace verturin\groupemailer\migrations;

class v10_install extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'groupemailer_campaigns');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v33x\v3314');
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array(
					'COLUMNS' => array(
						'campaign_id'		=> array('UINT', NULL, 'auto_increment'),
						'title'				=> array('VCHAR:255', ''),
						'subject'			=> array('VCHAR:255', ''),
						'body'				=> array('MTEXT', ''),
						'target_groups'		=> array('VCHAR:255', ''),
						'target_groups_names'	=> array('VCHAR:255', ''),
						'rate_count'		=> array('USINT', 10),
						'rate_interval'		=> array('USINT', 10),
						'status'			=> array('VCHAR:20', 'draft'),
						'created_time'		=> array('TIMESTAMP', 0),
						'started_time'		=> array('TIMESTAMP', 0),
						'completed_time'	=> array('TIMESTAMP', 0),
						'last_run_time'		=> array('TIMESTAMP', 0),
						'total_recipients'	=> array('UINT', 0),
						'sent_count'		=> array('UINT', 0),
						'error_count'		=> array('UINT', 0),
						'created_by'		=> array('UINT', 0),
					),
					'PRIMARY_KEY' => 'campaign_id',
				),
				$this->table_prefix . 'groupemailer_queue' => array(
					'COLUMNS' => array(
						'queue_id'			=> array('UINT', NULL, 'auto_increment'),
						'campaign_id'		=> array('UINT', 0),
						'user_id'			=> array('UINT', 0),
						'username'			=> array('VCHAR:255', ''),
						'email'				=> array('VCHAR:100', ''),
						'status'			=> array('VCHAR:20', 'pending'),
						'sent_time'			=> array('TIMESTAMP', 0),
						'error_message'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY' => 'queue_id',
					'KEYS' => array(
						'campaign_id'	=> array('INDEX', 'campaign_id'),
						'q_status'		=> array('INDEX', 'status'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'groupemailer_campaigns',
				$this->table_prefix . 'groupemailer_queue',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('groupemailer_default_rate_count', 10)),
			array('config.add', array('groupemailer_default_rate_interval', 10)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_GROUPEMAILER_TITLE',
			)),

			array('module.add', array(
				'acp',
				'ACP_GROUPEMAILER_TITLE',
				array(
					'module_basename'	=> '\verturin\groupemailer\acp\main_module',
					'modes'				=> array('campaigns', 'settings', 'history'),
				),
			)),

			array('module.remove', array(
				'acp',
				false,
				'ACP_BACKUP',
			)),
			array('module.add', array(
				'acp',
				'ACP_CAT_MAINTENANCE',
				'ACP_BACKUP',
			)),
		);
	}
}
