<div align="center"><table width="98%" cellspacing="0" cellpadding="4" border="0">
	<tr><form method="post" action="{S_POST_DAYS_ACTION}">
		<td align="left" valign="bottom" nowrap><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}" color="{T_FONTCOLOR1}"><a href="{U_INDEX}">{SITENAME}&nbsp;{L_INDEX}</a> -> {FORUM_NAME}</font></td>
		<td align="right" valign="bottom" nowrap><font face="{T_FONTFACE1}" size="{T_FONTSIZE2}" color="{T_FONTCOLOR1}">Display Topics from previous :&nbsp;{S_SELECT_POST_DAYS}&nbsp;<input type="submit"  value="Go"></font></td>
	</form></tr>
</table></div>

<div align="center"><table border="0" cellpadding="1" cellspacing="0" width="98%">
	<tr>
		<td bgcolor="{T_TH_COLOR1}"><table border="0" cellpadding="4" cellspacing="1" width="100%">
			<tr>
				<td colspan="6" bgcolor="{T_TH_COLOR2}"><table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td><font face="{T_FONTFACE1}" size="{T_FONTSIZE3}"><b>{FORUM_NAME}</b></font><br><font face="{T_FONTFACE2}" size="{T_FONTSIZE1}">{L_MODERATOR} : {MODERATORS}</font></TD>
						<td align="right"><a href="{U_POST_NEW_TOPIC}"><img src="templates/PSO/images/post.gif" border="1"></a></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td width="4%" bgcolor="{T_TH_COLOR3}">&nbsp;</td>
				<td bgcolor="{T_TH_COLOR3}"><font face="verdana" size="{T_FONTSIZE2}"><b>&nbsp;{L_TOPICS}</b></font></td>
				<td width="8%" bgcolor="{T_TH_COLOR3}" align="center"><font face="{T_FONTFACE2}" size="{T_FONTSIZE2}"><b>{L_REPLIES}</b></font></td>
				<td width="20%" bgcolor="{T_TH_COLOR3}" align="center"><font face="{T_FONTFACE2}" size="{T_FONTSIZE2}"><b>&nbsp;{L_AUTHOR}</b></font></td>
				<td width="6%" bgcolor="{T_TH_COLOR3}" align="center"><font face="{T_FONTFACE2}" size="{T_FONTSIZE2}"><b>{L_VIEWS}</b></font></td>
				<td width="17%" bgcolor="{T_TH_COLOR3}" align="center"><font face="{T_FONTFACE2}" size="{T_FONTSIZE2}"><b>{L_LASTPOST}</b></font></td>	
			</tr>
			<!-- BEGIN topicrow -->
			<tr>
				<td bgcolor="{T_TD_COLOR1}" align="center" valign="middle">&nbsp;{topicrow.FOLDER}&nbsp;</td>
				<td bgcolor="{T_TD_COLOR2}"><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}">&nbsp;<a href="{topicrow.U_VIEW_TOPIC}">{topicrow.TOPIC_TITLE}</a>&nbsp;{topicrow.GOTO_PAGE}</td>
				<td bgcolor="{T_TD_COLOR1}" align="center" valign="middle"><font face="{T_FONTFACE1}" size="{T_FONTSIZE2}">{topicrow.REPLIES}</font></td>
				<td bgcolor="{T_TD_COLOR2}" align="center" valign="middle"><font face="{T_FONTFACE1}" size="{T_FONTSIZE2}"><a href="{topicrow.U_TOPIC_POSTER_PROFILE}">{topicrow.TOPIC_POSTER}</a></font></td>
				<td bgcolor="{T_TD_COLOR1}" align="center" valign="middle"><font face="{T_FONTFACE1}" size="{T_FONTSIZE2}">{topicrow.VIEWS}</font></td>
				<td bgcolor="{T_TD_COLOR2}" align="center" valign="middle"><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}">{topicrow.LAST_POST_TIME}<br />{L_BY} <a href="{topicrow.U_LAST_POST_USER_PROFILE}">{topicrow.LAST_POST_USER}</a></font></td>
			</tr>
		    <!-- END topicrow -->
			<tr>
				<td colspan="6" bgcolor="{T_TH_COLOR2}"><table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="5" align="left" valign="middle"><a href="{U_POST_NEW_TOPIC}"><img src="templates/PSO/images/post.gif" border="1"></a></td>
						<td align="left" valign="middle">&nbsp;&nbsp;&nbsp;<font face="{T_FONTFACE1}" size="{T_FONTSIZE2}">{L_PAGE} <b>{ON_PAGE}</b> {L_OF} <b>{TOTAL_PAGES}</b></font>&nbsp;</td>
						<td align="right" valign="middle"><font face="{T_FONTFACE2}" size="{T_FONTSIZE2}">{L_GOTO_PAGE}:&nbsp;{PAGINATION}&nbsp;</font></td>
					</tr>
				</table></td>
			</tr>
		</table></td>
	</tr>
</table></div>

<div align="center"><table cellspacing="2" border="0" width="98%">
	<tr>
		<td width="20"></td>
		<td width="40%"><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}"><b>{S_TIMEZONE}</b></font></td>
		<td rowspan="6" align="right" valign="top" nowrap>{JUMPBOX}</td>
	</tr>
	<tr>
		<td><img src="images/red_folder.gif"></td>
		<td><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}">{L_NEWPOSTS}</td>
	</tr>
	<tr>
		<td><img src="images/hot_red_folder.gif"></td>
		<td><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}">{L_NEWPOSTS} [ > {L_HOT_THRESHOLD} ]</font></td>
	</tr>
	<tr>
		<td><img src="images/folder.gif"></td>
		<td><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}">{L_NONEWPOSTS}</font></td>
	</tr>
	<tr>
		<td><img src="images/hot_folder.gif"></td>
		<td><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}">{L_NONEWPOSTS} [ > {L_HOT_THRESHOLD} ]</font></td>
	</tr>
	<tr>
		<td><img src="images/lock.gif"></td>
		<td><font face="{T_FONTFACE1}" size="{T_FONTSIZE1}">{L_TOPIC_IS_LOCKED}</font></td>
	</tr>
</table></div>