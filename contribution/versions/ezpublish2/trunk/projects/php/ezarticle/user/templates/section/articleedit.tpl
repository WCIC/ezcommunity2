<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f08c00">
<tr>
    <td class="toppath" width="1"><img src="/images/1x1.gif" width="1" height="38"></td>
	<td class="toppath" align="left" width="100%">&nbsp;</td>
</tr>
<tr>
	<td class="toppathbottom" width="160" colspan="2"><img src="/images/1x1.gif" width="160" height="1"></td>
</tr>	
</table>

<form method="post" action="/article/articleedit/{action_value}/{article_id}/" >

<h1>{intl-head_line}</h1>

<!-- BEGIN error_message_tpl -->
<h3 class="error">{intl-error_parsing_xml}</h3>
<!-- END error_message_tpl -->

<p class="boxtext">{intl-article_name}:</p>
<input type="text" name="Name" size="40" value="{article_name}" />

<p class="boxtext">{intl-article_author}:</p>
<input type="text" name="AuthorText" size="40" value="{author_text}" />

<p class="boxtext">{intl-category}:</p>
<select name="CategoryIDSelect">
<!-- BEGIN value_tpl -->
<option value="{option_value}" {selected}>{option_level}{option_name}</option>
<!-- END value_tpl -->
</select>

<p class="boxtext">{intl-intro}:</p>
<textarea name="Contents[]" cols="40" rows="5" wrap="soft">{article_contents_0}</textarea>
<br /><br />

<p class="boxtext">{intl-contents}:</p>
<textarea name="Contents[]" cols="40" rows="20" wrap="soft">{article_contents_1}</textarea>
<br /><br />

<p class="boxtext">{intl-link_text}:</p>
<input type="text" name="LinkText" size="20" value="{link_text}" />
<br /><br />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<input class="okbutton" type="submit" value="{intl-ok}" />
	</form>
	</td>
	<td>&nbsp;</td>
	<td>
	<form method="post" action="/article/articleedit/cancel/{article_id}/" >
	<input class="okbutton" type="submit" value="{intl-cancel}" />	
	</form>
	</td>
</tr>
</table>

