<h2>{intl-headline}</h2>

<!-- BEGIN message_list_tpl -->
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
   	<th width="45%">{intl-topic}:</th>
    <th width="25%">{intl-author}:</th>
    <th width="30%"><div align="right">{intl-time}:</div></th>
</tr>

    <!-- BEGIN message_item_tpl -->
    <tr>
    	<td class="{td_class}">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="1%">{spacer}{spacer}</td>
		<td width="99%"><a class="{link_color}" href="/forum/message/{message_id}/">{topic}</a></td>
	</tr>
	</table>
		</td>
    	<td class="{td_class}">
	    {user}
	    </td>
    	<td class="{td_class}" align="right">
	   <span class="small">{postingtime}</span>
	   </td>
    </tr>
    <tr>
    <td colspan="3">
    <div class="p">
    {body}
    </div>
	<div class="spacer">
	<form action="/forum/userlogin/replysimple/{forum_id}/{message_id}/?RedirectURL={redirect_url}">
	<input class="stdbutton" type="submit" value="{intl-reply}" />
	</form>
	</div>
    </td>
    </tr>
    <!-- END message_item_tpl -->

</table>

<!-- END message_list_tpl -->

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<a class="path" href="/forum/category/forum/{forum_id}/?Offset={prev_offset}&Limit={limit}">{previous}</a>
	</td>
	<td align="right">
	<a class="path" href="/forum/category/forum/{forum_id}/?Offset={next_offset}&Limit={limit}">{next}</a>
	</td>
</tr>
</table>

<br />
<form action="/forum/userlogin/newsimple/{forum_id}">
<input class="stdbutton" type="submit" value="{intl-new-posting}" />
<input type="hidden" name="RedirectURL" value="{redirect_url}" />
</form>

