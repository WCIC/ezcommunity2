<?php
// 
// $Id: image.php,v 1.10 2001/08/09 11:43:08 jb Exp $
//
// Created on: <14-Jun-2001 13:18:27 amos>
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

include_once( "ezimagecatalogue/classes/ezimage.php" );
include_once( "ezimagecatalogue/classes/ezimagevariation.php" );
include_once( "ezimagecatalogue/classes/ezimagecategory.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcarray.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcbool.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcint.php" );

if( $Command == "data" ) // Dump image info!
{
    unset( $width );
    unset( $height );
    if ( is_object( $Data["Size"] ) )
    {
        $size = $Data["Size"]->value();
        if ( is_object( $size["Width"] ) and is_object( $size["Height"] ) )
        {
            $width = $size["Width"]->value();
            $height = $size["Height"]->value();
        }
    }
    
    if ( isset( $width ) and isset( $height ) )
    {
        $writeGroups = eZObjectPermission::getGroups( $ID, "imagecatalogue_image", 'w', false );
        $readGroups = eZObjectPermission::getGroups( $ID, "imagecatalogue_image", 'r', false );

        $rgp = array();
        $wgp = array();
        foreach( $readGroups as $group )
            $rgp[] = new eZXMLRPCInt( $group );
        foreach( $writeGroups as $group )
            $wgp[] = new eZXMLRPCInt( $group );

        $image = new eZImage( $ID );
        $variation = $image->requestImageVariation( $width, $height, false, true );
        if ( !is_bool( $variation ) )
        {
            $size = 0;
            if ( $image->fileExists( true ) )
            {
                $imagePath =& $image->filePath( true );
                $size = eZFile::filesize( $imagePath );
                $user = $image->user();
                $user_id = get_class( $user ) == "ezuser" ? $user->id() : 0;

                $ret = array( 
                    "Name" => new eZXMLRPCString( $image->name() ),
                    "Caption" => new eZXMLRPCString( $image->caption() ),
                    "Description" => new eZXMLRPCString( $image->description() ),
                    "FileName" => new eZXMLRPCString( $image->fileName() ),
                    "OriginalFileName" => new eZXMLRPCString( $image->originalFileName() ),
                    "FileSize" => new eZXMLRPCInt( $size ),
                    "UserID" => new eZXMLRPCInt( $user_id ),
                    "PhotographerID" => new eZXMLRPCInt( $image->photographer( false ) ),
                    "ReadGroups" => new eZXMLRPCArray( $rgp ),
                    "WriteGroups" => new eZXMLRPCArray( $wgp ),
                    "Categories" => new eZXMLRPCArray( $image->categories(), "integer" ),
                    "WebURL" => new eZXMLRPCString( "/" . $variation->imagePath() ),
                    "Size" => createSizeStruct( $variation->width(), $variation->height() )
                    );
                $ReturnData = new eZXMLRPCStruct( $ret );
            }
            else
                $Error = createErrorMessage( EZERROR_CUSTOM, "Image $ID does not exist on disk",
                                             EZIMAGECATALOGUE_NONEXISTING_IMAGE );
        }
        else
            $Error = createErrorMessage( EZERROR_CUSTOM, "Couldn't convert image $ID",
                                         EZIMAGECATALOGUE_CONVERT_ERROR );
    }
    else
    {
        $Error = createErrorMessage( EZERROR_CUSTOM, "Missing width and height in image request",
                                     EZIMAGECATALOGUE_SIZE_MISSING );
    }
}
else if ( $Command == "storedata" )
{
    if ( isset( $Data["Title"] ) and isset( $Data["Caption"] ) and isset( $Data["Description"] ) and isset( $Data["PhotographerID"] ) )
    {
        $title = $Data["Title"]->value();
        $caption = $Data["Caption"]->value();
        $description = $Data["Description"]->value();
        $photographer = $Data["PhotographerID"]->value();
        $readgroups = $Data["ReadGroups"]->value();
        $writegroups = $Data["WriteGroups"]->value();
        $categories = $Data["Categories"]->value();
        $image = new eZImage();
        if ( $ID != 0 )
        {
            if ( !$image->get( $ID ) )
                $Error = createErrorMessage( EZERROR_NONEXISTING_OBJECT );
        }
        if ( !$Error )
        {
            $image->setName( $title );
            $image->setCaption( $caption );
            $image->setDescription( $description );
            $image->setPhotographer( $photographer );
            if ( isset( $Data["Image"] ) and isset( $Data["ImageFileName"] ) )
            {
                $image_data = $Data["Image"]->value();
                $orig_file = $Data["ImageFileName"]->value();
                $image_file = new eZImageFile();
                $image_file->dumpDataToFile( $image_data, $orig_file );
                if ( !$image->setImage( $image_file ) )
                {
                    $Error = createErrorMessage( EZERROR_CUSTOM, "Failed to set image sent by client to image $ID",
                                                 EZIMAGECATALOGUE_BAD_IMAGE );
                }
            }
            if ( !$Error )
            {
                $image->store();

                // categories...
                $old_categories =& $image->categories();
                if ( is_bool( $old_categories ) )
                    $old_categories = array();
                $new_categories = array();
                foreach( $categories as $cat )
                {
                    $new_categories[] = $cat->value();
                }
                $remove_categories = array_diff( $old_categories, $new_categories );
                $add_categories = array_diff( $new_categories, $old_categories );

                foreach ( $remove_categories as $categoryItem )
                {
                    eZImageCategory::removeImage( $image, $categoryItem );
                }
                foreach ( $add_categories as $categoryItem )
                {
                    eZImageCategory::addImage( $image, $categoryItem );
                }

                // permissions...
                eZObjectPermission::removePermissions( $ID, "imagecatalogue_image", 'r' );
                foreach( $readgroups as $readgroup )
                    eZObjectPermission::setPermission( $readgroup->value(), $ID, "imagecatalogue_image", 'r' );

                eZObjectPermission::removePermissions( $ID, "imagecatalogue_image", 'w' );
                foreach( $writegroups as $writegroup )
                    eZObjectPermission::setPermission( $writegroup->value(), $ID, "imagecatalogue_image", 'w' );

                $ID = $image->id();

                $ReturnData = new eZXMLRPCStruct( array( "Location" => createURLStruct( "ezimagecatalogue", "image", $ID ),
                                                         // TODO: Fix Path
                                                         "Path" => new eZXMLRPCArray( array() ),
                                                         "UpdateType" => new eZXMLRPCString( $Command )
                                                         )
                                                  );
                $Command = "update";
            }
        }
    }
    else
    {
        $Error = createErrorMessage( EZERROR_BAD_REQUEST_DATA );
    }
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
    $result =& eZImage::search( $texts, true );
    foreach( $result as $item )
    {
        $cat =& $item->categoryDefinition();
        $elements[] = new eZXMLRPCStruct( array( "Name" => new eZXMLRPCString( $item->name() ),
                                                 "CategoryName" => new eZXMLRPCString( $cat->name() ),
                                                 "Location" => createURLStruct( "ezimagecatalogue", "image", $item->id() ),
                                                 "CategoryLocation" => createURLStruct( "ezimagecatalogue", "category", $cat->id() ),
                                                 "HasPreview" => new eZXMLRPCBool( true ) ) );
    }
    $ret = array( "Elements" => new eZXMLRPCArray( $elements ) );
    handleSearchData( $ret );
    $ReturnData = new eZXMLRPCStruct( $ret );
}

?>
