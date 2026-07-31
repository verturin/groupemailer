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

class v40_deactivated extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groupemailer_campaigns', 'exclude_deactivated');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v30_filters');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array(
					// Valeur 1 par défaut : conserve le comportement historique
					// (ces comptes étaient déjà toujours écartés)
					'exclude_deactivated'	=> array('BOOL', 1),
					'excluded_deactivated'	=> array('UINT', 0),
					'excluded_inactive'		=> array('UINT', 0),
					'excluded_massemail'	=> array('UINT', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'groupemailer_campaigns' => array('exclude_deactivated', 'excluded_deactivated', 'excluded_inactive', 'excluded_massemail'),
			),
		);
	}
}
