<?
// 
// $Id: entryexitpages.php,v 1.1 2001/01/12 17:19:59 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <12-Jan-2001 16:31:41 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2000 eZ systems as
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

include_once( "classes/INIFile.php" );
$ini = new INIFile( "site.ini" );

$Language = $ini->read_var( "eZStatsMain", "Language" );

include_once( "classes/eztemplate.php" );
include_once( "classes/ezdate.php" );

include_once( "ezstats/classes/ezpageview.php" );
include_once( "ezstats/classes/ezpageviewquery.php" );

$t = new eZTemplate( "ezstats/admin/" . $ini->read_var( "eZStatsMain", "AdminTemplateDir" ),
                     "ezstats/admin/intl", $Language, "entryexitpages.php" );

$t->setAllStrings();

$t->set_file( array(
    "entry_exit_report_tpl" => "entryexitpages.tpl"
    ) );

$t->set_block( "entry_exit_report_tpl", "exit_page_tpl", "exit_page" );

$query = new eZPageViewQuery();

$exitPages =& $query->topExitPage();

$exitPageArray = array();

foreach ( $exitPages as $page )
{
    $exitPageArray[$page]["Count"] += 1;
    $exitPageArray[$page]["PageID"] = $page;
}

arsort( $exitPageArray );

$pageView = new eZPageView();
$ExitPageLimit = 10;

$i=0;
foreach ( $exitPageArray as $exitPage )
{
    $t->set_var( "page_uri", $pageView->requestPageByID( $exitPage["PageID"] ) );
    $t->set_var( "exit_count", $exitPage["Count"] );

    $t->parse( "exit_page", "exit_page_tpl", true );
    
    $i++;
    if ( $i>=$ExitPageLimit )
        break;
}

$t->set_var( "this_month", $Month );
$t->set_var( "this_year", $Year );

$t->pparse( "output", "entry_exit_report_tpl" );


?>
