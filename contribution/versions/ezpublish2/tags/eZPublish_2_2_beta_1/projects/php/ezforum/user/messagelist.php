<?php
// 
// $Id: messagelist.php,v 1.38 2001/08/31 14:01:59 jhe Exp $
//
// Created on: <11-Sep-2000 22:10:06 bf>
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

include_once( "classes/INIFile.php" );

$ini =& INIFile::globalINI();

include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezdatetime.php" );
include_once( "classes/ezlist.php" );
include_once( "ezsession/classes/ezpreferences.php" );
include_once( "ezuser/classes/ezuser.php" );

include_once( "ezforum/classes/ezforummessage.php" );
include_once( "ezforum/classes/ezforumcategory.php" );
include_once( "ezforum/classes/ezforum.php" );

$Language = $ini->read_var( "eZForumMain", "Language" );
$NewMessageLimit = $ini->read_var( "eZForumMain", "NewMessageLimit" );

$t = new eZTemplate( "ezforum/user/" . $ini->read_var( "eZForumMain", "TemplateDir" ),
                     "ezforum/user/intl", $Language, "messagelist.php" );

$t->set_file( "messagelist", "messagelist.tpl" );

$t->set_block( "messagelist", "message_item_tpl", "message_item" );
$t->set_block( "message_item_tpl", "edit_message_item_tpl", "edit_message_item" );
$t->set_block( "messagelist", "previous_tpl", "previous" );

$t->set_block( "message_item_tpl", "new_icon_tpl", "new_icon" );
$t->set_block( "message_item_tpl", "old_icon_tpl", "old_icon" );

$t->set_block( "messagelist", "messages_element_tpl", "messages_element" );
$t->set_block( "messagelist", "show_threads_tpl", "show_threads" );
$t->set_block( "messagelist", "hide_threads_tpl", "hide_threads" ); 

$t->setAllStrings();

$session =& eZSession::globalSession();
$session->fetch();

if ( isSet( $ForumMessages ) )
{
    $session->setVariable( "ForumMessages", $ForumMessages );
    $UserLimit = $ForumMessages;
}
else
{
    $UserLimit = $ini->read_var( "eZForumMain", "MessageUserLimit" );
    if ( $session->variable( "ForumMessages" ) == false )
    {
        $session->setVariable( "ForumMessages", $UserLimit );
    }
    else
    {
        $UserLimit = $session->variable( "ForumMessages" );
    }
}

$forum = new eZForum( $ForumID );

$categories =& $forum->categories();

$user =& eZUser::currentUser();

if ( $user )
{
    $preferences = new eZPreferences();
    if ( isSet( $HideThreads ) )
        $preferences->setVariable( "eZForum_Threads", "Hide" );
    
    if ( isSet( $ShowThreads ) )
        $preferences->setVariable( "eZForum_Threads", "Show" );

    $showThreads =& $preferences->variable( "eZForum_Threads" );
}
else
{    
    $session =& eZSession::globalSession();
    
    if ( isSet( $HideThreads ) )
        $session->setVariable( "eZForum_Threads", "Hide" );
    
    if ( isSet( $ShowThreads ) )
        $session->setVariable( "eZForum_Threads", "Show" );
    
    $showThreads = $session->variable( "eZForum_Threads" );
}

if ( $showThreads == "" )
    $showThreads = "Hide";

$group =& $forum->group();
$viewer = $user;
if ( get_class( $group ) == "ezusergroup" )
{
    if ( get_class( $viewer ) == "ezuser" )
    {
        $groupList =& $viewer->groups();
        
        foreach ( $groupList as $userGroup )
        {
            if ( $userGroup->id() == $group->id() )
            {
                $readPermission = true;
                break;
            }
        }
    }
}
else
{
    $readPermission = true;
}

if ( count( $categories ) > 0 )
{
    $category = new eZForumCategory( $categories[0]->id() );
    
    $t->set_var( "category_id", $category->id( ) );
    $t->set_var( "category_name", $category->name( ) );
}

$locale = new eZLocale( $Language );

if ( !isSet( $Offset ) or !$Offset )
    $Offset = 0;

$limit_array = array_unique( array( 20, 30, 40, 50, 60, 80, 100, 150, 200, $UserLimit ) );
sort( $limit_array );

foreach ( $limit_array as $element_number )
{
    $t->set_var( "messages_number", $element_number );
    if ( $element_number == $UserLimit )
        $t->set_var( "is_selected", "selected" );
    else
        $t->set_var( "is_selected", "" );
    $t->parse( "messages_element", "messages_element_tpl", true );
}

$t->set_var( "offset", $Offset );

if ( $showThreads == "Hide" )
{
    $t->set_var( "hide_threads", "" );
    $t->parse( "show_threads", "show_threads_tpl" );
    $messageList =& $forum->messageTreeArray( $Offset, $UserLimit, false, false );
    $messageCount =& $forum->messageCount( false, false );
}
else if ( $showThreads == "Show" )
{
    $t->set_var( "show_threads", "" );
    $t->parse( "hide_threads", "hide_threads_tpl" );
    $messageList =& $forum->messageTreeArray( $Offset, $UserLimit );
    $messageCount =& $forum->messageCount();
}

if ( !$messageList )
{
    $languageIni = new INIFile( "ezforum/user/intl/" . $Language . "/messagelist.php.ini", false );
    $noitem =  $languageIni->read_var( "strings", "noitem" );

    $t->set_var( "message_item", $noitem );
}
else
{
    $db =& eZDB::globalDatabase();
    
    $level = 0;
    $i = 0;
    $time = new eZDateTime();
    foreach ( $messageList as $message )
    {
        $user = new eZUser();
        $t->set_var( "user", "" );
        $t->set_var( "edit_message_item", "" );

        if ( ( $i % 2 ) == 0 )
            $t->set_var( "td_class", "bglight" );
        else
            $t->set_var( "td_class", "bgdark" );
        
        $t->set_var( "topic", $message[$db->fieldName( "Topic" )] );

        $time->setTimeStamp( $message[$db->fieldName( "PostingTime" )] );
        $t->set_var( "postingtime", $locale->format( $time  ) );

        $t->set_var( "message_id", $message[$db->fieldName( "ID" )] );

        $messageAge = round( $message[$db->fieldName( "Age" )] / 86400 );
        if ( $messageAge <= $NewMessageLimit )
        {
            $t->parse( "new_icon", "new_icon_tpl" );
            $t->set_var( "old_icon", "" );
        }
        else
        {
            $t->parse( "old_icon", "old_icon_tpl" );
            $t->set_var( "new_icon", "" );
        }

        $userID = $message[$db->fieldName( "UserID" )];

        $user->get( $userID );
        
        if ( $showThreads == "Show" )
        {
            $t->set_var( "count_replies", "" );
            $level = $message[$db->fieldName( "Depth" )];

            if ( $level > 0 )
                $t->set_var( "spacer", str_repeat( "&nbsp;", $level ) );
            else
                $t->set_var( "spacer", "" );
        }
        else if ( $showThreads == "Hide" )
        {
            $count = eZForumMessage::threadMessageCount( $message[$db->fieldName( "ThreadID" )] ) - 1;
            $t->set_var( "spacer", "" );
            $t->set_var( "count_replies", $count );
        }

        if ( $user->id() == 0 )
        {
            if ( empty( $message[$db->fieldName( "UserName" )] ) )
                 $t->set_var( "user", $ini->read_var( "eZForumMain", "AnonymousPoster" ) );
            else
                $t->set_var( "user", $message[$db->fieldName( "UserName" )] );
        }
        else
        {
            $t->set_var( "user", $user->firstName() . " " . $user->lastName() );
        }
        
        /*        
        if ( get_class( $viewer ) == "ezuser" )
        {
            if ( $viewer->id() == $userID && eZForumMessage::countReplies( $message["ID"] ) == 0 && !$forum->IsModerated() )
            {
                $t->parse( "edit_message_item", "edit_message_item_tpl" );
            }
        }
        */
        $t->parse( "message_item", "message_item_tpl", true );
        $i++;
    }
}

eZList::drawNavigator( $t, $messageCount, $UserLimit, $Offset, "messagelist" );

$t->set_var( "forum_start", $Offset + 1 );
$t->set_var( "forum_end", min( $Offset + $UserLimit, $messageCount ) );
$t->set_var( "forum_total", $messageCount );

if ( !isSet( $newmessage ) )
    $newmessage = "";
$t->set_var( "newmessage", $newmessage );

$t->set_var( "forum_id", $forum->id() );
$t->set_var( "forum_name", $forum->name() );

if ( $readPermission == true )
    $t->pparse( "output", "messagelist" );

?>
