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

class v130_schedule extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groupemailer_campaigns', 'scheduled_time');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v120_unsubscribe_on');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array(
					// Date à laquelle l'envoi doit démarrer de lui-même
					'scheduled_time'	=> array('TIMESTAMP', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array('scheduled_time'),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_GROUPEMAILER_TITLE',
				array(
					'module_basename'	=> '\verturin\groupemailer\acp\main_module',
					'modes'				=> array('dashboard'),
				),
			)),
		);
	}
}
