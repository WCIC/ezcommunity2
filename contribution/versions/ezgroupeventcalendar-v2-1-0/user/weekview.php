<?php
// 
// $Id: weekview.php
//
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

include_once( "classes/ezdatetime.php" );
include_once( "classes/ezdate.php" );

include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezuser/classes/ezpermission.php" );

include_once( "ezgroupeventcalendar/classes/ezgroupevent.php" );
include_once( "ezgroupeventcalendar/classes/ezgroupeventtype.php" );
include_once( "ezgroupeventcalendar/classes/ezgroupnoshow.php" );
include_once( "ezgroupeventcalendar/classes/ezgroupeditor.php" );

function shortenText($text, $chars=25) {
  //$text = $text."||";
  // print(strlen($text));
  $text = substr($text,0,$chars);
  //$text = substr($text,0,strrpos($text,'||'));
  //  die($text);
  $text .= "...";

  return $text;
}

$ini =& $GLOBALS["GlobalSiteIni"];

$SiteDesign = $ini->read_var( "site", "SiteDesign" );
$Language   = $ini->read_var( "eZGroupEventCalendarMain", "Language" );
$Sitedesign = $ini->read_var( "site", "SiteDesign" );
$TruncateTitle = $ini->read_var( "eZGroupEventCalendarMain", "TruncateTitle" );
$Locale     = new eZLocale( $Language );

$user = eZUser::currentUser();
$session =& eZSession::globalSession();
$session->fetch();

$URL = split( "/", $REQUEST_URI );

if( count( $URL ) <= 5 )
{
	if ( is_numeric( $URL[3] ) )
		$groupID = $URL[3];
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


if ( ( $session->variable( "ShowOtherCalendarGroups" ) == false ) || ( isSet( $GetByGroup ) ) )
{
    $session->setVariable( "ShowOtherCalendarGroups", $GetByGroupID );
}

$eventGroup = new eZUserGroup( $session->variable( "ShowOtherCalendarGroups" ) );

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

$Week = date("W",$date->timestamp());

$session->setVariable( "Year", $Year );
$session->setVariable( "Month", $Month );
$session->setVariable( "Day", $Day );
$session->setVariable ( "Week", $Week );

$zMonth = addZero($Month);
$isMyCalendar = ( $groupID && $groupID == $GetByGroupID )? "-private" :"";



$t = new eZTemplate( "ezgroupeventcalendar/user/" . $ini->read_var( "eZGroupEventCalendarMain", "TemplateDir" ),
                     "ezgroupeventcalendar/user/intl", $Language, "weekview.php",
                     "default", "ezgroupeventcalendar" . "/user", "$Year-$zMonth-$Day-$GetByGroupID" . $isMyCalendar );

$t->set_file( "week_view_page_tpl", "weekview.tpl" );
$nextDate = $date;
$prevDate = $date;
    // next previous values.
$nextDate->move(0, 0, 7);
$prevDate->move(0, 0, -7);
$t->set_var( "next_year_number", $nextDate->year() );
$t->set_var( "next_day_number", $nextDate->day() );
$t->set_var( "next_month_number",$nextDate->month() );
$t->set_var( "prev_year_number", $prevDate->year() );
$t->set_var( "prev_day_number", $prevDate->day() );
$t->set_var( "prev_month_number", $prevDate->month() );

// group not to include
$noShowGroup = new eZGroupNoShow();

$user_groupID = "-1";
$groupsList   = "-1";

// retreive all the groups the user is a member of
if( $user )
{
	// if the user has root access, then the user has access to all groups
	if( $user->hasRootAccess() == true || eZPermission::checkPermission( $user, "eZGroupEventCalendar", "WriteToRoot" ) )
	{
		$groups     = new eZUserGroup();
		$groupsList = $groups->getAll( true );
		$rootAccess = true;
	}
	else
		$groupsList = $user->groups();
}

$permission = new eZGroupEditor();

// Determin if the user get the time stamps as "new event" links or not
if( $user )
	if( $permission->hasEditPermission( $user->id(), $eventGroup->id() ) == true || $rootAccess == true )
		$new_event_link = true;
	elseif( $permission->getByGroup( $eventGroup->id() ) == false )
	{
		foreach( $groupsList as $groups )
		{
			if( $permission->groupHasEditor( $groups->id() ) == false && $noShowGroup->groupEntry( $groups->id() ) == false )
			{
				if( $eventGroup->id() != 0 && $groups->id() == $eventGroup->id() )
				{
					$new_event_link = true;
					break;
				}
				elseif( $eventGroup->id() == 0 )
				{
					$new_event_link = true;
					break;
				}
			}
		}
	}

//if ( $t->hasCache() )
//{
//    print( "cached<br />" );
//    print( $t->cache() );
//}
//else
//{
//    print( "not cached<br />" );
    $t->setAllStrings();

    $t->set_block( "week_view_page_tpl", "group_item_tpl", "group_item" );
	$t->set_block( "week_view_page_tpl", "type_item_tpl", "type_item" );
	$t->set_block( "week_view_page_tpl", "group_print_tpl", "group_print" );
    $t->set_block( "week_view_page_tpl", "week_tpl", "week" );
    $t->set_block( "week_view_page_tpl", "new_event_form_tpl", "new_event_form" );
    $t->set_block( "week_tpl", "week_day_tpl", "week_day" );
    $t->set_block( "week_tpl", "day_tpl", "day" );

    $t->set_block( "day_tpl", "day_link_tpl", "day_link" );
    $t->set_block( "day_tpl", "day_no_link_tpl", "day_no_link" );

    $t->set_block( "day_tpl", "public_appointment_tpl", "public_appointment" );
    $t->set_block( "day_tpl", "private_appointment_tpl", "private_appointment" );
    $t->set_block( "day_tpl", "new_event_link_tpl", "new_event_link" );
    $t->set_block( "day_tpl", "no_new_event_link_tpl", "no_new_event_link" );

    $t->set_var( "sitedesign", $SiteDesign );
	$t->set_var( "month_name", $Locale->monthName( $date->monthName(), false ) );
    $t->set_var( "month_number", $Month );
    $t->set_var( "current_year_number", $Year );
    $t->set_var( "week", "" );
	$t->set_var( "sitedesign", $Sitedesign );
    $t->set_var("date_month", $date->month());
    $t->set_var("date_year", $date->year());
    $t->set_var("date_day", $date->day());
    $t->set_var( "week_num", $Week );


	if( $new_event_link == true )
	{
		$t->parse( "new_event_link", "new_event_link_tpl" );
		$t->parse( "new_event_form", "new_event_form_tpl" );
		$t->set_var( "no_new_event_link", "" );
	}
	else
	{
		$t->parse( "no_new_event_link", "no_new_event_link_tpl" );
		$t->set_var( "new_event_form", "" );
		$t->set_var( "new_event_link", "" );
	}


	if ( $eventGroup->id() != 0 )
	{
		$t->set_var( "group_print_name", $eventGroup->name() );
		$t->set_var( "group_print_id", $eventGroup->id() );
		$t->parse( "group_print", "group_print_tpl", true );
	}
	else
	{
		$t->set_var( "group_print", "" );
	}

    // Draw the week day header.
    $headerDate = new eZDate();
    $headerDate->setYear( 2001 );
    if ( $Locale->mondayFirst() )
    {
        // January 2001 starts on a Monday
        $headerDate->setMonth( 1 );
    }
    else
    {
        // April 2001 starts on a Sunday
        $headerDate->setMonth( 4 );
    }

    for ( $week_day=1; $week_day<=7; $week_day++ )
    {
        $headerDate->setDay( $week_day );
        $t->set_var( "week_day_name", $Locale->dayName( $headerDate->dayName( $Locale->mondayFirst() ), false ) );

        $t->parse( "week_day", "week_day_tpl", true );
    }

    $today = new eZDate();
    $tmpDate = new eZDate();
    $tmpGroupEvent = new eZGroupEvent();

 /*   for ( $week=0; $week<7; $week++ )
    {
        $t->set_var( "day", "" );
    */
 /*       if ( ( ( $week * 7 ) - $firstDay + 1 ) < ( $date->daysInMonth()  ) )
        {
            $date->setDay( 1 );
            $firstDay = $date->dayOfWeek( $Locale->mondayFirst() );
   */
          $tmpMove = moveToWeekBegin($date, $Locale->mondayFirst());
            $date->move(0,0, -$tmpMove);
            for ( $day=1; $day<=7; $day++ )
            {

                    // fetch the appointments for today
                    $tmpDate->setYear( $date->year() );
                    $tmpDate->setMonth( $date->month() );
                    $tmpDate->setDay( $date->day() );

					// Fetch all the appointments
					if( $eventGroup->id() == 0 && $type->id() == 0)
						$appointments =& $tmpGroupEvent->getAllByDate( $tmpDate, true );
					// Fetch all appointments by type
					elseif( $eventGroup->id() == 0 && $type->id() != 0 )
						$appointments =& $tmpGroupEvent->getAllByType( $tmpDate, $type, true );
					// Fetch all appointments by Group and Type
					elseif( $eventGroup->id() != 0 && $type->id() != 0 )
						$appointments =& $tmpGroupEvent->getByGroupType( $tmpDate, $eventGroup, $type, true );
					// Fetch all appointments by Group
					else
						$appointments =& $tmpGroupEvent->getByDate( $tmpDate, $eventGroup, true );

                    $t->set_var( "public_appointment", "" );
                    $t->set_var( "private_appointment", "" );




               foreach ( $appointments as $appointment )
              {
		      // kracker : trim apointment name to keep the calendar easy to read
		      $appointmentName = $appointment->name();
				$t->set_var ( "appointment_name", stripslashes($appointmentName) );
				$t->set_var ( "appointment_full_name", stripslashes($appointmentName) );
                $t->set_var ( "overlib_full_name", addslashes($appointmentFullName));
                $t->set_var ( "overlib_description", addslashes($appointment->description()) );
				$t->set_var ( "appointment_id" , $appointment->id() );
                $t->set_var ( "event_description", stripslashes($appointment->description()) );
                $eStartTime = $appointment->startTime();
                $eStopTime = $appointment->stopTime();
                $event_start_time = addZero($eStartTime->hour()) . ':'. addZero( $eStartTime->minute() );
                $event_stop_time =  addZero($eStopTime->hour()) . ':'. addZero( $eStopTime->minute() );
                $t->set_var ( "event_start_time", $event_start_time  );
                $t->set_var ( "event_stop_time", $event_stop_time );
						if( $groupsList != "-1" )
						{
							foreach ( $groupsList as $groups )
							{
								//If the user has a matching group set their group id to the matching group else there group id will be -1.
								if( $appointment->groupID() == $groups->id() )
								{
									$user_groupID = $groups->id();
									break;
								}
							}
						}

						$groupName = new eZUserGroup( $appointment->groupID() );
						$t->set_var ( "appointment_group", $groupName->name() );

                        if ( ( $appointment->isPrivate() == true && $user_groupID == $appointment->groupID() ) || ( $appointment->isPrivate() == true && eZPermission::checkPermission( $user, "eZGroupEventCalendar", "Read" ) ) || $appointment->isPrivate() == false )
                        {
                            $t->parse( "public_appointment", "public_appointment_tpl", true );
                        }
                        else
                        {
                            $t->parse( "private_appointment", "private_appointment_tpl", true );
                        }
                    }

                    $t->set_var( "day_number", $tmpDate->day() );
                    $t->set_var( "month_number_p", $Month );
                    $t->set_var( "year_number", $Year );

		    $t->set_var( "day_link", "" );

		// if ( count($appointments) > 0 && $day_loop_count < 30 )
		//  print(count($appointments) ." | $a1 |" ."<br />\n");

		// print(count($appointments) ." | $a1 |" ."<br />\n");
		// print(count($appointments) ."<br />\n");
		
		$day_appointment_count = count($appointments);
		$day_appointment_day = $currentDay;

		if ( $day_appointment_count > 0 ) { 
		  // needs calculation to say start / end of month begin / reached, note that items on the
		  // first and last few days of every month do not render their events only within the 
		  // calendar's current month (? bug, negative feature), this is a hack fix to prevent 
		  // the display of the links to days without events.
		  if ( $day_appointment_count > 0 && $day_loop_count <= 33 ){  
		    // print("Rare:  $day_appointment_day | $day_appointment_count | $day_loop_count <br />");
                    $t->set_var( "day_no_link", "" );
                    $t->parse( "day_link", "day_link_tpl" );
		  } else {   
		    // print("Rare2:  $day_appointment_day | $day_appointment_count | $day_loop_count <br />");

		    $t->set_var( "day_link", "" );
		    $t->parse( "day_no_link", "day_no_link_tpl" );
		  }
		} else {
		  $t->set_var( "day_link", "" );
		  $t->parse( "day_no_link", "day_no_link_tpl" );
		  }

          $t->parse( "day", "day_tpl", true );
          $date->move(0,0,1);
            }
        $t->parse( "week", "week_tpl", true );

    // group list
    $group = new eZUserGroup();
    $group_array =& $group->getAll();

    foreach( $group_array as $groupItem )
    {
		if( $noShowGroup->groupEntry( $groupItem->id() ) == false )
		{
			$t->set_var( "group_id", $groupItem->id() );
			$t->set_var( "group_name", $groupItem->Name() );

			if ( $eventGroup->id() == $groupItem->id() )
			{
				$t->set_var( "selected_group_id", $eventGroup->id() );
				$t->set_var( "group_is_selected", "selected" );
			}
			elseif ( $eventGroup->id() == 0 )
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

    $t->storeCache( "output", "week_view_page_tpl", true );
//}


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
//Returns the day of week numerically 1-7
function moveToWeekBegin ( $date, $mondayFirst )
{
 switch ($date->dayName( $mondayFirst ) )
 {
 	case 'mon':
 	$ret = 0;
 	break;
 	case 'tue':
 	$ret = 1;
 	break;
 	case 'wed':
 	$ret = 2;
 	break;
 	case 'thu':
 	$ret = 3;
 	break;
 	case 'fri':
 	$ret = 4;
 	break;
 	case 'sat':
 	$ret = 5;
 	break;
 	case 'sun':
 	$ret = 6;
 	break;
 }
 return $ret;
}

?>
