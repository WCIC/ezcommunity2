
<!-- BEGIN header_1_tpl -->
<h1>{contents}</h1>
<!-- END header_1_tpl -->

<!-- BEGIN header_2_tpl -->
<h2>{contents}</h2>
<!-- END header_2_tpl -->

<!-- BEGIN header_3_tpl -->
<h3>{contents}</h3>
<!-- END header_3_tpl -->

<!-- BEGIN header_4_tpl -->
<h4>{contents}</h4>
<!-- END header_4_tpl -->

<!-- BEGIN header_5_tpl -->
<h5>{contents}</h5>
<!-- END header_5_tpl -->

<!-- BEGIN header_6_tpl -->
<h6>{contents}</h6>
<!-- END header_6_tpl -->

<!-- BEGIN image_tpl -->
<br clear="all"><table width="{image_width}" align="{image_alignment}" border="0" cellspacing="0" cellpadding="4">
<tr>
<td>
   <a href="/imagecatalogue/imageview/{image_id}/?RefererURL=/article/{view_mode}/{article_id}/">
   <img src="{image_url}" border="0" width="{image_width}" height="{image_height}" alt="" />
   </a>   
</td>
</tr>
<tr>
   <td class="pictext" bgcolor="#eeeeee">
    {caption}
   </td>
</tr>
</table>
<!-- END image_tpl -->

<!-- BEGIN image_float_tpl -->
<a href="/imagecatalogue/imageview/{image_id}/?RefererURL=/article/{view_mode}/{article_id}/">
<img src="{image_url}" border="0" width="{image_width}" height="{image_height}" alt="" /></a>   
<!-- END image_float_tpl -->

<!-- BEGIN link_tpl -->
<a href="{href}">{link_text}</a>
<!-- END link_tpl -->


<!-- BEGIN bold_tpl -->
<b>{contents}</b>
<!-- END bold_tpl -->

<!-- BEGIN italic_tpl -->
<i>{contents}</i>
<!-- END italic_tpl -->

<!-- BEGIN underline_tpl -->
<u>{contents}</u>
<!-- END underline_tpl -->

<!-- BEGIN strong_tpl -->
<table align="left" width="30%">
<tr>
<td bgcolor="ddddee" >
<font color="335522" ><strong>{contents}</strong></font>
</td>
</tr>
</table>
<!-- END strong_tpl -->