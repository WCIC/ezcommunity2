<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td align="left" valign="bottom">
        <h1>{intl-headline}</h1>
     </td>
     <td align="right">
        <form action="{www_dir}{index}/forum/search/" method="post">
           <input type="text" name="QueryString" size="12" />
           <input class="stdbutton" type="submit" name="search" value="{intl-search}" />
        </form>
     </td>
  </tr>
</table>

<hr noshade size="4" />

	<img src="{www_dir}/images/path-arrow.gif" height="10" width="12" border="0" alt="" />
	<a class="path" href="{www_dir}{index}/forum/categorylist/">{intl-forum-main}</a>
	<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="" />
    <a class="path" href="{www_dir}{index}/forum/forumlist/{category_id}/">{category_name}</a>
	<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="" />
	<a class="path" href="{www_dir}{index}/forum/messagelist/{forum_id}">{forum_name}</a>

<hr noshade size="4" />

<form action="{www_dir}{index}/forum/userlogin/new/{forum_id}">

<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
    <th width="45%">{intl-topic}:</th>
    <th width="25%">{intl-author}:</th>
    <th width="29%"><div align="right">{intl-time}:</div></th>
    <th width="1%"></th>
</tr>

<!-- BEGIN message_item_tpl -->
<tr>
    <td class="{td_class}">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="1%">{spacer}{spacer}</td>
		<td width="99%"><a href="{www_dir}{index}/forum/message/{message_id}/">{topic}</a></td>
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

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<!-- BEGIN previous_tpl -->
	<td>
	<a class="path" href="{www_dir}{index}/forum/messagelist/{forum_id}/{prev_offset}/{limit}">&lt;&lt;&nbsp;{intl-previous}</a>
	</td>
<!-- END previous_tpl -->

<!-- BEGIN next_tpl -->
	<td align="right">
	<a class="path" href="{www_dir}{index}/forum/messagelist/{forum_id}/{next_offset}/{limit}">{intl-next}&nbsp;&gt;&gt;</a>
	</td>
<!-- END next_tpl -->
</tr>
</table>

<hr noshade size="4" />

<input class="stdbutton" type="submit" value="{intl-new-posting}" />
</form>

