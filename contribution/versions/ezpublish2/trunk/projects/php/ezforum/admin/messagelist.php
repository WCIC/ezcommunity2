<?
// $Id: messagelist.php,v 1.13 2001/03/01 14:06:25 jb Exp $
//
// Author: Lars Wilhelmsen <lw@ez.no>
// Created on: Created on: <18-Jul-2000 08:56:19 lw>
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
$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZForumMain", "Language" );

include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );

include_once( "ezforum/classes/ezforummessage.php" );
include_once( "ezforum/classes/ezforum.php" );
include_once( "ezforum/classes/ezforumcategory.php" );

require( "ezuser/admin/admincheck.php" );

$t = new eZTemplate( "ezforum/admin/" . $ini->read_var( "eZForumMain", "AdminTemplateDir" ),
                     "ezforum/admin/" . "/intl", $Language, "messagelist.php" );
$t->setAllStrings();

$t->set_file( Array( "message_page" => "messagelist.tpl" ) );

$t->set_block( "message_page", "message_item_tpl", "message_item" );

$t->set_var( "site_style", $SiteStyle );

$forum = new eZForum( $ForumID );
$t->set_var( "forum_name", $forum->name() );

$categories = $forum->categories();
if ( count( $categories ) > 0 )
{
    $category = new eZForumCategory( $categories[0]->id() );

    $t->set_var( "category_name", $category->name() );
}

$locale = new eZLocale( $Language );

if ( !isset( $Offset ) )
    $Offset = 0;

if ( !isset( $Limit ) )
    $Limit = 30;

$messages = $forum->messageTree( $Offset, $Limit );

$languageIni = new INIFile( "ezforum/admin/" . "intl/" . $Language . "/messagelist.php.ini", false );
$true =  $languageIni->read_var( "strings", "true" );
$false =  $languageIni->read_var( "strings", "false" );

if ( !$messages )
{
    $noitem = $languageIni->read_var( "strings", "noitem" );
    $t->set_var( "message_item", $noitem );
}
else
{

    $level = 0;
    $i = 0;
    foreach ( $messages as $message )
        {
            if ( ( $i % 2 ) == 0 )
                $t->set_var( "td_class", "bglight" );
            else
                $t->set_var( "td_class", "bgdark" );
    
            $level = $message->depth();
    
            if ( $level > 0 )
                $t->set_var( "spacer", str_repeat( "&nbsp;", $level ) );
            else
                $t->set_var( "spacer", "" );
    
            $t->set_var( "message_topic", $message->topic() );

            $t->set_var( "message_postingtime", $locale->format( $message->postingTime() ) );

            $t->set_var( "message_id", $message->id() );

            $user = $message->user();
    
            $t->set_var( "message_user", $user->firstName() . " " . $user->lastName() );

            if( $message->emailNotice() == "Y" )
                $t->set_var( "emailnotice", $true );
            else
                $t->set_var( "emailnotice", $false );


            $t->set_var( "limit", $Limit );
            $t->set_var( "prev_offset", $Offset - $Limit );
            $t->set_var( "next_offset", $Offset + $Limit );    
    
            $t->parse( "message_item", "message_item_tpl", true );
            $i++;
        }
} 

$t->set_var( "link1-url", "");
$t->set_var( "link2-url", "search.php");

$t->set_var( "back-url", "admin/forum.php" );
$t->set_var( "category_id", $CategoryID );
$t->set_var( "forum_id", $ForumID );

$t->pparse( "output", "message_page" );
?>
