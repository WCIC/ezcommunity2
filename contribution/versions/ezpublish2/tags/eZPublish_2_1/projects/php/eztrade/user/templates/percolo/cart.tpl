 <table width="100%" height="100" border="0" vspace="0" hspace="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="1%"><img src="/sitedesign/percolo/images/onepix.gif" alt="luft" width="14" height="14" hspace="0" vspace="0" /><br /></td>
			<td width="98%" valign="top"><img src="/sitedesign/percolo/images/onepix.gif" alt="luft" width="1" height="14" hspace="0" vspace="0" /><br /></td>
            <td rowspan="3" valign="top" width="1%" align="right"><img src="/sitedesign/percolo/images/helikopter.gif" alt="helikopter" width="140" height="100" /><br /></td>
        </tr>
        <tr>
            <td bgcolor="#009ebf" valign="top"><img src="/sitedesign/percolo/images/onepix.gif" alt="luft" width="14" height="32" hspace="0" vspace="0" /><br /></td>
            <td bgcolor="#009ebf" valign="top"><h1 class="sidetittel">{intl-cart}</h1></td>
        </tr>
        <tr>
            <td><img src="/sitedesign/percolo/images/onepix.gif" alt="luft" width="14" height="14" hspace="0" vspace="0" /><br /></td>
            <td><img src="/sitedesign/percolo/images/onepix.gif" alt="luft" width="50" height="60" hspace="0" vspace="0"></td>
        </tr>
		<tr>
		    <td><img src="/sitedesign/percolo/images/onepix.gif" alt="luft" width="14" height="14" hspace="0" vspace="0" /><br /></td>
			<td colspan="2">

<!-- BEGIN empty_cart_tpl -->
<h2>{intl-empty_cart}</h2>
<!-- END empty_cart_tpl -->


<form action="/trade/cart/" method="post">
<!-- BEGIN cart_item_list_tpl -->
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<th>{intl-product_image}:</th>
	<th>{intl-product_name}:</th>
	<!-- BEGIN product_available_header_tpl -->

	<!-- END product_available_header_tpl -->
	<th>{intl-product_qty}:</th>

	<td align="right"><b>{intl-product_price}:</b></td>
	<td align="right"><b>&nbsp;</b></td>
</tr>

<!-- BEGIN cart_item_tpl -->
<tr>
	<td class="{td_class}">
	<!-- BEGIN cart_image_tpl -->
	<img src="{product_image_path}" border="0" width="{product_image_width}" height="{product_image_height}" alt="{product_image_caption}"/>
	<!-- END cart_image_tpl -->&nbsp;
	</td>
	<td class="{td_class}">
	<a href="/trade/productview/{product_id}/">{product_name}</a>
	</td>
        <!-- BEGIN cart_item_option_tpl -->
	<!-- BEGIN cart_item_option_availability_tpl -->
<!-- END cart_item_option_availability_tpl -->
        <!-- END cart_item_option_tpl -->
	<!-- BEGIN product_available_item_tpl -->

	<!-- END product_available_item_tpl -->
	<td class="{td_class}">
	<input type="hidden" name="CartIDArray[]" value="{cart_item_id}" />
	<input size="3" type="text" name="CartCountArray[]" value="{cart_item_count}" />
	</td>
	<td class="{td_class}" align="right">
	{product_price}
	</td>
	<td class="{td_class}" align="right">
	<a href="/trade/cart/remove/{cart_item_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('eztrade{cart_item_id}-slett','','/images/slettminimrk.gif',1)"><img name="eztrade{cart_item_id}-slett" border="0" src="/images/slettmini.gif" width="16" height="16" align="top"></a>
	</td>
</tr>
<!-- END cart_item_tpl -->

<tr>
	<td colspan="1">&nbsp;</td>
	<td align="right" colspan="2"><span class="boxtext">{intl-shipping}:</span></td>
	<td align="right">
	{shipping_sum}
	</td>
</tr>
<tr>
	<td colspan="1">&nbsp;</td>
	<td align="right" colspan="2"><span class="boxtext">{intl-vat}:</span></td>
	<td align="right">
	{cart_vat_sum}
	</td>
</tr>
<tr>
	<td colspan="1">&nbsp;</td>
	<td align="right" colspan="2"><span class="boxtext">{intl-total}:</span></td>
	<td align="right">
	{cart_sum}
	</td>
</tr>
</table>
<!-- END cart_item_list_tpl -->

<!-- BEGIN cart_checkout_tpl -->

<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<!-- BEGIN cart_checkout_button_tpl -->
	<td>
	<input class="okbutton" type="submit" name="DoCheckOut" value="{intl-checkout}" />
	</td>
	<!-- END cart_checkout_button_tpl -->

	<td>&nbsp;</td>
	<td>
	<input class="okbutton" type="submit" value="{intl-update}" />
	</td>
</td>
</table>
<!-- END cart_checkout_tpl -->


<input type="hidden" name="Action" value="Refresh" />

</form>

</td>
</tr>
</table>
