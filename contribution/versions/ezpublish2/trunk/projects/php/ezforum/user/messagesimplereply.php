<?php
// 
// $Id: messagesimplereply.php,v 1.10 2001/02/09 11:01:25 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <24-Sep-2000 12:20:32 bf>
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


if ( isset( $Cancel ) )
{
    eZHTTPTool::header( "Location: $RedirectURL" );
    exit();
}

include_once( "classes/INIFile.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZForumMain", "Language" );
$ReplyPrefix = $ini->read_var( "eZForumMain", "ReplyPrefix" );

include_once( "classes/eztemplate.php" );
include_once( "classes/ezmail.php" );
include_once( "classes/eztexttool.php" );
include_once( "classes/ezlocale.php" );

include_once( "ezuser/classes/ezuser.php" );

include_once( "ezforum/classes/ezforummessage.php");
include_once( "ezforum/classes/ezforumcategory.php");
include_once( "classes/ezhttptool.php" );


if ( $Action == "insert" )
{
    $original = new eZForumMessage( $MessageID );
    
    $reply = new eZForumMessage( );

    $reply->setForumID( $original->forumID() );

    $reply->setTopic( strip_tags( $Topic ) );
         
    $reply->setBody( strip_tags( $Body, "<b>,<i>,<u>,<font>" ) );

    $reply->setParent( $original->id() );
    
    $user = eZUser::currentUser();
    
    $reply->setUserId( $user->id() );

    if ( $notice )
        $reply->enableEmailNotice();
    else
        $reply->disableEmailNotice();

    $reply->setIsApproved( true  );    

    $reply->store();

    $forum_id = $original->forumID();

    // send out email notices
    $forum = new eZForum( $original->forumID() );
    $messages = $forum->messageThreadTree( $reply->threadID() );

    $mail = new eZMail();

    $mail->setFrom( "noreply@ez.no" );
    
    $locale = new eZLocale( $Language );
    
    $mailTemplate = new eZTemplate( "ezforum/user/" . $ini->read_var( "eZForumMain", "TemplateDir" ),
                                    "ezforum/user/intl", $Language, "mailreply.php" );
    
    $mailTemplate->set_file( "mailreply", "mailreply.tpl" );
    $mailTemplate->setAllStrings();
    
    foreach ( $messages as $message )
    {
        if ( $message->id() != $reply->id() )
        {
            if ( ( $message->treeID() > $reply->treeID() ) && $message->emailNotice() )
            {
                $headersInfo = ( getallheaders() );
                $mailTemplate->set_var( "arthur", $user->firstName() . " " . $user->lastName() );
                $mailTemplate->set_var( "postingtime", $locale->format( $message->postingTime() ) );
                
                $mailTemplate->set_var( "topic", $reply->topic() );
                $mailTemplate->set_var( "body", $reply->body() );
                $mailTemplate->set_var( "link", "http://" . $headersInfo["Host"] . "forum/message/" . $reply->id() );

                $bodyText = ( $mailTemplate->parse( "dummy", "mailreply" ) );

                $mail->setSubject( $reply->topic() );

                $user =& $message->user();

                $mail->setTo( $user->email() );
                $mail->setBody( $bodyText );
                
                $mail->send();
            }
        }
    }    


    // clear the cache files.

//      $dir = dir( "ezforum/cache/" );
//      $files = array();
//      while( $entry = $dir->read() )
//      { 
//          if ( $entry != "." && $entry != ".." )
//          { 
//              $files[] = $entry; 
//              $numfiles++; 
//          } 
//      } 
//      $dir->close();

//      foreach( $files as $file )
//      {
//          if ( ereg( "forum,([^,]+),.*", $file, $regArray  ) )
//          {
//              if ( $regArray[1] == $forum_id )
//              {
//                  unlink( "ezforum/cache/" . $file );
//              }
//          }
//      }

//      // add deleting of every message in the thread
//      unlink( "ezforum/cache/message," . $ReplyID . ".cache" );

    if ( $RedirectURL == "" )
    {        
        eZHTTPTool::header( "Location: /" );
    }
    else
    {
        eZHTTPTool::header( "Location: $RedirectURL" );        
    }
    exit();

}

$t = new eZTemplate( "ezforum/user/" . $ini->read_var( "eZForumMain", "TemplateDir" ),
                     "ezforum/user/intl", $Language, "messagesimplereply.php" );
$t->setAllStrings();

$t->set_file( "replymessage", "messagesimplereply.tpl");

$category = new eZForumCategory();

$msg = new eZForumMessage( $MessageID );
$ForumID = $msg->forumID();
$forum = new eZForum( $ForumID );

$t->set_var( "forum_name", $forum->name() );
$user = eZUser::currentUser();

if ( !$user )
{
    eZHTTPTool::header( "Location: /forum/userlogin/reply/$MessageID" );
    exit();
}

$t->set_var( "forum_id", $ForumID );

$t->set_var( "msg_id", $msg->id() );

$t->set_var( "topic", ( $ReplyPrefix . htmlspecialchars( stripslashes( $msg->topic() ) ) ) );

$t->set_var( "user", $user->firstName() . " " . $user->lastName() );

$text = eZTextTool::addPre( $msg->body() );

$t->set_var("body", $text );

$t->set_var( "message_id", $MessageID );

$t->set_var( "forum_id", $MessageID );

$t->set_var( "redirect_url", $RedirectURL );

$t->pparse("output", "replymessage");
?>
