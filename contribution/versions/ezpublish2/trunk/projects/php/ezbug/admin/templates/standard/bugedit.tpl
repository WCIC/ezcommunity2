<form method="post" action="/bug/edit/">

<h1>{intl-edit_bug}</h1>

<hr noshade="noshade" size="4">

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<p class="boxtext">{intl-bug_module}:</p>
	<select name="ModuleID">
	<!-- BEGIN module_item_tpl -->
	<option value="{module_id}" {selected}>{module_name}</option>
	<!-- END module_item_tpl -->
	</select>
	</td>

	<td>
	<p class="boxtext">{intl-bug_category}:</p>
	<select name="CategoryID">
	<!-- BEGIN category_item_tpl -->
	<option value="{category_id}" {selected}>{category_name}</option>
	<!-- END category_item_tpl -->
	</select>
	</td>
</tr>
</table>

<p class="boxtext">{intl-bug_title}:</p>
<input type="text" size="40" name="Name" value="{name_value}" />

<p class="boxtext">{intl-bug_description}:</p>
<textarea name="Description" cols="40" rows="5" wrap="soft">{description_value}</textarea>
<br />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<p class="boxtext">{intl-assigned_priority}:</p>
	<select name="PriorityID">
	<!-- BEGIN priority_item_tpl -->
	<option value="{priority_id}" {selected}>{priority_name}</option>
	<!-- END priority_item_tpl -->
	</select>
	</td>

	<td>
	<p class="boxtext">{intl-assigned_status}:</p>
	<select name="StatusID">
	<!-- BEGIN status_item_tpl -->
	<option value="{status_id}" {selected}>{status_name}</option>
	<!-- END status_item_tpl -->
	</select>
	</td>
</tr>
</table>


<br />

<p class="boxtext">{intl-log_message}:</p>
<textarea name="LogMessage" cols="40" rows="5" wrap="soft"></textarea>
<br />

<hr noshade="noshade" size="4">

<input type="submit" value="{intl-update}">

<hr noshade="noshade" size="4">

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td class="menuspacer">&nbsp;</td>
</tr>

<tr>
	<td>
	<input class="okbutton" type="submit" value="{intl-ok}">
	</td>
</tr>

<input type="hidden" name="Action" value="{action_value}">
<input type="hidden" name="BugID" value="{bug_id}">

</table>
</form>


