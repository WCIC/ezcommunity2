<h1>{intl-head_line}</h1>

<hr noshade="noshade" size="4" />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<h2>{article_name}</h2> 
	<p class="byline">{intl-article_author}: {author_text}</p>


	<p>
	{article_body}
	</p>
	</td>
</tr>
</table>
<br />

<!-- <span class="boxtext">{intl-link_text}:</span> {link_text} -->


<!-- BEGIN page_menu_separator_tpl -->
<hr noshade="noshade" size="4" />
<!-- END page_menu_separator_tpl -->


<!-- BEGIN prev_page_link_tpl -->
<a class="path" href="/article/articlepreview/{article_id}/{prev_page_number}/">&lt;&lt; {intl-prev_page}</a>
<!-- END prev_page_link_tpl -->

<!-- BEGIN page_link_tpl -->
| <a class="path" href="/article/articlepreview/{article_id}/{page_number}/">{page_number}</a>	
<!-- END page_link_tpl -->

<!-- BEGIN next_page_link_tpl -->
| <a class="path" href="/article/articlepreview/{article_id}/{next_page_number}/">{intl-next_page} &gt;&gt;</a>
<!-- END next_page_link_tpl -->

<br />

<hr noshade="noshade" size="4" />

<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
		<form action="/article/articleedit/edit/{article_id}/" method="post">
		<input class="okbutton" type="submit" value="{intl-edit}" />
		</form>
	</td>
	<td>&nbsp;</td>
	<td>
		<form action="/article/articleedit/delete/{article_id}/" method="post">
		<input class="okbutton" type="submit" value="{intl-delete}" />
		</form>
	</td>
</tr>
</table>

