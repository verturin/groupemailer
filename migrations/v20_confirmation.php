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

class v20_confirmation extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groupemailer_queue', 'confirm_token');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v10_install');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array(
					'require_confirm'		=> array('BOOL', 0),
					'parent_campaign_id'	=> array('UINT', 0),
				),
				$this->table_prefix . 'groupemailer_queue' => array(
					'confirm_token'		=> array('VCHAR:64', ''),
					'confirmed_time'	=> array('TIMESTAMP', 0),
				),
			),
			'add_index' => array(
				$this->table_prefix . 'groupemailer_queue' => array(
					'gm_token'	=> array('confirm_token'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_keys' => array(
				$this->table_prefix . 'groupemailer_queue' => array('gm_token'),
			),
			'drop_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array('require_confirm', 'parent_campaign_id'),
				$this->table_prefix . 'groupemailer_queue' => array('confirm_token', 'confirmed_time'),
			),
		);
	}
}
