<form method="post" action="{www_dir}{index}/imagecatalogue/image/new/" enctype="multipart/form-data">

<input type="hidden" name="CategoryID" value="{main_category_id}">

<h1>{intl-images}</h1>

<!-- BEGIN current_category_tpl -->

<!-- END current_category_tpl -->

<hr noshade="noshade" size="4" />

<img src="{www_dir}/images/path-arrow.gif" height="10" width="12" border="0" alt="">
<a class="path" href="{www_dir}{index}/imagecatalogue/image/list/0/">{intl-image_root}</a>

<!-- BEGIN path_item_tpl -->
<img src="{www_dir}/images/path-slash.gif" height="10" width="16" border="0" alt="">
<a class="path" href="{www_dir}{index}/imagecatalogue/image/list/{category_id}/">{category_name}</a>
<!-- END path_item_tpl -->

<hr noshade="noshade" size="4" />

<div class="spacer"><div class="p">{current_category_description}</div></div>

<!-- BEGIN category_list_tpl -->
<table width="100%" border="0" cellspacing="0" cellpadding="4" >

<!-- BEGIN category_tpl -->
<tr>
        <!-- BEGIN category_read_tpl -->
	<td width="1%">
	<a href="{www_dir}{index}/imagecatalogue/image/list/{category_id}/"><img src="{www_dir}/images/folder.gif" alt="" width="16" height="16" border="0" /></a>
	</td>
	<td width="38%">
	<a href="{www_dir}{index}/imagecatalogue/image/list/{category_id}/">{category_name}</a>
	</td>
	<td width="59%">
	{category_description}
	</td>
        <!-- END category_read_tpl -->
        <!-- BEGIN category_write_tpl -->
	<td width="1%">
	<a href="{www_dir}{index}/imagecatalogue/category/edit/{category_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezim{category_id}-red','','/images/redigerminimrk.gif',1)"><img name="ezim{category_id}-red" border="0" src="{www_dir}/images/redigermini.gif" width="16" height="16" align="top"></a>
	</td>
	<td width="1%">
	<input type="checkbox" name="CategoryArrayID[]" value="{category_id}" />
	</td>
        <!-- END category_write_tpl -->
</tr>
<!-- END category_tpl -->

</table>
<!-- END category_list_tpl -->

<!-- BEGIN image_list_tpl -->
<table width="100%" border="0" cellspacing="0" cellpadding="4" >
<!-- BEGIN image_tpl -->
{begin_tr}
	<!-- BEGIN read_tpl -->
	<td>
	<a href="{www_dir}{index}/imagecatalogue/imageview/{image_id}/?RefererURL=/imagecatalogue/image/list/{main_category_id}/"><img src="{www_dir}{image_src}" width="{image_width}" height="{image_height}" border="0" alt="{image_alt}" /></a>
	</td>
	<!-- END read_tpl -->
	<!-- BEGIN write_tpl -->

	<!-- END write_tpl -->
{end_tr}

<!-- END image_tpl -->
<!-- BEGIN detail_view_tpl -->
<tr>
	<!-- BEGIN detail_read_tpl -->
	<td>
	<a href="{www_dir}{index}/imagecatalogue/imageview/{image_id}/?RefererURL=/imagecatalogue/image/list/{main_category_id}/"><img src="{www_dir}{image_src}" width="{image_width}" height="{image_height}" border="0" alt="{image_alt}" /></a>
	</td>
	<td class="{td_class}">
	{image_size}&nbsp;Kb
	</td>
	<td class="{td_class}" width="1%">
	<a href="{www_dir}{index}/imagecatalogue/download/{image_id}/{original_image_name}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezimg{image_id}-dl','','/ezimagecatalogue/user/{image_dir}/downloadminimrk.gif',1)"><img name="ezimg{image_id}-dl" border="0" src="{www_dir}/ezimagecatalogue/user/{image_dir}/downloadmini.gif" width="16" height="16" align="top" alt="Download" /></a><br />
	</td>
	<!-- END detail_read_tpl -->
	<!-- BEGIN detail_write_tpl -->
	<td class="{td_class}" width="1%">
	<a href="{www_dir}{index}/imagecatalogue/image/edit/{image_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezimg{image_id}-red','','/ezimagecatalogue/user/{image_dir}/redigerminimrk.gif',1)"><img name="ezimg{image_id}-red" border="0" src="{www_dir}/ezimagecatalogue/user/{image_dir}/redigermini.gif" width="16" height="16" align="top" alt="Edit" /></a>
	</td>
	<td class="{td_class}" width="1%">
	<input type="checkbox" name="ImageArrayID[]" value="{image_id}">
	</td>
	<!-- END detail_write_tpl -->
</tr>
<!-- END detail_view_tpl -->
</table>


<!-- END image_list_tpl -->

<!-- BEGIN write_menu_tpl -->
<!-- BEGIN default_delete_tpl -->
<hr noshade="noshade" size="4" />
<table cellspacing="0" cellpadding="0" border="0">
<tr>

    <!-- BEGIN delete_categories_button_tpl -->
    <td>
	<input class="stdbutton" type="submit" name="DeleteCategories" value="{intl-delete_categories}">
	</td>
	<td>&nbsp;</td>
	<!-- END delete_categories_button_tpl -->

    <!-- BEGIN delete_images_button_tpl -->
    <td>
	<input class="stdbutton" type="submit" name="DeleteImages" value="{intl-delete_images}">
	</td>
    <!-- END delete_images_button_tpl -->

</tr>
</table>
<!-- END default_delete_tpl -->

<!-- BEGIN default_new_tpl -->
<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
    <td>
	<input class="stdbutton" type="submit" name="NewImage" value="{intl-new_image}">
	</td>
	<td>&nbsp;</td>
    <td>
	<input class="stdbutton" type="submit" name="NewCategory" value="{intl-new_category}">
	</td>
</tr>
</table>
<!-- END default_new_tpl -->
<!-- END write_menu_tpl -->
</form>


<form method="post" action="{www_dir}{index}/imagecatalogue/image/list/{main_category_id}/" enctype="multipart/form-data">
<input type="hidden" name="Detail" value="{is_detail_view}">

<!-- BEGIN normal_view_button -->

<hr noshade="noshade" size="4" />

<input class="stdbutton" type="submit" name="NormalView" value="{intl-normal_view}">

<!-- END normal_view_button -->

<!-- BEGIN detail_view_button -->

<hr noshade="noshade" size="4" />

<input class="stdbutton" type="submit" name="DetailView" value="{intl-detail_view}">

<!-- END detail_view_button -->

</form>

