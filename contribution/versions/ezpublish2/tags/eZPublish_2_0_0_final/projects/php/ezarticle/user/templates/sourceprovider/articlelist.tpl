<!-- BEGIN header_item_tpl -->
<h1>SourceProvider</h1>
<hr noshade="noshade" size="4" />
<!-- END header_item_tpl -->

<p>
We will do our best to deliver high quality products and services. If you have any views on
how we can make your customer experience better, please contact us.
 </p>

<p>
You can start shopping by clicking on the "Products" link. 
</p>

<br />

<h1>News</h1>

<!-- BEGIN path_item_tpl -->

<!-- END path_item_tpl -->

<hr noshade="noshade" size="4" />


<!-- BEGIN category_list_tpl -->

<!-- BEGIN category_item_tpl -->

<!-- END category_item_tpl -->

<!-- END category_list_tpl -->


<!-- BEGIN article_list_tpl -->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<tr>
	<td>
	</td>
</tr>
<!-- BEGIN article_item_tpl -->
<tr>
	<td>
	<a href="/article/articleview/{article_id}/">
	<h2>{article_name}</h2>
	</a>
	

	<!-- BEGIN article_image_tpl -->
	    <table align="right"  width="{thumbnail_image_width}">
	        <tr>
			<td>
			<a href="/article/articleview/{article_id}/">
                        <img src="{thumbnail_image_uri}" border="0" width="{thumbnail_image_width}" height="{thumbnail_image_height}" />
			</a>
                        </td>
                </tr>
                <tr>
                         <td class="pictext">
                         {thumbnail_image_caption}
                         </td>
                </tr>
             </table>
        <!-- END article_image_tpl -->


	<p>
	{article_intro}
	</p>
	<img src="/ezarticle/user/{image_dir}/path-arrow.gif" height="10" width="15" border="0" alt="">
	<a class="path" href="/article/articleview/{article_id}/">
	{article_link_text}
	</a>
	<br />
	<br />
	<br />
	</td>
</tr>
<!-- END article_item_tpl -->
</table>
<!-- END article_list_tpl -->




