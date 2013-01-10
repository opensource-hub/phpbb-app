<?php
/**
*
* @package migration
* @copyright (c) 2012 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

class phpbb_db_migration_data_3_0_11_rc1 extends phpbb_db_migration
{
	function depends_on()
	{
		return array('phpbb_db_migration_data_3_0_10');
	}

	function update_schema()
	{
		return array();
	}

	function update_data()
	{
		return array(
			array('custom', array(array(&$this, 'cleanup_deactivated_styles'))),
			array('custom', array(array(&$this, 'delete_orphan_private_messages'))),

			array('config.update', array('version', '3.0.11-rc1')),
		);
	}

	function cleanup_deactivated_styles()
	{
		// Updates users having current style a deactivated one
		$sql = 'SELECT style_id
			FROM ' . STYLES_TABLE . '
			WHERE style_active = 0';
		$result = $this->sql_query($sql);

		$deactivated_style_ids = array();
		while ($style_id = $this->db->sql_fetchfield('style_id', false, $result))
		{
			$deactivated_style_ids[] = (int) $style_id;
		}
		$this->db->sql_freeresult($result);

		if (!empty($deactivated_style_ids))
		{
			$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_style = ' . (int) $this->config['default_style'] .'
				WHERE ' . $this->db->sql_in_set('user_style', $deactivated_style_ids);
			$this->sql_query($sql);
		}
	}

	function delete_orphan_private_messages()
	{
		// Delete orphan private messages
		$batch_size = 500;

		$sql_array = array(
			'SELECT'	=> 'p.msg_id',
			'FROM'		=> array(
				PRIVMSGS_TABLE	=> 'p',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(PRIVMSGS_TO_TABLE => 't'),
					'ON'	=> 'p.msg_id = t.msg_id',
				),
			),
			'WHERE'		=> 't.user_id IS NULL',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query_limit($sql, $batch_size);

		$delete_pms = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$delete_pms[] = (int) $row['msg_id'];
		}
		$this->db->sql_freeresult($result);

		if (!empty($delete_pms))
		{
			$sql = 'DELETE FROM ' . PRIVMSGS_TABLE . '
				WHERE ' . $this->db->sql_in_set('msg_id', $delete_pms);
			$this->sql_query($sql);

			return false;
		}
	}
}
