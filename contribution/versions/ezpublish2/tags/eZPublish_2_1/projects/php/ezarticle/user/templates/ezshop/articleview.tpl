<!-- BEGIN article_url_item_tpl -->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<center><h3>{intl-found}: http://{article_url}</h3></center>
	</td>
</tr>
</table>
<!-- END article_url_item_tpl -->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td bgcolor="#f08c00"  width="100%">
  
	<strong class="h1"><img src="/images/1x1.gif" width="3" height="1" border="0">{article_name}</strong>
	</td>
</tr>
</table>

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
<a class="path" href="/article/articleview/{article_id}/{prev_page_number}/">&lt;&lt; {intl-prev_page}</a>
<!-- END prev_page_link_tpl -->

<!-- BEGIN page_link_tpl -->
| <a class="path" href="/article/articleview/{article_id}/{page_number}/">{page_number}</a>	
<!-- END page_link_tpl -->

<!-- BEGIN current_page_link_tpl -->
| < {page_number} >
<!-- END current_page_link_tpl -->


<!-- BEGIN next_page_link_tpl -->
| <a class="path" href="/article/articleview/{article_id}/{next_page_number}/">{intl-next_page} &gt;&gt;</a>
<!-- END next_page_link_tpl -->

<!-- BEGIN print_page_link_tpl -->
<!-- | <a class="path" href="/article/articleprint/{article_id}/">{intl-print_page}</a> | -->
<!-- END print_page_link_tpl -->

