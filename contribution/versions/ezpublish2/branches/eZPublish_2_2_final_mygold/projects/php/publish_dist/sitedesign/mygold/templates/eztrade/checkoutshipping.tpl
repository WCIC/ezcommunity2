<!-- BEGIN address_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
<a class="path" href="{www_dir}{index}/trade/checkout/address/">{intl-address-path}</a>
<!-- END address_path_tpl -->

<!-- BEGIN address_dummy_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
{intl-address-path}
<!-- END address_dummy_path_tpl -->

<!-- BEGIN shipping_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
<a class="path" href="{www_dir}{index}/trade/checkout/shipping/">{intl-shipping-path}</a>
<!-- END shipping_path_tpl -->

<!-- BEGIN shipping_dummy_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
{intl-shipping-path}
<!-- END shipping_dummy_path_tpl -->

<!-- BEGIN packing_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
<a class="path" href="{www_dir}{index}/trade/checkout/packing/">{intl-packing-path}</a>
<!-- END packing_path_tpl -->

<!-- BEGIN packing_dummy_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
{intl-packing-path}
<!-- END packing_dummy_path_tpl -->

<!-- BEGIN payment_method_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
<a class="path" href="{www_dir}{index}/trade/checkout/paymentmethod/">{intl-payment_method-path}</a>
<!-- END payment_method_path_tpl -->

<!-- BEGIN payment_method_dummy_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
{intl-payment_method-path}
<!-- END payment_method_dummy_path_tpl -->

<!-- BEGIN overview_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
<a class="path" href="{www_dir}{index}/trade/checkout/overview/">{intl-overview-path}</a>
<!-- END overview_path_tpl -->

<!-- BEGIN overview_dummy_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
{intl-overview-path}
<!-- END overview_dummy_path_tpl -->

<!-- BEGIN payment_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
<a class="path" href="{www_dir}{index}/trade/checkout/payment/">{intl-payment-path}</a>
<!-- END payment_path_tpl -->

<!-- BEGIN payment_dummy_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
{intl-payment-path}
<!-- END payment_dummy_path_tpl -->

<!-- BEGIN ordersent_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
<a class="path" href="{www_dir}{index}/trade/checkout/ordersent/">{intl-ordersent-path}</a>
<!-- END ordersent_path_tpl -->

<!-- BEGIN ordersent_dummy_path_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="0" />
{intl-ordersent-path}
<!-- END ordersent_dummy_path_tpl -->

<br /><br />
<hr noshade="noshade" size="4" />

<form action="{www_dir}{index}/trade/checkout/shipping/" method="post">

<select name="ShippingTypeID">
<!-- BEGIN shipping_type_tpl -->
<option value="{shipping_type_id}" {type_selected}>{shipping_type_name}</option>
<!-- END shipping_type_tpl -->
</select>

<br /><br />
<hr noshade="noshade" size="4" />
<input class="stdbutton" type="submit" name="Next" value="{intl-next}" />

</form>
