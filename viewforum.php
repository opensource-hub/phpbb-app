<?php
/***************************************************************************  
 *                                 
 *                            -------------------                         
 *   begin                : Saturday, Feb 13, 2001 
 *   copyright            : (C) 2001 The phpBB Group        
 *   email                : support@phpbb.com                           
 *                                                          
 *   $Id$
 *                                                            
 * 
 ***************************************************************************/ 


/***************************************************************************  
 *                                                     
 *   This program is free software; you can redistribute it and/or modify    
 *   it under the terms of the GNU General Public License as published by   
 *   the Free Software Foundation; either version 2 of the License, or  
 *   (at your option) any later version.                      
 *                                                          
 * 
 ***************************************************************************/ 
include('extension.inc');
include('common.'.$phpEx);

$pagetype = "viewforum";
$page_title = "View Forum - $forum_name";

//
// Obtain which forum id is required
//
if(!isset($HTTP_GET_VARS['forum']) && !isset($HTTP_POST_VARS['forum']))  // For backward compatibility
{
	$forum_id = ($HTTP_GET_VARS[POST_FORUM_URL]) ? $HTTP_GET_VARS[POST_FORUM_URL] : $HTTP_POST_VARS[POST_FORUM_URL];
}
else
{
	$forum_id = ($HTTP_GET_VARS['forum']) ? $HTTP_GET_VARS['forum'] : $HTTP_POST_VARS['forum'];
}

//
// Check if the user has actually sent a forum ID with his/her request
// If not give them a nice error page.
//
if(isset($forum_id))
{
	$sql = "SELECT f.forum_type, f.forum_name, f.forum_topics, u.username, u.user_id
		FROM ".FORUMS_TABLE." f, ".FORUM_MODS_TABLE." fm, ".USERS_TABLE." u
		WHERE f.forum_id = '$forum_id'
			AND fm.forum_id = '$forum_id'
			AND u.user_id = fm.user_id";
}
else 
{
	error_die(GENERAL_ERROR, "You have reached this page in error, please go back and try again");
}

//
// Start session management
//
$userdata = session_pagestart($user_ip, $forum_id, $session_length);
init_userprefs($userdata);
//
// End session management
//

if(!$result = $db->sql_query($sql))
{
	error_die(SQL_QUERY, "Couldn't obtain forums information.", __LINE__, __FILE__);
}
// If the query doesn't return any rows this 
// isn't a valid forum. Inform the user.
if(!$total_rows = $db->sql_numrows($result)) 
{
   error_die(GENERAL_ERROR, "The forum you selected does not exist. Please go back and try again.");
}

//
// Add checking for private forums here!!
//



$forum_row = $db->sql_fetchrowset($result);
if(!$forum_row)
{
	error_die(SQL_QUERY, "Couldn't obtain rowset.", __LINE__, __FILE__);
}

$forum_name = stripslashes($forum_row[0]["forum_name"]);
$topics_count = $forum_row[0]["forum_topics"];

for($x = 0; $x < $db->sql_numrows($result); $x++)
{
	if($x > 0)
		$forum_moderators .= ", ";
	
	$forum_moderators .= "<a href=\"".append_sid("profile.$phpEx?mode=viewprofile&".POST_USERS_URL."=".$forum_row[$x]["user_id"])."\">".$forum_row[$x]["username"]."</a>";
}

if(!isset($start))
{
	$start = 0;
}

$sql = "SELECT t.*, u.username, u.user_id, u2.username as user2, u2.user_id as id2, p.post_time
	FROM " . TOPICS_TABLE ." t
	LEFT JOIN ". USERS_TABLE. " u ON t.topic_poster = u.user_id
	LEFT JOIN ".POSTS_TABLE." p ON p.post_id = t.topic_last_post_id
	LEFT JOIN " . USERS_TABLE . " u2 ON p.poster_id = u2.user_id
	WHERE t.forum_id = '$forum_id'
	ORDER BY topic_time DESC
	LIMIT $start, ".$board_config['topics_per_page'];
if(!$t_result = $db->sql_query($sql))
{
   error_die(SQL_QUERY, "Couldn't obtain topic information.", __LINE__, __FILE__);
}
$total_topics = $db->sql_numrows($t_result);

//
// Post URL generation for 
// templating vars
//
$post_new_topic_url = append_sid("posting.".$phpEx."?mode=newtopic&".POST_FORUM_URL."=$forum_id");
$template->assign_vars(array(
	"U_POST_NEW_TOPIC" => $post_new_topic_url));

//
// Dump out the page header
//
include('includes/page_header.'.$phpEx);

//
// Okay, lets dump out the page ...
//
if($total_topics)
{
	$topic_rowset = $db->sql_fetchrowset($t_result);
	for($x = 0; $x < $total_topics; $x++)
	{
		$topic_title = stripslashes($topic_rowset[$x]["topic_title"]);
		$topic_id = $topic_rowset[$x]["topic_id"];
		$replies = $topic_rowset[$x]["topic_replies"];
		if($replies > $board_config['posts_per_page'])
		{
			$goto_page = "&nbsp;&nbsp;&nbsp;(<img src=\"".$images['posticon']."\">$l_gotopage: ";
			$times = 1;
			for($i = 0; $i < ($replies + 1); $i += $board_config['posts_per_page'])
			{
				if($times > 4) 
				{
					if(($i + $board_config['posts_per_page']) >= ($replies + 1)) 
					{
						$goto_page.=" ... <a href=\"".append_sid("viewtopic.$phpEx?".POST_TOPIC_URL."=".$topic_id."&start=$i")."\">$times</a>";
					}
				}
				else 
				{
					if($times != 1)
					{
						$goto_page.= ", ";
					}
					$goto_page.= "<a href=\"".append_sid("viewtopic.$phpEx?".POST_TOPIC_URL."=".$topic_id."&start=$i")."\">$times</a>";
				}
				$times++;
			}
			$goto_page.= ")";
		}
		else 
		{
			$goto_page = "";
		}

		$folder_img = "<img src=\"".$images['folder']."\">";

		$view_topic_url = append_sid("viewtopic.".$phpEx."?".POST_TOPIC_URL."=".$topic_id."&".$replies);

		$topic_poster = stripslashes($topic_rowset[$x]["username"]);
		$topic_poster_profile_url = append_sid("profile.$phpEx?mode=viewprofile&".POST_USERS_URL."=".$topic_rowset[$x]["user_id"]);

		$last_post_time = create_date($board_config['default_dateformat'], $topic_rowset[$x]["post_time"], $board_config['default_timezone']);
		$last_post_user = $topic_rowset[$x]["user2"];
		$last_post_profile_url = append_sid("profile.$phpEx?mode=viewprofile&".POST_USERS_URL."=".$topic_rowset[$x]["id2"]);

		$views = $topic_rowset[$x]["topic_views"];

		$template->assign_block_vars("topicrow", array(
			"FORUM_ID" => $forum_id,
			"TOPIC_ID" => $topic_id,
			"FOLDER" => $folder_img, 
			"TOPIC_POSTER" => $topic_poster,
			"U_TOPIC_POSTER_PROFILE" => $topic_poster_profile_url,
			"GOTO_PAGE" => $goto_page,
			"REPLIES" => $replies,
			"TOPIC_TITLE" => $topic_title,
			"VIEWS" => $views,
			"LAST_POST_TIME" => $last_post_time,
			"LAST_POST_USER" => $last_post_user,

			"U_VIEW_TOPIC" => $view_topic_url,
			"U_LAST_POST_USER_PROFILE" => $last_post_profile_url)
		);
	}

	$template->assign_vars(array(
		"PAGINATION" => generate_pagination("viewforum.$phpEx?".POST_FORUM_URL."=$forum_id", $topics_count, $board_config['topics_per_page'], $start))
	);
	$template->pparse("body");
}
else
{
	error_die(NO_POSTS);
}
			       
include('includes/page_tail.'.$phpEx);

?>