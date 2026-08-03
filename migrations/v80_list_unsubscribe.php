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

class v80_list_unsubscribe extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['groupemailer_list_unsubscribe']);
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v70_recipient_lang');
	}

	public function update_data()
	{
		return array(
			// Activé par défaut : cet en-tête est attendu par Gmail et Yahoo
			// des expéditeurs en nombre et améliore le placement en boîte de
			// réception. Il reste désactivable dans les Réglages.
			array('config.add', array('groupemailer_list_unsubscribe', 1)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('groupemailer_list_unsubscribe')),
		);
	}
}
