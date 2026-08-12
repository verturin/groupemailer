<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Ces tests portent sur la structure du module d'administration. Ils visent
 * les défauts qui ont réellement été rencontrés : méthode appelée mais
 * absente, gabarit référencé mais manquant, résultat de requête libéré deux
 * fois, ou liens de tri jamais transmis au gabarit.
 */

use PHPUnit\Framework\TestCase;

class acp_module_test extends TestCase
{
	protected $source;

	protected function setUp(): void
	{
		$this->source = file_get_contents(GM_TESTS_ROOT . 'acp/main_module.php');
	}

	public function test_chaque_action_dispose_de_sa_methode()
	{
		preg_match_all(
			'/case \'(\w+)\':\s*(?:[^\n]*\n){1,8}?\s*\$this->(\w+)\(/',
			$this->source,
			$matches,
			PREG_SET_ORDER
		);

		$this->assertIsArray($matches);

		$missing = array();

		foreach ($matches as $match)
		{
			if (strpos($this->source, 'function ' . $match[2] . '(') === false)
			{
				$missing[] = $match[1] . ' -> ' . $match[2] . '()';
			}
		}

		$this->assertSame(array(), $missing);
	}

	public function test_chaque_gabarit_reference_existe()
	{
		preg_match_all("/'(acp_groupemailer_\w+)'/", $this->source, $matches);

		$missing = array();

		foreach (array_unique($matches[1]) as $template)
		{
			if (!file_exists(GM_TESTS_ROOT . 'adm/style/' . $template . '.html'))
			{
				$missing[] = $template;
			}
		}

		$this->assertSame(array(), $missing);
	}

	public function test_aucun_resultat_de_requete_libere_deux_fois()
	{
		$lines = explode("\n", $this->source);
		$last_query = -1;
		$last_free = -1;
		$doubles = array();

		foreach ($lines as $i => $line)
		{
			if (preg_match('/function \w+\(/', $line))
			{
				$last_query = $last_free = -1;
			}

			if (strpos($line, 'sql_query') !== false && strpos($line, 'sql_freeresult') === false)
			{
				$last_query = $i;
			}

			if (strpos($line, 'sql_freeresult') !== false)
			{
				if ($last_free > $last_query && $last_query !== -1)
				{
					$doubles[] = 'ligne ' . ($i + 1);
				}

				$last_free = $i;
			}
		}

		$this->assertSame(array(), $doubles);
	}

	public function test_les_gabarits_triables_recoivent_leurs_liens()
	{
		$pages = array(
			'history'			=> 'acp_groupemailer_history',
			'campaign_details'	=> 'acp_groupemailer_details',
			'campaign_list'		=> 'acp_groupemailer_campaigns',
		);

		$problems = array();

		foreach ($pages as $method => $template)
		{
			$start = strpos($this->source, 'function ' . $method . '(');
			$end = strpos($this->source, "\n\tprotected function", $start + 20);
			$body = substr($this->source, $start, $end - $start);

			$html = file_get_contents(GM_TESTS_ROOT . 'adm/style/' . $template . '.html');

			if (strpos($html, 'U_SORT_') !== false && strpos($body, 'assign_sort_urls') === false)
			{
				$problems[] = $template . ' : liens de tri non générés';
			}

			if (strpos($html, 'S_PAGINATED') !== false && strpos($body, 'build_pagination') === false)
			{
				$problems[] = $template . ' : pagination non générée';
			}
		}

		$this->assertSame(array(), $problems);
	}

	public function test_les_gabarits_n_incluent_aucun_fichier_du_coeur()
	{
		$problems = array();

		foreach (glob(GM_TESTS_ROOT . 'adm/style/*.html') as $file)
		{
			$html = file_get_contents($file);

			preg_match_all("/{%\s*include\s+'([^']+)'/", $html, $matches);

			foreach ($matches[1] as $included)
			{
				// Les gabarits du cœur ne sont pas résolus depuis une extension
				$problems[] = basename($file) . ' inclut ' . $included;
			}
		}

		$this->assertSame(array(), $problems);
	}

	public function test_aucun_acces_direct_aux_superglobales()
	{
		$files = array_merge(
			glob(GM_TESTS_ROOT . 'acp/*.php'),
			glob(GM_TESTS_ROOT . 'controller/*.php'),
			glob(GM_TESTS_ROOT . 'cron/task/*.php')
		);

		$problems = array();

		foreach ($files as $file)
		{
			if (preg_match('/\$_(GET|POST|REQUEST|FILES)/', file_get_contents($file)))
			{
				$problems[] = basename($file);
			}
		}

		$this->assertSame(array(), $problems);
	}

	public function test_les_actions_modifiant_l_etat_sont_protegees()
	{
		preg_match('/\$guarded = array\(([^)]*)\)/', $this->source, $match);

		$guarded = explode(',', str_replace(array("'", ' '), '', $match[1]));

		// Toute action qui modifie une campagne doit exiger un jeton de lien
		$expected = array('start', 'pause', 'resume', 'cancel', 'relance', 'duplicate', 'unschedule');

		$missing = array_values(array_diff($expected, $guarded));

		$this->assertSame(array(), $missing);
	}
}
