<?php
//
// $Id: vote.php,v 1.20 2001/08/17 13:36:00 jhe Exp $
//
// $Id: vote.php,v 1.20 2001/08/17 13:36:00 jhe Exp $
//
// $Id: vote.php,v 1.20 2001/08/17 13:36:00 jhe Exp $
//
// Created on: <20-Sep-2000 13:32:11 ce>
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
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZPollMain", "Language" );
$DOC_ROOT = $ini->read_var( "eZPollMain", "DocumentRoot" );
if ( $ini->read_var( "eZPollMain", "AllowDoubleVotes" ) == "enabled" )
   $AllowDoubleVotes = true;

include_once( "ezpoll/classes/ezpoll.php" );
include_once( "ezpoll/classes/ezvote.php" );
include_once( "ezpoll/classes/ezpollchoice.php" );
include_once( "ezsession/classes/ezsession.php" );

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

// Check if poll is closed.
$poll = new eZPoll( $PollID );
if ( $poll->isClosed() )
{
    eZHTTPTool::header( "Location: /poll/result/$PollID" );
    exit();
}

// Check if the poll is anonymous or not.
$poll = new eZPoll( $PollID );
if ( !$poll->anonymous() )
{
    $pollUser =& eZUser::currentUser();
    if ( !$pollUser )
    {
        eZHTTPTool::header( "Location: /user/user/new/" );
        exit();
    }
}
else
{

    $vote = new eZVote();
    //check if user has or can vote twice

    if ( $AllowDoubleVotes )
    {
       $Voted = false;
    }
    elseif ( $vote->ipHasVoted( $REMOTE_ADDR, $PollID ) == true )
    {
       $Voted = true;
    }
    else
    {
       $Voted = false;
    }

}

if ( $pollUser )
{
    $checkvote = new eZVote();
    if ( $checkvote->isVoted( $pollUser->id(), $PollID  ))
        $Voted = true;
    else
        $Voted = false;
}

if ( !$Voted )
{
    $vote = new eZVote();
    $vote->setPollID( $PollID );
    $vote->setChoiceID( $ChoiceID );
    $vote->setVotingIP( $REMOTE_ADDR );
    if ( $pollUser )
        $vote->setUserID( $pollUser->id() );
    if ( !$ChoiceID == 0 )
    $vote->store();
}

eZHTTPTool::header( "Location: /poll/result/" . $PollID );
exit();

?>
