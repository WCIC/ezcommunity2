<?php
// 
// $Id: categoryedit.php,v 1.1 2000/11/16 15:26:52 bf-cvs Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <16-Nov-2000 13:02:32 bf>
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

include_once( "eznewsfeed/classes/eznews.php" );

include_once( "classes/ezdatetime.php" );

include_once( "eznewsfeed/classes/eznews.php" );
include_once( "eznewsfeed/classes/eznewscategory.php" );

if ( $Action == "Insert" )
{
    $category = new eZNewsCategory();
    $category->setName( $CategoryName );
    $category->setDescription( $CategoryDescription );
    $category->store();
}

$news = new eZNews( );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZNewsFeedMain", "Language" );
$ImageDir = $ini->read_var( "eZNewsFeedMain", "ImageDir" );

$t = new eZTemplate( "eznewsfeed/admin/" . $ini->read_var( "eZNewsFeedMain", "AdminTemplateDir" ),
                     "eznewsfeed/admin/intl/", $Language, "categoryedit.php" );

$t->setAllStrings();

$t->set_file( array(
    "category_edit_page_tpl" => "categoryedit.tpl"
    ) );

//  $t->set_block( "news_edit_page_tpl", "news_edit_tpl", "head_line" );

$t->set_var( "category_name_value", "" );
$t->set_var( "category_description_value", "" );
$t->set_var( "action_value", "Insert" );


$t->pparse( "output", "category_edit_page_tpl" );


?>



