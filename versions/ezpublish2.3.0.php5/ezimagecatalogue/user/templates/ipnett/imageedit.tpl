<form method="post" action="/imagecatalogue/image/{action_value}/{image_id}/" enctype="multipart/form-data">
<input type="hidden" name="max_file_size" value="30000000">

<h1>{intl-imageupload}</h1>

<!-- BEGIN errors_tpl -->
<h3 class="error">{intl-error_headline}</h3>
<ul>
    <!-- BEGIN error_name_tpl -->
    <li>{intl-error_name}
    <!-- END error_name_tpl -->

    <!-- BEGIN error_caption_tpl -->
    <li>{intl-error_caption}
    <!-- END error_caption_tpl -->

    <!-- BEGIN error_description_tpl -->
    <li>{intl-error_description}
    <!-- END error_description_tpl -->

    <!-- BEGIN error_file_upload_tpl -->
    <li>{intl-error_file_upload}
    <!-- END error_file_upload_tpl -->

    <!-- BEGIN error_read_everybody_permission_tpl -->
    <li>{intl-error_read_check}
    <!-- END error_read_everybody_permission_tpl -->

    <!-- BEGIN error_write_everybody_permission_tpl -->
    <li>{intl-error_write_check}
    <!-- END error_write_everybody_permission_tpl -->

</ul>

<!-- END errors_tpl -->

<br />

	<!-- BEGIN image_tpl -->
	<img src="{image_src}" width="{image_width}" height="{image_height}" border="0" alt="{image_alt}" />
	<!-- END image_tpl -->

<p class="boxtext">{intl-imagetitle}:</p>
<input type="text" class="box" size="40" name="Name" value="{name_value}"/>

<p class="boxtext">{intl-photographer}:</p>
<select name="PhotoID">
<!-- BEGIN photographer_item_tpl -->
<option value="{photo_id}" {selected}>{photo_name}</option>
<!-- END photographer_item_tpl -->
</select>

<p class="boxtext">{intl-category}:</p>
<select name="CategoryID">
<!-- BEGIN value_tpl -->
<option value="{option_value}" {selected}>{option_level}{option_name}</option>
<!-- END value_tpl -->
</select>

<p class="boxtext">{intl-additional_category}:</p>

<select multiple size="{num_select_categories}" name="CategoryArray[]">

<!-- BEGIN multiple_value_tpl -->
<option value="{option_value}" {multiple_selected}>{option_level}{option_name}</option>
<!-- END multiple_value_tpl -->

</select>


<p class="boxtext">{intl-imagefile}:</p>
<input size="40" class="box" name="userfile" type="file" />

<p class="boxtext">{intl-imagecaption}:</p>
<input type="text" size="40" class="box" name="Caption" value="{caption_value}"/>

<p class="boxtext">{intl-description}:</p>
<textarea name="Description" class="box" cols="40" rows="5" wrap="soft">{image_description}</textarea>

<br /><br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td valign="top">
    <p class="boxtext">{intl-read_permissions}:</p>
    <select multiple size="5" name="ReadGroupArrayID[]">
    <option value="0" {read_everybody}>{intl-everybody}</option>
    <!-- BEGIN read_group_item_tpl -->
    <option value="{group_id}" {is_read_selected1}>{group_name}</option>
    <!-- END read_group_item_tpl -->
    </select>
    <br /><br />
    </td>
    <td valign="top">
    <p class="boxtext">{intl-write_permissions}:</p>
    <select multiple size="5" name="WriteGroupArrayID[]">
    <option value="0" {write_everybody}>{intl-everybody}</option>
    <!-- BEGIN write_group_item_tpl -->
    <option value="{group_id}" {is_write_selected1}>{group_name}</option>
    <!-- END write_group_item_tpl -->
    </select>
    <br /><br />
    </td>
</tr>
</table>

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<input type="hidden" name="ArticleID" value="{article_id}" />
	<input type="hidden" name="ImageID" value="{image_id}" />
	<input type="hidden" name="Action" value="{action_value}" />
	<input class="okbutton" type="submit" value="{intl-ok}" />
	</td>
	<td>&nbsp;</td>
	<td>
	<input class="okbutton" type="submit" name="Cancel" value="{intl-cancel}" />
	</td>

</tr>
</table>
</form>
