<!-- wishlist.tpl -->
<!-- $Id: wishlist.tpl,v 1.4 2000/12/19 10:15:00 bf Exp $ --> 
<body onLoad="MM_preloadImages('/eztrade/user/images/slettminimrk.gif')">
<table width="470" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
      <h1>{intl-wishlist}</h1>
      <hr noshade size="1" />
      <!-- BEGIN empty_wishlist_tpl -->
      <h2>{intl-empty_wishlist}</h2>
      <!-- END empty_wishlist_tpl --> <!-- BEGIN wishlist_item_list_tpl -->
      <table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
		<tr> 
		  <th>{intl-product_image}:</th>
		  <th>{intl-product_name}:</th>
		  <th>{intl-product_options}:</th>
		  <th>{intl-move_to_cart}:</th>
		  <td align="right"><b>{intl-product_price}:</b></td>
		  <td align="right">&nbsp;</td>
		</tr>
		<!-- BEGIN wishlist_item_tpl --> 
		<tr> 
		  <td class="{td_class}"> <!-- BEGIN wishlist_image_tpl --> <img src="{product_image_path}" border="0" width="{product_image_width}" height="{product_image_height}" alt="{product_image_caption}"/> 
			<!-- END wishlist_image_tpl --> </td>
		  <td class="{td_class}"> <a href="/trade/productview/{product_id}/">{product_name}</a> 
		  </td>
		  <td class="{td_class}"> <!-- BEGIN wishlist_item_option_tpl --> {option_name}: 
			{option_value}<br>
			<!-- END wishlist_item_option_tpl --> &nbsp;</td>
		  <td class="{td_class}"> <a href="/trade/wishlist/movetocart/{wishlist_item_id}/"> 
			{intl-move_to_cart} </a> </td>
		  <td class="{td_class}" align="right"> {product_price} </td>
		  <td class="{td_class}" align="right"><a href="/trade/wishlist/remove/{wishlist_item_id}/"  
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezuser{wishlist_item_id}-slett','','/eztrade/user/images/slettminimrk.gif',1)"><img name="ezuser{wishlist_item_id}-slett" border="0" src="/eztrade/user/images/slettmini.gif" width="16" height="16" align="top"></a></td>
		</tr>
		<!-- END wishlist_item_tpl --> 
		<tr> 
		  <td colspan="3">&nbsp;</td>
		  <th>{intl-shipping}:</th>
		  <td align="right"> {shipping_cost} </td>
		  <td align="right">&nbsp;</td>
		</tr>
		<tr> 
		  <td colspan="3">&nbsp;</td>
		  <th>{intl-total}:</th>
		  <td align="right"> {wishlist_sum} </td>
		  <td align="right">&nbsp;</td>
		</tr>
	  </table>
      <!-- END wishlist_item_list_tpl -->
      <hr noshade size="1" />
    </td>
  </tr>
</table>