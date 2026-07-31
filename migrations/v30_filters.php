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

class v30_filters extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groupemailer_campaigns', 'exclude_inactive');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v20_confirmation');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array(
					'exclude_inactive'	=> array('BOOL', 0),
					'inactive_days'		=> array('UINT', 180),
					'respect_massemail'	=> array('BOOL', 0),
					'excluded_count'	=> array('UINT', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array('exclude_inactive', 'inactive_days', 'respect_massemail', 'excluded_count'),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('groupemailer_default_inactive_days', 180)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('groupemailer_default_inactive_days')),
		);
	}
}
