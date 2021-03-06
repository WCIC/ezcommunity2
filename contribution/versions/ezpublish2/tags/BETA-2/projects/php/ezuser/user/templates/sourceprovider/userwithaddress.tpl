<form method="post" action="/user/userwithaddress/{action_value}/{user_id}/">

<h1>Registrer ny bruker</h1>

<hr noshade="noshade" size="4" />

<!-- BEGIN required_fields_error_tpl -->
<h3 class="error" >{intl-required_fields_error}</h3>
<!-- END required_fields_error_tpl -->

<!-- BEGIN user_exists_error_tpl -->
<h3 class="error" >{intl-user_exists_error}</h3>
<!-- END user_exists_error_tpl -->

<!-- BEGIN password_error_tpl -->
<h3 class="error" >{intl-password_error}</h3>
<!-- END password_error_tpl -->

<br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<p class="boxtext">{intl-firstname}</p>
	<input type="text" size="20" name="FirstName" value="{first_name_value}"/>
	</td>
	<td>
	<p class="boxtext">{intl-lastname}</p>
	<input type="text" size="20" name="LastName" value="{last_name_value}"/>
	</td>
</tr>
</table>

<p class="boxtext">{intl-login}</p>
<input type="text" size="20" name="Login" value="{login_value}"/>

<p class="boxtext">{intl-email}</p>
<input type="text" size="20" name="Email" value="{email_value}"/>

<p class="boxtext">{intl-street1}</p>
<input type="text" size="20" name="Street1" value="{street1_value}"/>

<p class="boxtext">{intl-street2}</p>
<input type="text" size="20" name="Street2" value="{street2_value}"/>

<p class="boxtext">{intl-zip}</p>
<input type="text" size="20" name="Zip" value="{zip_value}"/>

<p class="boxtext">{intl-place}</p>
<input type="text" size="20" name="Place" value="{place_value}"/>

<br /><br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<p class="boxtext">{intl-password}</p>
	<input type="password" size="20" name="Password" value="{password_value}"/>
	</td>
	<td>
	<p class="boxtext">{intl-verifypassword}</p>
	<input type="password" size="20" name="VerifyPassword" value="{verify_password_value}"/>
	</td>
</tr>
</table>

<br />

<hr noshade="noshade" size="4" />

<input type="hidden" name="UserID" value="{user_id}" />
<input class="okbutton" type="submit" value="OK" />

<input type="hidden" name="RedirectURL" value="{redirect_url}" />
</form>



