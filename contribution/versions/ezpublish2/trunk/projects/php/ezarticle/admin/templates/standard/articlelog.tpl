<form action="/article/articleedit/edit/{article_id}/" method="post">

<h1>{intl-article_log}</h1>

<hr size="4" noshade="noshade" />
<br />
<!-- BEGIN log_list_tpl -->

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<th>
	{intl-log_date}:
	</th>
	<th>
	{intl-log_message}:
	</th>
</tr>
<!-- BEGIN log_item_tpl -->
<tr>
	<td>
	{log_date}
	</td>
	<td>
	{log_message}
	</td>
</tr>

<!-- END log_item_tpl -->
</tr>
</table>
<br />
<!-- END log_list_tpl -->

<hr size="4" noshade="noshade" />

<input class="okbutton" type="submit" name="Edit" value="{intl-edit}" />

</form>
