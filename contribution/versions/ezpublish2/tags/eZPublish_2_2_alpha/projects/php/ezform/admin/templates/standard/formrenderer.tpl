<!-- BEGIN text_field_item_tpl -->
<input type="text" class="box" size="40" name="{field_name}" value="{field_value}" />
<!-- END text_field_item_tpl -->

<!-- BEGIN text_area_item_tpl -->
<textarea class="box" name="{field_name}" cols="40" rows="5" wrap="soft">{field_value}</textarea>
<!-- END text_area_item_tpl -->

<!-- BEGIN error_list_tpl -->
<h2 class="error">{intl-error}</h2>
<!-- BEGIN error_item_tpl -->
<div class="error">{error_value} {error_message}.</div>
<!-- END error_item_tpl -->
<hr noshade="noshade" size="4" />
<br />
<!-- END error_list_tpl -->

<!-- BEGIN form_list_tpl -->
<!-- BEGIN form_start_tag_tpl -->
<form action="{www_dir}{index}/form/form/process/{form_id}/" method="post">
<h1>{form_name}</h1>
<hr noshade="noshade" size="4" />
<br />
<!-- END form_start_tag_tpl -->
<!-- BEGIN form_instructions_tpl -->
<a href="{www_dir}{index}{form_instruction_page}">{intl-instructions}</a>
<!-- END form_instructions_tpl -->
<input type="hidden" name="formID" value="{form_id}" />
<input type="hidden" name="mailSubject" value="{form_name}" />
<input type="hidden" name="redirectTo" value="{form_completed_page}" />
<!-- BEGIN form_sender_tpl -->
<p class="boxtext">{intl-form_sender}</p> 
<input type="text" name="formSender" value="{form_sender}" />
<br /><br />

<hr noshade="noshade" size="4" />
<!-- END form_sender_tpl -->

<!-- BEGIN form_item_tpl -->
    <p class="boxtext">{element_name}:</p>
    {element}
	<br /><br />
<!-- END form_item_tpl -->

<!-- BEGIN form_buttons_tpl -->
<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td valign="top">
	<input class="okbutton" type="submit" name="OK" value="{intl-ok}" />
	</td>
</tr>
</table>
<!-- END form_buttons_tpl -->

<!-- BEGIN form_end_tag_tpl -->
</form>
<!-- END form_end_tag_tpl -->

<!-- END form_list_tpl -->
