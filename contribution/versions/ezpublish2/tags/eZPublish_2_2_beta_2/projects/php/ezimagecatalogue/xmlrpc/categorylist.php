<?php
//
// $Id: categorylist.php,v 1.7 2001/09/08 11:15:03 jb Exp $
//
// Created on: <26-Oct-2000 19:40:18 bf>
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

include_once( "ezimagecatalogue/classes/ezimagecategory.php" );
include_once( "ezimagecatalogue/classes/ezimage.php" );

if ( $Command == "list" )
{
    $list_categories = false;
    $list_images = false;
    if ( is_object( $Data["ListType"] ) )
    {
        $ListType = $Data["ListType"]->value();
        if ( is_object( $ListType["Catalogues"] ) )
            $list_categories = true;
        if ( is_object( $ListType["Elements"] ) )
            $list_images = true;
    }

    if ( !$list_categories and !$list_images )
    {
        $list_categories = true;
        $list_images = true;
    }

    $offset = 0;
    $max = -1;
    $total = 0;

    if ( is_object( $Data["Part"] ) )
    {
        $Part = $Data["Part"]->value();
        $offset = $Part["Offset"]->value();
        $max = $Part["Max"]->value();
//      eZLog::writeNotice( "Article: Offset: $offset, Max: $max" );
    }

    $category = new eZImageCategory( $ID );

    $loc_max = $max;
    $loc_offset = $offset;

    $cat = array();
    if ( $list_categories )
    {
        $categoryCount = $category->countByParent( $category );
        $total += $categoryCount;
        if ( $loc_offset < $categoryCount )
        {
            $categoryList =& $category->getByParent( $category, $loc_offset, $loc_max );
            $loc_max -= count( $categoryList );

            foreach ( $categoryList as $catItem )
            {
                $cat[] = new eZXMLRPCStruct( array( "URL" => createURLStruct( "ezimagecatalogue",
                                                                              "category",
                                                                              $catItem->id() ),
                                                    "Name" => new eZXMLRPCString( $catItem->name( false ) )
                                                    )
                                             );
            }
        }
        $loc_offset = max( 0, $loc_offset - $categoryCount );
    }

    $art = array();
    if ( $list_images )
    {
        $imageCount = $category->imageCount();
        $total += $imageCount;
        if ( $loc_max > 0 and $loc_offset >= 0 )
        {
            $imageList =& $category->images( "time", $loc_offset, $loc_max );
            foreach( $imageList as $imageItem )
            {
                $art[] = new eZXMLRPCStruct( array( "URL" => createURLStruct( "ezimagecatalogue",
                                                                              "image",
                                                                              $imageItem->id() ),
                                                    "Name" => new eZXMLRPCString( $imageItem->name( false ) ),
                                                    "Thumbnail" => new eZXMLRPCBool( true )
                                                    )
                                             );
            }
        }
//      if ( $offset > 0 )
//          usleep( 5000000 );
    }

    $par = array();
    if ( $offset == 0 )
    {
        $path =& $category->path();
        if ( $category->id() != 0 )
        {
            $par[] = createURLStruct( "ezimagecatalogue", "category", 0 );
        }
        else
        {
            $par[] = createURLStruct( "ezimagecatalogue", "" );
        }
        foreach( $path as $item )
        {
            if ( $item[0] != $category->id() )
                $par[] = createURLStruct( "ezimagecatalogue", "category", $item[0] );
        }
    }

    $part_arr = array( "Offset" => new eZXMLRPCInt( $offset ),
                       "Total" => new eZXMLRPCInt( $total ) );
    if ( $offset == 0 )
    {
        $part_arr["Begin"] = new eZXMLRPCBool( true );
    }
    if ( $total == $offset + count( $cat ) + count( $art ) )
    {
        $part_arr["End"] = new eZXMLRPCBool( true );
    }
    $part = new eZXMLRPCStruct( $part_arr );

    $ReturnData = new eZXMLRPCStruct( array( "Catalogues" => $cat,
                                             "Elements" => $art,
                                             "Path" => $par,
                                             "Part" => $part ) ); // array starting with top level catalogue, ending with parent.
}
else if ( $Command == "tree" )
{
    $cat = new eZImageCategory();
    $tree =& categoryTree( $cat );
    $ReturnData = createTreeStruct( $tree, "ezimagecatalogue", "category" );
}
else if ( $Command == "search" )
{
    $keywords = $Data["Keywords"]->value();
    $texts = array();
    foreach( $keywords as $keyword )
    {
        $texts[] = $keyword->value();
    }
    $elements = array();
    $result =& eZImageCategory::search( $texts, false, "name", $User );
    foreach( $result as $item )
    {
        $catid = $item->parent( false );
        $cat = new eZImageCategory();
        $cat->get( $catid );
        $element = array();
        $element["Name"] = new eZXMLRPCString( $item->name( false ) );
        $element["CategoryName"] = new eZXMLRPCString( $cat->name( false ) );
        $element["Location"] =  createURLStruct( "ezimagecatalogue", "category", $item->id() );
        $element["CategoryLocation"] = createURLStruct( "ezimagecatalogue", "category", $catid );
        $element["WebURL"] = new eZXMLRPCString( "/imagecatalogue/imageview/$itemid/" );
        $element["CategoryWebURL"] = new eZXMLRPCString( "/imagecatalogue/image/list/$catid/" );
        $elements[] = new eZXMLRPCStruct( $element );
    }
    $ret = array( "Elements" => new eZXMLRPCArray( $elements ) );
    handleSearchData( $ret );
    $ReturnData = new eZXMLRPCStruct( $ret );
}

function &categoryTree( $cat )
{
    $children =& eZImageCategory::getByParent( $cat );
    $child_array = array();
    foreach( $children as $child )
    {
        $child_array[] = categoryTree( $child );
    }
    $item = array( "ID" => $cat->id(),
                   "Name" => $cat->name( false ),
                   "Children" => $child_array );
    return $item;
}
?>
