<?php
/**
 *
 * Group Mailer extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Verturin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace verturin\groupemailer\acp;

class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\verturin\groupemailer\acp\main_module',
			'title'		=> 'ACP_GROUPEMAILER_TITLE',
			'version'	=> '2.7.2',
			'modes'		=> array(
				'campaigns'	=> array(
					'title'	=> 'ACP_GROUPEMAILER_CAMPAIGNS',
					'auth'	=> 'ext_verturin/groupemailer && acl_a_board',
					'cat'	=> array('ACP_GROUPEMAILER_TITLE'),
				),
				'settings'	=> array(
					'title'	=> 'ACP_GROUPEMAILER_SETTINGS',
					'auth'	=> 'ext_verturin/groupemailer && acl_a_board',
					'cat'	=> array('ACP_GROUPEMAILER_TITLE'),
				),
				'history'	=> array(
					'title'	=> 'ACP_GROUPEMAILER_HISTORY',
					'auth'	=> 'ext_verturin/groupemailer && acl_a_board',
					'cat'	=> array('ACP_GROUPEMAILER_TITLE'),
				),
			),
		);
	}
}
