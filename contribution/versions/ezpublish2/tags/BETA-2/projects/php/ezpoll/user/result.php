<?
// 
// $Id: result.php,v 1.2 2000/10/26 12:44:59 ce-cvs Exp $
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

$Language = $ini->read_var( "eZPollMain", "Language" );

include_once( "ezpoll/classes/ezpoll.php" );
include_once( "ezpoll/classes/ezvote.php" );
include_once( "ezpoll/classes/ezpollchoice.php" );


$t = new eZTemplate( "ezpoll/user/" . $ini->read_var( "eZPollMain", "TemplateDir" ),
                     "ezpoll/user/intl/", $Language, "result.php" );

$t->setAllStrings();

$t->set_file( array(
    "result" => "result.tpl"
    ) );

$t->set_block( "result", "result_list_tpl", "result_list" );
$t->set_block( "result_list_tpl", "result_item_tpl", "result_item" );

$poll = new eZPoll();
if ( $Show == "all"  )
{
    $pollArray = $poll->getAll();    
}
else
{
    $poll->get( $PollID );
    $pollArray[] = $poll;
}

foreach ( $pollArray as $poll )
{
    $t->set_var( "poll_name", $poll->name() );

    $pollchoice = new eZPollChoice();
    $choiceList = $pollchoice->getAll( $poll->id() );

    $vote =  new eZVote();
    $total = 0;
    $total = $poll->totalVotes();
    
    setType( $total, "double" );

    $i=1;

    if ( $poll->showResult() )
    {
        $t->set_var( "result_item", "" );        
        foreach( $choiceList as $choiceItem )
        {
            $value = 0;
            $t->set_var( "choice_name", $choiceItem->name() );
            $t->set_var( "choice_id", $choiceItem->id() );

            $t->set_var( "choice_vote", $choiceItem->voteCount() );
            $t->set_var( "choice_number", $i );

            if ( $total != 0 )
            {
                $percent = ( ( $choiceItem->voteCount() / $total ) * 100 );
                setType( $percent, "integer" );
                $t->set_var( "choice_percent", $percent );
                $t->set_var( "choice_inverted_percent", 100 - $percent );
        
            }
            else
            {
                $t->set_var( "choice_percent", 0 );
                $t->set_var( "choice_inverted_percent", 100 );
            }
    
            $value = $choiceItem->voteCount();
    
            setType( $value, "double" );
            setType( $total, "double" );
    
            $t->parse( "result_item", "result_item_tpl", true );
            $i++;    
        }

        $t->set_var( "total_votes", $poll->totalVotes() );
        
        $t->parse( "result_list", "result_list_tpl", true );
    }
    else
    {
        $ini = new INIFile( "ezpoll/intl/" . $Language . "/result.php.ini", false );
        $result =  $ini->read_var( "strings", "no_result" );

        $t->set_var( "result_list", $result );
    }
}

$t->pparse( "output", "result" );
?>
