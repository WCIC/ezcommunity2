<?php
// 
// $Id: yearview.php,v 1.3 2001/01/16 17:12:02 gl Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <27-Dec-2000 11:29:22 bf>
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
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );
include_once( "classes/ezlocale.php" );

include_once( "classes/ezdatetime.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZCalendarMain", "Language" );
$locale = new eZLocale( $Language );

$t = new eZTemplate( "ezcalendar/user/" . $ini->read_var( "eZCalendarMain", "TemplateDir" ),
                     "ezcalendar/user/intl/", $Language, "yearview.php" );

$t->set_file( "month_list_page_tpl", "yearview.tpl" );

$t->setAllStrings();

$t->set_block( "month_list_page_tpl", "month_tpl", "month" );
$t->set_block( "month_tpl", "week_tpl", "week" );
$t->set_block( "week_tpl", "day_tpl", "day" );
$t->set_block( "week_tpl", "empty_day_tpl", "empty_day" );

$datetime = new eZDateTime( );

if ( $Year != "" )
{
    $datetime->setYear( $Year );
}
else
{
    $Year = $datetime->year();
}

$t->set_var( "year_number", $Year );
$t->set_var( "prev_year_number", $Year - 1 );
$t->set_var( "next_year_number", $Year + 1 );

$i=0;
for ( $month=1; $month<13; $month++ )
{
    if ( ( $i % 3 ) == 0 )
    {
        $t->set_var( "begin_tr", "<tr>" );
        $t->set_var( "end_tr", "" );        
    }
    else if ( ( $i % 3 ) == 2 )
    {
        $t->set_var( "begin_tr", "" );
        $t->set_var( "end_tr", "</tr>" );
    }
    else
    {
        $t->set_var( "begin_tr", "" );
        $t->set_var( "end_tr", "" );        
    }
    
    $datetime->setMonth( $month );
    $t->set_var( "month_number", $month );
    $t->set_var( "month_name", $locale->monthName( $datetime->monthName(), false ) );

    $t->set_var( "week", "" );
    for ( $week=0; $week<6; $week++ )
    {
        $t->set_var( "day", "" );
        $t->set_var( "empty_day", "" );
        
        for ( $day=1; $day<=7; $day++ )
        {
                
            $datetime->setDay( 1 );
            $firstDay = $datetime->dayOfWeek();

            $currentDay = $day + ( $week * 7 ) - $firstDay + 1;

            if ( ( ( $day + ( $week * 7 ) )  >= $firstDay ) &&
                 ( $currentDay <= $datetime->daysInMonth() ) )
            {
                $datetime->setDay( $currentDay );

                $t->set_var( "td_class", "bglight" );
                $t->set_var( "day_number", $currentDay );
                $t->parse( "day", "day_tpl", true );
            }
            else
            {
                $t->set_var( "td_class", "bglight" );                
                $t->parse( "day", "empty_day_tpl", true );
            }
        }
        $t->parse( "week", "week_tpl", true );
    }
    $t->parse( "month", "month_tpl", true );

    $i++;
}

$t->pparse( "output", "month_list_page_tpl" );


?>
