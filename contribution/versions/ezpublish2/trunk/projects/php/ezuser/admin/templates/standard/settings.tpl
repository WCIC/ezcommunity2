<form method="post" action="/user/settings/{action_value}/">
<h1>{intl-headline}{first_name} {last_name}</h1>

<hr noshade size="4">

<input type="checkbox" name="SingleModule" {single_module} />
<span class="boxtext">{intl-single_module}</span><br />

<br />
<hr noshade size="4">

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<input class="okbutton" type="submit" value="{intl-ok}" />
	</td>	
	<td>&nbsp;</td>
	<td>
	<input class="okbutton" type="submit" name="Cancel" value="{intl-abort}" />
	</td>
</tr>
</table>
</form>			
