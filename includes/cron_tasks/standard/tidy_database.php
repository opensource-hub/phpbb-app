<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Tidy database cron task.
*
* @package phpBB3
*/
class tidy_database_cron_task extends cron_task_base
{
	/**
	* Runs this cron task.
	*/
	public function run()
	{
		include_once($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
		tidy_database();
	}

	/**
	* Returns whether this cron task should run now, because enough time
	* has passed since it was last run.
	*/
	public function should_run()
	{
		global $config;
		return $config['database_last_gc'] < time() - $config['database_gc'];
	}
}
