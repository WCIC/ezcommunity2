<div onLoad="MM_preloadImages('/ezarticle/admin/images/redigerminimrk.gif','/ezarticle/admin/images/slettminimrk.gif')"></div>

<table width="100%" border="0">
<tr>
	<td>
	<h1>{intl-head_line}: "{search_text}"</h1>
	</td>
	<td align="right">
	<form action="/article/search/" method="post">
	<input type="text" name="SearchText" size="10" />	
	<input type="submit" value="{intl-search}" />
	</form>	
	</td>
</tr>
</table>

<form method="post" action="/article/search/" enctype="multipart/form-data">

<!--
<p>
{current_category_description}
</p>
-->

<hr noshade="noshade" size="4" />

<!-- BEGIN article_list_tpl -->
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<th>{intl-article}:</th>
	<th>{intl-published}:</th>
	<th colspan="2">&nbsp;</th>
</tr>

<!-- BEGIN article_item_tpl -->
<tr>
	<td class="{td_class}">
	<a href="/article/articlepreview/{article_id}/">
	{article_name}
	</a>
	</td>
	<td class="{td_class}">
	<!-- BEGIN article_is_published_tpl -->
	{intl-is_published}
	<!-- END article_is_published_tpl -->
	<!-- BEGIN article_not_published_tpl -->
	{intl-not_published}
	<!-- END article_not_published_tpl -->
	&nbsp;
	</td>
	<td width="1%" class="{td_class}">
	<a href="/article/articleedit/edit/{article_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezaa{article_id}-red','','/ezarticle/admin/images/redigerminimrk.gif',1)"><img name="ezaa{article_id}-red" border="0" src="/ezarticle/admin/images/redigermini.gif" width="16" height="16" align="top"></a>
	</td>
	<td width="1%" class="{td_class}">
          <input type="checkbox" name="ArticleArrayID[]" value="{article_id}" />
	</td>
</tr>
<!-- END article_item_tpl -->

</table>
<!-- END article_list_tpl -->

<hr noshade="noshade" size="4">

<!-- BEGIN article_delete_tpl -->
<input class="stdbutton" type="submit" name="Delete" value="{intl-delete}" />
<!-- END article_delete_tpl -->
<input type="hidden" name="SearchText" value="{search_text}">
</form>
