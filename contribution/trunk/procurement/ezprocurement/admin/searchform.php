<?php
// 
// $Id: searchform.php,v 1.2 2001/09/11 21:12:16 fh Exp $
//
// Created on: <08-Sep-2001 10:32:19 fh>
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
include_once( "classes/ezlocale.php" );

include_once( "ezrfp/classes/ezrfpcategory.php" );
include_once( "ezrfp/classes/ezrfp.php" );

include_once( "ezuser/classes/ezauthor.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );
$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZrfpMain", "Language" );

$t = new eZTemplate( "ezrfp/admin/" . $ini->read_var( "eZrfpMain", "AdminTemplateDir" ),
                     "ezrfp/admin/intl/", $Language, "searchform.php" );

$t->setAllStrings();

$t->set_file( "search_form_tpl", "searchform.tpl" );
$t->set_block( "search_form_tpl", "category_item_tpl", "category_item" );
$t->set_block( "search_form_tpl", "author_item_tpl", "author_item" );
// $t->set_block( "search_form_tpl", "photographer_item_tpl", "photographer_item" );

// put authors and photographers into authors list
//$author = new eZAuthor();

$author = new eZUser();
//$author = new eZUserGroup(4);
$authorArray = $author->getAll();
//$authorArray = $author->getUsersByGroup(2);

//v_array($authorArray[1]);

foreach ( $authorArray as $author )
{
    $t->set_var( "author_id", $author->id() );
    $t->set_var( "author_name", $author->name() );
    $t->parse( "author_item", "author_item_tpl", true );
    
    //    $t->set_var( "photographer_id", $author->id() );
    //    $t->set_var( "photographer_name", $author->name() );
    //    $t->parse( "photographer_item", "photographer_item_tpl", true );
}


$tree = new eZrfpCategory();
$treeArray =& $tree->getTree();
$user =& eZUser::currentUser();

$catCount = count( $treeArray );
$t->set_var( "num_select_categories", min( $catCount + 1, 10 ) );

foreach ( $treeArray as $catItem )
{
    if ( eZObjectPermission::hasPermission( $catItem[0]->id(), "rfp_category", 'w', $user ) == true  ||
         eZrfpCategory::isOwner( eZUser::currentUser(), $catItem[0]->id() ) )
    {    
        $t->set_var( "category_id", $catItem[0]->id() );
        $t->set_var( "option_name", $catItem[0]->name() );

        if ( $catItem[1] > 1 )
            $t->set_var( "option_level", str_repeat( "&nbsp;&nbsp;", $catItem[1] ) );
        else
            $t->set_var( "option_level", "" );

        $t->parse( "category_item", "category_item_tpl", true );
    }
}

$t->pparse( "output", "search_form_tpl" );
?>
