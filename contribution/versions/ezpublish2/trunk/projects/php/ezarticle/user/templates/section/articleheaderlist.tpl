<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f08c00">
<tr>
    <td class="toppath" width="1"><img src="/images/1x1.gif" width="1" height="38"></td>
	<td class="toppath" align="left" class="path" width="100%">&nbsp;</td>
</tr>
<tr>
	<td class="toppathbottom" width="160" colspan="2"><img src="/images/1x1.gif" width="160" height="1"></td>
</tr>	
</table>

<h1>{intl-head_line}</h1>

<!-- BEGIN path_item_tpl -->

<!-- END path_item_tpl -->

<!-- BEGIN category_list_tpl -->

<!-- BEGIN category_item_tpl -->

<!-- END category_item_tpl -->

<!-- END category_list_tpl -->


<!-- BEGIN article_list_tpl -->
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<th>
	{intl-article}:
	</th>
	<th>
	{intl-category}:
	</th>
	<th>
	<div align="right">{intl-publishing_date}:</div>
	</th>
</tr>
<!-- BEGIN article_item_tpl -->
<tr>
	<td class="{td_class}">
	<a href="/article/articleview/{article_id}/1/{category_id}">
	{article_name}
	</a>
	</td>
	<td class="{td_class}">{article_category_name}</td>
	<td align="right" class="{td_class}">
	<span class="small">{article_published}</span>
	</td>
</tr>
<!-- END article_item_tpl -->
</table>
<!-- END article_list_tpl -->




