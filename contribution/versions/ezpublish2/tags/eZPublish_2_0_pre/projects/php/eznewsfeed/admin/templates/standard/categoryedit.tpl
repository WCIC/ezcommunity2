<form method="post" action="/newsfeed/category/">

<h1>{intl-category_edit}</h1>

<hr noshade="noshade" size="4" />

<p class="boxtext">{intl-category_name}:</p>
<input type="text" size="40" name="CategoryName" value="{category_name_value}"/>

<p class="boxtext">{intl-category_description}:</p>
<textarea cols="40" rows="5" wrap="soft" name="CategoryDescription">{category_description_value}</textarea>
<br /><br />

<hr noshade="noshade" size="4" />


<input type="submit" class="okbutton" value="{intl-ok}" />
<input class="okbutton" type="submit" name="Cancel" value="{intl-cancel}" />

<input type="hidden" value="{action_value}" name="Action" />
<input type="hidden" value="{category_id}" name="CategoryID" />
</form>
