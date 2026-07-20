<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace verturin\groupemailer\event;

use phpbb\event\data;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup' => 'load_language_on_setup',
		);
	}

	/**
	 * Charge common.php globalement, y compris au moment de la construction
	 * du menu ACP (les libellés du menu ont besoin des clés de langue
	 * avant même que le module ne soit ouvert).
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name'	=> 'verturin/groupemailer',
			'lang_set'	=> 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
}
