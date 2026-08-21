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

class v160_long_texts extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groupemailer_queue', 'unsub_resent_time');
	}

	static public function depends_on()
	{
		return array('\verturin\groupemailer\migrations\v150_unsub_notify');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'groupemailer_queue' => array(
					// Dernier renvoi de l'accusé de désabonnement
					'unsub_resent_time'	=> array('TIMESTAMP', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'groupemailer_queue' => array('unsub_resent_time'),
			),
		);
	}

	public function update_data()
	{
		// config_value est un VARCHAR(255) : les textes longs y sont tronqués.
		// Ils passent donc dans la table prévue pour les textes longs, en
		// reprenant au passage les valeurs déjà saisies.
		$header = isset($this->config['groupemailer_header']) ? (string) $this->config['groupemailer_header'] : '';
		$footer = isset($this->config['groupemailer_footer']) ? (string) $this->config['groupemailer_footer'] : '';

		return array(
			array('config_text.add', array('groupemailer_header', $header)),
			array('config_text.add', array('groupemailer_footer', $footer)),
			array('config_text.add', array('groupemailer_unsub_text', '')),

			array('config.remove', array('groupemailer_header')),
			array('config.remove', array('groupemailer_footer')),
		);
	}

	public function revert_data()
	{
		return array(
			array('config_text.remove', array('groupemailer_header')),
			array('config_text.remove', array('groupemailer_footer')),
			array('config_text.remove', array('groupemailer_unsub_text')),
		);
	}
}
