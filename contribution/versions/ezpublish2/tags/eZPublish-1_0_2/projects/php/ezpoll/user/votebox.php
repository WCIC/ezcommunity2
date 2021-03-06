<?
// 
// $Id: votebox.php,v 1.7 2000/11/02 14:38:37 ce-cvs Exp $
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <20-Sep-2000 13:32:11 ce>
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
include_once( "classes/eztemplate.php" );

$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZUserMain", "Language" );
$PageCaching = $ini->read_var( "eZPollMain", "PageCaching" );
$errorIni = new INIFIle( "ezpoll/user/intl/" . $Language . "/votebox.php.ini", false );

$noItem = $errorIni->read_var( "strings", "noitem" );

unset( $menuCachedFile );
// do the caching
if ( $PageCaching == "enabled" )
{
    $menuCachedFile = "ezpoll/cache/menubox.cache";
    
    if ( file_exists( $menuCachedFile ) )
    {
        include( $menuCachedFile );
    }
    else
    {
        createPollMenu( true );
    }            
}
else
{
    createPollMenu();
}

function createPollMenu( $generateStaticPage = false )
{
    global $ini;
    global $menuCachedFile;
    global $noItem;

    $Language = $ini->read_var( "eZPollMain", "Language" );
    
    include_once( "ezpoll/classes/ezpoll.php" );
    include_once( "ezpoll/classes/ezpollchoice.php" );

    $t = new eZTemplate( "ezpoll/user/" . $ini->read_var( "eZPollMain", "TemplateDir" ),
                         "ezpoll/user/intl/", $Language, "votebox.php" );

    $t->setAllStrings();

    $poll = new eZPoll( $PollID );
    $poll = $poll->mainPoll();
    $PollID = $poll->id();
    $poll = new eZPoll( $PollID );


    if ( $poll->isClosed() )
    {
        Header( "Location: /poll/result/$PollID" );
        exit();
    }

    $t->set_file( array(
        "vote_box" => "votebox.tpl"
        ) );

    $t->set_block( "vote_box", "vote_item_tpl", "vote_item" );
    $t->set_block( "vote_box", "novote_item_tpl", "novote_item" );

    $choice = new eZPollChoice();

    $choiceList = $choice->getAll( $PollID );

    if ( !$choiceList )
    {
        $t->set_var( "vote_item", "" );
        $t->set_var( "novote_item", $noItem );
        $t->parse( "novote_item", "novote_item_tpl" );
    }
    else
    {
        foreach( $choiceList as $choiceItem )
            {
                $t->set_var( "choice_name", $choiceItem->name() );
                $t->set_var( "choice_id", $choiceItem->id() );

                $t->set_var( "novote_item", "" );
                $t->parse( "vote_item", "vote_item_tpl", true );
            }
    }

    $poll = new eZPoll();
    $poll->get( $PollID );
    $t->set_var( "head_line", $poll->name() );
    $t->set_var( "poll_id", $PollID );

    if ( $generateStaticPage == true )
    {
        $fp = fopen ( $menuCachedFile, "w+");

        $output = $t->parse( $target, "vote_box" );
        // print the output the first time while printing the cache file.
    
        print( $output );
        fwrite ( $fp, $output );
        fclose( $fp );
    }
    else
    {
        $t->pparse( "output", "vote_box" );
    }
}
?>
