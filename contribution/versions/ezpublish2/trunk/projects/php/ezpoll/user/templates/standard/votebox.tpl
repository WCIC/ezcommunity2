<table width="100%" cellspacing="0" cellpadding="2" border="0">
<form method="post" action="/poll/userlogin/vote/{poll_id}/">
<tr>
	<td colspan="2" class="menuhead" bgcolor="#c82828">{intl-headline}</td>
</tr>
<tr>
	<td colspan="2" class="menutext">
	{head_line}
	</td>
</tr>
<!-- BEGIN vote_item_tpl -->
<tr>
	<td width="1%">
	<input type="radio" value="{choice_id}" name="ChoiceID">
	</td>
	<td class="small">
	{choice_name}
	</td>
</tr>
<!-- END vote_item_tpl -->

<!-- BEGIN novote_item_tpl -->
<tr>
	<td class="small">
	{novote_item}
	</td>
</tr>
<!-- END novote_item_tpl -->

<tr>
	<td colspan="2">
	<input type="hidden" name="PollID" value="{poll_id}" />
	<input type="submit" value="{intl-vote}" />
	</td>
</tr>
<tr>
	<td colspan="2">
	<img src="/images/dot.gif" width="12" height="10"><a class="menu" href="/poll/result/{poll_id}">{intl-result}</a>
	</td>
</tr>
<tr>
	<td colspan="2">
	<img src="/images/dot.gif" width="12" height="10"><a class="menu" href="/poll/polls">{intl-polls}</a>
	</td>
</tr>
</form>
</table>
