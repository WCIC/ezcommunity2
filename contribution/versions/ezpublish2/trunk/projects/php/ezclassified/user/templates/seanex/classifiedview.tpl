<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="1%" class="tdmini" bgcolor="#465da1" align="left" valign="top"><img src="/images/box-tl.gif" width="4" height="4" border="0" alt="" /><br /></td>
	<td width="98%" bgcolor="#465da1" class="tdminipath" rowspan="3" valign="middle"><div class="smallpath"><span class="smallbold">Jobbmarked</span> | Stillingsannonse</div></td>
	<td width="1%" class="tdmini" bgcolor="#465da1" align="right" valign="top"><img src="/images/box-tr.gif" width="4" height="4" border="0" alt="" /><br /></td>
</tr>
<tr>
	<td width="1%" class="tdmini" bgcolor="#465da1"><img src="/images/1x1.gif" width="1" height="1" border="0" alt="" /><br /></td>
	<td width="1%" class="tdmini" bgcolor="#465da1"><img src="/images/1x1.gif" width="1" height="1" border="0" alt="" /><br /></td>
</tr>
<tr>
	<td width="1%" class="tdmini" bgcolor="#465da1" align="left" valign="bottom"><img src="/images/box-bl.gif" width="4" height="4" /><br /></td>
	<td width="1%" class="tdmini" bgcolor="#465da1" align="right" valign="bottom"><img src="/images/box-br.gif" width="4" height="4" /><br /></td>
</tr>
</table>

<!-- BEGIN company_view_tpl -->
<h1>{company_name}</h1>
<!-- <p class="boxtext">{intl-logo}:</p> -->
<!-- BEGIN no_logo_tpl -->
<!-- <p>{intl-no_logo}</p> -->
<!-- END no_logo_tpl -->

<!-- BEGIN logo_view_tpl -->
<img src="{logo_image_src}" width="{image_width}" height="{image_height}" border="0" alt="{image_alt}" align="right" />
<!-- END logo_view_tpl -->

<!-- BEGIN address_item_tpl -->
<p class="boxtext">{intl-address}:</p>
{street1}<br/>
{street2}<br />
{zip} {place}<br />
<!-- END address_item_tpl -->

<br clear="all" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="50%">
<!-- BEGIN phone_item_tpl -->
<p class="boxtext">{intl-telephone}:</p>
{telephone}
<!-- END phone_item_tpl -->
<!-- BEGIN no_phone_item_tpl -->
<p class="boxtext">{intl-telephone}:</p>
{intl-no_telephone}
<!-- END no_phone_item_tpl -->
	</td>
	<td>
<!-- BEGIN fax_item_tpl -->
<p class="boxtext">{intl-fax}:</p>
{fax}
<!-- END fax_item_tpl -->
<!-- BEGIN no_fax_item_tpl -->
<p class="boxtext">{intl-fax}:</p>
{intl-no_fax}
<!-- END no_fax_item_tpl -->
	</td>
</tr>
</table>

<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="50%">
<!-- BEGIN web_item_tpl -->
<p class="boxtext">{intl-web}:</p>
<a href="http://{web}">{web}</a>
<!-- END web_item_tpl -->
<!-- BEGIN no_web_item_tpl -->
<p class="boxtext">{intl-web}:</p>
{intl-no_web}
<!-- END no_web_item_tpl -->
	</td>
	<td>
<!-- BEGIN email_item_tpl -->
<p class="boxtext">{intl-email}:</p>
<a href="mailto:{email}">{email}</a>
<!-- END email_item_tpl -->
<!-- BEGIN no_email_item_tpl -->
<p class="boxtext">{intl-email}:</p>
{intl-no_email}
<!-- END no_email_item_tpl -->
	</td>
</tr>
</table>

<p class="boxtext">{intl-description}:</p>
{company_description}<br /><br />

<!-- END company_view_tpl -->

<h2>Stillingsinformasjon</h2>

<p class="boxtext">{intl-reference}:</p>
{classified_reference}

<p class="boxtext">{intl-title}:</p>
{classified_title}

<p class="boxtext">{intl-duedate}:</p>
{classified_duedate}

<p class="boxtext">{intl-position_type}:</p>
{classified_position_type}

<p class="boxtext">{intl-initiate_type}:</p>
{classified_initiate_type}

<p class="boxtext">{intl-description}:</p>
{classified_description}

<p class="boxtext">{intl-contact_persons}:</p>
<!-- BEGIN person_item_tpl -->
<p>
Name: {person_name}<br />
Title: {person_title}<br />
<!-- BEGIN person_mail_item_tpl -->
Mail: <a href="mailto:{person_mail}">{person_mail}</a><br />
<!-- END person_mail_item_tpl -->
<!-- BEGIN person_phone_item_tpl -->
Phone: {person_phone}<br />
<!-- END person_phone_item_tpl -->
<!-- BEGIN person_fax_item_tpl -->
Fax: {person_fax}<br />
<!-- END person_fax_item_tpl -->
</p>
<!-- END person_item_tpl -->
<!-- BEGIN no_person_item_tpl -->
{intl-no_persons}
<!-- END no_person_item_tpl -->

<p class="boxtext">{intl-pay}:</p>
{classified_pay}

<p class="boxtext">{intl-worktime}:</p>
{classified_worktime}

<p class="boxtext">{intl-duration}:</p>
{classified_duration}

<p class="boxtext">{intl-workplace}:</p>
{classified_workplace}

