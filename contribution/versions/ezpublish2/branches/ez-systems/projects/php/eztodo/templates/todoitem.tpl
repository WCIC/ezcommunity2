<tr>
	<tr>
	<td bgcolor="{bg_color}">
	<a href="javascript:NewWindow( 200, 150, '{document_root}noteinfo.php?NID={note_id}' );">{todo_title}</a>
	</td>

	<td bgcolor="{bg_color}">
	{todo_category}
	</td>

	<td bgcolor="{bg_color}">
	{todo_date}
	</td>

	<td bgcolor="{bg_color}">
	{todo_due}
	</td>

	<td bgcolor="{bg_color}">
	{todo_priority}
	</td>

	<td bgcolor="{bg_color}">
	{todo_permission}
	</td>

	<td bgcolor="{bg_color}">
	{todo_status}
	</td>

	<td bgcolor="{bg_color}">
	<a href="index.php?page={document_root}todoedit.php&Action=edit&TodoID={todo_id}">Rediger</a>
	</td>

	<td bgcolor="{bg_color}">
	<a href="index.php?page={document_root}todoedit.php&Action=delete&TodoID={todo_id}">Slett</a>
	</td>

</tr>
</tr>