<tr>
	<td bgcolor="{bg_color}" colspan="2">
			<img src="/{document_root}images/1x1.gif" width="6" height="18"  border="0">
			<img name="firma{company_id}-se" border="0" src="/{document_root}images/firmamini.gif" width="16" height="16">
			<img src="/{document_root}images/1x1.gif" width="4" height="18"  border="0">
		<a href="javascript:NewWindow( 250, 350, '/contact/companyinfo/?CID={company_id}' );" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('firma{company_id}-se','','/{document_root}images/firmaminimrk.gif',1)">
			{company_name}
		</a>
	</td>
		
	<td bgcolor="{bg_color}">
		<a href="/contact/companyedit/?Action=edit&CID={company_id}" onMouseOut="MM_swapImgRestore(); MM_swapImage('firma{company_id}-se','','/{document_root}images/firmamini.gif',1)" onMouseOver="MM_swapImage('firma{company_id}-se','','/{document_root}images/firmaminimrk.gif',1); MM_swapImage('firma{company_id}-red','','/{document_root}images/redigerminimrk.gif',1)"><img name="firma{company_id}-red" border="0" src="/{document_root}images/redigermini.gif" width="16" height="16" align="top"></a>

		</a>
	</td>
	
	<td bgcolor="{bg_color}">
<a href="#" onClick="verify( 'Slette kontaktfirma?', '/contact/companyedit/?Action=delete&CID={company_id}'); return false;" onMouseOut="MM_swapImgRestore(); MM_swapImage('firma15-se','','/{document_root}images/firmamini.gif',1)" onMouseOver="MM_swapImage('firma15-se','','/{document_root}images/firmaminimrk.gif',1); MM_swapImage('firma15-slett','','/{document_root}images/slettminimrk.gif',1)"><img name="firma15-slett" border="0" src="/{document_root}images/slettmini.gif" width="16" height="16" align="top"></a>

	</td>
</tr>

{person_list}

