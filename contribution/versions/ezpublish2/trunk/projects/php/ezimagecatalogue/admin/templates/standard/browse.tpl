<form method="post" action="{action_url}" enctype="multipart/form-data">

<input type="hidden" name="CategoryID" value="{main_category_id}">

<h1>{intl-add_images_for} {name}</h1>

<!-- BEGIN current_category_tpl -->

<!-- END current_category_tpl -->

<hr noshade="noshade" size="4" />

<img src="/images/path-arrow.gif" height="10" width="12" border="0" alt="">
<a class="path" href="/imagecatalogue/browse/0/">{intl-image_root}</a>

<!-- BEGIN path_item_tpl -->
<img src="/images/path-slash.gif" height="10" width="16" border="0" alt="">
<a class="path" href="/imagecatalogue/browse/{category_id}/">{category_name}</a>
<!-- END path_item_tpl -->

<hr noshade="noshade" size="4" />

<div class="spacer"><div class="p">{current_category_description}</div></div>

<!-- BEGIN category_list_tpl -->
<table class="list" width="100%" border="0" cellspacing="0" cellpadding="4">

<!-- BEGIN category_tpl -->
<tr>
        <!-- BEGIN category_read_tpl -->
	<td class="{td_class}" width="1%" valign="top">
	<a href="/imagecatalogue/browse/{category_id}/"><img src="/images/folder.gif" alt="" width="16" height="16" border="0" /></a>
	</td>
	<td class="{td_class}" width="38%" valign="top">
	<a href="/imagecatalogue/browse/{category_id}/">{category_name}</a>
	</td>
	<td class="{td_class}" width="59%" valign="top">
	{category_description}
	</td>
        <!-- END category_read_tpl -->
</tr>
<!-- END category_tpl -->

</table>
<!-- END category_list_tpl -->

<!-- BEGIN image_list_tpl -->
<table class="list" width="100%" border="0" cellspacing="0" cellpadding="4" >
<!-- BEGIN detail_view_tpl -->
<tr>
	<!-- BEGIN detail_read_tpl -->
	<td class="{td_class}" valign="top" width="1%">
	<a href="/imagecatalogue/imageview/{image_id}/?RefererURL=/imagecatalogue/browse/{main_category_id}/"><img src="{image_src}" width="{image_width}" height="{image_height}" border="0" alt="{image_alt}" /></a>
	</td>
	<td class="{td_class}" valign="top" width="77%">
	{image_caption}
	</td>
	<td class="{td_class}" valign="top" width="20%">
	{image_size}&nbsp;{image_unit}
	</td>
	<!-- BEGIN multi_images_tpl -->
	<td class="{td_class}" valign="top" width="1%">
	<input type="checkbox" name="ImageArrayID[]" value="{image_id}">
	</td>
	<!-- END multi_images_tpl -->
	<!-- BEGIN single_images_tpl -->
	<td class="{td_class}" valign="top" width="1%">
	<input type="radio" name="ImageID" value="{image_id}">
	</td>
	<!-- END single_images_tpl -->
	<!-- END detail_read_tpl -->

</tr>
<!-- END detail_view_tpl -->
</table>
<!-- END image_list_tpl -->

<hr noshade="noshade" size="4" />

<input class="okbutton" type="submit" name="AddImages" value="{intl-add_images}">&nbsp;
<input class="okbutton" type="submit" name="Cancel" value="{intl-cancel}">
</form>


