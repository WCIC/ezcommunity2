<form action="/article/articleedit/mediaedit/{article_id}/" method="post">

<h1>{intl-media}: {article_name}</h1>

<hr noshade="noshade" size="4" />

<!-- BEGIN no_media_tpl -->
{intl-no_media}
<!-- END no_media_tpl -->

<!-- BEGIN media_list_tpl -->
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<th>{intl-media_id}:</th>
	<th>{intl-media_name}:</th>
	<th>{intl-media_caption}:</th>
	<th>{intl-media_description}:</th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
</tr>

<!-- BEGIN media_tpl -->
<tr>
	<td width="1%" class="{td_class}">
	{media_number}
	</td>
	<td width="94%" class="{td_class}">
	{media_name}
	</td>
	<td width="94%" class="{td_class}">
	{media_caption}
	</td>
	<td width="94%" class="{td_class}">
	{media_description}
	</td>
	<td width="1%" class="{td_class}">
	<a href="/article/articleedit/mediaedit/{article_id}/{media_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapMedia('eztp2{media_id}-red','','/admin/media/{site_style}/mediamapminimrk.gif',1)"><img name="eztp2{media_id}-red" border="0" src="/admin/media/{site_style}/mediamapmini.gif" width="16" height="16" align="top" border="0" alt="Media map" /></a>
	</td>
	<td class="{td_class}" width="1%" align="center">
	<input type="checkbox" name="MediaArrayID[]" value="{media_id}">
	</td>
</tr>
<!-- END media_tpl -->

</table>
<!-- END media_list_tpl -->

<br/>

<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td valign="top">
	<input class="stdbutton" type="submit" name="NewMedia" value="{intl-media_upload}" />
	</td>
	<td>&nbsp;</td>
	<td>
	<input class="stdbutton" type="submit" name="Browse" value="{intl-browse}" />
	</td>
</tr>
</table>

<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td valign="top">
	<input class="okbutton" type="submit" value="{intl-ok}" />
	</form>
	</td>
	<td>&nbsp;</td>
	<td>
	<form action="/article/articleedit/edit/{article_id}/" method="post">
	<input class="okbutton" type="submit" value="{intl-abort}" />
	</form>
	</td>
	<td>&nbsp;</td>
	<td>
	</td>
</tr>
</table>

