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

class v110_bounces extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'groupemailer_bounces');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v100_unsubscribe');
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'groupemailer_bounces' => array(
					'COLUMNS' => array(
						'bounce_id'		=> array('UINT', null, 'auto_increment'),
						'user_id'		=> array('UINT', 0),
						'email'			=> array('VCHAR:100', ''),
						'username'		=> array('VCHAR:255', ''),
						// Échecs consécutifs : remis à zéro dès qu'un envoi aboutit
						'fail_count'	=> array('UINT', 0),
						'last_fail_time'	=> array('TIMESTAMP', 0),
						'last_error'	=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY' => 'bounce_id',
					'KEYS' => array(
						'gm_user'	=> array('UNIQUE', 'user_id'),
						'gm_fails'	=> array('INDEX', 'fail_count'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array($this->table_prefix . 'groupemailer_bounces'),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('groupemailer_bounce_threshold', 3)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('groupemailer_bounce_threshold')),
		);
	}
}
