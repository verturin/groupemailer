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

class v150_unsub_notify extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['groupemailer_unsub_confirm']);
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v140_unsub_module');
	}

	public function update_data()
	{
		return array(
			// Accusé de désabonnement adressé au membre
			array('config.add', array('groupemailer_unsub_confirm', 1)),

			// Copie à un administrateur : personne par défaut
			array('config.add', array('groupemailer_unsub_notify_id', 0)),
			array('config.add', array('groupemailer_unsub_notify_email', 1)),
			array('config.add', array('groupemailer_unsub_notify_pm', 0)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('groupemailer_unsub_confirm')),
			array('config.remove', array('groupemailer_unsub_notify_id')),
			array('config.remove', array('groupemailer_unsub_notify_email')),
			array('config.remove', array('groupemailer_unsub_notify_pm')),
		);
	}
}
