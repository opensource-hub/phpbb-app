
<br clear="all" />

<h1>{L_DATABASE_RESTORE}</h1>

<P>{L_RESTORE_EXPLAIN}</p>

<table cellspacing="1" cellpadding="4" border="0" align="center">
	<tr>
		<th>{L_SELECT_FILE}</th>
	</tr>
	<tr><form enctype="multipart/form-data" method="post" action="{S_DBUTILS_ACTION}">
		<td class="row1" align="center">&nbsp;<input type="file" name="backup_file">&nbsp;&nbsp;{S_HIDDEN_FIELDS}<input type="submit" name="restore_start" value="{L_START_RESTORE}">&nbsp;</td>
	</form></tr>
</table></div>

<br clear="all" />
