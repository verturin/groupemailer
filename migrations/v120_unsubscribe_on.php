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

class v120_unsubscribe_on extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v110_bounces');
	}

	public function update_data()
	{
		// L'en-tête List-Unsubscribe avait été introduit désactivé, le temps
		// de vérifier qu'il ne gênait pas la remise des messages. Ce point
		// étant confirmé, il est activé une fois sur les installations
		// existantes. Il reste décochable dans les Réglages, et cette
		// migration ne repassera pas dessus.
		return array(
			array('config.update', array('groupemailer_list_unsubscribe', 1)),
		);
	}
}
