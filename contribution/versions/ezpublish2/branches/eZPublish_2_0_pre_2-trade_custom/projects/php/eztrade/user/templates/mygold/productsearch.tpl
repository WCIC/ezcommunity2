<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    	<td align="left" valign="bottom">
     	   <h1>{intl-head_line}</h1>
     	</td>
   		<td align="right">
			<form action="/trade/search/" method="post">
	      		<input type="text" name="Query" />
	      		<input type="submit" name="search" value="{intl-search_button}" />
	        </form>
	    </td>
	</tr>
</table>
<hr noshade size="1" />
<h2>Ihre Suche nach "{query_string}" ergab:</h2>
<br />
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<!-- BEGIN product_tpl -->
	<tr>
		<td>
			<a href="/trade/productview/{product_id}/{category_id}/"><h2>{product_name}</h2></a><br>
		    <!-- BEGIN image_tpl -->
    		<table align="right">
		    	<tr>
        			<td>
			        	<img src="{thumbnail_image_uri}" border="0" width="{thumbnail_image_width}" height="{thumbnail_image_height}" />
			        </td>
			    </tr>
			    <tr>
        			<td>
		        		{thumbnail_image_caption}
        			</td>
			    </tr>
    		</table>
		    <!-- END image_tpl -->
	    	{product_intro_text}
			<br />
			<!-- BEGIN price_tpl -->
			<p class "pris">
			{product_price}
			</p>
			<!-- END price_tpl -->
		</td>
	</tr>
	<tr>
		<td><hr noshade size="1" /></td>
	</tr>
	<!-- END product_tpl -->
	<tr>
		<td>
			<!-- BEGIN previous_tpl -->
			<a href="/trade/search/?Offset={prev_offset}&URLQueryString={url_query_string}">{intl-prev}</a>
			<!-- END previous_tpl -->
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<!-- BEGIN next_tpl -->
			<a href="/trade/search/?Offset={next_offset}&URLQueryString={url_query_string}">{intl-next}</a>
			<!-- END next_tpl -->
		</td>
	</tr>
</table>