<form method="post" action="/todo/todoedit/edit/{todo_id}/">

<h1>{intl-headline}</h1>

<hr noshade="noshade" size="4" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td  colspan="2">
	<h2>{todo_name}</h2><br />
	</td>
</tr>
<tr>
	<td >
	<p class="boxtext">{intl-owner}:</p>
	{first_name} {last_name}
	<br><br>
	</td>
	<td >
	<p class="boxtext">{intl-user}:</p>
	{user_firstname} {user_lastname}
	<br><br>
	</td>
</tr>
<tr>
	<td >
	<p class="boxtext">{intl-cat}:</p>
	<!-- BEGIN category_select_tpl -->
	{todo_category}
	<!-- END category_select_tpl -->
	<br><br>
	</td>

	<td >
	<p class="boxtext">{intl-pri}:</p>
	<!-- BEGIN priority_select_tpl -->
	{todo_priority}
	<!-- END priority_select_tpl -->
	</td>
</tr>
<tr>
	<td colspan="2">
	<p class="boxtext">{intl-desc}:</p>
	</td>
</tr>
<tr>
	<td colspan="2" class="bglight">
	{todo_description}
	</td>
</tr>
<tr>
	<td>
	<br />
	<p class="boxtext">{intl-status}:</p>
	{todo_status}
	</td>
	<td >
	<br />
	<p class="boxtext">{intl-view_others}:</p>
	{todo_permission}
	</td>
</tr>
</table>
<br />

<!-- BEGIN mark_as_done -->
<hr noshade size="4"/>
<input class="stdbutton" type="submit" Name="Done" value="{intl-mark_as_done}">
<!-- END mark_as_done -->

<hr noshade size="4"/>

<table cellpadding="0" cellspacing="0" border="0">
<tr>
    <td>
	<input class="okbutton" type="submit" Name="List" value="{intl-ok}">
	</td>
	<td>&nbsp;</td>
        <td>
	<input class="okbutton" type="submit" Name="Edit" value="{intl-edit}">
	</td>
</tr>
</table>
</form>

