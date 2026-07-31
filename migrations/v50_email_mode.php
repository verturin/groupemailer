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

class v50_email_mode extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groupemailer_campaigns', 'email_mode');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v40_deactivated');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array(
					// 'full' = message complet dans l'email (comportement historique)
					// 'notice' = notification seule, message consultable en ligne
					'email_mode'	=> array('VCHAR:20', 'full'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array('email_mode'),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('groupemailer_add_unsubscribe', 1)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('groupemailer_add_unsubscribe')),
		);
	}
}
