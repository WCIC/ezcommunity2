<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td class="tdminipath" width="1%"><img src="/images/1x1.gif" width="1" height="38"></td>
	<td class="tdminipath" align="left" width="99%">

	<img src="/sitedesign/designsection1/images/path-arrow-top.gif" width="16" height="10" border="0" alt="" />
	<a class="toppath" href="/forum/categorylist/">{intl-forum-main}</a>
	<img src="/sitedesign/designsection1/images/path-arrow-top.gif" width="16" height="10" border="0" alt="" />
    <a class="toppath" href="/forum/forumlist/{category_id}/">{category_name}</a>
	<img src="/sitedesign/designsection1/images/path-arrow-top.gif" width="16" height="10" border="0" alt="" />
	<a class="toppath" href="/forum/messagelist/{forum_id}/">{forum_name}</a>

	</td>
</tr>
<tr>
	<td class="toppathbottom" colspan="2"><img src="/images/1x1.gif" width="1" height="2"><br /></td>
</tr>	
</table>

<br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td align="left" valign="bottom">
        <h1>{intl-message_view}</h1>
    </td>
    <td align="right">
    <td align="right">
	 <form action="/forum/search/" method="post">
	       <input type="text" name="QueryString" size="12" />
	       <input class="stdbutton" type="submit" name="search" value="{intl-search}" />
         </form>
     </td>
</tr>
</table>

<h2>{topic}</h2>
<br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>        
   	<td valign="top">
	<p class="boxtext">{intl-author}:</p>
    {main-user}
	</td>
	<td align="right" valign="top">
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

<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
    <form method="post" action="/forum/userlogin/reply/{reply_id}/">
    <input class="stdbutton" type="submit" value="{intl-answer}" />
    </form>
</td>
<!-- BEGIN edit_current_message_item_tpl -->
<td>
    &nbsp;
</td>
<td>
    <form method="post" action="/forum/messageedit/edit/{message_id}/">
    <input class="stdbutton" type="submit" value="{intl-edit}" />
    </form>
</td>
<td>
    &nbsp;
</td>
<td>
    <form method="post" action="/forum/messageedit/delete/{message_id}/">
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
    	<td class="{td_class}" valign="top">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>

		<td width="1%" valign="top">
		{spacer}{spacer}
		<!-- BEGIN new_icon_tpl -->
                <img src="/images/message_new.gif" width="16" height="16" border="0" alt="New message" />&nbsp;
		<!-- END new_icon_tpl -->
		<!-- BEGIN old_icon_tpl -->
                <img src="/images/message.gif" width="16" height="16" border="0" alt="Message" />&nbsp;
		<!-- END old_icon_tpl -->	
		</td>
		<td width="99%" valign="top">
		<a class="{link_color}" href="/forum/message/{message_id}/">{reply_topic}</a>
		</td>
	</tr>
	</table>
	</td>
    	<td class="{td_class}" valign="top">
	<span class="small">{user}</span>
	</td>
    	<td class="{td_class}" align="right" valign="top">
	<span class="small">{postingtime}</span>
	</td>
    <td class="{td_class}" align="right" valign="top">
	&nbsp;
        <!-- BEGIN edit_message_item_tpl -->
        <nobr><a href="/forum/messageedit/edit/{message_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezfrm{message_id}-red','','/images/redigerminimrk.gif',1)"><img name="ezfrm{message_id}-red" border="0" src="/images/redigermini.gif" width="16" height="16" align="top" alt="Edit" /></a>&nbsp;<a href="/forum/messageedit/delete/{message_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezfrm{message_id}-slett','','/images/slettminimrk.gif',1)"><img name="ezfrm{message_id}-slett" border="0" src="/images/slettmini.gif" width="16" height="16" align="top" alt="Delete" /></a></nobr>
        <!-- END edit_message_item_tpl -->
    </td>

</tr>
    <!-- END message_item_tpl -->

</table>
</form>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
	<form action="/forum/userlogin/new/{forum_id}">
  	<input class="stdbutton" type="submit" value="{intl-new-posting}" />
	</td>
</tr>
</table>

