<h1>Brukeradministrasjon</h1>

<form name="form" action="index.php4?page=useredit.php4" method="post" onsubmit="return validate_form(this)">

<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<td bgcolor="#3c3c3c">
	<p class="white">
	<b>Identifikasjon</b>
	</p>
	</td>
</tr>
<tr>
	<td bgcolor="#f0f0f0">
	<p>
	Brukernavn:<br>
	<input type=text name="UserName" value="{nick_name}"><br></p>

	<p>
	Brukergruppe:<br>
	<select name="UserGroup">
	   {choices}
	</select>
	</p>
	</td>
	<img src="../images/1x1.gif" width="1" height="4" border="0"><br>
</tr>
</table>

<img src="../images/1x1.gif" width="1" height="4" border="0"><br>

<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<td bgcolor="#3c3c3c">
	<p class="white">
	<b>Personopplysninger</b>
	</p>
	</td>
</tr>
<tr>
	<td bgcolor="#f0f0f0">
	<p>Fornavn:<br>
	<input type="text" name="FirstName" value="{first_name}"><br></p>
	
	<p>Etternavn:<br>
	<input type="text" name="LastName" value="{last_name}"><br></p>
	<p>
	<p>
	<p>
	E-postadresse:<br>
	<input type=text name="EMail" value="{email}"><br></p>
	</td>
	<img src="../images/1x1.gif" width="1" height="4" border="0"><br>
</tr>
</table>

<img src="../images/1x1.gif" width="1" height="4" border="0"><br>

<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<td bgcolor="#3c3c3c">
	<p class="white">
	<b>Adgangskontroll</b>
	</p>
	</td>
</tr>
<tr>
	<td bgcolor="#f0f0f0">
	<p>Skriv inn �nsket passord (x-y tegn):<br>
	<input type="password" name="Password1" value="{password}"><br>
	</p>
	<p>
	Gjenta passord:<br>
	<input type="password" name="Password2" value="{password}"><br>
	</p>
	</td>
	<img src="../images/1x1.gif" width="1" height="4" border="0"><br>
</tr>
</table>
<br>

<input type="hidden" name="UserID" value="{user_id}">
<input type="hidden" name="Action" value="{action_value}">
<input type="submit" value="OK">
</form>
