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

class v90_backup_module extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v80_list_unsubscribe');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_GROUPEMAILER_TITLE',
				array(
					'module_basename'	=> '\verturin\groupemailer\acp\main_module',
					'modes'				=> array('backup'),
				),
			)),
		);
	}
}
