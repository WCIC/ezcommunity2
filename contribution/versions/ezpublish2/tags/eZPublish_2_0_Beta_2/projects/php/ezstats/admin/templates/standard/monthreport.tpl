<h1>{intl-month_report} - 
<!-- BEGIN month_january_tpl -->
{intl-january_month}
<!-- END month_january_tpl -->
<!-- BEGIN month_february_tpl -->
{intl-february_month}
<!-- END month_february_tpl -->
<!-- BEGIN month_march_tpl -->
{intl-march_month}
<!-- END month_march_tpl -->
<!-- BEGIN month_april_tpl -->
{intl-april_month}
<!-- END month_april_tpl -->
<!-- BEGIN month_may_tpl -->
{intl-may_month}
<!-- END month_may_tpl -->
<!-- BEGIN month_june_tpl -->
{intl-june_month}
<!-- END month_june_tpl -->
<!-- BEGIN month_july_tpl -->
{intl-july_month}
<!-- END month_july_tpl -->
<!-- BEGIN month_august_tpl -->
{intl-august_month}
<!-- END month_august_tpl -->
<!-- BEGIN month_september_tpl -->
{intl-september_month}
<!-- END month_september_tpl -->
<!-- BEGIN month_october_tpl -->
{intl-october_month}
<!-- END month_october_tpl -->
<!-- BEGIN month_november_tpl -->
{intl-november_month}
<!-- END month_november_tpl -->
<!-- BEGIN month_december_tpl -->
{intl-december_month}
<!-- END month_december_tpl -->
 {this_year}</h1>

<hr noshade size="4" />

<!-- BEGIN result_list_tpl -->


<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
	<td>
	<b>{intl-total_page_views}</b>: {total_page_views}
	</td>
</tr>
<tr>
	<td>
	<b>{intl-pages_pr_day}</b>: {pages_pr_day}
	</td>
</tr>
<tr>
{month}
</tr>
<!-- BEGIN day_tpl -->
<tr>
	<td>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="50%">
		<!-- BEGIN day_link_tpl -->
		<b>{intl-day}:</b> <a href="/stats/dayreport/{this_year}/{this_month}/{current_day}">{current_day}</a>
		<!-- END day_link_tpl -->
		<!-- BEGIN no_day_link_tpl -->
		<b>{intl-day}:</b> {current_day}
		<!-- END no_day_link_tpl -->
		</td>
		<td align="right">
		{page_view_count} {intl-pages} ({percent_count}%)
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<!-- BEGIN percent_marker_tpl -->
			<td width="{page_view_percent}%" bgcolor="#ffee00">
			<img src="/images/1x1.gif" width="1" height="10" border="0"></td>
			<!-- END percent_marker_tpl -->
			<!-- BEGIN no_percent_marker_tpl -->
			<td width="{page_view_percent}%" bgcolor="#ffee00">
			</td>
			<!-- END no_percent_marker_tpl -->
			<td width="{page_view_inverted_percent}%"  bgcolor="#eeeeee">
			<img src="/images/1x1.gif" width="1" height="10" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
<tr>
<!-- END day_tpl -->

<tr>
	<td>
<!-- BEGIN month_tpl -->
<table>
<tr>
	<!-- BEGIN month_previous_tpl -->
	<td>
	<a href="/stats/monthreport/{previous_year}/{previous_month}">{intl-previous}</a>
	</td>
	<!-- END month_previous_tpl -->
	
	<!-- BEGIN month_previous_inactive_tpl -->
	<td>
	{intl-previous}
	</td>
	<!-- END month_previous_inactive_tpl -->

	<!-- BEGIN month_next_tpl -->
	<td>
	<a href="/stats/monthreport/{next_year}/{next_month}">{intl-next}</a>
	</td>
	<!-- END month_next_tpl -->

	<!-- BEGIN month_next_inactive_tpl -->
	<td>
	{intl-next}
	</td>
	<!-- END month_next_inactive_tpl -->

	<td>
	<a href="/stats/yearreport/{this_year}">{intl-year_report}</a>
	</td>
</tr>
</table>
<!-- END month_tpl -->
	</td>
</tr>

</table>

<!-- END result_list_tpl -->
