<h1>{intl-latest_news}</h1>

<!-- BEGIN news_list_tpl -->

<hr noshade="noshade" size="4" />

<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<td colspan="{num_cols}">
	<a class="noline" href="/newsfeed/latest/{category_id}/"><h1>{category_name}</h1></a>
	</td>
</tr>

<!-- BEGIN news_item_tpl -->
{starttr}
	<td valign="top" width="50%">
	<div class="listheadline"><a class="listheadline" href="{news_url}" target="_vblank">{news_name}</a></div>
	<div class="small">( {news_date} )</div>
	<div class="spacer"><div class="p">{news_intro}</div></div>
	<img src="/images/path-arrow.gif" height="10" width="12" border="0" alt="">&nbsp;<a class="path" href="{news_url}" target="_vblank">{intl-read_more}</a>
	</td>
{endtr}
<!-- END news_item_tpl -->
</table>
<br />

<!-- END news_list_tpl -->

