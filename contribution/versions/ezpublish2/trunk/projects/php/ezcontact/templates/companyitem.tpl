<tr>
	<td bgcolor="{bg_color}" colspan="2">
		<a href="javascript:NewWindow( 300, 250, '{document_root}companyinfo.php4?CID={company_id}' );" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('{company_name}-se','','{document_root}images/firmaminimrk.gif',1)">
			<img src="{document_root}images/1x1.gif" width="6" height="18"  border="0">
			<img name="{company_name}-se" border="0" src="{document_root}images/firmamini.gif" width="16" height="16">
			<img src="{document_root}images/1x1.gif" width="4" height="18"  border="0">
			{company_name}
		</a>
	</td>
		
	<td bgcolor="{bg_color}">
		<a href="index.php4?page={document_root}companyedit.php4&Action=edit&CID={company_id}" onMouseOut="MM_swapImgRestore(); MM_swapImage('{company_name}-se','','{document_root}images/firmamini.gif',1)" onMouseOver="MM_swapImage('{company_name}-se','','{document_root}images/firmaminimrk.gif',1); MM_swapImage('{company_name}-red','','{document_root}images/redigerminimrk.gif',1)"><img name="{company_name}-red" border="0" src="{document_root}images/redigermini.gif" width="16" height="16"></a>

		</a>
	</td>
	
	<td bgcolor="{bg_color}">
		{delete_company}
	</td>
</tr>
<tr>
	{person_list}
</tr>

