<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
        <td valign="bottom">
        <h1>{intl-view_headline}</h1>
        </td>
        <td rowspan="2" align="right">
        <form action="/contact/person/search/" method="post">
        <input type="text" name="SearchText" size="12" />       
        <input class="stdbutton" type="submit" value="{intl-search}" />
        </form> 
        </td>
</tr>
</table>

<hr noshade="noshade" size="4" />

<br />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
	<p class="boxtext">{intl-name_headline}:</p>
	<span class="p">{firstname} {lastname}</span>
	</td>

	<td>
	<p class="boxtext">{intl-birthday_headline}: </p>
	<!-- BEGIN birth_item_tpl -->
	<span class="p">{birthdate}</span>
	<!-- END birth_item_tpl -->
	<!-- BEGIN no_birth_item_tpl -->
	<span class="p">{intl-no_birthday}</span>
	<!-- END no_birth_item_tpl -->
	</td>
</tr>
</table>

<h2>{intl-companies}</h2>

<!-- BEGIN company_item_tpl -->

<a href="/contact/company/view/{company_id}/">{company_name}</a><br />

<!-- END company_item_tpl -->
<br />

<p class="boxtext">{intl-description_headline}:</p>
<div class="p">{description}</div>
<br />

<!-- BEGIN address_item_tpl -->
<h2>{intl-addresses_headline}</h2>
<br />
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<!-- BEGIN address_line_tpl -->
	<td>
	<p class="boxtext">{address_type_name}:</p>
	<div class="p">{street1}</div>
	<div class="p">{street2}</div>
	<div class="p">{zip} {place}</div>
	<div class="p">{country}</div>
	</td>
<!-- END address_line_tpl -->
</tr>
</table>
<!-- END address_item_tpl -->
<!-- BEGIN no_address_item_tpl -->
<h2>{intl-addresses_headline}</h2>
<p>{intl-error_no_addresses}</p>
<!-- END no_address_item_tpl -->


<h2>{intl-telephone_headline}</h2>
<!-- BEGIN phone_item_tpl -->
<br />
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<!-- BEGIN phone_line_tpl -->
	<td valign="top">
	<p class="boxtext">{phone_type_name}:</p>
	{phone}
	</td>
<!-- END phone_line_tpl -->
</tr>
</table>
<!-- END phone_item_tpl -->

<!-- BEGIN no_phone_item_tpl -->
<p>{intl-error_no_phones}</p>
<!-- END no_phone_item_tpl -->


<h2>{intl-online_headline}</h2>
<!-- BEGIN online_item_tpl -->
<br />
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<!-- BEGIN online_line_tpl -->
	<td>
	<p class="boxtext">{online_type_name}:</p>
	<a href="{online_prefix}{online}">{online_visual_prefix}{online}</a>
	</td>
<!-- END online_line_tpl -->
</tr>
</table>
<!-- END online_item_tpl -->
<!-- BEGIN no_online_item_tpl -->
<p>{intl-error_no_onlines}</p>
<!-- END no_online_item_tpl -->

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="100%">
	<p class="boxtext">{intl-project_status}:</p>
	<!-- BEGIN project_status_tpl -->
	{project_status}
	<!-- END project_status_tpl -->
	<!-- BEGIN no_project_status_tpl -->
	{intl-no_project_status}
	<!-- END no_project_status_tpl -->
	<br /><br />
	</td>
</tr>
</table>

<!-- BEGIN consultation_table_item_tpl -->
<h2>{intl-consultation_headline}</h2>

<table class="list" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<th>{intl-consultation_date}:</th>
	<th>{intl-consultation_short_description}:</th>
	<th>{intl-consultation_status}:</th>
	<th colspan="2">&nbsp;</th>
</tr>

<!-- BEGIN consultation_item_tpl -->
<tr class="{bg_color}">
	<td>
        {consultation_date}
	</td>
	<td>
        <a href="/contact/consultation/view/{consultation_id}">{consultation_short_description}</a>
	</td>
	<td>
        <a href="/contact/consultation/type/list/{consultation_status_id}">{consultation_status}</a>
	</td>

	<td width="1%">
	<a href="/contact/consultation/edit/{consultation_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezc{consultation_id}-red','','/images/redigerminimrk.gif',1)"><img name="ezc{consultation_id}-red" border="0" src="/images/redigermini.gif" width="16" height="16" align="top" alt="Edit" /></a>
	</td>

	<td width="1%">
	<a href="/contact/consultation/delete/{consultation_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezc{consultation_id}-slett','','/images/slettminimrk.gif',1)"><img name="ezc{consultation_id}-slett" border="0" src="/images/slettmini.gif" width="16" height="16" align="top" alt="Delete" /></a>
	</td>	

</tr>
<!-- END consultation_item_tpl -->
</table>

<!-- END consultation_table_item_tpl -->


<form method="post" action="/contact/person/edit/{person_id}/">

<hr noshade="noshade" size="4" />

<!-- BEGIN consultation_buttons_tpl -->
<input class="stdbutton" type="submit" name="ListConsultation" value="{intl-consultation_list}">
<input class="stdbutton" type="submit" name="NewConsultation" value="{intl-consultation}">
<hr noshade="noshade" size="4" />
<!-- END consultation_buttons_tpl -->

<input class="okbutton" type="submit" name="Edit" value="{intl-edit}">
<!--
<input type="submit" name="Delete" value="{intl-delete}" />
<input type="submit" name="Back" value="{intl-list}">
-->
</form>
