
<!-- BEGIN last_consultations_item_tpl -->
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<tr>
	<td colspan="2" class="menuhead">{intl-consultation_headline}</td>
</tr>

<!-- BEGIN consultation_item_tpl -->
<tr>
    <td valign="top"><a class="menutext" href="/contact/consultation/view/{consultation_id}">{consultation_desc}</a></td>
    <td valign="top">
	<!-- BEGIN consultation_person_item_tpl -->
	<a class="menutext" href="/contact/person/view/{person_id}">{contact_lastname}, {contact_firstname}</a>
	<!-- END consultation_person_item_tpl -->
	<!-- BEGIN consultation_no_person_item_tpl -->
	{contact_lastname}, {contact_firstname}
	<!-- END consultation_no_person_item_tpl -->
	<!-- BEGIN consultation_company_item_tpl -->
	<a class="menutext" href="/contact/company/view/{company_id}">{contact_name}</a>
	<!-- END consultation_company_item_tpl -->
	<!-- BEGIN consultation_no_company_item_tpl -->
	{contact_name}
	<!-- END consultation_no_company_item_tpl -->
    </td>
</tr>
<!-- END consultation_item_tpl -->

</table>
<!-- END last_consultations_item_tpl -->
