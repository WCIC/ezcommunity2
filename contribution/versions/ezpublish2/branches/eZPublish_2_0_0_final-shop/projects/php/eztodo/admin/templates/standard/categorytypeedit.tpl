<form method="post" action="/todo/categorytypeedit/{action_value}/{category_type_id}">
<h1>{intl-head_line}</h1>

<hr noshade size="4" />

<p class="boxtext">{intl-name}:</p>
<input type="text" size="20" name="Name" value="{category_type_name}" />
<br /><br />

<hr noshade size="4" />

<input type="hidden" name="CategoryID" value="{category_type_id}">
<input type="hidden" name="Action" value="{action_value}">

<input class="okbutton" type="submit" value="{intl-submit_text}">

</form>
