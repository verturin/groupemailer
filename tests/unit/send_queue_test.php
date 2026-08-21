<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

use PHPUnit\Framework\TestCase;

require_once GM_TESTS_ROOT . 'cron/task/send_queue.php';

class send_queue_test extends TestCase
{
	protected $task;
	protected $reflection;

	protected function setUp(): void
	{
		$config = new \phpbb\config\config(array(
			'sitename'			=> 'Mon Forum',
			'default_lang'		=> 'fr',
			'enable_mod_rewrite'	=> 0,
			'board_contact'		=> 'contact@example.test',
			'groupemailer_header'	=> '',
			'groupemailer_footer'	=> '',
			'groupemailer_from_name'	=> '',
			'groupemailer_from_email'	=> '',
			'groupemailer_add_unsubscribe'	=> 1,
			'groupemailer_list_unsubscribe'	=> 1,
		));

		$this->task = new \verturin\groupemailer\cron\task\send_queue(
			$config,
			new \phpbb\config\db_text(),
			new \phpbb\db\driver\fake(),
			new \phpbb\user(),
			new \phpbb\group\helper(),
			'phpbb_',
			GM_TESTS_ROOT . 'tests/fixtures/',
			'php'
		);

		$this->reflection = new ReflectionClass($this->task);
	}

	protected function call($method, array $args = array())
	{
		$m = $this->reflection->getMethod($method);
		$m->setAccessible(true);

		return $m->invokeArgs($this->task, $args);
	}

	public function test_langue_du_destinataire_retenue_si_disponible()
	{
		$this->assertSame('fr', $this->call('resolve_lang', array('fr')));
		$this->assertSame('en', $this->call('resolve_lang', array('en')));
	}

	public function test_repli_sur_la_langue_du_forum()
	{
		// Langue non installée, valeur vide ou fantaisiste
		$this->assertSame('fr', $this->call('resolve_lang', array('de')));
		$this->assertSame('fr', $this->call('resolve_lang', array('')));
		$this->assertSame('fr', $this->call('resolve_lang', array('../../etc/passwd')));
	}

	public function test_mode_notification_ne_contient_pas_le_message()
	{
		$campaign = array(
			'subject'			=> 'Nouveautés',
			'body'				=> 'CONTENU SECRET DU MESSAGE',
			'email_mode'		=> 'notice',
			'require_confirm'	=> 1,
		);
		$row = array('username' => 'Pseudo', 'confirm_token' => 'abc123', 'user_lang' => 'fr');

		$message = $this->call('build_message', array($campaign, $row, '', '', 'fr'));

		$this->assertStringNotContainsString('CONTENU SECRET', $message);
		$this->assertStringContainsString('/gm/abc123', $message);
	}

	public function test_mode_complet_contient_le_message_et_le_lien()
	{
		$campaign = array(
			'subject'			=> 'Nouveautés',
			'body'				=> 'CONTENU DU MESSAGE',
			'email_mode'		=> 'full',
			'require_confirm'	=> 1,
		);
		$row = array('username' => 'Pseudo', 'confirm_token' => 'abc123', 'user_lang' => 'fr');

		$message = $this->call('build_message', array($campaign, $row, '', '', 'fr'));

		$this->assertStringContainsString('CONTENU DU MESSAGE', $message);
		$this->assertStringContainsString('/gm/abc123', $message);
	}

	public function test_mode_complet_sans_suivi_n_ajoute_aucun_lien_de_lecture()
	{
		$campaign = array(
			'subject'			=> 'Nouveautés',
			'body'				=> 'CONTENU DU MESSAGE',
			'email_mode'		=> 'full',
			'require_confirm'	=> 0,
		);
		$row = array('username' => 'Pseudo', 'confirm_token' => 'abc123', 'user_lang' => 'fr');

		$message = $this->call('build_message', array($campaign, $row, '', '', 'fr'));

		$this->assertStringContainsString('CONTENU DU MESSAGE', $message);
		$this->assertStringNotContainsString('/gm/abc123', $message);
	}

	public function test_le_pseudonyme_remplace_le_marqueur()
	{
		$campaign = array(
			'subject'			=> 'Sujet',
			'body'				=> 'Bonjour {USERNAME}, voici la suite.',
			'email_mode'		=> 'full',
			'require_confirm'	=> 0,
		);
		$row = array('username' => 'Alice', 'confirm_token' => '', 'user_lang' => 'fr');

		$message = $this->call('build_message', array($campaign, $row, '', '', 'fr'));

		$this->assertStringContainsString('Bonjour Alice,', $message);
		$this->assertStringNotContainsString('{USERNAME}', $message);
	}

	public function test_aucune_entite_html_dans_un_message_en_texte_brut()
	{
		$campaign = array(
			'subject'			=> 'Sujet',
			'body'				=> 'Corps',
			'email_mode'		=> 'notice',
			'require_confirm'	=> 1,
		);
		$row = array('username' => 'Pseudo', 'confirm_token' => 'abc123', 'user_lang' => 'fr');

		$message = $this->call('build_message', array($campaign, $row, '', '', 'fr'));

		// Une entité HTML dans un email en texte brut casse les liens
		$this->assertStringNotContainsString('&amp;', $message);
	}

	public function test_en_tete_de_desabonnement_est_une_liste_de_chaines()
	{
		$headers = $this->call('unsubscribe_headers');

		$this->assertIsArray($headers);

		foreach ($headers as $header)
		{
			// messenger->headers() de phpBB attend une chaîne
			$this->assertIsString($header);
		}
	}

	public function test_en_tete_de_desabonnement_desactivable()
	{
		$config = new \phpbb\config\config(array('groupemailer_list_unsubscribe' => 0));
		$prop = $this->reflection->getProperty('config');
		$prop->setAccessible(true);
		$prop->setValue($this->task, $config);

		$this->assertSame(array(), $this->call('unsubscribe_headers'));
	}

	public function test_lien_de_lecture_selon_la_reecriture_d_url()
	{
		$this->assertSame(
			'https://example.test/app.php/gm/jeton',
			$this->call('get_confirm_url', array('jeton'))
		);

		$config = new \phpbb\config\config(array('enable_mod_rewrite' => 1));
		$prop = $this->reflection->getProperty('config');
		$prop->setAccessible(true);
		$prop->setValue($this->task, $config);

		$this->assertSame(
			'https://example.test/gm/jeton',
			$this->call('get_confirm_url', array('jeton'))
		);
	}
}
