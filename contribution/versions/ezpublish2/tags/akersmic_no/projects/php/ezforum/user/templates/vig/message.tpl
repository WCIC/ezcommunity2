<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td align="left" valign="bottom">
        <h1>{topic}</h1>
    </td>
    <td align="right">
    <td align="right">
	 <form action="{www_dir}{index}/forum/search/" method="post">
	       <input type="text" name="QueryString" size="12" />
	       <input class="stdbutton" type="submit" name="search" value="{intl-search}" />
         </form>
     </td>
</tr>
</table>

<hr noshade="noshade" size="4" />

	<img src="{www_dir}/images/path-arrow.gif" height="10" width="12" border="0" alt="" />
	<a class="path" href="{www_dir}{index}/forum/categorylist/">{intl-forum-main}</a>
	<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="" />
    <a class="path" href="{www_dir}{index}/forum/forumlist/{category_id}/">{category_name}</a>
	<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="" />
	<a class="path" href="{www_dir}{index}/forum/messagelist/{forum_id}/">{forum_name}</a>
<!--
	<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="" />	
    <a class="path" href="{www_dir}{index}/forum/message/{message_id}/">{message_topic}</a>
-->

<hr noshade="noshade" size="4" />

<br />

<p class="boxtext">{intl-topic}:</p>
<span class="p">{topic}</span>
<br /><br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>        
   	<td>
	<p class="boxtext">{intl-author}:</p>
    {main-user}
	</td>
	<td align="right">
	<p class="boxtext">{intl-time}:</p>
	<span class="small">{main-postingtime}</span>
	</td>
</tr>
</table>


<p class="boxtext">{intl-text}:</p>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
<tr>        
   	<td class="bglight">
	{body}
	</td>
</tr>
</table>

<br />


<hr noshade="noshade" size="4" />
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
    <form method="post" action="{www_dir}{index}/forum/userlogin/reply/{reply_id}/">
    <input class="stdbutton" type="submit" value="{intl-answer}" />
    </form>
</td>
<!-- BEGIN edit_current_message_item_tpl -->
<td>
    &nbsp;
</td>
<td>
    <form method="post" action="{www_dir}{index}/forum/messageedit/edit/{message_id}/">
    <input class="stdbutton" type="submit" value="{intl-edit}" />
    </form>
</td>
<td>
    &nbsp;
</td>
<td>
    <form method="post" action="{www_dir}{index}/forum/messageedit/delete/{message_id}/">
    <input class="stdbutton" type="submit" value="{intl-delete}" />
    </form>
</td>
<!-- END edit_current_message_item_tpl -->
</tr>
</table>
<br />

<h2>{intl-message_thread}</h2>

<table class="list" width="100%" border="0" cellspacing="0" cellpadding="4">
<tr>
	<th width="45%">{intl-reply-topic}:</th>
    <th width="25%">{intl-reply-author}:</th>
    <th width="29%"><div align="right">{intl-reply-time}:</div></th>
    <th width="1%"></th>
</tr>

    <!-- BEGIN message_item_tpl -->
<tr>
    	<td class="{td_class}">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="1%">{spacer}{spacer}</td>
		<td width="99%"><a class="{link_color}" href="{www_dir}{index}/forum/message/{message_id}/">{reply_topic}</a></td>
	</tr>
	</table>
	</td>
    	<td class="{td_class}">
	{user}
	</td>
    	<td class="{td_class}" align="right">
	<span class="small">{postingtime}</span>
	</td>
    <td class="{td_class}" align="right">
	&nbsp;
        <!-- BEGIN edit_message_item_tpl -->
        <nobr><a href="{www_dir}{index}/forum/messageedit/edit/{message_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezfrm{message_id}-red','','/images/redigerminimrk.gif',1)"><img name="ezfrm{message_id}-red" border="0" src="{www_dir}/images/redigermini.gif" width="16" height="16" align="top" alt="Edit" /></a>&nbsp;<a href="{www_dir}{index}/forum/messageedit/delete/{message_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezfrm{message_id}-slett','','/images/slettminimrk.gif',1)"><img name="ezfrm{message_id}-slett" border="0" src="{www_dir}/images/slettmini.gif" width="16" height="16" align="top" alt="Delete" /></a></nobr>
        <!-- END edit_message_item_tpl -->
    </td>

</tr>
    <!-- END message_item_tpl -->

</table>
</form>

<hr noshade="noshade" size="4" />

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
	<form action="{www_dir}{index}/forum/userlogin/new/{forum_id}">
  	<input class="stdbutton" type="submit" value="{intl-new-posting}" />
	</td>
</tr>
</table>

