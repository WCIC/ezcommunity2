<?
// 
// $Id: categoryedit.php,v 1.10 2001/03/04 15:00:28 fh Exp $
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

include_once( "classes/ezhttptool.php" );

if ( isset( $Cancel ) )
{
    eZHTTPTool::header( "Location: /article/archive/$categoryID/" );
    exit();
}

if ( isset ( $DeleteCategories ) )
{
    $Action = "DeleteCategories";
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezcachefile.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZArticleMain", "Language" );

include_once( "ezarticle/classes/ezarticlecategory.php" );

// Direct actions
if ( $Action == "insert" )
{
    // clear the menu cache
    if ( file_exists( "ezarticle/cache/menubox.cache" ) )
        unlink( "ezarticle/cache/menubox.cache" );

    $parentCategory = new eZArticleCategory();
    $parentCategory->get( $ParentID );

    $category = new eZArticleCategory();
    $category->setName( $Name );
    $category->setParent( $parentCategory );
    $category->setDescription( $Description );

    $category->setSortMode( $SortMode );
    
    if ( $ExcludeFromSearch == "on" )
    {
        $category->setExcludeFromSearch( true );
    }
    else
    {
        $category->setExcludeFromSearch( false );
    }

    $category->setOwner( eZUser::currentUser() );
    
    $category->store();
    $categoryID = $category->id();
    
    /* write access select */
    if( isset( $WriteGroupArray ) )
    {
        if( $WriteGroupArray[0] == 0 )
        {
            eZObjectPermission::setPermission( -1, $categoryID, "article_category", 'w' );
        }
        else
        {
            eZObjectPermission::removePermissions( $categoryID, "article_category", 'w' ); 
            foreach( $WriteGroupArray as $groupID )
            {
                eZObjectPermission::setPermission( $groupID, $categoryID, "article_category", 'w' );
            }
        }
    }
    else
    {
        eZObjectPermission::removePermissions( $categoryID, "article_category", 'w' );
    }

    /* read access thingy */
    if ( isset( $GroupArray ) )
    {
        if( $GroupArray[0] == 0 )
        {
            eZObjectPermission::setPermission( -1, $categoryID, "article_category", 'r' );
        }
        else // some groups are selected.
        {
            eZObjectPermission::removePermissions( $categoryID, "article_category", 'r' ); 
            foreach ( $GroupArray as $groupID )
            {
                eZObjectPermission::setPermission( $groupID, $categoryID, "article_category", 'r' );
            }
        }
    }
    else
    {
        eZObjectPermission::removePermissions( $categoryID, "article_category", 'r' );
    }

    

    $files =& eZCacheFile::files( "ezarticle/cache/",
                                  array( "articlelist", $ParentID, NULL ),
                                  "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }

    eZHTTPTool::header( "Location: /article/archive/$categoryID/" );
    exit();
}

if ( $Action == "update" )
{
    // clear the menu cache
    if ( file_exists( "ezarticle/cache/menubox.cache" ) )
        unlink( "ezarticle/cache/menubox.cache" );
    
    $parentCategory = new eZArticleCategory();
    $parentCategory->get( $ParentID );
    
    $category = new eZArticleCategory();
    $category->get( $CategoryID );
    $category->setName( $Name );
    $category->setParent( $parentCategory );
    $category->setDescription( $Description );

    $category->setSortMode( $SortMode );

    if ( $ExcludeFromSearch == "on" )
    {
        $category->setExcludeFromSearch( true );
    }
    else
    {
        $category->setExcludeFromSearch( false );
    }

//    $ownerGroup = new eZUserGroup( $OwnerID );
//    if( isset( $Recursive ) )
//        $category->setOwnerGroup( $ownerGroup, true );
//    else
//        $category->setOwnerGroup( $ownerGroup, false );

    $categoryID = $category->id();
    /* write access select */
    if( isset( $WriteGroupArray ) )
    {
        if( $WriteGroupArray[0] == 0 )
        {
            eZObjectPermission::setPermission( -1, $categoryID, "article_category", 'w' );
        }
        else
        {
            eZObjectPermission::removePermissions( $categoryID, "article_category", 'w' ); //not really necessary..
            foreach( $WriteGroupArray as $groupID )
            {
                eZObjectPermission::setPermission( $groupID, $categoryID, "article_category", 'w' );
            }
        }
    }
    else
    {
        eZObjectPermission::removePermissions( $categoryID, "article_category", 'w' );
    }

    /* read access thingy */
    if ( isset( $GroupArray ) )
    {
        if( $GroupArray[0] == 0 )
        {
            eZObjectPermission::setPermission( -1, $categoryID, "article_category", 'r' );
        }
        else // some groups are selected.
        {
            eZObjectPermission::removePermissions( $categoryID, "article_category", 'r' ); 
            foreach ( $GroupArray as $groupID )
            {
                eZObjectPermission::setPermission( $groupID, $categoryID, "article_category", 'r' );
            }
        }
    }
    else
    {
        eZObjectPermission::removePermissions( $categoryID, "article_category", 'r' );
    }
    
    $category->store();

    $categoryID = $category->id();
    $files =& eZCacheFile::files( "ezarticle/cache/",
                                  array( "articlelist", array( $CategoryID, $ParentID ), NULL ),
                                  "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }
    eZHTTPTool::header( "Location: /article/archive/$ParentID/" );
    exit();
}

if ( $Action == "delete" )
{
    // clear the menu cache
    if ( file_exists( "ezarticle/cache/menubox.cache" ) )
        unlink( "ezarticle/cache/menubox.cache" );

    $category = new eZArticleCategory();
    $category->get( $CategoryID );

    $files =& eZCacheFile::files( "ezarticle/cache/",
                                  array( "articlelist",
                                         array( $CategoryID, $category->parent( false ) ), NULL ),
                                  "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }

    $category->delete();

    eZHTTPTool::header( "Location: /article/archive/" );
    exit();
}

if ( $Action == "DeleteCategories" )
{
    if ( count ( $CategoryArrayID ) != 0 )
    {
        if ( file_exists( "ezarticle/cache/menubox.cache" ) )
            unlink( "ezarticle/cache/menubox.cache" );

        $categories = array();
        foreach( $CategoryArrayID as $ID )
        {
            $categories[] = $ID;
            $category = new eZArticleCategory( $ID );
            $categories[] = $category->parent( false );
            if( eZObjectPermission::hasPermission( $ID , "article_category", 'w' ) )
                $category->delete();
        }
        $categories = array_unique( $categories );
        $files =& eZCacheFile::files( "ezarticle/cache/",
                                      array( "articlelist",
                                             $categories, NULL ),
                                      "cache", "," );
        foreach( $files as $file )
        {
            $file->delete();
        }
    }

    eZHTTPTool::header( "Location: /article/archive/" );
    exit();
}


$t = new eZTemplate( "ezarticle/admin/" . $ini->read_var( "eZArticleMain", "AdminTemplateDir" ),
                     "ezarticle/admin/intl/", $Language, "categoryedit.php" );

$t->setAllStrings();

$t->set_file( array( "category_edit_tpl" => "categoryedit.tpl" ) );


$t->set_block( "category_edit_tpl", "value_tpl", "value" );
$t->set_block( "category_edit_tpl", "category_owner_tpl", "category_owner" );
$t->set_block( "category_edit_tpl", "group_item_tpl", "group_item" );

$category = new eZArticleCategory();

$categoryArray = $category->getAll( );

$t->set_var( "description_value", "" );
$t->set_var( "name_value", "" );
$t->set_var( "action_value", "insert" );
$t->set_var( "exclude_checked", "" );
$t->set_var( "all_selected", "selected" );
$t->set_var( "all_write_selected", "selected" );
$writeGroupsID = array(); 
$readGroupsID = array(); 


$t->set_var( "1_selected", "" );
$t->set_var( "2_selected", "" );
$t->set_var( "3_selected", "" );
$t->set_var( "4_selected", "" );

// edit
if ( $Action == "edit" )
{
    $category = new eZArticleCategory();
    $category->get( $CategoryID );

    $t->set_var( "name_value", $category->name() );
    $t->set_var( "description_value", $category->description() );
    $t->set_var( "action_value", "update" );
    $t->set_var( "category_id", $category->id() );
    $parent = $category->parent();

    // set the current sortmode to selected
    $t->set_var( $category->sortMode( true ) . "_selected", "selected" );    

    if( is_object( $parent ) )
    {
        $parentID = $parent->id();
    }

    if ( $category->excludeFromSearch() == true )
    {
        $t->set_var( "exclude_checked", "checked" );
    }

    $writeGroupsID = eZObjectPermission::getGroups( $CategoryID, "article_category", 'w' , false );
    $readGroupsID = eZObjectPermission::getGroups( $CategoryID, "article_category", 'r', false );

    if( $writeGroupsID[0] != -1 )
        $t->set_var( "all_write_selected", "" );
    if( $readGroupsID[0] != -1 )
        $t->set_var( "all_selected", "" );
}

$category = new eZArticleCategory();

$tree = $category->getTree();

foreach( $tree as $item )
{
    if( eZObjectPermission::hasPermission( $item[0]->id(), "article_category", 'w' ) )
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
}

// group selector
$group = new eZUserGroup();
$groupList = $group->getAll();

$t->set_var( "selected", "" );
foreach( $groupList as $groupItem )
{
    /* for the group owner selector */
    $t->set_var( "module_owner_id", $groupItem->id() );
    $t->set_var( "module_owner_name", $groupItem->name() );

    if( in_array( $groupItem->id(), $writeGroupsID ) )
        $t->set_var( "is_selected", "selected" );
    else
        $t->set_var( "is_selected", "" );
    
    $t->parse( "category_owner", "category_owner_tpl", true );
        
    /* for the read access groups selector */
    $t->set_var( "group_name", $groupItem->name() );
    $t->set_var( "group_id", $groupItem->id() );

    if( in_array( $groupItem->id() , $readGroupsID ) )
        $t->set_var( "selected", "selected" );
    else
        $t->set_var( "selected", "" );
        
    $t->parse( "group_item", "group_item_tpl", true );
}

$t->pparse( "output", "category_edit_tpl" );

?>
