<?
// 
// $Id: categorylist.php,v 1.19 2001/03/01 14:06:26 jb Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <13-Sep-2000 14:56:11 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
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
include_once( "classes/ezcurrency.php" );
include_once( "classes/ezcachefile.php" );
include_once( "classes/ezlist.php" );

function deleteCache( $ProductID, $CategoryID, $CategoryArray )
{
    if ( get_class( $ProductID ) == "ezproduct" )
    {
        $CategoryID =& $ProductID->categoryDefinition( false );
        $CategoryArray =& $ProductID->categories( false );
        $ProductID = $ProductID->id();
    }

    $files = eZCacheFile::files( "eztrade/cache/", array( "productlist",
                                                          array_merge( $CategoryID, $CategoryArray ) ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }
    $files = eZCacheFile::files( "eztrade/cache/", array( "hotdealslist" ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }
}

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTradeMain", "Language" );
$Limit = $ini->read_var( "eZTradeMain", "ProductLimit" );

include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "eztrade/classes/ezproduct.php" );

$t = new eZTemplate( "eztrade/admin/" . $ini->read_var( "eZTradeMain", "AdminTemplateDir" ) . "/categorylist/",
                     "eztrade/admin/intl/", $Language, "categorylist.php" );

$t->setAllStrings();

$t->set_file( array(
    "category_list_page_tpl" => "categorylist.tpl"
    ) );

// path
$t->set_block( "category_list_page_tpl", "path_item_tpl", "path_item" );

// category
$t->set_block( "category_list_page_tpl", "category_list_tpl", "category_list" );
$t->set_block( "category_list_tpl", "category_item_tpl", "category_item" );

// product
$t->set_block( "category_list_page_tpl", "product_list_tpl", "product_list" );
$t->set_block( "product_list_tpl", "product_item_tpl", "product_item" );
$t->set_block( "product_item_tpl", "product_active_item_tpl", "product_active_item" );
$t->set_block( "product_item_tpl", "product_inactive_item_tpl", "product_inactive_item" );

// move up / down
$t->set_block( "product_list_tpl", "absolute_placement_header_tpl", "absolute_placement_header" );
$t->set_block( "product_item_tpl", "absolute_placement_item_tpl", "absolute_placement_item" );

$t->set_var( "site_style", $SiteStyle );

$category = new eZProductCategory();
$category->get( $ParentID );

// move products  up / down

if ( $category->sortMode() == "absolute_placement" )
{
    if ( is_numeric( $MoveUp ) )
    {
        $category->moveUp( $MoveUp );
        deleteCache( $MoveUp, false, false );
    }
    if ( is_numeric( $MoveDown ) )
    {
        $category->moveDown( $MoveDown );
        deleteCache( $MoveDown, false, false );
    }
}

// path
$pathArray =& $category->path();

$t->set_var( "path_item", "" );
foreach ( $pathArray as $path )
{
    $t->set_var( "category_id", $path[0] );

    $t->set_var( "category_name", $path[1] );
    
    $t->parse( "path_item", "path_item_tpl", true );
}

$categoryList =& $category->getByParent( $category );

// categories
$i=0;
$t->set_var( "category_list", "" );
foreach ( $categoryList as $categoryItem )
{
    $t->set_var( "category_id", $categoryItem->id() );

    $t->set_var( "category_name", $categoryItem->name() );

    $parent = $categoryItem->parent();
    

    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }
    $t->set_var( "category_description", $categoryItem->description() );

    $t->parse( "category_item", "category_item_tpl", true );
    $i++;
}

if ( count( $categoryList ) > 0 )
    $t->parse( "category_list", "category_list_tpl" );
else
    $t->set_var( "category_list", "" );

if ( !isset( $Limit ) or !is_numeric( $Limit ) )
    $Limit = 10;
if ( !isset( $Offset ) or !is_numeric( $Offset ) )
    $Offset = 0;

// products
$TotalTypes =& $category->productCount( $category->sortMode(), true );
$productList =& $category->products( $category->sortMode(), true, $Offset, $Limit );

$locale = new eZLocale( $Language );
$i=0;
$t->set_var( "product_list", "" );

if ( $category->sortMode() == "absolute_placement" )
{
    $t->parse( "absolute_placement_header", "absolute_placement_header_tpl" );
}
else
{
    $t->set_var( "absolute_placement_header", "" );
}

foreach ( $productList as $product )
{
    $t->set_var( "td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );

    $t->set_var( "product_name", $product->name() );

    $t->set_var( "product_price", "" );
    if ( $product->hasPrice() )
    {
        $price = new eZCurrency( $product->price() );

        $t->set_var( "product_price", $locale->format( $price ) );
    }
    $t->set_var( "product_active_item", "" );
    $t->set_var( "product_inactive_item", "" );
    if ( $product->showProduct() )
    {
        $t->parse( "product_active_item", "product_active_item_tpl" );
    }
    else
    {
        $t->parse( "product_inactive_item", "product_inactive_item_tpl" );
    }
    $t->set_var( "product_id", $product->id() );

    $t->set_var( "category_id", $category->id() );

    if ( $category->sortMode() == "absolute_placement" )
    {
        $t->parse( "absolute_placement_item", "absolute_placement_item_tpl" );
    }
    else
    {
        $t->set_var( "absolute_placement_item", "" );
    }

    $t->parse( "product_item", "product_item_tpl", true );
    $i++;
}

$t->set_var( "offset", $Offset );

eZList::drawNavigator( $t, $TotalTypes, $Limit, $Offset, "product_list_tpl" );

if ( count( $productList ) > 0 )    
    $t->parse( "product_list", "product_list_tpl" );
else
    $t->set_var( "product_list", "" );

$t->pparse( "output", "category_list_page_tpl" );

?>
