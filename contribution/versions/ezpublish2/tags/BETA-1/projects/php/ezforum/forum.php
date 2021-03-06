<?
/*!
    $Id: forum.php,v 1.28 2000/08/29 13:56:04 th-cvs Exp $

    Author: Lars Wilhelmsen <lw@ez.no>
    
    Created on: <14-Jul-2000 12:57:16 lw>
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/

$ini = new INIFile( "site.ini" ); // get language settings
$DOC_ROOT = $ini->read_var( "eZForumMain", "DocumentRoot" );

//include( "ezforum/dbsettings.php" );

include_once( "ezphputils.php" );
include_once( "template.inc" );
include_once( "class.INIFile.php" );
include_once( $DOC_ROOT . "/classes/ezdb.php" );
include_once( $DOC_ROOT . "/classes/ezforummessage.php" );
include_once( $DOC_ROOT . "/classes/ezforumforum.php" );
include_once( "classes/ezuser.php" );
include_once( "classes/ezsession.php" );
include_once( "classes/eztemplate.php" );

$ini = new INIFile( "ezforum.ini" ); // get language settings
$Language = $ini->read_var( "MAIN", "Language" );

$msg = new eZforumMessage( $forum_id );
$t = new eZTemplate( "$DOC_ROOT/templates", $DOC_ROOT . "/intl", $Language, "forum.php" );
$t->setAllStrings();

$t->set_file( Array("forum" => "forum.tpl",
                    "elements" => "forum-elements.tpl",
                    "preview" => "forum-preview.tpl",
                    "navigation" => "navigation.tpl",
                    "navigation-bottom" => "navigation-bottom.tpl",
                    "login" => "login.tpl",
                    "logout" => "logout.tpl"
                   )
            );


$session = new eZSession();

$t->set_var( "docroot", $DOC_ROOT );
$t->set_var( "category_id", $category_id );
$t->set_var( "forum_id", $forum_id );

$forum = new eZForumForum( );
$forum->get( $forum_id );

$category_id = $forum->categoryID();

$category = new eZForumCategory( );
$category->get( $category_id );
$forumPath = "<img src=\"ezforum/images/pil.gif\" width=\"10\" height=\"10\" border=\"0\"> <a href=\"index.php?page=" . $DOC_ROOT .  "category.php&category_id=" . $category_id . "\">" . $category->name() . "</a> ";

$forumPath .= "<img src=\"ezforum/images/pil.gif\" width=\"10\" height=\"10\" border=\"0\"> <a href=\"index.php?page=" . $DOC_ROOT .  "forum.php&forum_id=" . $forum_id . "&category_id=" . $category_id . "\">" . $forum->name() . "</a>";

$t->set_var( "forum_path", $forumPath );



//navbar setup
if ( $session->get( $AuthenticatedSession ) == 0 )
{
    $UserID = $session->UserID();

    $user = new eZUser();
    $t->set_var( "user", $user->resolveUser( $session->UserID() ) );

    $t->parse( "logout-message", "logout", true );
}
else
{
    $UserID = 0;
    $t->set_var( "user", "Anonym" );
    $t->parse( "logout-message", "login", true );
}
$t->parse( "navigation-bar", "navigation", true );


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
    $messages = $msg->printHeaderTree( $forum_id, 0, 0, $DOC_ROOT, $category_id );
    $t->set_var( "messages", $messages );
    
    $t->set_var( "newmessage", $newmessage);

    $t->set_var( "link1-url", "newmessage.php" );
    $t->set_var( "link2-url", "search.php" );

    $t->set_var( "back-url", "category.php");
    $t->parse( "navigation-bar-bottom", "navigation-bottom", true);

    $t->pparse("output","forum");
}

?>
