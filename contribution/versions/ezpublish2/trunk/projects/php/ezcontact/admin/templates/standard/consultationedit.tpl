<form method="post" action="/contact/consultation/{action_value}/{consultation_id}/" enctype="multipart/form-data">
<input type="hidden" name="max_file_size" value="3000000">

<h1>{intl-edit_headline}</h1>

<hr noshade="noshade" size="4" />

<br />

<!-- BEGIN errors_tpl -->
<h3 class="error">{intl-error_headline}</h3>
<ul>

<!-- BEGIN error_company_person_item_tpl -->
<li>{intl-error_company_person}
<!-- END error_company_person_item_tpl -->

<!-- BEGIN error_no_company_person_item_tpl -->
<li>{intl-error_no_company_person}
<!-- END error_no_company_person_item_tpl -->

<!-- BEGIN error_no_status_item_tpl -->
<li>{intl-error_no_status}
<!-- END error_no_status_item_tpl -->

<!-- BEGIN error_short_description_item_tpl -->
<li>{intl-error_short_description}
<!-- END error_short_description_item_tpl -->

<!-- BEGIN error_description_item_tpl -->
<li>{intl-error_description}
<!-- END error_description_item_tpl -->

<!-- BEGIN error_email_notice_item_tpl -->
<li>{intl-error_email_notice}
<!-- END error_email_notice_item_tpl -->

</ul>
<!-- END errors_tpl -->

<!-- BEGIN contact_item_tpl -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
    	    <td width="50%">
	    <p class="boxtext">{intl-company_contacts}:</p>
	    <select single size="7" name="CompanyContact">

	    <!-- BEGIN company_contact_select_tpl -->
	    <option value="{contact_id}" {selected}>{contact_name}</option>
	    <!-- END company_contact_select_tpl -->

	    </select>
	    </td>

    	    <td width="50%">
	    <p class="boxtext">{intl-person_contacts}:</p>
	    <select single size="7" name="PersonContact">

	    <!-- BEGIN person_contact_select_tpl -->
	    <option value="{contact_id}" {selected}>{contact_lastname}, {contact_firstname}</option>
	    <!-- END person_contact_select_tpl -->

	    </select>
	    </td>
    </tr>
</table>
<!-- END contact_item_tpl -->
<!-- BEGIN hidden_company_contact_item_tpl -->
<input type="hidden" name="CompanyContact" value="{company_contact}">
<!-- END hidden_company_contact_item_tpl -->
<!-- BEGIN hidden_person_contact_item_tpl -->
<input type="hidden" name="PersonContact" value="{person_contact}">
<!-- END hidden_person_contact_item_tpl -->

<!-- BEGIN company_contact_item_tpl -->
<p>{intl-new_company_consultation_for} {company_name}</p>
<!-- END company_contact_item_tpl -->
<!-- BEGIN person_contact_item_tpl -->
<p>{intl-new_person_consultation_for} {person_lastname}, {person_firstname}</p>
<!-- END person_contact_item_tpl -->

<!-- BEGIN consultation_item_tpl -->
<h2>{intl-consultation_headline}</h2>
<!-- BEGIN consultation_date_item_tpl -->
<p class="boxtext">{intl-date}:</p>
{consultation_date}
<!-- END consultation_date_item_tpl -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
	    <td width="50%">
	    <p class="boxtext">{intl-shortdescription}:</p>
	    <input type="text" size="40" name="ShortDescription" value="{short_description}"/>
	    </td>

	    <td width="50%">
	    <p class="boxtext">{intl-status}:</p>
	    <!-- BEGIN status_item_tpl -->
	    <select name="StatusID">

	    <option value="-1">{intl-unknown_status}</option>
	    <!-- BEGIN status_select_tpl -->
	    <option value="{status_id}" {selected}>{status_name}</option>
	    <!-- END status_select_tpl -->

	    </select>
	    <!-- END status_item_tpl -->
	    <!-- BEGIN no_status_item_tpl -->
	    <a href="/contact/consultationtype/new/">{intl-new_consultation_type}</a>
	    <!-- END no_status_item_tpl -->
	    </td>
    </tr>
    <tr>
	    <td width="50%" rowspan="2">
	    <p class="boxtext">{intl-description}:</p>
	    <textarea name="Description" cols="40" rows="20" wrap="soft">{description}</textarea>
	    </td>

	    <td>
	    <p class="boxtext">{intl-group_notice}:</p>
	    <select multiple size="13" name="GroupNotice[]">
	    <!-- BEGIN group_notice_select_tpl -->
	    <option value="{group_notice_id}" {is_selected}>{group_notice_name}</option>
	    <!-- END group_notice_select_tpl -->
	    </select>
	    <td>
    </tr>
    <tr>
	    <td width="50%">
	    <p class="boxtext">{intl-email_notice}:</p>
	    <input type="text" size="30" name="EmailNotice" value="{email_notification}"/>
	    </td>
    </tr>
</table>
<!-- END consultation_item_tpl -->


<br />

<hr noshade="noshade" size="4" />

<input type="hidden" name="ConsultationID" value="{consultation_id}" />

<table cellpadding="0" cellspacing="0" border="0">
<tr>
    <td>
	<input class="okbutton" type="submit" value="{intl-ok}" />
	</form>
	</td>
	<td>&nbsp;</td>
	<td>
	<form method="post" action="/contact/consultation/list/">
	<input class="okbutton" type="submit" name="Back" value="{intl-back}">
	</form>
	</td>
</tr>
</table>

