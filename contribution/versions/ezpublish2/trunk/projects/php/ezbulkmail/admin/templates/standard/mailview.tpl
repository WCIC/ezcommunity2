<h1>{intl-mail_preview}</h1>

<hr noshade="noshade" size="4">

<form method="post" action="/bulkmail/preview/{current_mail_id}" enctype="multipart/form-data" >

<p>{intl-category}: {category}</p>

<p>{intl-from}: {from}</p>

<p>{intl-subject}: {subject}</p>
<table width="100%">
<tr>
  <td class="bgdark">
  {mail_body}
  </td>
<tr>
</table>

<hr noshade="noshade" size="4" />
<table cellspace="0" cellpadding="0" border="0">
<tr>
  <!-- BEGIN send_button_tpl -->
  <td><input class="okbutton" type="submit" Name="Send" value="{intl-send}" /></td>
  <td>&nbsp;</td>
  <!-- END send_button_tpl -->
  <!-- BEGIN edit_button_tpl -->
  <td><input class="okbutton" type="submit" Name="Edit" value="{intl-edit}" /></td>
  <!-- END edit_button_tpl -->
</tr>
</table>
</form>