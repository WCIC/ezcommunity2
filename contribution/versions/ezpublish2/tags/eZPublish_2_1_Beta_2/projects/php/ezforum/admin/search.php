<?
// 
// $Id: search.php,v 1.7 2001/03/05 13:46:35 pkej Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <12-Oct-2000 20:33:02 bf>
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

include_once( "classes/INIFile.php" );
include_once( "ezforum/classes/ezforum.php" );

include_once( "classes/ezlocale.php" );

include_once( "ezuser/classes/ezuser.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZForumMain", "Language" );

include_once( "classes/eztemplate.php" );


$t = new eZTemplate( "ezforum/admin/" . $ini->read_var( "eZForumMain", "AdminTemplateDir" ),
                     "ezforum/admin/intl", $Language, "search.php" );

$t->setAllStrings();

$t->set_file( "search_tpl", "search.tpl" );

$t->set_block( "search_tpl", "message_tpl", "message" );

$t->set_block( "search_tpl", "empty_result_tpl", "empty_result" );
$t->set_block( "search_tpl", "search_result_tpl", "search_result" );

$t->set_block( "search_tpl", "previous_tpl", "previous" );
$t->set_block( "search_tpl", "next_tpl", "next" );
$t->set_var( "site_style", $SiteStyle );


if ( isSet( $URLQueryString ) )
{
    $QueryString = urldecode( $URLQueryString );
}

$t->set_var( "query_string", $QueryString );

$t->set_var( "previous", "" );
$t->set_var( "next", "" );

$t->set_var( "search_result", "" );


if ( $QueryString != "" )
{
    $t->set_var( "query_string", $QueryString );

    if ( !isset( $Offset ) )
        $Offset = 0;

    if ( !isset( $Limit ) )
        $Limit = 30;

$t->set_var( "this_offset", "$Offset" );
$t->set_var( "this_limit", "$Limit" );

    $forum = new eZForum();
    
    // do a search in all forums
    $messages = $forum->search( $QueryString, $Offset, $Limit );
    $total_count = $forum->getQueryCount( $QueryString );

    $locale = new eZLocale( $Language );

    $level = 0;
    $i = 0;

    $AnonymousPoster = $ini->read_var( "eZForumMain", "AnonymousPoster" );

    foreach ( $messages as $message )
    {
        if ( ( $i % 2 ) == 0 )
            $t->set_var( "td_class", "bglight" );
        else
            $t->set_var( "td_class", "bgdark" );
    
        $t->set_var( "message_topic", $message->topic() );
        
        $t->set_var( "postingtime", $locale->format( $message->postingTime() ) );

        $t->set_var( "message_id", $message->id() );
        
        $user = $message->user();
        
        if( $user->id() != 0 )
        {
            $t->set_var( "user", $user->firstName() . " " . $user->lastName() );
        }
        else
        {
            $t->set_var( "user", $AnonymousPoster );
        }

        $prevOffs = $Offset - $Limit;
        $nextOffs = $Offset + $Limit;
        
        if ( $prevOffs >= 0 )
        {
            $t->set_var( "prev_offset", $prevOffs  );
            $t->parse( "previous", "previous_tpl" );
        }
        else
        {
        $t->set_var( "previous", "" );
        }
        
        if ( $nextOffs <= $total_count )
        {
            $t->set_var( "next_offset", $nextOffs  );
            $t->parse( "next", "next_tpl" );
        }
        else
        {
            $t->set_var( "next", "" );
        }
        
        $t->set_var( "limit", $Limit );
        
        $t->parse( "message", "message_tpl", true );
        $i++;
    }
    
    if ( count( $messages ) == 0 )
    {
        $t->parse( "empty_result", "empty_result_tpl" );
    }
    else
    {
        $t->parse( "search_result", "search_result_tpl", true );
        $t->set_var( "empty_result", "" );
    }
}
else
{
    $t->parse( "empty_result", "empty_result_tpl" );
} 

$t->set_var( "url_query_string", urlencode( $QueryString ) );

$t->pparse("output","search_tpl");
?>
