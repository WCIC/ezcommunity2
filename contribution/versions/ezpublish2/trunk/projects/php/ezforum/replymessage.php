<?php
// 
// $Id: replymessage.php,v 1.21 2000/10/17 15:19:58 bf-cvs Exp $
//
// 
//
// B�rd Farstad <bf@ez.no>
// Created on: <24-Sep-2000 12:20:32 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );

$ini = new INIFile( "site.ini" ); // get language settings

include_once( "common/ezphputils.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezmail.php" );
include_once( "ezuser/classes/ezuser.php" );

include_once( "ezforum/classes/ezforummessage.php");
include_once( "ezforum/classes/ezforumcategory.php");


if ( $Action == "Insert" )
{
    $original = new eZForumMessage( $ReplyID );
    
    $reply = new eZForumMessage( );

    $reply->setForumID( $original->forumID() );

    $reply->setTopic( $Topic );
    $reply->setBody( $Body );

    $reply->setParent( $original->id() );
    
    $user = eZUser::currentUser();

    
    $reply->setUserId( $user->id() );

    if ( $notice )
        $reply->enableEmailNotice();
    else
        $reply->disableEmailNotice();

    $reply->store();

    $forum_id = $original->forumID();

    // send out email notices
    $forum = new eZForum( $forum_id );
    $messages = $forum->messageThreadTree( $reply->threadID() );

    $mail = new eZMail();

    $mail->setFrom( "noreply@ez.no" );
    
    $mail->setSubject( "reply" );
    
   
    foreach ( $messages as $message )
    {
        if ( $message->id() != $reply->id() )
        {
            if ( ( $message->treeID() > $reply->treeID() ) && $message->emailNotice() )
            {
                $user =& $message->user();
                
                $mail->setTo( $user->email() );
                $mail->setBody(  $reply->body() . "" . $user->email() );
                
                $mail->send();
            }
        }
    }    


    // clear the cache files.

    $dir = dir( "ezforum/cache/" );
    $files = array();
    while( $entry = $dir->read() )
    { 
        if ( $entry != "." && $entry != ".." )
        { 
            $files[] = $entry; 
            $numfiles++; 
        } 
    } 
    $dir->close();

    foreach( $files as $file )
    {
        if ( ereg( "forum,([^,]+),.*", $file, $regArray  ) )
        {
            if ( $regArray[1] == $forum_id )
            {
                unlink( "ezforum/cache/" . $file );
            }
        }
    }

    // add deleting of every message in the thread
    unlink( "ezforum/cache/message," . $ReplyID . ".cache" );
    
    Header( "Location: /forum/category/forum/$forum_id/" );

}

$t = new Template(".");

$t->set_file("replymessage","ezforum/templates/replymessage.tpl");

$t->set_var( "category_id", $category_id );


$category = new eZForumCategory();


// chech for login..

// add path

$msg = new eZForumMessage( $ReplyID );

$user = eZUser::currentUser();

$t->set_var( "forum_id", $forum_id );
$t->set_var( "msg_id", $msg->id() );
$t->set_var( "info",  $infoString);

$t->set_var( "topic", ("SV: " . stripslashes( $msg->topic() ) ) );

$t->set_var( "user", $user->firstName() . " " . $user->lastName() );

$t->set_var("body", nl2br( stripslashes( $msg->body() ) ) );

//  $t->set_var("replier", $user->resolveUser( $UserID ) );

$t->pparse("output", "replymessage");
?>
