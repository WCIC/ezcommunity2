<?
// 
// $Id: votebox.php,v 1.5 2000/10/10 13:26:02 ce-cvs Exp $
//
// Definition of eZPoll class
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <20-Sep-2000 13:32:11 ce>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZPollMain", "Language" );
$DOC_ROOT = $ini->read_var( "eZPollMain", "DocumentRoot" );

include_once( $DOC_ROOT . "/classes/ezpoll.php" );
include_once( $DOC_ROOT . "/classes/ezpollchoice.php" );


$t = new eZTemplate( $DOC_ROOT . $ini->read_var( "eZPollMain", "TemplateDir" ) . "/vote/",
                     $DOC_ROOT . "/intl/", $Language, "pollist.php" );

$t->setAllStrings();

$t->set_file( array(
    "vote_form" => "voteform.tpl"
    ) );

$t->set_block( "vote_form", "vote_item_tpl", "vote_item" );

$choice = new eZPollChoice();

$choiceList = $choice->getAll( $PollID );

foreach( $choiceList as $choiceItem )
{
    $t->set_var( "choice_name", $choiceItem->name() );
    $t->set_var( "choice_id", $choiceItem->id() );

    $t->parse( "vote_item", "vote_item_tpl", true );
    
}

$poll = new eZPoll();
$poll->get( $PollID );
$t->set_var( "head_line", $poll->name() );
$t->set_var( "poll_id", $PollID );


  
$t->pparse( "output", "vote_form" );

?>
