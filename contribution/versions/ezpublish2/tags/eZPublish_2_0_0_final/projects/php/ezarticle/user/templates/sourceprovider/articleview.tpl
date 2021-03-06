<!-- BEGIN article_url_item_tpl -->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<center><h3>{intl-found}: http://{article_url}</h3></center>
	</td>
</tr>
</table>
<!-- END article_url_item_tpl -->
<h1>{article_name}</h1>

<hr noshade="noshade" size="4" />

<br />

<!-- BEGIN article_header_tpl -->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<p class="byline">{intl-article_author}: {author_text}</p>
	</td>
	<td align="right">
	<p class="byline">{intl-article_date}: {article_created}</p>
	</td>
</tr>
</table>
<!-- END article_header_tpl -->

<p>
{article_body}
</p>



<br />

<!-- BEGIN prev_page_link_tpl -->
<a href="/article/articleview/{article_id}/{prev_page_number}/">{intl-prev_page}</a>
<!-- END prev_page_link_tpl -->

<!-- BEGIN page_link_tpl -->
| <a href="/article/articleview/{article_id}/{page_number}/">{page_number}</a>	
<!-- END page_link_tpl -->


<!-- BEGIN next_page_link_tpl -->
| <a href="/article/articleview/{article_id}/{next_page_number}/">{intl-next_page}</a>
<!-- END next_page_link_tpl -->

<!-- BEGIN numbered_page_link_tpl -->
| <a class="path" href="/article/articleview/{article_id}/0/">{intl-numbered_page}</a> |
<!-- END numbered_page_link_tpl -->

<!-- BEGIN print_page_link_tpl -->
| <a class="path" href="/article/articleprint/{article_id}/">{intl-print_page}</a> |
<!-- END print_page_link_tpl -->
</p>