
<h1>{intl-appointment_edit}</h1>

<!-- BEGIN user_error_tpl -->
	<!-- BEGIN no_user_error_tpl -->
	<p class="error">{intl-no_user_error}</p>
	<!-- END no_user_error_tpl -->

	<!-- BEGIN wrong_user_error_tpl -->
	<p class="error">{intl-wrong_user_error}</p>
	<!-- END wrong_user_error_tpl -->
<!-- END user_error_tpl -->


<!-- BEGIN no_error_tpl -->

<!-- BEGIN title_error_tpl -->
<p class="error">{intl-title_error}</p>
<!-- END title_error_tpl -->

<!-- BEGIN start_time_error_tpl -->
<p class="error">{intl-start_time_error}</p>
<!-- END start_time_error_tpl -->

<!-- BEGIN stop_time_error_tpl -->
<p class="error">{intl-stop_time_error}</p>
<!-- END stop_time_error_tpl -->

<hr noshade="noshade" size="4" />

<form method="post" action="/calendar/appointmentedit/{action_value}/{appointment_id}/">

<br />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td valign="top">

<p class="boxtext">{intl-appointment_title}:</p>
<input type="text" size="40" name="Name" value="{name_value}"/>
	
	<td valign="top">
	<td align="right">

	<input {is_private} type="checkbox" name="IsPrivate" />&nbsp;<span class="check">{intl-private_appointment}</span>

	</td>
</tr>
</table>
<br />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td valign="top">

<p class="boxtext">{intl-type}:</p>

<select name="TypeID">

<!-- BEGIN value_tpl -->
<option value="{option_value}" {selected}>{option_level}{option_name}</option>
<!-- END value_tpl -->

</select>

	<td valign="top">
	<td>

<p class="boxtext">{intl-priority}:</p>

<select name="Priority">
<option value="0" {0_selected}>{intl-low_priority}</option>
<option value="1" {1_selected}>{intl-normal_priority}</option>
<option value="2" {2_selected}>{intl-high_priority}</option>
</select>

	</td>
</tr>
</table>
<br />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td valign="top">
	<p class="boxtext">{intl-appointment_day}:</p>

	<select name="Day">
	<!-- BEGIN day_tpl -->
		<option value="{day_id}" {selected}>{day_name}</option>
	<!-- END day_tpl -->
	</select>

	</td>
	<td valign="top">
	<p class="boxtext">{intl-appointment_month}:</p>

	<select name="Month">
	<!-- BEGIN month_tpl -->
		<option value="{month_id}" {selected}>{month_name}</option>
	<!-- END month_tpl -->
	</select>

	</td>
	<td width="40%" valign="top">
	<p class="boxtext">{intl-appointment_year}:</p>
	<input type="text" name="Year" value="{year_value}" />
	</td>
</tr>
</table>
<br />

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td valign="top">
	<p class="boxtext">{intl-appointment_start}:</p>
	<input type="text" size="6" name="Start" value="{start_value}" />
	</td>
	<td>&nbsp;</td>
	<td>
	<p class="boxtext">{intl-appointment_stop}:</p>
	<input type="text" size="6" name="Stop" value="{stop_value}" />
	</td>
</tr>
</table>


<p class="boxtext">{intl-appointment_description}:</p>
<textarea name="Description" cols="40" rows="5" wrap="soft">{description_value}</textarea>

<br /><br />



<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<input class="okbutton" type="submit" value="{intl-ok}" />

	</td>
	<td>&nbsp;</td>
	<td>

	<input class="okbutton" type="submit" name="Cancel" value="{intl-cancel}" />

	</td>
</tr>
</table>

<input type="hidden" name="Action" value="{action_value}" />
<input type="hidden" name="AppointmentID" value="{appointment_id}" />
</form>

<!-- END no_error_tpl -->
