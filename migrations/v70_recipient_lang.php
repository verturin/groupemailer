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

class v70_recipient_lang extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groupemailer_queue', 'user_lang');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v60_diag_module');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'groupemailer_queue' => array(
					// Langue du destinataire, figée au moment où la file est constituée
					'user_lang'	=> array('VCHAR:30', ''),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'groupemailer_queue' => array('user_lang'),
			),
		);
	}
}
