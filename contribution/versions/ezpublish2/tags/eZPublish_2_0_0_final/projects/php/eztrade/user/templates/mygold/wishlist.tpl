<!-- wishlist.tpl -->

<form action="/trade/wishlist/" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
    <td>
      <h1>{intl-wishlist}</h1>
    </td>
    <td align="right">
      <!-- BEGIN public_wishlist_tpl -->
      {intl-public_wishlist_text}
      <input class="okbutton" type="submit" name="IsPublicButton" value="{intl-non_public_wishlist}" />
      <!-- END public_wishlist_tpl -->
      <!-- BEGIN non_public_wishlist_tpl -->
      {intl-non_public_wishlist_text}
      <input class="okbutton" type="submit" name="IsPublicButton" value="{intl-public_wishlist}" />
      <!-- END non_public_wishlist_tpl -->
    </td>
<tr>
    <td colspan="2">

      <hr noshade="noshade" size="1" />
      <!-- BEGIN empty_wishlist_tpl -->
      <h2>{intl-empty_wishlist}</h2>
      <!-- END empty_wishlist_tpl --> <!-- BEGIN wishlist_item_list_tpl -->
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr align="left"> 
		  <th>&nbsp;{intl-product_image}:</th>
		  <th>{intl-product_name}:</th>
		  <th>{intl-product_options}:</th>
		  <th>{intl-move_to_cart}:</th>
		  <th>{intl-someone_has_bought_this}:</th>
		  <th>{intl-product_qty}:</th>
		  <th align="right">{intl-product_price}:</th>
		  <th>&nbsp;</th>
		</tr>
		<!-- BEGIN wishlist_item_tpl --> 
		<tr> 
		  <td class="{td_class}"> <!-- BEGIN wishlist_image_tpl --> <img src="{product_image_path}" border="0" width="{product_image_width}" height="{product_image_height}" alt="{product_image_caption}"/> 
			<!-- END wishlist_image_tpl --> </td>
		  <td class="{td_class}"> <a href="/trade/productview/{product_id}/">{product_name}</a> 
		  </td>
		  <td class="{td_class}"> <!-- BEGIN wishlist_item_option_tpl --> <div class="small">{option_name}: {option_value}</div>
			<!-- END wishlist_item_option_tpl --> &nbsp;</td>
		  <td class="{td_class}"> <a href="/trade/wishlist/movetocart/{wishlist_item_id}/"> 
			{intl-move_to_cart} </a> 
                  </td>

  		  <td class="{td_class}">

		  <!-- BEGIN is_bought_tpl -->
		  {intl-is_bought}
		  <!-- END is_bought_tpl -->

		  <!-- BEGIN is_not_bought_tpl -->
		  {intl-is_not_bought}
		  <!-- END is_not_bought_tpl -->

   		  </td>

  		  <td class="{td_class}">
		  	<input type="hidden" name="WishlistIDArray[]" value="{wishlist_item_id}" />
			<input size="3" type="text" name="WishlistCountArray[]" value="{wishlist_item_count}" />
   		  </td>
		  <td class="{td_class}" align="right"> {product_price} </td>
		  <td class="{td_class}" align="right"><a href="/trade/wishlist/remove/{wishlist_item_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('eztrade{wishlist_item_id}-slett','','/images/slettminimrk.gif',1)"><img name="eztrade{wishlist_item_id}-slett" border="0" src="/images/slettmini.gif" width="16" height="16" align="top"></a></td>
		</tr>
		<!-- END wishlist_item_tpl --> 
		<tr> 
		  <td class="sum"  colspan="3">&nbsp;</td>
		  <td class="sum" colspan="3" align="right">{intl-shipping}:</td>
		  <td class="sum" align="right"> {shipping_cost} </td>
		  <td class="sum" align="right">&nbsp;</td>
		</tr>
		<tr> 
		  <td class="sum" colspan="3">&nbsp;</td>
		  <td class="sum" colspan="3" align="right">{intl-total}:</td>
		  <td class="sum" align="right"> {wishlist_sum} </td>
		  <td class="sum" align="right">&nbsp;</td>
		</tr>
	  </table>
      <!-- END wishlist_item_list_tpl -->
      <hr noshade="noshade" size="1" />
    </td>
  </tr>
</table>

<table border="0" width="100%">
<tr>
	<td>
          <input type="hidden" name="Action" value="Refresh" />
	  <input class="okbutton" type="submit" value="{intl-update}" />
	</td>
</tr>
<tr>
	<td align="center">
	  <a href="/trade/sendwishlist/">{intl-send_wishlist}</a>
	</td>
</td>
</table>

</form>