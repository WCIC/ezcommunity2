<h1>{intl-head_line}</h1>

<p class="error">{error_msg}</p>

<form action="/poll/pollist/">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<tr>
	<td>
	<h3>{intl-poll}:</h3>
	</td>

	<td>
	<h3>{intl-description}:</h3>
	</td>

	<td>
	<h3>{intl-anonymous}:</h3>
	</td>

	<td>
	<h3>{intl-enabled}:</h3>
	</td>

	<td>
	<h3>{intl-closed}:</h3>
	</td>

	<td>
	<h3>{intl-main}:</h3>
	</td>

	<td>
	<h3>{intl-edit}:</h3>
	</td>

	<td>
	<h3>{intl-delete}:</h3>
	</td>


</tr>
<tr>
	<td>
	{nopolls}
	</td>
	<!-- BEGIN poll_item_tpl -->
	<tr>
	<td class="{td_class}">
	<a href="/poll/polledit/edit/{poll_id}/">{poll_name}</a>
	</td>
	<td class="{td_class}">
	{poll_description}
	</td>

	<td class="{td_class}">
	{anonymous}
	</td>

	<td class="{td_class}">
	{poll_is_enabled}
	</td>

	<td class="{td_class}">
	{poll_is_closed}
	</td>

	<td class="{td_class}">
	<input type="radio" name="MainPollID" value="{poll_id}" {is_checked} />
	</td>

	<td class="{td_class}">
	<a href="/poll/polledit/edit/{poll_id}/">{intl-edit}</a>
	</td>
	<td class="{td_class}">
	<a href="#" onClick="verify( '{intl-delete}', '/poll/polledit/delete/{poll_id}'); return false;">{intl-delete}</a>
	</td>	
	</tr>
	<!-- END poll_item_tpl -->
</tr>

<tr>
	<td>
	<input type="hidden" name="Action" value="StoreMainPoll">
	<input type="submit" value="{intl-savechanges}" />
	</td>
<tr>

</form>

<tr>
	<td>
	<form method="post" action="/poll/polledit/new/">
	<input type="submit" value="{intl-addpoll}">
	</form>
	</td>
<tr>

</table>
