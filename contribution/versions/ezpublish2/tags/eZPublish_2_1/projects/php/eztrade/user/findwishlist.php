<?php
//
// $Id: findwishlist.php,v 1.3 2001/05/14 15:31:15 fh Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <15-Jan-2001 16:46:13 bf>
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
include_once( "classes/ezlocale.php" );

include_once( "ezuser/classes/ezuser.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTradeMain", "Language" );

include_once( "eztrade/classes/ezwishlist.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once( "ezmail/classes/ezmail.php" );



$t = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                     "eztrade/user/intl/", $Language, "findwishlist.php" );

$t->setAllStrings();

$t->set_file( array(
    "findwishlist_page_tpl" => "findwishlist.tpl"
    ) );

$t->set_block( "findwishlist_page_tpl", "wishlist_tpl", "wishlist" );

$wishlist = new eZWishList();

$wishLists = $wishlist->search( $SearchText );

$t->set_var( "search_text", $SearchText );

$t->set_var( "wishlist", "" );

$i=0;
foreach ( $wishLists as $wishlist )
{
    if ( ( $i % 2 ) == 0 )
        $t->set_var( "td_class", "bglight" );
    else
        $t->set_var( "td_class", "bgdark" );
    
    $user = $wishlist->user();
    
    $t->set_var( "user_id", $user->id() );
    $t->set_var( "first_name", $user->firstName() );
    $t->set_var( "last_name", $user->lastName() );

    $t->parse( "wishlist", "wishlist_tpl", true );
    $i++;
}

    

$t->pparse( "output", "findwishlist_page_tpl" );

?>

