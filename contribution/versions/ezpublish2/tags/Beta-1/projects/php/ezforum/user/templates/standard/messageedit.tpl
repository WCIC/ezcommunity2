<form action="/forum/messageedit/insert/{forum_id}/" method="post">
<!-- <form action="/forum/userlogin/insert/{forum_id}/" method="post"> -->

<h1>{intl-headline}</h1>

<hr noshade size="4" />

	<img src="/ezforum/images/path-arrow.gif" height="10" width="15" border="0">
	<a class="path" href="/forum/categorylist/">{intl-forum-main}</a>
	<img src="/ezforum/images/path-slash.gif" height="10" width="20" border="0">
    <a class="path" href="/forum/category/{category_id}/">{category_name}</a>
	<img src="/ezforum/images/path-slash.gif" height="10" width="20" border="0">
	<a class="path" href="/forum/category/forum/{forum_id}">{forum_name}</a>

<hr noshade size="4" />

<br/ >

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<p class="boxtext">{intl-topic}</p>
	<input type="text" name="Topic" size="40">
	</td>
	<td>
	<p class="boxtext">{intl-author}</p>
	{user}
	</td>
</tr>
</table>

<p class="boxtext">{intl-text}</p>
<textarea wrap="soft" name="Body" rows="15" cols="40" class="body"></textarea>

<br /><br />

<input type="checkbox" name="notice"> {intl-email-notice}<br />
<br />

<hr noshade size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<input class="okbutton" type="submit" name="post" value="{intl-post}">
	</form>
	</td>
	<td>&nbsp;</td>
	<td>
	{intl-abort}knapp!
	</td>
</tr>
</table>

