<SCRIPT LANGUAGE="JavaScript1.2">
<!--//

	function MM_swapImgRestore() 
	{
		var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
	}

	function MM_preloadImages() 
	{
		var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
		var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
		if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
	}

	function MM_findObj(n, d) 
	{
		var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
		if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
		for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
	}

	function MM_swapImage() 
	{
		var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
		if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
	}
	
//-->
</SCRIPT> 

<div onLoad="MM_preloadImages('{www_dir}/ezarticle/images/redigerminimrk.gif','{www_dir}/ezarticle/images/slettminimrk.gif')"></div>

<h1>{intl-head_line} - {current_category_name}</h1>

<p>
{current_category_description}
</p>

<!-- BEGIN path_tpl -->

<!-- END path_tpl -->

<!-- BEGIN path_item_tpl -->

<!-- END path_item_tpl -->

<hr noshade="noshade" size="4" />

<!-- BEGIN category_list_tpl -->
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<th>Kategori:</td>
	<th>Beskrivelse:</th>
	<th colspan="2">&nbsp;</th>
</tr>

<!-- BEGIN category_item_tpl -->
<tr>
	<td class="{td_class}">
	<a href="{www_dir}{index}/article/archive/{category_id}/">{category_name}</a>&nbsp;
	</td>
	<td class="{td_class}">
	{category_description}&nbsp;
	</td>
	<td width="1%" class="{td_class}">
	<a href="{www_dir}{index}/article/categoryedit/edit/{category_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezac{category_id}-red','','/ezarticle/images/redigerminimrk.gif',1)"><img name="ezac{category_id}-red" border="0" src="{www_dir}/ezarticle/images/redigermini.gif" width="16" height="16" align="top"></a>
	</td>
	<td width="1%" class="{td_class}">
	<a href="{www_dir}{index}/article/categoryedit/delete/{category_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezac{category_id}-slett','','/ezarticle/images/slettminimrk.gif',1)"><img name="ezac{category_id}-slett" border="0" src="{www_dir}/ezarticle/images/slettmini.gif" width="16" height="16" align="top"></a>
	</td>
</tr>
<!-- END category_item_tpl -->
</table>
<hr noshade size="4"/>
<!-- END category_list_tpl -->


<!-- BEGIN article_list_tpl -->
<table class="list" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<th>Artikkel:</th>
	<th colspan="2">&nbsp;</th>
</tr>

<!-- BEGIN article_item_tpl -->
<tr>
	<td class="{td_class}">
	<a href="{www_dir}{index}/article/articlepreview/{article_id}/">
	{article_name}
	</a>
	</td>
	<td width="1%" class="{td_class}">
	<a href="{www_dir}{index}/article/articleedit/edit/{article_id}/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezaa{article_id}-red','','/ezarticle/images/redigerminimrk.gif',1)"><img name="ezaa{article_id}-red" border="0" src="{www_dir}/ezarticle/images/redigermini.gif" width="16" height="16" align="top"></a>
	</td>
	<td width="1%" class="{td_class}">

<a href="#" onClick="verify( 'delete', '/article/articleedit/delete/{article_id}'); return false;"
onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezaa{article_id}-slett','','/ezarticle/images/slettminimrk.gif',1)"><img name="ezaa{article_id}-slett" border="0" src="{www_dir}/ezarticle/images/slettmini.gif" width="16" height="16" align="top">
</a>

	</td>
</tr>
<!-- END article_item_tpl -->

</table>
<!-- END article_list_tpl -->



<hr noshade size="4"/>

<table border="0">
<tr>
	<td>
<form action="{www_dir}{index}/article/categoryedit/edit/{current_category_id}/" method="post">
<input class="okbutton" type="submit" value="Rediger kategori" />
</form>
	</td>
	<td>

<form action="{www_dir}{index}/article/articleedit/new/" method="post">
<input class="okbutton" type="submit" value="Ny artikkel" />
</form>

	</td>
</tr>
</table>


