<h1>{intl-item_view} - {item_name}</h1>

<hr size="4" noshade="noshade" />

<!-- BEGIN image_view_tpl -->
<img src="{www_dir}{image_src}" width="{image_width}" height="{image_height}" border="0" alt="{image_alt}" /><br /><br />
<!-- END image_view_tpl -->
<!-- BEGIN item_value_list_tpl -->

<table width="100%" cellpadding="4" cellspacing="2" >
<!-- BEGIN item_value_tpl -->
<tr>
	<td class="{td_class}">
	<b>{data_type_name}</b><br />
	{data_type_value}
	</td>
</tr>
<!-- END item_value_tpl -->

</table>

<hr size="4" noshade="noshade" />

<!-- END item_value_list_tpl -->
