<?php
/***************************************************************************
 *                                 mcp.php
 *                            -------------------
 *   begin                : July 4, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id$
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

// TODO for 2.2:
//
// * Plug-in based?
// * Add session_id checks for all Moderator ops
// * Tab based system
// * Front page:
//    * Select box listing all forums to which user has moderator rights
//    * Five(?) most recent Moderator log entries (for relevant forum/s)
//    * Five(?) most recent Moderator note entries (for relevant forum/s)
//    * Five(?) most recent Report to Moderator messages (for relevant forum/s)
//    * Note that above three, bar perhaps log entries could be on other tabs but with counters
//      or some such on front page indicating new messages are present
//    * List of topics awaiting Moderator approval (if appropriate and for relevant forum/s)
// * Topic view:
//    * As current(?) plus differing colours for Approved/Unapproved topics/posts
//    * When moving topics to forum for which Mod doesn't have Mod rights set for Mod approval
// * Split topic:
//    * As current but need better way of listing all posts
// * Merge topics:
//    * Similar to split(?) but reverse 
// * Find duplicates:
//    * List supiciously similar posts across forum/s
// * "Ban" user/s:
//    * Limit read/post/reply/etc. permissions

define('IN_PHPBB', true);
define('NEED_SID', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/functions_admin.'.$phpEx);

//
// Obtain initial var settings
//
$forum_id = (!empty($_REQUEST['f'])) ? intval($_REQUEST['f']) : '';
$topic_id = (!empty($_REQUEST['t'])) ? intval($_REQUEST['t']) : '';
$post_id = (!empty($_REQUEST['p'])) ? intval($_REQUEST['p']) : '';
$confirm = (!empty($_POST['confirm'])) ? TRUE : FALSE;

//
// Check if user did or did not confirm
// If they did not, forward them to the last page they were on
//
if (isset($_POST['cancel']))
{
	if ($topic_id)
	{
		$redirect = "viewtopic.$phpEx$SID&t=$topic_id";
	}
	else if ($forum_id)
	{
		$redirect = "viewforum.$phpEx$SID&f=$forum_id";
	}
	else
	{
		$redirect = "index.$phpEx$SID";
	}

	redirect($redirect);
}

// Start session management
$user->start();
$user->setup();
$auth->acl($user->data);
// End session management


//
// Continue var definitions
//
$start = (isset($_GET['start'])) ? $_GET['start'] : 0;

$delete = (isset($_POST['delete'])) ? TRUE : FALSE;
$move = (isset($_POST['move'])) ? TRUE : FALSE;
$lock = (isset($_POST['lock'])) ? TRUE : FALSE;
$unlock = (isset($_POST['unlock'])) ? TRUE : FALSE;

if (isset($_POST['mode']) || isset($_GET['mode']))
{
	$mode = (isset($_POST['mode'])) ? $_POST['mode'] : $_GET['mode'];
}
else
{
	if ($delete)
	{
		$mode = 'delete';
	}
	else if ($move)
	{
		$mode = 'move';
	}
	else if ($lock)
	{
		$mode = 'lock';
	}
	else if ($unlock)
	{
		$mode = 'unlock';
	}
	else
	{
		$mode = '';
	}
}

//
// Obtain relevant data
//
if (!empty($topic_id))
{
	$sql = "SELECT f.forum_id, f.forum_name, f.forum_topics
		FROM " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f
		WHERE t.topic_id = " . $topic_id . "
			AND f.forum_id = t.forum_id";
	$result = $db->sql_query($sql);

	$topic_row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$forum_topics = ($topic_row['forum_topics'] == 0) ? 1 : $topic_row['forum_topics'];
	$forum_id = $topic_row['forum_id'];
	$forum_name = $topic_row['forum_name'];
}
else if (!empty($forum_id))
{
	$sql = "SELECT forum_name, forum_topics
		FROM " . FORUMS_TABLE . "
		WHERE forum_id = " . $forum_id;
	$result = $db->sql_query($sql);

	$topic_row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$forum_topics = ($topic_row['forum_topics'] == 0) ? 1 : $topic_row['forum_topics'];
	$forum_name = $topic_row['forum_name'];
}
else
{
	trigger_error('Forum_not_exist');
}

//
// Auth check
//
if (!$auth->acl_gets('m_', 'a_', $forum_id))
{
	trigger_error($user->lang['Not_Moderator']);
}

//
// Do major work ...
//
switch($mode)
{
	case 'delete':
		$page_title = $user->lang['Mod_CP'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		if ($confirm)
		{
			$topics = (isset($_POST['topic_id_list'])) ?  $_POST['topic_id_list'] : array($topic_id);

			$topic_id_sql = '';
			for($i = 0; $i < count($topics); $i++)
			{
				$topic_id_sql .= (($topic_id_sql != '') ? ', ' : '') . intval($topics[$i]);
			}

			// Use prune feature?
			prune($forum_id, '', $topic_id_sql);

			$sql = "SELECT vote_id
				FROM " . VOTE_DESC_TABLE . "
				WHERE topic_id IN ($topic_id_sql)";
			$result = $db->sql_query($sql);

			if ($row = $db->sql_fetchrow($result))
			{
				$vote_id_sql = '';
				do
				{
					$vote_id_sql .= (($vote_id_sql != '') ? ', ' : '') . intval($row['vote_id']);
				}
				while ($row = $db->sql_fetchrow($result));

				$db->sql_transaction();

				$sql = "DELETE
					FROM " . VOTE_DESC_TABLE . "
					WHERE vote_id IN ($vote_id_sql)";
				$db->sql_query($sql);

				$sql = "DELETE
					FROM " . VOTE_RESULTS_TABLE . "
					WHERE vote_id IN ($vote_id_sql)";
				$db->sql_query($sql);

				$sql = "DELETE
					FROM " . VOTE_USERS_TABLE . "
					WHERE vote_id IN ($vote_id_sql)";
				$db->sql_query($sql);

				$db->sql_transaction('commit');
			}
			$db->sql_freeresult($result);

			if (!empty($topic_id))
			{
				$redirect_page = "viewforum.$phpEx$SID&ampf==$forum_id";
				$l_redirect = sprintf($user->lang['Click_return_forum'], '<a href="' . $redirect_page . '">', '</a>');
			}
			else
			{
				$redirect_page = "mcp.$phpEx$SID&ampf==$forum_id";
				$l_redirect = sprintf($user->lang['Click_return_modcp'], '<a href="' . $redirect_page . '">', '</a>');
			}

			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="3;url=' . $redirect_page . '">')
			);

			trigger_error($user->lang['Topics_Removed'] . '<br /><br />' . $l_redirect);
		}
		else
		{
			// Not confirmed, show confirmation message
			if (empty($_POST['topic_id_list']) && empty($topic_id))
			{
				trigger_error($user->lang['None_selected']);
			}

			$hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="f" value="' . $forum_id . '" />';

			if (isset($_POST['topic_id_list']))
			{
				$topics = $_POST['topic_id_list'];
				for($i = 0; $i < count($topics); $i++)
				{
					$hidden_fields .= '<input type="hidden" name="topic_id_list[]" value="' . intval($topics[$i]) . '" />';
				}
			}
			else
			{
				$hidden_fields .= '<input type="hidden" name="t" value="' . $topic_id . '" />';
			}

			// Set template files
			$template->set_filenames(array(
				'body' => 'confirm_body.html')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE' => $user->lang['Confirm'],
				'MESSAGE_TEXT' => $user->lang['Confirm_delete_topic'],

				'L_YES' => $user->lang['Yes'],
				'L_NO' => $user->lang['No'],

				'S_CONFIRM_ACTION' => "mcp.$phpEx$SID",
				'S_HIDDEN_FIELDS' => $hidden_fields)
			);

			include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
		}
		break;

	case 'move':
		$page_title = $user->lang['Mod_CP'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		if ($confirm)
		{
			$new_forum_id = $_POST['new_forum'];
			$old_forum_id = $forum_id;

			if ($new_forum_id != $old_forum_id)
			{
				$topics = (isset($_POST['topic_id_list'])) ?  $_POST['topic_id_list'] : array($topic_id);

				$topic_list = '';
				for($i = 0; $i < count($topics); $i++)
				{
					$topic_list .= (($topic_list != '') ? ', ' : '') . intval($topics[$i]);
				}

				$sql = "SELECT *
					FROM " . TOPICS_TABLE . "
					WHERE topic_id IN ($topic_list)
						AND topic_status <> " . ITEM_MOVED;
				$result = $db->sql_query($sql);

				$row = $db->sql_fetchrowset($result);
				$db->sql_freeresult($result);

				$db->sql_transaction();

				for($i = 0; $i < count($row); $i++)
				{
					$topic_id = $row[$i]['topic_id'];

					if (isset($_POST['move_leave_shadow']))
					{
						$shadow_sql = array(
							'forum_id' => $old_forum_id, 
							'topic_title' => $db->sql_escape($row[$i]['topic_title']),
							'topic_poster' => $row[$i]['topic_poster'],
							'topic_time' => $row[$i]['topic_time'],
							'topic_status' => ITEM_MOVED,
							'topic_type' => POST_NORMAL,
							'topic_vote' => $row[$i]['topic_vote'],
							'topic_views' => $row[$i]['topic_views'],
							'topic_replies' => $row[$i]['topic_replies'],
							'topic_first_post_id' => $row[$i]['topic_first_post_id'],
							'topic_last_post_id' => $row[$i]['topic_last_post_id'],
							'topic_moved_id' => $topic_id,
						);

						// Insert topic in the old forum that indicates that the forum has moved.
						$sql = 'INSERT INTO ' . TOPICS_TABLE . ' ' . $db->sql_build_array('INSERT', $shadow_sql);
						$db->sql_query($sql);
					}

					$sql = "UPDATE " . TOPICS_TABLE . "
						SET forum_id = $new_forum_id
						WHERE topic_id = $topic_id";
					$db->sql_query($sql);

					$sql = "UPDATE " . POSTS_TABLE . "
						SET forum_id = $new_forum_id
						WHERE topic_id = $topic_id";
					$db->sql_query($sql);
				}

				// Sync the forum indexes
				sync('forum', $new_forum_id);
				sync('forum', $old_forum_id);

				$db->sql_transaction('commit');

				$message = $user->lang['Topics_Moved'] . '<br /><br />';

			}
			else
			{
				$message = $user->lang['No_Topics_Moved'] . '<br /><br />';
			}

			if (!empty($topic_id))
			{
				$redirect_page = "viewtopic.$phpEx$SID&amp;t=$topic_id";
				$message .= sprintf($user->lang['Click_return_topic'], '<a href="' . $redirect_page . '">', '</a>');
			}
			else
			{
				$redirect_page = "mcp.$phpEx$SID&amp;f=$forum_id";
				$message .= sprintf($user->lang['Click_return_modcp'], '<a href="' . $redirect_page . '">', '</a>');
			}

			$message = $message . '<br \><br \>' . sprintf($user->lang['Click_return_forum'], '<a href="' . "viewforum.$phpEx$SID&amp;f=$old_forum_id" . '">', '</a>');

			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="3;url=' . $redirect_page . '">')
			);

			trigger_error($message);
		}
		else
		{
			if (empty($_POST['topic_id_list']) && empty($topic_id))
			{
				trigger_error($user->lang['None_selected']);
			}

			$hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="f" value="' . $forum_id . '" />';

			if (isset($_POST['topic_id_list']))
			{
				$topics = $_POST['topic_id_list'];

				for($i = 0; $i < count($topics); $i++)
				{
					$hidden_fields .= '<input type="hidden" name="topic_id_list[]" value="' . intval($topics[$i]) . '" />';
				}
			}
			else
			{
				$hidden_fields .= '<input type="hidden" name="t" value="' . $topic_id . '" />';
			}

			// Set template files
			$template->set_filenames(array(
				'body' => 'mcp_move.html')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE' => $user->lang['Confirm'],
				'MESSAGE_TEXT' => $user->lang['Confirm_move_topic'],

				'L_MOVE_TO_FORUM' => $user->lang['Move_to_forum'],
				'L_LEAVE_SHADOW' => $user->lang['Leave_shadow_topic'],

				'S_FORUM_SELECT' => '<select name="new_forum">' . make_forum_select(0, $forum_id) . '</select>',
				'S_MODCP_ACTION' => "mcp.$phpEx$SID",
				'S_HIDDEN_FIELDS' => $hidden_fields)
			);

			include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
		}
		break;

	case 'lock':
		$topics = (!empty($_POST['topic_id_list'])) ? $_POST['topic_id_list'] : array($topic_id);

		$topic_id_sql = '';
		for($i = 0; $i < count($topics); $i++)
		{
			$topic_id_sql .= (($topic_id_sql != '') ? ', ' : '') . $topics[$i];
		}

		$sql = "UPDATE " . TOPICS_TABLE . "
			SET topic_status = " . ITEM_LOCKED . "
			WHERE topic_id IN ($topic_id_sql)
				AND topic_moved_id = 0";
		$db->sql_query($sql);

		if (!empty($topic_id))
		{
			$redirect_page = "viewtopic.$phpEx$SID&amp;t=$topic_id";
			$message = sprintf($user->lang['Click_return_topic'], '<a href="' . $redirect_page . '">', '</a>');
		}
		else
		{
			$redirect_page = "mcp.$phpEx$SID&amp;f=$forum_id";
			$message = sprintf($user->lang['Click_return_modcp'], '<a href="' . $redirect_page . '">', '</a>');
		}

		$message .= '<br \><br \>' . sprintf($user->lang['Click_return_forum'], "<a href=\"viewforum.$phpEx$SID&amp;f=$forum_id\">", '</a>');

		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . $redirect_page . '">')
		);

		trigger_error($user->lang['Topics_Locked'] . '<br /><br />' . $message);

		break;

	case 'unlock':
		$topics = (isset($_POST['topic_id_list'])) ?  $_POST['topic_id_list'] : array($topic_id);

		$topic_id_sql = '';
		for($i = 0; $i < count($topics); $i++)
		{
			$topic_id_sql .= (($topic_id_sql != "") ? ', ' : '') . $topics[$i];
		}

		$sql = "UPDATE " . TOPICS_TABLE . "
			SET topic_status = " . ITEM_UNLOCKED . "
			WHERE topic_id IN ($topic_id_sql)
				AND topic_moved_id = 0";
		$db->sql_query($sql);

		if (!empty($topic_id))
		{
			$redirect_page = "viewtopic.$phpEx$SID&amp;t=$topic_id";
			$message = sprintf($user->lang['Click_return_topic'], '<a href="' . $redirect_page . '">', '</a>');
		}
		else
		{
			$redirect_page = "mcp.$phpEx$SID&amp;f=$forum_id";
			$message = sprintf($user->lang['Click_return_modcp'], '<a href="' . $redirect_page . '">', '</a>');
		}

		$message = $message . '<br \><br \>' . sprintf($user->lang['Click_return_forum'], '<a href="' . "viewforum.$phpEx$SID&amp;f=$forum_id" . '">', '</a>');

		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . $redirect_page . '">')
		);

		trigger_error($user->lang['Topics_Unlocked'] . '<br /><br />' . $message);

		break;

	case 'split':
		$page_title = $user->lang['Mod_CP'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		if (isset($_POST['split_type_all']) || isset($_POST['split_type_beyond']))
		{
			$posts = $_POST['post_id_list'];

			$sql = "SELECT poster_id, topic_id, post_time
				FROM " . POSTS_TABLE . "
				WHERE post_id = " . $posts[0];
			$result = $db->sql_query($sql);

			$post_rowset = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$first_poster = $post_rowset['poster_id'];
			$topic_id = $post_rowset['topic_id'];
			$post_time = $post_rowset['post_time'];

			$post_subject = $db->sql_escape(trim(htmlspecialchars($_POST['subject'])));
			if (empty($post_subject))
			{
				trigger_error($user->lang['Empty_subject']);
			}

			$new_forum_id = intval($_POST['new_forum_id']);
			$topic_time = time();

			$db->sql_transaction();

			$sql  = "INSERT INTO " . TOPICS_TABLE . " (topic_title, topic_poster, topic_time, forum_id, topic_status, topic_type)
				VALUES ('$post_subject', $first_poster, " . $topic_time . ", $new_forum_id, " . ITEM_UNLOCKED . ", " . POST_NORMAL . ")";
			$db->sql_query($sql);

			$new_topic_id = $db->sql_nextid();

			if(!empty($_POST['split_type_all']))
			{
				$post_id_sql = '';
				for($i = 0; $i < count($posts); $i++)
				{
					$post_id_sql .= (($post_id_sql != '') ? ', ' : '') . $posts[$i];
				}

				$sql = "UPDATE " . POSTS_TABLE . "
					SET topic_id = $new_topic_id, forum_id = $new_forum_id
					WHERE post_id IN ($post_id_sql)";
			}
			else if(!empty($_POST['split_type_beyond']))
			{
				$sql = "UPDATE " . POSTS_TABLE . "
					SET topic_id = $new_topic_id, forum_id = $new_forum_id
					WHERE post_time >= $post_time
						AND topic_id = $topic_id";
			}

			$db->sql_query($sql);

			sync('topic', $new_topic_id);
			sync('topic', $topic_id);
			sync('forum', $new_forum_id);
			sync('forum', $forum_id);

			$db->sql_transaction('commit');

			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="3;url=' . "viewtopic.$phpEx$SID&amp;t==$topic_id" . '">')
			);

			trigger_error($user->lang['Topic_split'] . '<br /><br />' . sprintf($user->lang['Click_return_topic'], '<a href="' . "viewtopic.$phpEx$SID&amp;t=$topic_id" . '">', '</a>'));
		}
		else
		{
			//
			// Set template files
			//
			$template->set_filenames(array(
				'body' => 'mcp_split.html')
			);

			$sql = "SELECT u.username, p.*, pt.post_text, pt.bbcode_uid, pt.post_subject, p.post_username
				FROM " . POSTS_TABLE . " p, " . USERS_TABLE . " u, " . POSTS_TEXT_TABLE . " pt
				WHERE p.topic_id = $topic_id
					AND p.poster_id = u.user_id
					AND p.post_id = pt.post_id
				ORDER BY p.post_time ASC";
			$result = $db->sql_query($sql);

			$s_hidden_fields = '<input type="hidden" name="f" value="' . $forum_id . '" /><input type="hidden" name="mode" value="split" />';

			if(($total_posts = $db->sql_numrows($result)) > 0)
			{
				$postrow = $db->sql_fetchrowset($result);

				$template->assign_vars(array(
					'L_SPLIT_TOPIC' => $user->lang['Split_Topic'],
					'L_SPLIT_ITEM_EXPLAIN' => $user->lang['Split_Topic_explain'],
					'L_AUTHOR' => $user->lang['Author'],
					'L_MESSAGE' => $user->lang['Message'],
					'L_SELECT' => $user->lang['Select'],
					'L_SPLIT_SUBJECT' => $user->lang['Split_title'],
					'L_SPLIT_FORUM' => $user->lang['Split_forum'],
					'L_POSTED' => $user->lang['Posted'],
					'L_SPLIT_POSTS' => $user->lang['Split_posts'],
					'L_SUBMIT' => $user->lang['Submit'],
					'L_SPLIT_AFTER' => $user->lang['Split_after'],
					'L_POST_SUBJECT' => $user->lang['Post_subject'],
					'L_MARK_ALL' => $user->lang['Mark_all'],
					'L_UNMARK_ALL' => $user->lang['Unmark_all'],
					'L_POST' => $user->lang['Post'],

					'FORUM_NAME' => $forum_name,

					'U_VIEW_FORUM' => "viewforum.$phpEx$SID&amp;f=$forum_id",

					'S_SPLIT_ACTION' => "mcp.$phpEx$SID",
					'S_HIDDEN_FIELDS' => $s_hidden_fields,
					'S_FORUM_SELECT' => '<select name="new_forum_id">' . make_forum_select() . '</select>')
				);

				for($i = 0; $i < $total_posts; $i++)
				{
					$post_id = $postrow[$i]['post_id'];
					$poster_id = $postrow[$i]['user_id'];
					$poster = $postrow[$i]['username'];

					$post_date = $user->format_date($postrow[$i]['post_time']);

					$bbcode_uid = $postrow[$i]['bbcode_uid'];
					$message = $postrow[$i]['post_text'];
					$post_subject = ($postrow[$i]['post_subject'] != '') ? $postrow[$i]['post_subject'] : $topic_title;

					// If the board has HTML off but the post has HTML
					// on then we process it, else leave it alone
					if (!$config['allow_html'])
					{
						if ($postrow[$i]['enable_html'])
						{
							$message = preg_replace('#(<)([\/]?.*?)(>)#is', '&lt;\\2&gt;', $message);
						}
					}

					if ($bbcode_uid != '')
					{
//						$message = ($config['allow_bbcode']) ? bbencode_second_pass($message, $bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $message);
					}

					// Define censored word matches
					$orig_word = array();
					$replacement_word = array();
					obtain_word_list($orig_word, $replacement_word);

					if (count($orig_word))
					{
						$post_subject = preg_replace($orig_word, $replacement_word, $post_subject);
						$message = preg_replace($orig_word, $replacement_word, $message);
					}

					if ($config['allow_smilies'] && $postrow[$i]['enable_smilies'])
					{
					}

					$message = nl2br($message);

					$checkbox = ($i > 0) ? '<input type="checkbox" name="post_id_list[]" value="' . $post_id . '" />' : '&nbsp;';

					$template->assign_block_vars('postrow', array(
						'POSTER_NAME' => $poster,
						'POST_DATE' => $post_date,
						'POST_SUBJECT' => $post_subject,
						'MESSAGE' => $message,
						'POST_ID' => $post_id,

						'S_SPLIT_CHECKBOX' => $checkbox)
					);
				}
			}
		}
		break;

	case 'ip':
		$page_title = $user->lang['Mod_CP'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$rdns_ip_num = (isset($_GET['rdns'])) ? $_GET['rdns'] : '';

		if (!$post_id)
		{
			trigger_error($user->lang['No_such_post']);
		}

		// Set template files
		$template->set_filenames(array(
			'body' => 'mcp_viewip.html')
		);

		// Look up relevent data for this post
		$sql = "SELECT poster_ip, poster_id
			FROM " . POSTS_TABLE . "
			WHERE post_id = $post_id";
		$result = $db->sql_query($sql);

		if (!($post_row = $db->sql_fetchrow($result)))
		{
			trigger_error($user->lang['No_such_post']);
		}

		$ip_this_post = $post_row['poster_ip'];
		$ip_this_post = ($rdns_ip_num == $ip_this_post) ? gethostbyaddr($ip_this_post) : $ip_this_post;

		$poster_id = $post_row['poster_id'];

		$template->assign_vars(array(
			'L_IP_INFO' => $user->lang['IP_info'],
			'L_THIS_POST_IP' => $user->lang['This_posts_IP'],
			'L_OTHER_IPS' => $user->lang['Other_IP_this_user'],
			'L_OTHER_USERS' => $user->lang['Users_this_IP'],
			'L_LOOKUP_IP' => $user->lang['Lookup_IP'],
			'L_SEARCH' => $user->lang['Search'],

			'SEARCH_IMG' => $images['icon_search'],

			'IP' => $ip_this_post,

			'U_LOOKUP_IP' => "mcp.$phpEx$SID&amp;mode=ip&amp;p=$post_id&amp;t=$topic_id&amp;rdns=" . $ip_this_post)
		);

		//
		// Get other IP's this user has posted under
		//
		$sql = "SELECT poster_ip, COUNT(*) AS postings
			FROM " . POSTS_TABLE . "
			WHERE poster_id = $poster_id
			GROUP BY poster_ip
			ORDER BY postings DESC";
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			$i = 0;
			do
			{
				if ($row['poster_ip'] == $post_row['poster_ip'])
				{
					$template->assign_vars(array(
						'POSTS' => $row['postings'] . ' ' . (($row['postings'] == 1) ? $user->lang['Post'] : $user->lang['Posts']))
					);
					continue;
				}

				$ip = $row['poster_ip'];
				$ip = ($rdns_ip_num == $row['poster_ip'] || $rdns_ip_num == 'all') ? gethostbyaddr($ip) : $ip;

				$template->assign_block_vars('iprow', array(
					'IP' => $ip,
					'POSTS' => $row['postings'] . ' ' . (($row['postings'] == 1) ? $user->lang['Post'] : $user->lang['Posts']),

					'U_LOOKUP_IP' => "mcp.$phpEx$SID&amp;mode=ip&amp;p=$post_id&amp;t=$topic_id&amp;rdns=" . $row['poster_ip'])
				);

				$i++;
			}
			while ($row = $db->sql_fetchrow($result));
		}
		$db->sql_freeresult($result);

		// Get other users who've posted under this IP
		$sql = "SELECT u.user_id, u.username, COUNT(*) as postings
			FROM " . USERS_TABLE ." u, " . POSTS_TABLE . " p
			WHERE p.poster_id = u.user_id
				AND p.poster_ip = '" . $post_row['poster_ip'] . "'
			GROUP BY u.user_id, u.username
			ORDER BY postings DESC";
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			$i = 0;
			do
			{
				$id = $row['user_id'];
				$username = (!$id) ? $user->lang['Guest'] : $row['username'];

				$template->assign_block_vars('userrow', array(
					'USERNAME' => $username,
					'POSTS' => $row['postings'] . ' ' . (($row['postings'] == 1) ? $user->lang['Post'] : $user->lang['Posts']),
					'L_SEARCH_POSTS' => sprintf($user->lang['Search_user_posts'], $username),

					'U_PROFILE' => "ucp.$phpEx$SID&amp;mode=viewprofile&amp;u=$id",
					'U_SEARCHPOSTS' => "search.$phpEx$SID&amp;search_author=" . urlencode($username) . "&amp;showresults=topics")
				);

				$i++;
			}
			while ($row = $db->sql_fetchrow($result));
		}
		$db->sql_freeresult($result);
		break;

	default:
		$page_title = $user->lang['Mod_CP'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mcp_topics.html')
		);
		make_jumpbox('mcp.'.$phpEx);

		$template->assign_vars(array(
			'FORUM_NAME' => $forum_name,

			'L_MOD_CP' => $user->lang['Mod_CP'],
			'L_MOD_CP_EXPLAIN' => $user->lang['Mod_CP_explain'],
			'L_SELECT' => $user->lang['Select'],
			'L_DELETE' => $user->lang['Delete'],
			'L_MOVE' => $user->lang['Move'],
			'L_LOCK' => $user->lang['Lock'],
			'L_UNLOCK' => $user->lang['Unlock'],
			'L_TOPICS' => $user->lang['Topics'],
			'L_REPLIES' => $user->lang['Replies'],
			'L_LASTPOST' => $user->lang['Last_Post'],
			'L_SELECT' => $user->lang['Select'],

			'U_VIEW_FORUM' => "viewforum.$phpEx$SID&amp;f=$forum_id",
			'S_HIDDEN_FIELDS' => '<input type="hidden" name="f" value="' . $forum_id . '">',
			'S_MODCP_ACTION' => "mcp.$phpEx$SID")
		);

		// Define censored word matches
		$orig_word = array();
		$replacement_word = array();
		obtain_word_list($orig_word, $replacement_word);

		$sql = "SELECT t.*, u.username, u.user_id, p.post_time
			FROM " . TOPICS_TABLE . " t, " . USERS_TABLE . " u, " . POSTS_TABLE . " p
			WHERE t.forum_id = $forum_id
				AND t.topic_poster = u.user_id
				AND p.post_id = t.topic_last_post_id
			ORDER BY t.topic_type DESC, p.post_time DESC
			LIMIT $start, " . $config['topics_per_page'];
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$topic_title = '';

			if ($row['topic_status'] == ITEM_LOCKED)
			{
				$folder_img = $user->img('folder_locked');
				$folder_alt = $user->lang['Topic_locked'];
			}
			else
			{
				if ($row['topic_type'] == POST_ANNOUNCE)
				{
					$folder_img = $user->img('folder_announce');
					$folder_alt = $user->lang['Announcement'];
				}
				else if ($row['topic_type'] == POST_STICKY)
				{
					$folder_img = $user->img('folder_sticky');
					$folder_alt = $user->lang['Sticky'];
				}
				else
				{
					$folder_img = $user->img('folder');
					$folder_alt = $user->lang['No_new_posts'];
				}
			}

			$topic_id = $row['topic_id'];
			$topic_type = $row['topic_type'];
			$topic_status = $row['topic_status'];

			if ($topic_type == POST_ANNOUNCE)
			{
				$topic_type = $user->lang['Topic_Announcement'] . ' ';
			}
			else if ($topic_type == POST_STICKY)
			{
				$topic_type = $user->lang['Topic_Sticky'] . ' ';
			}
			else if ($topic_status == ITEM_MOVED)
			{
				$topic_type = $user->lang['Topic_Moved'] . ' ';
			}
			else
			{
				$topic_type = '';
			}

			if ($row['topic_vote'])
			{
				$topic_type .= $user->lang['Topic_Poll'] . ' ';
			}

			$topic_title = $row['topic_title'];
			if (count($orig_word))
			{
				$topic_title = preg_replace($orig_word, $replacement_word, $topic_title);
			}

			$u_view_topic = "mcp.$phpEx$SID&amp;mode=split&amp;t=$topic_id";
			$topic_replies = $row['topic_replies'];

			$last_post_time = $user->format_date($row['post_time']);

			$template->assign_block_vars('topicrow', array(
				'U_VIEW_TOPIC' => $u_view_topic,

				'TOPIC_FOLDER_IMG' => $folder_img,
				'TOPIC_TYPE' => $topic_type,
				'TOPIC_TITLE' => $topic_title,
				'REPLIES' => $topic_replies,
				'LAST_POST_TIME' => $last_post_time,
				'TOPIC_ID' => $topic_id,

				'L_TOPIC_FOLDER_ALT' => $folder_alt)
			);
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'PAGINATION' => generate_pagination("mcp.$phpEx$SID&amp;f=$forum_id", $forum_topics, $config['topics_per_page'], $start),
			'PAGE_NUMBER' => sprintf($user->lang['Page_of'], (floor($start / $config['topics_per_page']) + 1), ceil($forum_topics / $config['topics_per_page'])),
			'L_GOTO_PAGE' => $user->lang['Goto_page'])
		);

		break;
}

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>