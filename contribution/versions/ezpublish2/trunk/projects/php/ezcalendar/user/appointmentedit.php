<?
// 
// $Id: appointmentedit.php,v 1.27 2001/01/29 18:10:37 gl Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <03-Jan-2001 12:47:22 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2001 eZ systems as
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

include_once( "classes/ezhttptool.php" );

if ( isSet( $DeleteAppointments ) )
{
    $Action = "DeleteAppointment";
}

if ( isSet( $GoDay ) )
{
    include_once( "classes/ezdate.php" );

    $session =& eZSession::globalSession();
    $session->fetch();

    $year = $session->variable( "Year" );
    $month = $session->variable( "Month" );
    $day = $session->variable( "Day" );

    $date = new eZDate( $year, $month, $day );
    if ( $date->daysInMonth() < $day )
        $day = $date->daysInMonth();

    eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day" );
    exit();
}
else if ( isSet( $GoMonth ) )
{
    $session =& eZSession::globalSession();
    $session->fetch();

    $year = $session->variable( "Year" );
    $month = $session->variable( "Month" );

    eZHTTPTool::header( "Location: /calendar/monthview/$year/$month" );
    exit();
}
else if ( isSet( $GoYear ) )
{
    $session =& eZSession::globalSession();
    $session->fetch();

    $year = $session->variable( "Year" );

    eZHTTPTool::header( "Location: /calendar/yearview/$year" );
    exit();
}
else if ( isSet( $GoToday ) )
{
    $today = new eZDate();

    $year = addZero( $today->year() );
    $month = addZero( $today->month() );
    $day = addZero( $today->day() );

    eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day" );
    exit();
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezdatetime.php" );
include_once( "classes/ezdate.php" );
include_once( "classes/eztime.php" );

include_once( "ezcalendar/classes/ezappointment.php" );
include_once( "ezcalendar/classes/ezappointmenttype.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZCalendarMain", "Language" );
$StartTimeStr = $ini->read_var( "eZCalendarMain", "DayStartTime" );
$StopTimeStr = $ini->read_var( "eZCalendarMain", "DayStopTime" );

$Locale = new eZLocale( $Language );


$user = eZUser::currentUser();

if ( $user == false )
    $userID = false;
else
    $userID = $user->id();

if ( $Action == "New"  )
    $app = new eZAppointment();
else
    $app = new eZAppointment( $AppointmentID );

// only the appointment owner is allowed to edit or delete an appointment
if ( $Action == "Edit" && $app->userID() != $userID )
{
    $t = new eZTemplate( "ezcalendar/user/" . $ini->read_var( "eZCalendarMain", "TemplateDir" ),
                         "ezcalendar/user/intl/", $Language, "appointmentedit.php" );

    $t->set_file( "appointment_edit_tpl", "appointmentedit.tpl" );

    $t->setAllStrings();

    $t->set_block( "appointment_edit_tpl", "user_error_tpl", "user_error" );
    $t->set_block( "appointment_edit_tpl", "no_user_error_tpl", "no_user_error" );

    $t->set_var( "no_user_error", "" );
    $t->parse( "user_error", "user_error_tpl" );
    $t->pparse( "output", "appointment_edit_tpl" );

    exit();
}


if ( $Action == "DeleteAppointment" )
{
    if ( count ( $AppointmentArrayID ) != 0 )
    {
        $tmpAppointment = new eZAppointment( $AppointmentArrayID[0]);
        $datetime = $tmpAppointment->dateTime();

        if ( $tmpAppointment->userID() == $userID )
        {
            foreach( $AppointmentArrayID as $ID )
            {
                $appointment = new eZAppointment( $ID );
                $appointment->delete();
            }
        }
        // user not allowed to delete this appointment
        else
        {
            $t = new eZTemplate( "ezcalendar/user/" . $ini->read_var( "eZCalendarMain", "TemplateDir" ),
                                 "ezcalendar/user/intl/", $Language, "appointmentedit.php" );

            $t->set_file( "appointment_edit_tpl", "appointmentedit.tpl" );

            $t->setAllStrings();

            $t->set_block( "appointment_edit_tpl", "user_error_tpl", "user_error" );
            $t->set_block( "appointment_edit_tpl", "no_user_error_tpl", "no_user_error" );

            $t->set_var( "no_user_error", "" );
            $t->parse( "user_error", "user_error_tpl" );
            $t->pparse( "output", "appointment_edit_tpl" );

            exit();
        }
    }

    $year = addZero( $datetime->year() );
    $month = addZero( $datetime->month() );
    $day = addZero( $datetime->day() );
    deleteCache( "default", $Language, $year, $month, $day, $userID );

    eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day/" );
    exit();

}


// Allowed format for start and stop time:
// 14 14:30 14:0 1430
// the : can be replaced with any non number character
if ( $Action == "Insert" || $Action == "Update" )
{
    if ( isSet( $Cancel ) )
    {
        $app = new eZAppointment( $AppointmentID );
        $dt = $app->dateTime();
        $year = $dt->year();
        $month = $dt->month();
        $day = $dt->day();
        eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day" );
        exit();
    }

    $user = eZUser::currentUser();
    if ( $user )
    {
        $type = new eZAppointmentType( $TypeID );

        if ( $Action == "Update" )
            $appointment = new eZAppointment( $AppointmentID );
        else
            $appointment = new eZAppointment( );
        
        $appointment->setDescription( $Description );
        $appointment->setType( $type );
        $appointment->setOwner( $user );
        $appointment->setPriority( $Priority );

        if ( $IsPrivate == "on" )
            $appointment->setIsPrivate( true );
        else
            $appointment->setIsPrivate( false );

        if ( $Name != "" )
        {
            $appointment->setName( $Name );
        }
        else
        {
            $TitleError = true;
        }

        // start/stop time for the day
        $dayStartTime = new eZTime();
        $dayStopTime = new eZTime();

        if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $StartTimeStr, $dayStartArray ) )
        {
            $hour = $dayStartArray[2];
            $dayStartTime->setHour( $hour );

            $min = $dayStartArray[3];
            $dayStartTime->setMinute( $min );

            $dayStartTime->setSecond( 0 );
        }

        if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $StopTimeStr, $dayStopArray ) )
        {
            $hour = $dayStopArray[2];
            $dayStopTime->setHour( $hour );

            $min = $dayStopArray[3];
            $dayStopTime->setMinute( $min );

            $dayStopTime->setSecond( 0 );
        }

        // start/stop time for the appointment
        $startTime = new eZTime();
        $stopTime = new eZTime();

        $startTime->setSecond( 0 );
        $stopTime->setSecond( 0 );

        if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $Start, $startArray ) )
        {
            $hour = $startArray[2];
            settype( $hour, "integer" );

            $startTime->setHour( $hour );

            $min = $startArray[3];
            settype( $min, "integer" );
// This causes trouble when you want e.g. 14:03
//            if ( $min < 6 )
//                $min = $min*10;

            $startTime->setMinute( $min );

            if ( $startTime->isGreater( $dayStartTime ) )
                $StartTimeError = true;
        }
        else
        {
            $StartTimeError = true;
        }

        if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $Stop, $stopArray ) )
        {
            $hour = $stopArray[2];
            settype( $hour, "integer" );

            $stopTime->setHour( $hour );

            $min = $stopArray[3];
            settype( $min, "integer" );
// This causes trouble when you want e.g. 14:03
//            if ( $min < 6 )
//                $min = $min*10;

            $stopTime->setMinute( $min );

            if ( $dayStopTime->isGreater( $stopTime ) )
                $StopTimeError = true;
        }
        else
        {
            $StopTimeError = true;
        }

        if ( $stopTime->isGreater( $startTime, true ) )
            $StopTimeError = true;

        $datetime = new eZDateTime( $Year, $Month, $Day,
        $startTime->hour(), $startTime->minute(), 0 );

        $appointment->setDateTime( $datetime );

        $duration = new eZTime( $stopTime->hour() - $startTime->hour(),
        $stopTime->minute() - $startTime->minute() );

        $appointment->setDuration( $duration );

        if ( $TitleError == false && $StartTimeError == false && $StopTimeError == false )
        {
            $appointment->store();

            $year = addZero( $datetime->year() );
            $month = addZero( $datetime->month() );
            $day = addZero( $datetime->day() );
            deleteCache( "default", $Language, $year, $month, $day, $userID );

            eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day/" );
            exit();
        }
    }
}


$t = new eZTemplate( "ezcalendar/user/" . $ini->read_var( "eZCalendarMain", "TemplateDir" ),
                     "ezcalendar/user/intl/", $Language, "appointmentedit.php" );

$t->set_file( "appointment_edit_tpl", "appointmentedit.tpl" );

$t->setAllStrings();

$t->set_block( "appointment_edit_tpl", "user_error_tpl", "user_error" );
$t->set_block( "appointment_edit_tpl", "no_user_error_tpl", "no_user_error" );

$t->set_block( "no_user_error_tpl", "title_error_tpl", "title_error" );
$t->set_block( "no_user_error_tpl", "start_time_error_tpl", "start_time_error" );
$t->set_block( "no_user_error_tpl", "stop_time_error_tpl", "stop_time_error" );
$t->set_block( "no_user_error_tpl", "value_tpl", "value" );
$t->set_block( "no_user_error_tpl", "month_tpl", "month" );
$t->set_block( "no_user_error_tpl", "day_tpl", "day" );

$t->set_var( "user_error", "" );


if ( $Action == "Update" )
{
    $t->set_var( "name_value", $Name );
    $t->set_var( "description_value", $Description );
    $t->set_var( "start_value", $Start );
    $t->set_var( "stop_value", $Stop );

    $typeID = $TypeID;

    $t->set_var( "0_selected", "" );
    $t->set_var( "1_selected", "" );
    $t->set_var( "2_selected", "" );

    if ( $Priority == 0 )
        $t->set_var( "0_selected", "selected" );
    else if ( $Priority == 1 )
        $t->set_var( "1_selected", "selected" );
    else if ( $Priority == 2 )
        $t->set_var( "2_selected", "selected" );

    if ( $IsPrivate == "on" )
        $t->set_var( "is_private", "checked" );
    else
        $t->set_var( "is_private", "" );

    $t->set_var( "action_value", $Action );
    $t->set_var( "appointment_id", $AppointmentID );
}


if ( $Action == "Edit" )
{
    $appointment = new eZAppointment( $AppointmentID );
    $t->set_var( "name_value", $appointment->name() );
    $t->set_var( "appointment_id", $appointment->id() );
    $t->set_var( "description_value", $appointment->description() );

    $type =& $appointment->type();
    $typeID = $type->id();

    $startTime =& $appointment->startTime();
    $startHour = ( addZero( $startTime->hour() ) );
    $startMinute = ( addZero( $startTime->minute() ) );
    $t->set_var( "start_value", $startHour . $startMinute );

    $stopTime =& $appointment->stopTime();
    $stopHour = ( addZero( $stopTime->hour() ) );
    $stopMinute = ( addZero( $stopTime->minute() ) );
    $t->set_var( "stop_value", $stopHour . $stopMinute );

    $t->set_var( "0_selected", "" );
    $t->set_var( "1_selected", "" );
    $t->set_var( "2_selected", "" );

    if ( $appointment->priority() == 0 )
        $t->set_var( "0_selected", "selected" );
    else if ( $appointment->priority() == 1 )
        $t->set_var( "1_selected", "selected" );
    else if ( $appointment->priority() == 2 )
        $t->set_var( "2_selected", "selected" );

    $dt =& $appointment->dateTime();

    $t->set_var( "year_value", $dt->year() );

    if ( $appointment->isPrivate() )
        $t->set_var( "is_private", "checked" );
    else
        $t->set_var( "is_private", "" );

    $t->set_var( "action_value", "update" );
}


// print out error messages
if ( $TitleError == true )
    $t->parse( "title_error", "title_error_tpl" );
else
    $t->set_var( "title_error", "" );

if ( $StartTimeError == true )
    $t->parse( "start_time_error", "start_time_error_tpl" );
else
    $t->set_var( "start_time_error", "" );

if ( $StopTimeError == true )
    $t->parse( "stop_time_error", "stop_time_error_tpl" );
else
    $t->set_var( "stop_time_error", "" );


$today = new eZDate();
$tmpdate = new eZDate( $Year, $Month, $Day );

if ( $Action == "New" )
{
    $t->set_var( "action_value", "Insert" );
    $t->set_var( "name_value", "" );
    $t->set_var( "description_value", "" );
    $t->set_var( "is_private", "" );
    $t->set_var( "start_value", "" );
    $t->set_var( "stop_value", "" );

    if ( $Year != 0 )
        $year = $Year;
    else
        $year = $today->year();

    if ( $Month != 0 )
        $month = $Month;
    else
        $month = $today->month();

    if ( $Day != 0 )
        $day = $Day;
    else
        $day = $today->day();

    $tmpdate = new eZDate( $year, $month, $day );

    if ( $StartTime != 0 )
        $t->set_var( "start_value", $StartTime );
}

// print the appointment types
$type = new eZAppointmentType();
$typeList =& $type->getTree();

foreach ( $typeList as $type )
{
    if ( $type[1] > 0 )
        $t->set_var( "option_level", str_repeat( "&nbsp;", $catItem[1] ) );
    else
        $t->set_var( "option_level", "" );

    if ( $typeID )
    {
        if ( $typeID == $type[0]->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }
    
    $t->set_var( "option_name", $type[0]->name() );
    $t->set_var( "option_value", $type[0]->id() );
    
    $t->parse( "value", "value_tpl", true );
}

// set day combobox
$daysInMonth = $tmpdate->daysInMonth();
for ( $i=1; $i<=$daysInMonth; $i++ )
{
    if ( $tmpdate->day() == $i )
        $t->set_var( "selected", "selected" );
    else
        $t->set_var( "selected", "" );

    if ( $Action == "Edit" )
    {
        if ( $dt->day() == $i )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }
    
    $t->set_var( "day_id", $i );
    $t->set_var( "day_name", $i );

    $t->parse( "day", "day_tpl", true );
}

// set month combobox
$month = $tmpdate->month();
for ( $i=1; $i<13; $i++ )
{
    if ( $month == $i )   // don't use $tmpdate->month() since it gets changed
        $t->set_var( "selected", "selected" );
    else
        $t->set_var( "selected", "" );

    if ( $Action == "Edit" )
    {
        if ( $dt->month() == $i )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }

    $tmpdate->setMonth( $i );
    $t->set_var( "month_id", $i );
    $t->set_var( "month_name", $Locale->monthName( $tmpdate->monthName() ) );

    $t->parse( "month", "month_tpl", true );
}

if ( $Action != "Edit" )
    $t->set_var( "year_value", $tmpdate->year() );

$t->parse( "no_user_error", "no_user_error_tpl" );
$t->pparse( "output", "appointment_edit_tpl" );


// deletes the dayview cache file for a given day
function deleteCache( $siteStyle, $language, $year, $month, $day, $userID )
{
    unlink( "ezcalendar/user/cache/dayview.tpl-$siteStyle-$language-$year-$month-$day-$userID.cache" );
    unlink( "ezcalendar/user/cache/monthview.tpl-$siteStyle-$language-$year-$month-$userID.cache" );
}

//Adds a "0" in front of the value if it's below 10.
function addZero( $value )
{
    settype( $value, "integer" );
    $ret = $value;
    if ( $ret < 10 )
    {
        $ret = "0". $ret;
    }
    return $ret;
}


?>
