<?
// 
// $Id: categoryedit.php,v 1.4 2001/01/22 14:42:59 jb Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Sep-2000 14:46:19 bf>
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



if ( isset( $Cancel ) )
{
    Header( "Location: /ad/archive/$categoryID/" );
    exit();
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZAdMain", "Language" );

include_once( "ezad/classes/ezad.php" );
include_once( "ezad/classes/ezadcategory.php" );


// Direct actions
if ( $Action == "Insert" )
{
    $parentCategory = new eZAdCategory();
    $parentCategory->get( $ParentID );

    $category = new eZAdCategory();
    $category->setName( $Name );
    $category->setParent( $parentCategory );
    $category->setDescription( $Description );

    
    $category->store();

    $categoryID = $category->id();

    Header( "Location: /ad/archive/$categoryID/" );
    exit();
}

if ( $Action == "Update" )
{
    $parentCategory = new eZAdCategory();
    $parentCategory->get( $ParentID );
    
    $category = new eZAdCategory();
    $category->get( $CategoryID );
    $category->setName( $Name );
    $category->setParent( $parentCategory );
    $category->setDescription( $Description );

    $category->store();

    $categoryID = $category->id();

    Header( "Location: /ad/archive/$categoryID/" );
    exit();
}

if ( $Action == "Delete" )
{
    $category = new eZAdCategory();
    $category->get( $CategoryID );

    $category->delete();
    
    Header( "Location: /ad/archive/" );
    exit();
}

$t = new eZTemplate( "ezad/admin/" . $ini->read_var( "eZAdMain", "AdminTemplateDir" ),
                     "ezad/admin/intl/", $Language, "categoryedit.php" );

$t->setAllStrings();

$t->set_file( array( "category_edit_tpl" => "categoryedit.tpl" ) );


$t->set_block( "category_edit_tpl", "value_tpl", "value" );
               
$category = new eZAdCategory();

$categoryArray = $category->getAll( );

$t->set_var( "description_value", "" );
$t->set_var( "name_value", "" );
$t->set_var( "action_value", "insert" );
$t->set_var( "category_id", "" );


// edit
if ( $Action == "Edit" )
{
    $category = new eZAdCategory();
    $category->get( $CategoryID );

    $t->set_var( "name_value", $category->name() );
    $t->set_var( "description_value", $category->description() );
    $t->set_var( "action_value", "update" );
    $t->set_var( "category_id", $category->id() );

    $parent = $category->parent();
    $parentID = $parent->id();

    if ( $category->excludeFromSearch() == true )
    {
        $t->set_var( "exclude_checked", "checked" );
    }
}

$category = new eZAdCategory();

$tree = $category->getTree();

foreach( $tree as $item )
{
    $t->set_var( "option_value", $item[0]->id() );
    $t->set_var( "option_name", $item[0]->name() );

    if ( $item[1] > 0 )
        $t->set_var( "option_level", str_repeat( "&nbsp;", $item[1] ) );
    else
        $t->set_var( "option_level", "" );
    
    if ( $item[0]->id() == $parentID )
    {
        $t->set_var( "selected", "selected" );
        $selected = true;
    }
    else
    {
        $t->set_var( "selected", "" );
    }            


    $t->parse( "value", "value_tpl", true );
}



$t->pparse( "output", "category_edit_tpl" );

?>
