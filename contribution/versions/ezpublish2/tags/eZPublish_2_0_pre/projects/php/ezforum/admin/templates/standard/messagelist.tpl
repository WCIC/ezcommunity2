<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td align="left" valign="bottom">
        <h1>{intl-headline}</h1>
     </td>
     <td align="right">
	 <form action="/forum/search/" method="post">
	       <input type="text" name="QueryString">
	       <input class="stdbutton" type="submit" name="search" value="{intl-search}">
         </form>
     </td>
</tr>
</table>

<hr noshade="noshade" size="4" />

<img src="/images/{site_style}/path-arrow.gif" height="10" width="12" border="0" alt="" />
<a class="path" href="/forum/categorylist/">{intl-forum-main}</a>

<img src="/images/{site_style}/path-slash.gif" height="10" width="16" border="0" alt="" />
<a class="path" href="/forum/forumlist/{category_id}/">{category_name}</a>

<img src="/images/{site_style}/path-slash.gif" height="10" width="16" border="0" alt="" />
<a class="path" href="/forum/messagelist/{forum_id}/">{forum_name}</a>

<hr noshade="noshade" size="4" />

<form method="post" action="/forum/messageedit/edit/" enctype="multipart/form-data">
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
     <th>{intl-subject}:</th> 
     <th>{intl-author}:</th>
     <th>{intl-time}:</th>
     <th>{intl-notice}:</th>
     <th colspan="2">&nbsp;</th>
</tr>
<!-- BEGIN message_item_tpl -->
<tr bgcolor="{color}">
     <td width="33%" class="{td_class}">
     {spacer}
     <a href="/forum/message/{message_id}">{message_topic}</a>
     </td>
     <td width="32%" class="{td_class}">
     {message_user}
     </td>
     <td width="32%" class="{td_class}">
     <span class="small">{message_postingtime}</span>
     </td>
     <td width="1%" class="{td_class}">
     {emailnotice}&nbsp;
     </td class="{td_class}">
     <td width="1%" class="{td_class}">
	 <a href="/forum/messageedit/edit/{message_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('efm{message_id}-red','','/images/{site_style}/redigerminimrk.gif',1)"><img name="efm{message_id}-red" border="0" src="/images/{site_style}/redigermini.gif" width="16" height="16" align="top" border="" alt="Edit" /></a>
     </td>
	<td class="{td_class}" width="1%" align="center">
	<input type="checkbox" name="MessageArrayID[]" value="{message_id}">
	</td>
</tr>
<!-- END message_item_tpl -->
</table>

<hr noshade="noshade" size="4" />
<input class="stdbutton" type="submit" Name="DeleteMessages" value="{intl-deletemessages}">
</form>

