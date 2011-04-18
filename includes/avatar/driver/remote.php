<?php
/**
*
* @package avatar
* @copyright (c) 2011 phpBB Group
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
* Handles avatars hosted remotely
* @package avatars
*/
class phpbb_avatar_driver_remote extends phpbb_avatar_driver
{
	/**
	* @inheritdoc
	*/
	public function get_data($user_row, $ignore_config = false)
	{
		if ($ignore_config || $this->config['allow_avatar_remote'])
		{
			return array(
				'src' => $user_row['user_avatar'],
				'width' => $user_row['user_avatar_width'],
				'height' => $user_row['user_avatar_height'],
			);
		}
		else
		{
			return array(
				'src' => '',
				'width' => 0,
				'height' => 0,
			);
		}
	}

	/**
	* @inheritdoc
	*/
	public function handle_form($template, &$error = array(), $submitted = false)
	{
		if ($submitted) {
			$error[] = 'TODO';
			return '';
		}
		else
		{
			return true;
		}
	}
}
