<!-- wishlist.tpl --> 
<!-- $Id: wishlist.tpl,v 1.1 2000/10/21 16:49:38 bf-cvs Exp $ -->

<h1>{intl-wishlist}</h1>

<hr noshade="noshade" size="4" />

<!-- BEGIN empty_wishlist_tpl -->
<h2>{intl-empty_wishlist}</h2>
<!-- END empty_wishlist_tpl -->



<!-- BEGIN wishlist_item_list_tpl -->
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<th>Bilde:</th>
	<th>Varenavn:</th>
	<th>Opsjoner:</th>
	<td class="path" align="right">Pris:</td>
</tr>

<!-- BEGIN wishlist_item_tpl -->
<tr>
	<td class="{td_class}">
	<img src="{product_image_path}" border="0" width="{product_image_width}" height="{product_image_height}" alt="{product_image_caption}"/>
	</td>
	<td class="{td_class}">
	{product_name}
	</td>
	<td class="{td_class}">
        <!-- BEGIN wishlist_item_option_tpl -->
	{option_name}:
	{option_value}<br>
        <!-- END wishlist_item_option_tpl -->
	&nbsp;</td>
	<td class="{td_class}" align="right">
	{product_price}
	</td>
</tr>
<!-- END wishlist_item_tpl -->

<tr>
	<td colspan="2">&nbsp;</td>
	<th>Frakt:</th>
	<td align="right">
	{shipping_cost}
	</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<th>Totalt:</th>
	<td align="right">
	{wishlist_sum}
	</td>
</tr>
</table>
<!-- END wishlist_item_list_tpl -->

<hr noshade="noshade" size="4" />
