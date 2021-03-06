<form method="post" action="/article/articleedit/{action_value}/{article_id}/" >

<h1>{intl-head_line}</h1>

<!-- BEGIN error_message_tpl -->
<h3 class="error">{intl-error_parsing_xml}</h3>
<!-- END error_message_tpl -->

<hr noshade="noshade" size="4" />

<br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<p class="boxtext">{intl-article_name}:</p>
	<input type="text" name="Name" size="40" value="{article_name}" />
	</td>
	<td>
	<input type="checkbox" name="IsPublished" {article_is_published} />
	<span class="boxtext">{intl-article_is_published}</span><br />
	</td>
</tr>
</table>

<p class="boxtext">{intl-article_author}:</p>
<input type="text" name="AuthorText" size="40" value="{author_text}" />

<p class="boxtext">{intl-category}:</p>

<select name="CategoryID">

<!-- BEGIN value_tpl -->
<option value="{option_value}" {selected}>{option_level}{option_name}</option>
<!-- END value_tpl -->

</select>

<br />
<p class="boxtext">{intl-additional_category}:</p>

<select multiple name="CategoryArray[]">

<!-- BEGIN multiple_value_tpl -->
<option value="{option_value}" {multiple_selected}>{option_level}{option_name}</option>
<!-- END multiple_value_tpl -->

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

<hr noshade="noshade" size="4" />

<input class="stdbutton" type="submit" name="Image" value="{intl-pictures}" />
<input class="stdbutton" type="submit" name="File" value="{intl-files}" />
<input class="stdbutton" type="submit" name="Preview" value="{intl-preview}" />

<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<input  class="okbutton" type="submit" value="{intl-ok}" />
	</form>
	</td>
	<td>&nbsp;</td>
	<td>
	<form method="post" action="/article/articleedit/cancel/{article_id}/" >
	<input  class="okbutton" type="submit" value="{intl-cancel}" />	
	</form>
	</td>
</tr>
</table>

