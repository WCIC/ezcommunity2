<?
// $Id: messageedit.php,v 1.7 2000/11/22 16:46:26 bf-cvs Exp $
//
// Author: Lars Wilhelmsen <lw@ez.no>
// Created on: Created on: <18-Jul-2000 08:56:19 lw>
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

$Language = $ini->read_var( "eZForumMain", "Language" );
$error = new INIFIle( "ezforum/admin/intl/" . $Language . "/messageedit.php.ini", false );

include_once( "ezforum/classes/ezforummessage.php" );
include_once( "ezforum/classes/ezforum.php" );
include_once( "ezforum/classes/ezforumcategory.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );

require( "ezuser/admin/admincheck.php" );

if ( $Action == "insert" )
{
    // Admin does not support insert.
}
if ( $Action == "update" )
{
    if ( eZPermission::checkPermission( $user, "eZForum", "MessageModify" ) )
    {
        if ( $Topic != "" &&
        $Body != "" )
        {
            $msg = new eZForumMessage();
            $msg->get( $MessageID );
            $msg->setTopic( $Topic );
            $msg->setBody( $Body );

            if ( $notice )
                $msg->enableEmailNotice();
            else 
                $msg->disableEmailNotice();

            $ForumID = $msg->forumID();

            $forum = new eZForum( $ForumID );

            $msg->store();

            Header( "Location: /forum/messagelist/$ForumID/" );
            exit();
        }
        else
        {
            $error_msg = $error->read_var( "strings", "error_missingdata" );
        }
    }
    else
    {
        Header( "Location: /forum/norights" );
   }
    
}

if ( $Action == "delete" )
{
    if ( eZPermission::checkPermission( $user, "eZForum", "MessageDelete" ) )
    {
        if ( $MessageID != "" )
        {
            $msg = new eZForumMessage();
            $msg->get( $MessageID );
            $msg->delete();
            
            $ForumID = $msg->forumID();
            $forum = new eZForum( $ForumID );
            
            Header( "Location: /forum/messagelist/$ForumID" );
            exit();
        }
        else
        {
            $error_msg = $error->read_var( "strings", "error_missingdata" );
        }
    }
    else
    {
        Header( "Location: /forum/norights" );
    }
}

$t = new eZTemplate( "ezforum/admin/" . $ini->read_var( "eZForumMain", "TemplateDir" ),
"ezforum/admin/" . "/intl", $Language, "messageedit.php" );
$t->setAllStrings();

$t->set_file( Array( "message_page" => "messageedit.tpl" ) );

$ini = new INIFile( "ezforum/admin/" . "intl/" . $Language . "/messageedit.php.ini", false );
$headline =  $ini->read_var( "strings", "head_line_insert" );

$t->set_block( "message_page", "message_edit_tpl", "message_edit" );
$locale = new eZLocale( $Language );

$t->set_var( "message_topic", "" );
$t->set_var( "message_postingtime", "" );
$t->set_var( "message_body", "" );
$t->set_var( "message_user", "" );
$t->set_var( "message_id", $MessageID );
$action_value = "update";

if ( $Action == "new" )
{
    if ( !eZPermission::checkPermission( $user, "eZForum", "MessageModifyAdd" ) )
    {
        Header( "Location: /forum/norights" );
    }

    $action_value = "insert";
}


if ( $Action == "edit" )
{
    $ini = new INIFile( "ezforum/admin/" . "intl/" . $Language . "/messageedit.php.ini", false );
    $headline =  $ini->read_var( "strings", "head_line_edit" );

    if ( !eZPermission::checkPermission( $user, "eZForum", "MessageModify" ) )
    {
        Header( "Location: /forum/norights" );
    }
    else
    {
        $msg = new eZForumMessage();
        $msg->get( $MessageID );
        $t->set_var( "message_topic", $msg->topic() );
        $t->set_var( "message_postingtime", $locale->format( $msg->postingTime() ) );
        $t->set_var( "message_body", $msg->body() );
        $user = $msg->user();
        $t->set_var( "message_user", $user->firstName() . " " . $user->lastName() );
        $action_value = "update";
        $t->set_var( "message_id", $MessageID );
        $t->set_var( "forum_id", $msg->forumID() );
    }
}

$t->set_var( "action_value", $action_value );
$t->set_var( "error_msg", $error_msg );
$t->set_var( "category_id", $CategoryID );
$t->set_var( "headline", $headline );
$t->pparse( "output", "message_page" );

?>
