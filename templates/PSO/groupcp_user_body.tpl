<div align="center"><table width="98%" cellspacing="0" cellpadding="4" border="0">
	<tr>
		<td align="left"><span class="gensmall"><a href="{U_INDEX}">{SITENAME}&nbsp;{L_INDEX}</a></span></td>
	</tr>
</table></div>

<div align="center"><table border="0" cellpadding="1" cellspacing="0" width="98%">
	<tr>
		<td class="tablebg"><table width="100%" cellpadding="4" cellspacing="1" border="0">
			<!-- BEGIN group_joined -->
			<tr>
				<td class="cat" colspan="2"><span class="cattitle"><b>{L_GROUP_MEMBERSHIP_DETAILS}</b></span></td>
			</tr>
			<!-- BEGIN group_member -->
			<tr>
				<td class="row1"><span class="gen">{L_YOU_BELONG_GROUPS}</span></td>
				<td class="row2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr><form method="post" action="{S_USERGROUP_ACTION}">
						<td width="40%" align="center">&nbsp;{GROUP_MEMBER_SELECT}&nbsp;</td>
						<td width="30%" align="center">&nbsp;<input type="submit" name="viewinfo" value="{L_VIEW_INFORMATION}">&nbsp;</td>
						<td width="30%" align="center">&nbsp;<input type="submit" name="unsub" value="{L_UNSUBSCRIBE}">&nbsp;</td>
					</form></tr>
				</table></td>
			</tr>
			<!-- END group_member -->
			<!-- BEGIN group_pending -->
			<tr>
				<td class="row1"><span class="gen">{L_PENDING_GROUPS}</span></td>
				<td class="row2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr><form method="post" action="{S_USERGROUP_ACTION}">
						<td width="40%" align="center">&nbsp;{GROUP_PENDING_SELECT}&nbsp;</td>
						<td width="30%" align="center">&nbsp;<input type="submit" name="viewinfo" value="{L_VIEW_INFORMATION}">&nbsp;</td>
						<td width="30%" align="center">&nbsp;<input type="submit" name="unsubpending" value="{L_UNSUBSCRIBE}">&nbsp;</td>
					</form></tr>
				</table></td>
			</tr>
			<!-- END group_pending -->
			<!-- END group_joined -->
			<!-- BEGIN group_subscribe -->
			<tr>
				<td class="cat" colspan="2"><span class="cattitle"><b>{L_JOIN_A_GROUP}</b></span></td>
			</tr>
			<tr>
				<td class="row1"><span class="gen">{L_SELECT_A_GROUP}</span></td>
				<td class="row2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr><form method="post" action="{S_USERGROUP_ACTION}">
						<td width="40%" align="center">&nbsp;{GROUP_LIST_SELECT}&nbsp;</td>
						<td width="30%" align="center">&nbsp;<input type="submit" name="viewinfo" value="{L_VIEW_INFORMATION}">&nbsp;</td>
						<td width="30%" align="center">&nbsp;</td>
					</form></tr>
				</table></td>
			</tr>
			<!-- END group_join -->
		</table></td>
	</tr>
</table></div>

<br clear="all">
