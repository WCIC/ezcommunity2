<?
ob_start();

// brukes for sider som skal redirectes..
if ( file_exists( $prePage ) )
{
  include( $prePage );
  die();
}
?>

<html>
<head>
	<title>
	eZ contact
	</title>
	<link rel="stylesheet" type="text/css" href="ez.css">
<SCRIPT LANGUAGE="JavaScript1.2">
<!--//
                function NewWindow(bredde,hoyde,url) {
                        window.open(url,"_blank","menubars=0,scrollbars=1,resizable=0,height="+hoyde+",width="+bredde);
                }

     function verify( msg, url )
     {
	if ( confirm( msg ) )
	{
	this.location = url;
	}
     }

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

</head>
<body onLoad="MM_preloadImages('{document_root}images/firmaminimrk.gif','{document_root}images/personminimrk.gif','{document_root}images/redigerminimrk.gif','{document_root}images/slettminimrk.gif')">

<table width="100%" cellspacing="0" cellpadding="3" border="0">
<tr>
<td bgcolor="#000000">
<table width="100%" cellspacing="0" cellpadding="13" border="0">
<tr>
<td bgcolor="#808080">


	<table width="100%" cellspacing="0" cellpadding="3" border="0">
	<tr>
	<td bgcolor="#000000">
	<table width="100%" cellspacing="0" cellpadding="10" border="0">
	<tr>
	<td bgcolor="#ffffff">

<? // hovedinnholdet p� siden
print( session_id() );
if ( file_exists( $page ) )
{
  include( $page );
}
else
{
  print( "<h1>Feil: fant ikke filen: $page</h1>" );
}
?>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
</td>
</tr>
</table>

</body>
</html>
<?
    ob_end_flush();
?>
