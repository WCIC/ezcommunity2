<form action="/todo/prioritytypeedit/new/">

<h1>{intl-headline}</h1>

<hr noshade size="4"/>

<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<!-- BEGIN priority_item_tpl -->
<tr>
	<td class="{td_class}">
	{priority_type_name}
	</td>
	<td width="1%" class="{td_class}">
	<a href="/todo/prioritytypeedit/edit/{priority_type_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('pt{priority_type_id}-red','','/admin/images/{site_style}/redigerminimrk.gif',1)"><img name="pt{priority_type_id}-red" border="0" src="/admin/images/{site_style}/redigermini.gif" width="16" height="16" align="top" alt="Edit" /></a>
	</td>
	<td width="1%" class="{td_class}">
	<a href="/todo/prioritytypeedit/delete/{priority_type_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('pt{priority_type_id}-slett','','/admin/images/{site_style}/slettminimrk.gif',1)"><img name="pt{priority_type_id}-slett" border="0" src="/admin/images/{site_style}/slettmini.gif" width="16" height="16" align="top" alt="Delete" /></a>
	</td>
</tr>
<!-- END priority_item_tpl -->
</table>

<hr noshade size="4"/>

<input class="stdbutton" type="submit" value="{intl-newpriority}">

</form>
