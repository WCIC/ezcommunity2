<?
// 
// $Id: messagebody.php,v 1.3 2001/02/26 16:42:28 pkej Exp $
//
// Paul K Egell-Johnsen <pkej@ez.no>
// Created on: <21-Feb-2001 18:00:00 pkej>
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

if( $ShowMessage == true )
{
    include_once( "classes/eztexttool.php" );
    $AllowedTags = $ini->read_var( "eZForumMain", "AllowedTags" );
    $AllowHTML = $ini->read_var( "eZForumMain", "AllowHTML" );
    
    $t->set_file( "body", "messagebody.tpl"  );
    
    $msg = new eZForumMessage( $MessageID );
    $MessageTopic = $msg->topic();
    
    if( $AllowHTML == "enabled" )
    {
        $MessageBody = $msg->body( false );
    }
    else
    {
        $MessageBody = eZTextTool::nl2br( $msg->body( false ) );
    }
    $author = new eZUser ( $msg->userId() );
    $MessageNotice = $msg->emailNotice();

    if( isset( $NewMessageAuthor ) )
    {
        $MessageAuthor = $NewMessageAuthor;
    }
    else
    {
        if( !is_object( $author ) )
        {
            $author = new eZUser ( $msg->userId() );
        }

        if( $author->id() == 0 )
        {
            $MessageAuthor = $ini->read_var( "eZForumMain", "AnonymousPoster" );
        }
        else
        {
            $MessageAuthor = $author->firstName() . " " . $author->lastName();
        }
    }

    if( isset( $NewMessagePostedAt ) )
    {
        $MessagePostedAt = $NewMessagePostedAt;
    }
    else
    {
        $MessagePostedAt = $locale->format( $msg->postingTime() );
    }

    if( isset( $NewMessageNotice ) )
    {
        $MessageNotice = $NewMessageNotice;
    }

    switch( $MessageNotice )
    {
        case "on":
        case "y":
        case "checked":
        case 1:
        case true:
        {
            $t->Ini->read_var( "strings", "notice_yes" );
        }
        break;

        case "off":
        case "n":
        case "unchecked":
        case 0:
        case false:
        {
            $t->Ini->read_var( "strings", "notice_no" );
        }
        break;
    }

    $t->set_var( "message_topic", $MessageTopic );
    $t->set_var( "message_body", $MessageBody );
    $t->set_var( "message_posted_at", $MessagePostedAt );
    $t->set_var( "message_author", $MessageAuthor );
    $t->set_var( "message_id", $MessageID );
    $t->set_var( "message_notice", $MessageNotice );

    if( $doPrint == true )
    {
        $t->pparse( "message_body_file", "body" );
    }
    else
    {
        $t->parse( "message_body_file", "body" );
    }
}
else
{
    $t->set_var( "message_body_file", "" );
}

?>
