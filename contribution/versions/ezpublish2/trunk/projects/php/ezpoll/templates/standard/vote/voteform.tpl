<h2>{head_line}</h2>
<form method="post" action="/poll/vote/{poll_id}/">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<!-- BEGIN vote_item_tpl -->
<tr>
<input type="radio" value="{choice_id}" name="ChoiceID"> {choice_name}<br>
</tr>
<!-- END vote_item_tpl -->
<br>
<input type="hidden" name="PollID" value="{poll_id}" />
<input type="submit" value="Vote" />

</table>
</form>
