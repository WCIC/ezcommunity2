<?php
// 
// $Id: dayview.php,v 1.13 2001/01/19 14:45:56 gl Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <08-Jan-2001 12:48:35 bf>
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

include_once( "classes/ezdate.php" );
include_once( "classes/eztime.php" );

include_once( "ezcalendar/classes/ezappointment.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZCalendarMain", "Language" );
$Locale = new eZLocale( $Language );

$t = new eZTemplate( "ezcalendar/user/" . $ini->read_var( "eZCalendarMain", "TemplateDir" ),
                     "ezcalendar/user/intl/", $Language, "dayview.php" );

$t->set_file( "day_view_page_tpl", "dayview.tpl" );

$t->setAllStrings();

$t->set_block( "day_view_page_tpl", "link_tpl", "link" );
$t->set_block( "day_view_page_tpl", "time_table_tpl", "time_table" );
$t->set_block( "time_table_tpl", "appointment_tpl", "appointment" );
$t->set_block( "appointment_tpl", "delete_check_tpl", "delete_check" );

$user = eZUser::currentUser();

$session = new eZSession();
$session->fetch();

if ( ( $session->variable( "ShowOtherCalenderUsers" ) == false ) )
{
    $session->setVariable( "ShowOtherCalenderUsers", $user->id() );
}

$tmpUser = new eZUser( $session->variable( "ShowOtherCalenderUsers" ) );

if ( $tmpUser->id() == $user->id() )
{
    $showPrivate == true;
}
else
{
    $showPrivate == false;
}

$date = new eZDate();

if ( $Year != "" && $Month != "" && $Day != "" )
{
    $date->setYear( $Year );
    $date->setMonth( $Month );
    $date->setDay( $Day );
}
else
{
    $Year = $date->year();
    $Month = $date->month();
    $Day = $date->day();
}

$session->setVariable( "Year", $Year );
$session->setVariable( "Month", $Month );
$session->setVariable( "Day", $Day );

$t->set_var( "month_number", $Month );
$t->set_var( "year_number", $Year );
$t->set_var( "day_number", $Day );
$t->set_var( "long_date", $Locale->format( $date, false ) );

$today = new eZDate();
$tmpDate = new eZDate( $date->year(), $date->month(), $date->day() );
$tmpAppointment = new eZAppointment();

// fetch the appointments for the selected day
$appointments = $tmpAppointment->getByDate( $tmpDate, $tmpUser, $showPrivate );

$appointmentColumns = array();
$rowSpanColumns = array();

$startTime = new eZTime( 8, 0, 0 );
$interval = new eZTime( 0, 30, 0 );
$stopTime = new eZTime( 18, 0, 0 );
$now = new eZTime();

// places appointments into columns, creates extra columns as necessary
foreach ( $appointments as $appointment )
{
    $aCount = 0;
    $foundFreeColumn = false;
    while ( $foundFreeColumn == false  )
    {
        if ( gettype( $appointmentColumns[$aCount] ) != "array" )
        {
            $appointmentColumns[$aCount] = array();
            $rowSpanColumns[$aCount] = 0;
        }

        if ( isFree( $appointmentColumns[$aCount], $appointment ) )
        {
            $foundFreeColumn = true;
            $appointmentColumns[$aCount][] = $appointment;
        }

        $aCount++;
    }
}

$numCols = count( $appointmentColumns );
$emptyDone = false;
$nowSet = false;

// print out the time table
while ( $startTime->isGreater( $stopTime ) == true )
{
    $t->set_var( "short_time", $Locale->format( $startTime, true ) );
    $t->set_var( "start_time", eZTime::addZero( $startTime->hour() ) . eZTime::addZero( $startTime->minute() ) );

    $drawnColumn = array();
    $t->set_var( "appointment", "" );

    for ( $i=0; $i<$numCols; $i++ )
    {
        $drawnColumn[$i] = false;

        if ( $rowSpanColumns[$i] <= 1 )
        {
            foreach ( $appointmentColumns[$i] as $app )
            {
                // draw an appointment cell
                if ( intersects( $app, $startTime, $startTime->add( $interval ) ) )
                {
                    $rowSpanColumns[$i] = appointmentRowSpan( $app, $startTime, $interval );
                    $t->set_var( "td_class", "bgdark" );
                    $t->set_var( "rowspan_value", $rowSpanColumns[$i] );
                    $t->set_var( "appointment_id", $app->id() );
                    $t->set_var( "appointment_name", $app->name() );
                    $t->set_var( "appointment_description", $app->description() );
                    $t->set_var( "edit_button", "Edit" );
                    $t->parse( "delete_check", "delete_check_tpl" );

                    $t->parse( "appointment", "appointment_tpl", true );
                    $drawnColumn[$i] = true;
                }
            }

            // draw an empty cell
            if ( $drawnColumn[$i] == false )
            {
                $rowSpanColumns[$i] = emptyRowSpan( $appointmentColumns[$i], $startTime, $stopTime, $interval );
                $t->set_var( "td_class", "bglight" );
                $t->set_var( "rowspan_value", $rowSpanColumns[$i] );
                $t->set_var( "appointment_id", "" );
                $t->set_var( "appointment_name", "" );
                $t->set_var( "appointment_description", "" );
                $t->set_var( "edit_button", "" );
                $t->set_var( "delete_check", "" );

                $t->parse( "appointment", "appointment_tpl", true );
            }
        }
        else
        {
            $rowSpanColumns[$i]--;
        }
    }

    // if there are no appointments this day, draw a big empty cell
    if ( $numCols == 0 && $emptyDone == false )
    {
        $emptyArray = array();
        $rowSpanColumns[$i] = emptyRowSpan( $emptyArray, $startTime, $stopTime, $interval );
        $t->set_var( "td_class", "bglight" );
        $t->set_var( "rowspan_value", $rowSpanColumns[$i] );
        $t->set_var( "appointment_id", "" );
        $t->set_var( "appointment_name", "" );
        $t->set_var( "appointment_description", "" );
        $t->set_var( "edit_button", "" );
        $t->set_var( "delete_check", "" );

        $t->parse( "appointment", "appointment_tpl", true );

        $emptyDone = true;
    }

    $startTime = $startTime->add( $interval );

    $t->set_var( "td_class", "" );
    if ( $date->equals( $today ) && $nowSet == false && $now->isGreater( $startTime ) )
    {
        $t->set_var( "td_class", "bgcurrent" );
        $nowSet = true;
    }

    $t->parse( "time_table", "time_table_tpl", true );
}


// returns the number of rows an appointment covers.
function appointmentRowSpan( &$appointment, &$startTime, &$interval )
{
    $ret = 0;
    $tmpTime = new eZTime( $startTime->hour(), $startTime->minute(), $startTime->second() );
    $aStop =& $appointment->stopTime();

    while ( $tmpTime->isGreater( $aStop ) )
    {
        $tmpTime = $tmpTime->add( $interval );
        $ret++;
    }

    return $ret;
}


// returns the number of empty rows before an appointment.
function emptyRowSpan( &$appointmentArray, &$startTime, &$stopTime, &$interval )
{
    $ret = 0;
    $tmpTime = new eZTime( $startTime->hour(), $startTime->minute(), $startTime->second() );
    $foundAppointment = false;

    while ( $foundAppointment == false && $tmpTime->isGreater( $stopTime ) == true )
    {
        $tmpTime = $tmpTime->add( $interval );
        $ret++;

        foreach ( $appointmentArray as $app )
        {
            if ( intersects( $app, $tmpTime, $tmpTime->add( $interval ) ) )
            {
                $foundAppointment = true;
            }
        }
    }

    return $ret;
}


// checks if the appointment crashes with other appointments in the array given
function isFree( &$appointmentArray, &$appointment )
{
    $ret = true;
    foreach( $appointmentArray as $app )
    {
        if ( intersects( $appointment, $app->startTime(), $app->stopTime() ) )
        {
            $ret = false;
        }
    }
    return $ret;
}


// checks if an appointment intersects with a time interval
function intersects( &$app, &$startTime, &$stopTime )
{
    $ret = false;
    $aStart =& $app->startTime();
    $aStop =& $app->stopTime();

    if ( $aStart->isGreater( $startTime ) == true &&
    $startTime->isGreater( $aStop ) == true )
    {
        $ret = true;
    }

    if ( $aStart->isGreater( $stopTime ) == true &&
    $stopTime->isGreater( $aStop ) == true )
    {
        $ret = true;
    }

    if ( $startTime->isGreater( $aStart ) == true &&
    $aStop->isGreater( $stopTime ) == true )
    {
        $ret = true;
    }

    // 
    if ( $aStart->isGreater( $startTime, true ) == true &&
    $stopTime->isGreater( $aStop, true ) == true )
    {
        $ret = true;
    }

    return $ret;
}


$date->setYear( $Year );
$date->setMonth( $Month );
$date->setDay( $Day );

// previous year link
$date->setYear( $Year - 1 );
if ( $date->month() == 2 && $date->daysInMonth() < $date->day() )
    $date->setDay( $date->daysInMonth() );

$t->set_var( "1_year_number", $date->year() );
$t->set_var( "1_month_number", $date->month() );
$t->set_var( "1_day_number", $date->day() );

$date->setYear( $Year );
$date->setMonth( $Month );
$date->setDay( $Day );

// next year link
$date->setYear( $Year + 1 );
if ( $date->month() == 2 && $date->daysInMonth() < $date->day() )
    $date->setDay( $date->daysInMonth() );

$t->set_var( "2_year_number", $date->year() );
$t->set_var( "2_month_number", $date->month() );
$t->set_var( "2_day_number", $date->day() );

$date->setYear( $Year );
$date->setMonth( $Month );
$date->setDay( $Day );

// previous month link
if ( $date->month() == 1 )
{
    $date->setMonth( 12 );
    $date->setYear( $Year - 1 );
}
else
    $date->setMonth( $date->month() - 1 );

if ( $date->daysInMonth() < $date->day() )
    $date->setDay( $date->daysInMonth() );

$t->set_var( "3_year_number", $date->year() );
$t->set_var( "3_month_number", $date->month() );
$t->set_var( "3_day_number", $date->day() );

$date->setYear( $Year );
$date->setMonth( $Month );
$date->setDay( $Day );

// next month link
if ( $date->month() == 12 )
{
    $date->setMonth( 1 );
    $date->setYear( $Year + 1 );
}
else
    $date->setMonth( $date->month() + 1 );

if ( $date->daysInMonth() < $date->day() )
    $date->setDay( $date->daysInMonth() );

$t->set_var( "4_year_number", $date->year() );
$t->set_var( "4_month_number", $date->month() );
$t->set_var( "4_day_number", $date->day() );


$t->pparse( "output", "day_view_page_tpl" );

?>
