<form method="post" action="/imagecatalogue/image/new/" enctype="multipart/form-data">

<h1>{intl-images}</h1>

<!-- BEGIN current_category_tpl -->

<!-- END current_category_tpl -->

<hr noshade="noshade" size="4" />

<img src="/images/path-arrow.gif" height="10" width="12" border="0" alt="">
<a class="path" href="/imagecatalogue/image/list/0/">{intl-image_root}</a>

<!-- BEGIN path_item_tpl -->
<img src="/images/path-slash.gif" height="10" width="16" border="0" alt="">
<a class="path" href="/imagecatalogue/image/list/{category_id}/">{category_name}</a>
<!-- END path_item_tpl -->

<hr noshade="noshade" size="4" />

<p>{current_category_description}</p>

<!-- BEGIN category_list_tpl -->
<table width="100%" border="0" cellspacing="0" cellpadding="4" >

<!-- BEGIN category_tpl -->
<tr>
        <!-- BEGIN category_read_tpl -->
	<td width="1%">
	<a href="/imagecatalogue/image/list/{category_id}/"><img src="/images/folder.gif" alt="" width="16" height="16" border="0" /></a>
	</td>
	<td width="38%">
	<a href="/imagecatalogue/image/list/{category_id}/">{category_name}</a>
	</td>
	<td width="59%">
	{category_description}
	</td>
        <!-- END category_read_tpl -->
        <!-- BEGIN category_write_tpl -->
	<td width="1%">
	<a href="/imagecatalogue/category/edit/{category_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezim{category_id}-red','','/images/redigerminimrk.gif',1)"><img name="ezim{category_id}-red" border="0" src="/images/redigermini.gif" width="16" height="16" align="top"></a>
	</td>
	<td width="1%">
	<a href="/imagecatalogue/category/delete/{category_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezim{category_id}-slett','','/images/slettminimrk.gif',1)"><img name="ezim{category_id}-slett" border="0" src="/images/slettmini.gif" width="16" height="16" align="top"></a>
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
	<a href="/imagecatalogue/imageview/{image_id}/?RefererURL=/imagecatalogue/image/list/{main_category_id}/"><img src="{image_src}" width="{image_width}" height="{image_height}" border="0" alt="{image_alt}" /></a>
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
	<a href="/imagecatalogue/imageview/{image_id}/?RefererURL=/imagecatalogue/image/list/{main_category_id}/"><img src="{image_src}" width="{image_width}" height="{image_height}" border="0" alt="{image_alt}" /></a>
	</td>
	<td class="{td_class}">
	{image_size}Kb
	</td>
	<td class="{td_class}" width="1%">
	<a href="/imagecatalogue/download/{image_id}/{original_image_name}/">download<br />{original_image_name}</a><br />
	</td>
	<!-- END detail_read_tpl -->
	<!-- BEGIN detail_write_tpl -->
	<td class="{td_class}" width="1%">
	<a href="/imagecatalogue/image/edit/{image_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezimg{image_id}-red','','/ezimagecatalogue/user/{image_dir}/redigerminimrk.gif',1)"><img name="ezimg{image_id}-red" border="0" src="/ezimagecatalogue/user/{image_dir}/redigermini.gif" width="16" height="16" align="top">{image_id}</a>
	</td>
	<td class="{td_class}" width="1%">
	<input type="checkbox" name="ImageArrayID[]" value="{image_id}">
	</td>
	<!-- END detail_write_tpl -->
</tr>
<!-- END detail_view_tpl -->
</table>


<!-- END image_list_tpl -->

<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="4" border="0">
<!-- BEGIN write_menu_tpl -->
<tr>
        <td>
	<input type="hidden" name="CurrentCategoryID" value="{main_category_id}">
	<input type="submit" name="NewImage" value="{intl-new_image}">
	</td>
        <td>
	<input type="submit" name="NewCategory" value="{intl-new_category}">
	</td>
        <td>
	<input type="submit" name="Delete" value="{intl-delete}">
	</td>
</tr>
<!-- END write_menu_tpl -->
</form>
<form method="post" action="/imagecatalogue/image/list/{main_category_id}/" enctype="multipart/form-data">
<input type="hidden" name="Detail" value="{is_detail_view}">
<tr>
        <!-- BEGIN normal_view_button -->
        <td>
	<input type="submit" name="NormalView" value="{intl-normal_view}">
	</td>
        <!-- END normal_view_button -->
        <!-- BEGIN detail_view_button -->
        <td>
	<input type="submit" name="DetailView" value="{intl-detail_view}">
	</td>
        <!-- END detail_view_button -->

</tr>
</form>
</table>

