<form method="post" action="/todo/categorytypeedit/">
<h1>{head_line}</h1>

<hr noshade size="4"/>

<p class="boxtext">{intl-name}</p>
<input type="text" name="Title" value="{category_type_name}">

<hr noshade size="4"/>

<input type="hidden" name="CategoryID" value="{category_type_id}">
<input type="hidden" name="Action" value="{action_value}">

<input class="okbutton" type="submit" value="{submit_text}">

</form>