<table width="100%" cellspacing="0" cellpadding="2" border="0">
<tr>
	<td bgcolor="#c0c0c0" colspan="{hotdeal_columns}">
	<div class="listheadline">{intl-hot_deals}</div>
	</td>
</tr>
<!-- BEGIN product_list_tpl -->

<!-- BEGIN product_tpl -->
{begin_tr}
	<td class="menutext" valign="top" width="50%">

	<div class="listproducts"><a class="listproducts" href="/trade/productview/{product_id}/{category_id}/">{product_name}</a></div>

	<!-- BEGIN product_image_tpl -->
	<a href="/trade/productview/{product_id}/{category_id}/">
	<img align="right" src="{product_image_path}" border="0" width="{product_image_width}" height="{product_image_height}" alt="{product_image_caption}"/></a>
	<!-- END product_image_tpl -->

	<div class="p">{product_intro_text}</div>

<!-- BEGIN price_tpl -->
	<div class="pris">{product_price}</div>
<!-- END price_tpl -->

	</td>
{end_tr}

<!-- END product_tpl -->

<tr>
	<td class="menuspacer">&nbsp;</td>
</tr>
</table>

<!-- END product_list_tpl -->

