Persons�k:
<form method="post" action="index.php4?page={document_root}contactlist.php4">
<input type="text" name="PersonQuery">
<input type="submit" value="s�k">
</form>
Firmas�k:
<form method="post" action="index.php4?page={document_root}contactlist.php4">
 <input type="text" name="CompanyQuery">
<input type="submit" value="s�k">
</form>

<h1>Kontakt firma </h1>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
{company_list}
</table>


