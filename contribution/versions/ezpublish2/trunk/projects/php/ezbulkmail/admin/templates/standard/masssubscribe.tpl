<form method="post" action="/bulkmail/masssubscribe/">

<!-- BEGIN new_email_list_tpl -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
        <td>
	<h3>{intl-new_addresses}:</h3>
	</td>
</tr>
<!-- BEGIN new_email_item_tpl -->
<tr>
        <td>
	{new_email}
        </td>
</tr>
<!-- END new_email_item_tpl -->
</table>
<br /><br />
<!-- END new_email_list_tpl -->


<!-- BEGIN email_exists_list_tpl -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
        <td>
	<h3>{intl-addresses_already_exists}:</h3>
	</td>
</tr>
<!-- BEGIN email_exists_item_tpl -->
<tr>
        <td>
	{email_exists}
        </td>
</tr>
<!-- END email_exists_item_tpl -->
</table>
<!-- END email_exists_list_tpl -->

<!-- BEGIN not_valid_list_tpl -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
        <td>
	<h3>{intl-not_valid_addresses}:</h3>
	</td>
</tr>
<!-- BEGIN not_valid_item_tpl -->
<tr>
        <td>
	{not_valid}
        </td>
</tr>
<!-- END not_valid_item_tpl -->
</table>
<!-- END not_valid_list_tpl -->


<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
	<p class="boxtext">{intl-addresses}:</p>
	<textarea name="Addresses" rows="20" cols="30">{addresses}</textarea>
	</td>
</tr>
<tr>
        <td>&nbsp;</td>
</tr>
<tr>
        <td>
	<p class="boxtext">{intl-categories}:</p>
	<select multiple size="5" Name="CategoryArrayID">
	<!-- BEGIN category_item_tpl -->
	<option value="{category_id}">{category_name}</option>
	<!-- END category_item_tpl -->
	</select>
	</td>
</tr>
<tr>
        <td>
	<input type="submit" value="{intl-ok}" name="OK" />
	</td>
</tr>
</table>
</form>