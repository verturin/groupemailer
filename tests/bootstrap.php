<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Doublures minimales des dépendances phpBB, afin que les tests s'exécutent
 * sans installation du forum.
 */

namespace
{
	if (!defined('IN_PHPBB'))
	{
		define('IN_PHPBB', true);
	}

	define('GM_TESTS_ROOT', dirname(__DIR__) . '/');

	define('USERS_TABLE', 'phpbb_users');
	define('GROUPS_TABLE', 'phpbb_groups');
	define('USER_GROUP_TABLE', 'phpbb_user_group');
	define('USER_NORMAL', 0);
	define('USER_INACTIVE', 1);
	define('USER_IGNORE', 2);
	define('USER_FOUNDER', 3);
	define('NOTIFY_EMAIL', 0);

	function generate_board_url()
	{
		return 'https://example.test';
	}

	function append_sid($url, $params = '')
	{
		return $url . ($params !== '' ? '?' . $params : '');
	}
}

namespace phpbb\cron\task
{
	abstract class base
	{
		protected $task_name;

		public function set_name($name)
		{
			$this->task_name = $name;
		}

		public function get_name()
		{
			return $this->task_name;
		}
	}
}

namespace phpbb\config
{
	class config implements \ArrayAccess
	{
		public $data = array();

		public function __construct(array $data = array())
		{
			$this->data = $data;
		}

		public function offsetExists($offset): bool
		{
			return isset($this->data[$offset]);
		}

		public function offsetGet($offset): mixed
		{
			return isset($this->data[$offset]) ? $this->data[$offset] : null;
		}

		public function offsetSet($offset, $value): void
		{
			$this->data[$offset] = $value;
		}

		public function offsetUnset($offset): void
		{
			unset($this->data[$offset]);
		}

		public function set($key, $value)
		{
			$this->data[$key] = $value;
		}
	}
}

namespace phpbb\db\driver
{
	interface driver_interface
	{
	}

	/**
	 * Base de test : mémorise les requêtes et restitue des jeux de lignes
	 * préparés par le test.
	 */
	class fake implements driver_interface
	{
		public $queries = array();
		public $results = array();
		public $inserted = array();

		private $sets = array();
		private $next_id = 1;

		public function sql_query($sql)
		{
			$flat = preg_replace('/\s+/', ' ', trim($sql));
			$this->queries[] = $flat;

			$id = count($this->sets);
			$this->sets[$id] = array();

			foreach ($this->results as $needle => $rows)
			{
				if (stripos($flat, $needle) !== false)
				{
					$this->sets[$id] = $rows;
					break;
				}
			}

			return $id;
		}

		public function sql_query_limit($sql, $limit, $offset = 0)
		{
			return $this->sql_query($sql);
		}

		public function sql_fetchrow($result)
		{
			if (!isset($this->sets[$result]) || !$this->sets[$result])
			{
				return false;
			}

			return array_shift($this->sets[$result]);
		}

		public function sql_fetchfield($field, $rownum = false, $result = false)
		{
			return 0;
		}

		public function sql_freeresult($result)
		{
		}

		public function sql_build_array($mode, $ary)
		{
			$parts = array();

			foreach ($ary as $key => $value)
			{
				$parts[] = $key . " = '" . $value . "'";
			}

			return implode(', ', $parts);
		}

		public function sql_escape($value)
		{
			return addslashes($value);
		}

		public function sql_multi_insert($table, $rows)
		{
			$this->inserted = array_merge($this->inserted, $rows);
		}

		public function sql_in_set($field, $ids)
		{
			return $field . ' IN (' . implode(',', (array) $ids) . ')';
		}

		public function sql_nextid()
		{
			return $this->next_id++;
		}
	}
}

namespace phpbb
{
	class user
	{
		public $data = array('user_id' => 2, 'username' => 'admin', 'user_email' => 'admin@example.test', 'user_lang' => 'fr');
		public $lang_name = 'fr';

		public function lang(...$args)
		{
			return (string) array_shift($args);
		}

		public function format_date($timestamp)
		{
			return date('Y-m-d H:i', (int) $timestamp);
		}

		public function add_lang_ext($ext, $file)
		{
		}

		public function create_datetime()
		{
			return new \DateTime('now', new \DateTimeZone('UTC'));
		}
	}
}

namespace phpbb\group
{
	class helper
	{
		public function get_name($name)
		{
			return $name;
		}
	}
}
