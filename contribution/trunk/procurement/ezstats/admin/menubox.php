<?php
// 
// $Id: menubox.php,v 1.9 2001/07/20 11:28:54 jakobn Exp $
//
// Created on: <05-Jan-2001 11:18:10 bf>
//
// This source file is part of eZ publish, publishing software.
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//

// Supply $menuItems to get a menubox

$menuItems = array(
    array( "/stats/pageviewlist/last/500", "{intl-last_page_views}" ),
    array( "/stats/visitorlist/top/100", "{intl-top_visitors}" ),
    array( "/stats/requestpagelist/top/100/", "{intl-request_page_list}" ),
    array( "/stats/procurement/report/", "{intl-procurement_report}" ),

    array( "/stats/refererlist/top/100/", "{intl-referer_list}" ),
    array( "/stats/browserlist/top/50/", "{intl-browser_list}" ),
    array( "/stats/overview/", "{intl-overview}" ),

//    array( "/stats/yearreport/", "{intl-year_report}" ),
//    array( "/stats/monthreport/", "{intl-month_report}" ),
//    array( "/stats/dayreport/", "{intl-day_report}" )
    );
?>
