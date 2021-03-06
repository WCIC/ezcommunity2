<?php
// 
// $Id: dayview.php,v 1.38 2001/03/12 13:55:47 fh Exp $
//
// Adam Fallert <FallertA@umsystem.edu>
// Created on: <3-Oct-2001 14:36:00>
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

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );
include_once( "classes/ezlocale.php" );

include_once( "classes/ezdate.php" );
include_once( "classes/eztime.php" );

include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezuser/classes/ezpermission.php" );

include_once( "ezgroupeventcalendar/classes/ezgroupevent.php" );
include_once( "ezgroupeventcalendar/classes/ezgroupeventtype.php" );
include_once( "ezgroupeventcalendar/classes/ezgroupnoshow.php" );
include_once( "ezgroupeventcalendar/classes/ezgroupeditor.php" );

$ini =& $GLOBALS["GlobalSiteIni"];
$SiteDesign = $ini->read_var( "site", "SiteDesign" );

$Language = $ini->read_var( "eZGroupEventCalendarMain", "Language" );
$StartTimeStr = $ini->read_var( "eZGroupEventCalendarMain", "DayStartTime" );
$StopTimeStr = $ini->read_var( "eZGroupEventCalendarMain", "DayStopTime" );
$IntervalStr = $ini->read_var( "eZGroupEventCalendarMain", "DayInterval" );

$Locale = new eZLocale( $Language );

$user = eZUser::currentUser();
$session =& eZSession::globalSession();
$session->fetch();

$URL = split( "/", $REQUEST_URI );

if( count( $URL ) <= 5 )
{
	if ( is_numeric( $URL[6] ) )
		$groupID = $URL[6];
	else
		$groupID = 0;
}
else
{
	$readGroup = new eZUserGroup( $session->variable( "ShowOtherCalendarGroups" ) );
	$groupID = $readGroup->id();
}

if( !isset( $GetByGroup ) )
	$GetByGroup = $groupID;

if ( $GetByGroupID == false )
	$GetByGroupID = $groupID;

$type = new eZGroupEventType( $Type );

if ( ( $session->variable( "ShowOtherCalendarGroups" ) == false ) || ( isSet( $GetByGroup ) ) )
{
    $session->setVariable( "ShowOtherCalendarGroups", $GetByGroupID );
}
$tmpGroup = new eZUserGroup( $session->variable( "ShowOtherCalendarGroups" ) );


//Get the TYPE session data
if ( ( $session->variable( "ShowOtherCalendarTypes" ) == false ) || ( isSet( $GetByTypeID ) ) )
{
	if( !isSet( $GetByTypeID ) )
		$GetByTypeID = 0;

	$session->setVariable( "ShowOtherCalendarTypes", $GetByTypeID );
}
$type = new eZGroupEventType( $session->variable( "ShowOtherCalendarTypes" ) );

if( !isSet( $GetByTypeID ) )
	$GetByTypeID = $type->id();

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

$zMonth = addZero( $Month );
$zDay = addZero( $Day );
$isMyCalendar = ( $userID && $userID == $GetByUserID )? "-private" : "";
$t = new eZTemplate( "ezgroupeventcalendar/user/" . $ini->read_var( "eZGroupEventCalendarMain", "TemplateDir" ),
                     "ezgroupeventcalendar/user/intl", $Language, "dayview.php",
                     "default", "ezgroupeventcalendar" . "/user", "$Year-$zMonth-$zDay-$GetByUserID".$isMyCalendar );

$t->set_file( "day_view_page_tpl", "dayview.tpl" );

$user_groupID = "-1";
$groupsList   = "-1";

// groups not to include
$noShowGroup = new eZGroupNoShow();

// Determin the users group membership and access
if( $user )
{
	if( $user->hasRootAccess() == true || eZPermission::checkPermission( $user, "eZGroupEventCalendar", "WriteToRoot" ) )
	{
		$groups = new eZUserGroup();
		$groupsList = $groups->getAll( true );
		$rootAccess = true;
	}
	else
		$groupsList = $user->groups();


	$permission = new eZGroupEditor();

	// Determin if the user get the time stamps as "new event" links or not
	if( $permission->hasEditPermission( $user->id(), $tmpGroup->id() ) == true || $rootAccess == true )
		$editor = true;
	elseif( $permission->getByGroup( $tmpGroup->id() ) == false )
	{
		foreach( $groupsList as $groups )
		{
			if( $permission->groupHasEditor( $groups->id() ) == false && $noShowGroup->groupEntry( $groups->id() ) == false )
			{
				if( $tmpGroup->id() != 0 && $groups->id() == $tmpGroup->id() )
				{
					$editor = true;
					break;
				}
				elseif( $tmpGroup->id() == 0 )
				{
					$editor = true;
					break;				
				}
			}
		}
	}
}

//if ( $t->hasCache() && !isSet( $GetByGroup ) )
//{
   // print( "cached and $GetByGroup<br />" );
   // print( $t->cache() );
//}
//else
//{
//    print( "not cached<br />" );
    $t->setAllStrings();

    $t->set_block( "day_view_page_tpl", "group_item_tpl", "group_item" );
	$t->set_block( "day_view_page_tpl", "type_item_tpl", "type_item" );
	$t->set_block( "day_view_page_tpl", "group_print_tpl", "group_print" );
    $t->set_block( "day_view_page_tpl", "time_table_tpl", "time_table" );
	$t->set_block( "day_view_page_tpl", "valid_editor_tpl", "valid_editor" );
    $t->set_block( "time_table_tpl", "no_event_tpl", "no_event" );
    $t->set_block( "time_table_tpl", "private_event_tpl", "private_event" );
    $t->set_block( "time_table_tpl", "public_event_tpl", "public_event" );
    $t->set_block( "time_table_tpl", "new_event_link_tpl", "new_event_link" );
    $t->set_block( "time_table_tpl", "no_new_event_link_tpl", "no_new_event_link" );
    $t->set_block( "public_event_tpl", "delete_check_tpl", "delete_check" );
    $t->set_block( "public_event_tpl", "no_delete_check_tpl", "no_delete_check" );
    $t->set_block( "day_view_page_tpl", "week_tpl", "week" );
    $t->set_block( "week_tpl", "day_tpl", "day" );
    $t->set_block( "week_tpl", "empty_day_tpl", "empty_day" );

    $t->set_var( "sitedesign", $SiteDesign );
	$t->set_var( "month_number", $Month );
    $t->set_var( "year_number", $Year );
    $t->set_var( "day_number", $Day );
    $t->set_var( "long_date", $Locale->format( $date, false ) );

	if( $editor == true )
	{
		$t->parse( "new_event_link", "new_event_link_tpl" );
		$t->set_var( "no_new_event_link", "" );
	}
	else
	{
		$t->parse( "no_new_event_link", "no_new_event_link_tpl" );
		$t->set_var( "new_event_link", "" );
	}

	if ( $tmpGroup->id() != 0 )
	{
		$t->set_var( "group_print_name", $tmpGroup->name() );
		$t->set_var( "group_print_id", $tmpGroup->id() );
		$t->parse( "group_print", "group_print_tpl", true );
	}
	else
	{
		$t->set_var( "group_print_id", $tmpGroup->id() );
		$t->set_var( "group_print", "" );
	}

    $today = new eZDate();
    $tmpDate = new eZDate( $date->year(), $date->month(), $date->day() );
    $tmpGroupEvent = new eZGroupEvent();

	// Fetch all the events for day
	if( $tmpGroup->id() == 0 && $type->id() == 0) 
		$events =& $tmpGroupEvent->getAllByDate( $tmpDate, true );
	// Fetch all appointments by type
	elseif( $tmpGroup->id() == 0 && $type->id() != 0 )
		$events =& $tmpGroupEvent->getAllByType( $tmpDate, $type, true );
	// Fetch all appointments by Group and Type
	elseif( $tmpGroup->id() != 0 && $type->id() != 0 )
		$events =& $tmpGroupEvent->getByGroupType( $tmpDate, $tmpGroup, $type, true );
	// Fetch all appointments by Group
	else
		$events =& $tmpGroupEvent->getByDate( $tmpDate, $tmpGroup, true );


    // set start/stop and interval times
    $startTime = new eZTime();
    $stopTime = new eZTime();
    $interval = new eZTime();

    if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $StartTimeStr, $startArray ) )
    {
        $hour = $startArray[2];
        $startTime->setHour( $hour );

        $min = $startArray[3];
        $startTime->setMinute( $min );

        $startTime->setSecond( 0 );
    }

    if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $StopTimeStr, $stopArray ) )
    {
        $hour = $stopArray[2];
        $stopTime->setHour( $hour );

        $min = $stopArray[3];
        $stopTime->setMinute( $min );

        $stopTime->setSecond( 0 );
    }

    if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $IntervalStr, $intervalArray ) )
    {
        $hour = $intervalArray[2];
        $interval->setHour( $hour );

        $min = $intervalArray[3];
        $interval->setMinute( $min );

        $interval->setSecond( 0 );
    }

    // increase schedule span to fit early/late events
    $midNight = new eZTime();
    $midNight->setSecondsElapsed( 0 );
    $lastInterval = $midNight->subtract( $interval );
    $firstInterval = $midNight->add( $interval );

    foreach ( $events as $event )
    {
        $appStartTime =& $event->startTime();
        $appStopTime =& $event->stopTime();

        if ( $appStartTime->isGreater( $firstInterval ) )
            $startTime = $midNight;

        while ( $appStartTime->isGreater( $startTime ) )
        {
            $startTime = $startTime->subtract( $interval );
        }

        if ( $lastInterval->isGreater( $appStopTime ) )
            $stopTime = new eZTime( 23, 59 );

        while ( $stopTime->isGreater( $appStopTime ) )
        {
            $stopTime = $stopTime->add( $interval );
        }
    }


    // places events into columns, creates extra columns as necessary
    $numRows = 0;
    $numCols = 1;
    $tableCellsId = array();       // appointmend id for a cell
    $tableCellsRowSpan = array();  // rowspan for a cell
    $colTaken = array();           // number of non free rows in the current column, after the last event. 0 means col free.
    $emptyRows = array();          // number of empty rows in the current column, after the last event
    $eventDone = array();          // true when the event has been inserted into the table
    $tmpTime = new eZTime();
    $tmpTime->setSecondsElapsed( $startTime->secondsElapsed() );

    while ( $tmpTime->isGreater( $stopTime ) == true )
    {
        $numRows++;
        $tableCellsId[$numRows-1] = array();
        $tableCellsRowSpan[$numRows-1] = array();

        // marks cells as taken, -1
        for ( $col=0; $col<$numCols; $col++ )
        {
            if ( $colTaken[$col] > 0 )
            {
                $tableCellsId[$numRows-1][$col] = -1;
            }
        }

        foreach ( $events as $event )
        {
            // avoid wrapping around midnight
            $nextInterval = $tmpTime->add( $interval );
            if ( $nextInterval->isGreater( $tmpTime ) )
                $nextInterval = new eZTime( 23, 59 );

            // if this event should be inserted into the table now
            if ( $eventDone[$event->id()] == false &&
                 intersects( $event, $tmpTime, $nextInterval ) == true )
            {
                $foundFreeColumn = false;
                $col = 0;
                while ( $foundFreeColumn == false  )
                {
                    // the column is free, insert event here
                    if ( $tableCellsId[$numRows-1][$col] == 0 )
                    {
                        $tableCellsId[$numRows-1][$col] = $event->id();
                        $tableCellsRowSpan[$numRows-1][$col] = eventRowSpan( $event, $tmpTime, $interval );
                        $colTaken[$col] = $tableCellsRowSpan[$numRows-1][$col];
                        $eventDone[$event->id()] = true;
                        $foundFreeColumn = true;

                        // if we created a new column, mark leading empty spaces
                        if ( $col >= $numCols )
                            $emptyRows[$col] = $numRows - 1;

                        if ( $emptyRows[$col] > 0 )
                        {
                            $tableCellsId[ $numRows - 1 - $emptyRows[$col] ][$col] = -2;
                            $tableCellsRowSpan[ $numRows - 1 - $emptyRows[$col] ][$col] = $emptyRows[$col];
                            $emptyRows[$col] = 0;
                        }
                    }

                    // the column was not free, try the next one
                    $col++;
                    if ( $col > $numCols )
                        $numCols++;
                }
            }
        }

        // decrease/increase counts as we move down
        for ( $col=0; $col<$numCols; $col++ )
        {
            if ( $colTaken[$col] > 0 )
            {
                $colTaken[$col]--;
            }

            if ( $tableCellsId[$numRows-1][$col] == 0 )
            {
                $emptyRows[$col]++;
            }
        }

        if ( $tmpTime > $tmpTime->add( $interval ) )
            $tmpTime = new eZTime( 23, 59 );
        else
            $tmpTime = $tmpTime->add( $interval );
    }

    // mark remaining empty spaces as empty, -2
    for ( $col=0; $col<$numCols; $col++ )
    {
        if ( $emptyRows[$col] > 0 )
        {
            $tableCellsId[ $numRows - $emptyRows[$col] ][$col] = -2;
            $tableCellsRowSpan[ $numRows - $emptyRows[$col] ][$col] = $emptyRows[$col];
        }
    }


    // prints out the time table
    $emptyDone = false;
    $now = new eZTime();
    $nowSet = false;
    $row = 0;
    $tmpTime = new eZTime();
    $tmpTime->setSecondsElapsed( $startTime->secondsElapsed() );

    while ( $tmpTime->isGreater( $stopTime ) == true )
    {
        $t->set_var( "short_time", $Locale->format( $tmpTime, true ) );
        $t->set_var( "start_time", addZero( $tmpTime->hour() ) . addZero( $tmpTime->minute() ) );

        $drawnColumn = array();

        $t->set_var( "public_event", "" );
        $t->set_var( "private_event", "" );
        $t->set_var( "no_event", "" );
        $t->set_var( "delete_check", "" );

        for ( $col=0; $col<$numCols; $col++ )
        {
            $eventId      = $tableCellsId[$row][$col];
			$event_editor = false;
			$groupMember  = false;

            // an event
            if ( $eventId > 0 )
            {
                $event = new eZGroupEvent( $eventId );

				if( $rootAccess == true )
				{
					$groupMember = true;
				}
				elseif( $groupsList != "-1" && $rootAccess == false )
				{
					foreach ( $groupsList as $groups )
					{
						//If the user has a matching group set their group id to the matching group else there group id will be -1.
						if( $event->groupID() == $groups->id() )
						{
							$user_groupID = $groups->id();
							$groupMember = true;
							break;
						}
					}
				}

				// Determine editing permissions for mixed group dayviews
				if( $groupMember == true && $editor == true )
				{
					$event_editor = true;
				}


				$group = new eZUserGroup( $event->groupID() );
				$t->set_var( "event_groupName", $group->name() );

                // a private event
                if ( $event->isPrivate() == true && $groupMember != true && !eZPermission::checkPermission( $user, "eZGroupEventCalendar", "Read" ) )
                {
                    $t->set_var( "td_class", "bgdark" );
                    $t->set_var( "rowspan_value", $tableCellsRowSpan[$row][$col] );

                    $t->parse( "private_event", "private_event_tpl", true );
                }
                // a public event or a private event that a non group member can read
                elseif( $event->isPrivate() == false || ( $event->isPrivate() == true && eZPermission::checkPermission( $user, "eZGroupEventCalendar", "Read" ) ) )
                {
                    $t->set_var( "td_class", "bgdark" );
                    $t->set_var( "rowspan_value", $tableCellsRowSpan[$row][$col] );
                    $t->set_var( "event_id", $event->id() );
                    $t->set_var( "event_name", $event->name() );
                    $t->set_var( "event_description", $event->description() );
                    $t->set_var( "edit_button", "Edit" );

					$permission = new eZGroupEditor();
					if( $event_editor == true )
					{
						$t->parse( "delete_check", "delete_check_tpl" );
						$t->set_var( "no_delete_check", "" );
					}
					else
					{
						$t->parse( "no_delete_check", "no_delete_check_tpl" );
						$t->set_var( "delete_check", "" );
					}

                    $t->parse( "public_event", "public_event_tpl", true );
                }
            }

            // an empty space
            else if ( $eventId == -2 )
            {
                $t->set_var( "td_class", "bgweekend" );
                $t->set_var( "rowspan_value", $tableCellsRowSpan[$row][$col] );

                $t->parse( "no_event", "no_event_tpl", true );
            }
        }

        $t->set_var( "td_class", "" );

// Mark current time with bgcurrent. Does not currently go well together with caching.
//        if ( $date->equals( $today ) && $nowSet == false &&
//        $tmpTime->isGreater( $now, true ) && $now->isGreater( $tmpTime->add( $interval ) ) )
//        {
//            $t->set_var( "td_class", "bgcurrent" );
//            $nowSet = true;
//        }

        if ( $tmpTime > $tmpTime->add( $interval ) )
            $tmpTime = new eZTime( 23, 59 );
        else
            $tmpTime = $tmpTime->add( $interval );
        $row++;

		if ( ( $i %2 ) == 0 )
			$t->set_var( "td_class", "bglight" );
		else
			$t->set_var( "td_class", "bgdark" );

		$i++;

        $t->parse( "time_table", "time_table_tpl", true );
    }


    // group list
    $group = new eZUserGroup();
    $group_array =& $group->getAll();

    foreach( $group_array as $groupItem )
    {
		if( $noShowGroup->groupEntry( $groupItem->id() ) == false )
		{
			$t->set_var( "group_id", $groupItem->id() );
			$t->set_var( "group_name", $groupItem->Name() );

			if ( $tmpGroup->id() == $groupItem->id() )
			{
				$t->set_var( "selected_group_id", $tmpGroup->id() );
				$t->set_var( "group_is_selected", "selected" );
			}
			elseif ( $tmpGroup->id() == 0 )
				$t->set_var( "selected_group_id", 0 );
			else
				$t->set_var( "group_is_selected", "" );

			$t->parse( "group_item", "group_item_tpl", true );
		}
    }

	//type list
	$typeList = $type->getAll(); 

	foreach( $typeList as $type )
	{
		$t->set_var( "type_id", $type->id() );
		$t->set_var( "type_name", $type->name( true ) );

		if( $type->id() == $GetByTypeID )
		{
			$t->set_var( "selected_type_id", $type->id() );
			$t->set_var( "type_is_selected", "selected" );
		}
		else
			$t->set_var( "type_is_selected", "" );

		$t->parse( "type_item", "type_item_tpl", true );
	}

	if( $editor == true )
		$t->parse( "valid_editor", "valid_editor_tpl", true );
	else
		$t->set_var( "valid_editor", "" );

    // previous day link
    $date->setYear( $Year );
    $date->setMonth( $Month );

    $date->setDay( $Day - 1 );
    if ( $date->day() < 1 )
    {
        $date->setMonth( $Month - 1 );
        if ( $date->month() < 1 )
        {
            $date->setMonth( 12 );
            $date->setYear( $Year - 1 );
        }
        $date->setDay( $date->daysInMonth() );
    }
    $t->set_var( "pd_year_number", $date->year() );
    $t->set_var( "pd_month_number", $date->month() );
    $t->set_var( "pd_day_number", $date->day() );

    // next day link
    $date->setYear( $Year );
    $date->setMonth( $Month );

    $date->setDay( $Day + 1 );
    if ( $date->day() > $date->daysInMonth() )
    {
        $date->setDay( 1 );
        $date->setMonth( $Month + 1 );
        if ( $date->month() > 12 )
        {
            $date->setMonth( 1 );
            $date->setYear( $Year + 1 );
        }
    }
    $t->set_var( "nd_year_number", $date->year() );
    $t->set_var( "nd_month_number", $date->month() );
    $t->set_var( "nd_day_number", $date->day() );

    // previous month link
    $date->setYear( $Year );
    $date->setDay( $Day );

    $date->setMonth( $Month - 1 );
    if ( $date->month() < 1 )
    {
        $date->setMonth( 12 );
        $date->setYear( $Year - 1 );
    }
    if ( $date->day() > $date->daysInMonth() )
        $date->setDay( $date->daysInMonth() );
    $t->set_var( "pm_year_number", $date->year() );
    $t->set_var( "pm_month_number", $date->month() );
    $t->set_var( "pm_day_number", $date->day() );

    // next month link
    $date->setYear( $Year );
    $date->setDay( $Day );

    $date->setMonth( $Month + 1 );
    if ( $date->month() > 12 )
    {
        $date->setMonth( 1 );
        $date->setYear( $Year + 1 );
    }
    if ( $date->day() > $date->daysInMonth() )
        $date->setDay( $date->daysInMonth() );
    $t->set_var( "nm_year_number", $date->year() );
    $t->set_var( "nm_month_number", $date->month() );
    $t->set_var( "nm_day_number", $date->day() );


    // parse month table
    $date->setYear( $Year );
    $date->setMonth( $Month );
    $date->setDay( $Day );

    $t->set_var( "month_number", $date->month() );
    $t->set_var( "month_name", $Locale->monthName( $date->monthName(), false ) );

    $t->set_var( "week", "" );
    for ( $week=0; $week<6; $week++ )
    {
        $t->set_var( "day", "" );
        $t->set_var( "empty_day", "" );

        for ( $day=1; $day<=7; $day++ )
        {
            $date->setDay( 1 );
            $firstDay = $date->dayOfWeek( $Locale->mondayFirst() );

            $currentDay = $day + ( $week * 7 ) - $firstDay + 1;

            if ( ( ( $day + ( $week * 7 ) )  >= $firstDay ) &&
                 ( $currentDay <= $date->daysInMonth() ) )
            {
                $date->setDay( $currentDay );

                $t->set_var( "td_class", "bglight" );
//                if ( $date->equals( $today ) )
//                    $t->set_var( "td_class", "bgcurrent" );

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

        if ( $currentDay >= $date->daysInMonth() )
        {
            $week = 6;
        }
    }

    $t->storeCache( "output", "day_view_page_tpl", true );
//}


// returns the number of rows an event covers.
function eventRowSpan( &$event, &$startTime, &$interval )
{
    $ret = 0;
    $tmpTime = new eZTime();
    $tmpTime->setSecondsElapsed( $startTime->secondsElapsed() );
    $aStop =& $event->stopTime();

    while ( $tmpTime->isGreater( $aStop ) )
    {
        if ( $tmpTime > $tmpTime->add( $interval ) )
            $tmpTime = new eZTime( 23, 59 );
        else
            $tmpTime = $tmpTime->add( $interval );

        $ret++;
    }

    return $ret;
}


// checks if an event intersects with a given time interval
function intersects( &$app, &$startTime, &$stopTime )
{
    $ret = false;
    $appStartTime =& $app->startTime();
    $appStopTime =& $app->stopTime();

    // appstart is between start and stop
    if ( $startTime->isGreater( $appStartTime, true ) == true &&
    $appStartTime->isGreater( $stopTime ) == true )
    {
        $ret = true;
    }
    // appstop is between start and stop
    else if ( $startTime->isGreater( $appStopTime ) == true &&
    $appStopTime->isGreater( $stopTime, true ) == true )
    {
        $ret = true;
    }
    // appstart is before start, and appstop is after stop
    else if ( $appStartTime->isGreater( $startTime ) == true &&
    $stopTime->isGreater( $appStopTime ) == true )
    {
        $ret = true;
    }

    return $ret;
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
