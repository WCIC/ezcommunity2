<?
/*!
    $Id: forum.php,v 1.12 2000/07/25 10:55:37 lw-cvs Exp $

    Author: Lars Wilhelmsen <lw@ez.no>
    
    Created on: <14-Jul-2000 12:57:16 lw>
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/
include( "ezforum/dbsettings.php" );
include( "ezphputils.php" );
include( "template.inc" );
include( "$DOCROOT/classes/ezuser.php" );
include( "$DOCROOT/classes/ezforummessage.php" );
include( "$DOCROOT/classes/ezsession.php" );

$msg = new eZforumMessage( $forum_id );
$t = new Template(".");

$t->set_file( Array("forum" => "$DOCROOT/templates/forum.tpl",
                    "elements" => "$DOCROOT/templates/forum-elements.tpl",
                    "preview" => "$DOCROOT/templates/forum-preview.tpl",
                    "navigation" => "$DOCROOT/templates/navigation.tpl",
                    "navigation-bottom" => "$DOCROOT/templates/navigation-bottom.tpl"
                   )
            );

$t->set_var( "docroot", $DOCROOT );
$t->set_var( "category_id", $category_id );
$t->set_var( "forum_id", $forum_id );

//navbar setup
if ( $AuthenticatedSession )
{
    $session = new eZSession();
    $session->get( $AuthenticatedSession );
    $UserID = $session->UserID();

    $t->set_var( "user", eZUser::resolveUser( $session->UserID() ) );
}
else
{
    $UserID = 0;
    $t->set_var( "user", "Anonym" );
}
$t->parse( "navigation-bar", "navigation", true);


// new posting
if ( $post )
{
    $msg->newMessage();
    $msg->setTopic( $Topic );
    $msg->setBody( $Body );
    $msg->setUserId( $UserID );
    if ( $notice )
        $msg->enableEmailNotice();
    else
        $msg->disableEmailNotice();
    $msg->store();
}

// reply
if ( $reply )
{
    $msg->newMessage();    
    $msg->setTopic( $Topic );
    $msg->setBody( $Body );
    $msg->setUserId( $UserID );
    $msg->setParent( $parent );
    if ( $notice )
        $msg->enableEmailNotice();
    else
        $msg->disableEmailNotice();
    $msg->store();
}

// preview message
if ( $preview )
{
    $t->set_var( "topic", $Topic );
    $t->set_var( "body", nl2br( $Body ) );
    $t->set_var( "body-clean", $Body );
    $t->set_var( "userid", $UserID );
    $t->pparse( "output", "preview" );
}
else
{
    $headers = $msg->getHeaders( $forum_id );

    for ($i = 0; $i < count($headers); $i++)
    {
        $Id = $headers[$i]["Id"];
        $Topic  = $headers[$i]["Topic"];
        $User = $headers[$i]["UserId"];
        $PostingTime = $headers[$i]["PostingTimeFormated"];
        
        $j = $i + 1;
         
        $t->set_var( "id", $Id);
        $t->set_var( "forum_id", $forum_id);
        $t->set_var( "topic", $Topic);
        $t->set_var( "nr", $j );
        $t->set_var( "user", $User );
        $t->set_var( "postingtime", $PostingTime );
        $t->set_var( "link",$link );
         
        $t->set_var( "replies", eZforumMessage::countReplies( $Id ) );

        if ( ($i % 2) != 0)
            $t->set_var( "color", "#eeeeee");
        else
            $t->set_var( "color", "#bbbbbb");
    
        $t->parse("messages", "elements", true);
    }
     
    if ( count( $headers ) == 0)
        $t->set_var( "messages", "<tr><td colspan=\"4\">Ingen meldinger</td></tr>");
     
    $t->set_var("newmessage", $newmessage);

    $t->set_var( "link1-url", "newmessage.php" );
    $t->set_var( "link1-caption", "Ny Melding" );
    $t->set_var( "link2-url", "search.php" );
    $t->set_var( "link2-caption", "S�k" );

    $t->set_var( "back-url", "category.php");
    $t->parse( "navigation-bar-bottom", "navigation-bottom", true);

    $t->pparse("output","forum");
}
?>
