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

class v100_unsubscribe extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groupemailer_queue', 'unsubscribed_time');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v90_backup_module');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'groupemailer_queue' => array(
					// Date à laquelle le destinataire s'est désabonné depuis ce message
					'unsubscribed_time'	=> array('TIMESTAMP', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'groupemailer_queue' => array('unsubscribed_time'),
			),
		);
	}
}
