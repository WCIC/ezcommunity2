<?
// 
// $Id: imagelist.php,v 1.18 2001/03/06 16:04:35 fh Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <10-Dec-2000 16:16:20 bf>
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
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezpermission.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );

include_once( "ezimagecatalogue/classes/ezimage.php" );
include_once( "ezimagecatalogue/classes/ezimagecategory.php" );
include_once( "classes/ezhttptool.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZImageCatalogueMain", "Language" );

$ImageDir = $ini->read_var( "eZImageCatalogueMain", "ImageDir" );

$t = new eZTemplate( "ezimagecatalogue/user/" . $ini->read_var( "eZImageCatalogueMain", "TemplateDir" ),
                     "ezimagecatalogue/user/intl/", $Language, "imagelist.php" );

$t->set_file( "image_list_page_tpl", "imagelist.tpl" );

$t->setAllStrings();

$user = eZUser::currentUser();

// Set detail or normal mode
if ( isSet ( $DetailView ) )
{
    $session =& eZSession::globalSession();
    $session->setVariable( "ImageViewMode", "Detail" );
}
if ( isSet ( $NormalView ) )
{
    $session =& eZSession::globalSession();
    $session->setVariable( "ImageViewMode", "Normal" );
}

$checkMode =& eZSession::globalSession();

if ( $checkMode->variable( "ImageViewMode" ) == "Detail" )
{
    $DetailView = true;
}
else if ( $checkMode->variable( "ImageViewMode" ) == "Normal" )
{
    $NormalView = true;
}

$t->set_block( "image_list_page_tpl", "current_category_tpl", "current_category" );

// path
$t->set_block( "image_list_page_tpl", "path_item_tpl", "path_item" );

$t->set_block( "image_list_page_tpl", "image_list_tpl", "image_list" );

$t->set_block( "image_list_page_tpl", "normal_view_button", "normal_button" );
$t->set_block( "image_list_page_tpl", "detail_view_button", "detail_button" );

$t->set_block( "image_list_page_tpl", "write_menu_tpl", "write_menu" );

$t->set_block( "write_menu_tpl", "default_new_tpl", "default_new" );
$t->set_block( "write_menu_tpl", "default_delete_tpl", "default_delete" );

$t->set_block( "default_delete_tpl", "delete_categories_button_tpl", "delete_categories_button" );
$t->set_block( "default_delete_tpl", "delete_images_button_tpl", "delete_images_button" );

$t->set_block( "image_list_tpl", "image_tpl", "image" );
$t->set_block( "image_list_tpl", "detail_view_tpl", "detail_view" );

$t->set_block( "image_tpl", "read_tpl", "read" );
$t->set_block( "image_tpl", "write_tpl", "write" );

$t->set_block( "detail_view_tpl", "detail_read_tpl", "detail_read" );
$t->set_block( "detail_view_tpl", "detail_write_tpl", "detail_write" );

$t->set_block( "image_list_page_tpl", "category_list_tpl", "category_list" );
$t->set_block( "category_list_tpl", "category_tpl", "category" );

$t->set_block( "category_tpl", "category_write_tpl", "category_write" );
$t->set_block( "category_tpl", "category_read_tpl", "category_read" );

$t->set_var( "read", "" );
$t->set_var( "write_menu", "" );

$t->set_var( "delete_images_button" , "" );
$t->set_var( "delete_categories_button" , "" );
$t->set_var( "default_new" , "" );
$t->set_var( "default_delete" , "" );

$category = new eZImageCategory( $CategoryID );

// Check if user have permission to the current category

$error = true;

if ( eZObjectPermission::hasPermission( $category->id(), "imagecatalogue_category", "r", $user ) )
{
    $error = false;
}

if ( $CategoryID == 0 )
{
    $t->set_var( "current_category_description", "" );
    $error = false;
}

$t->set_var( "current_category", "" );

if ( $category->id() != 0 )
{
    $t->set_var( "current_category_description", $category->description() );
    $t->set_var( "category_id", $category->id() );
    $t->set_var( "category_name", $category->name() );
    
    $t->parse( "current_category", "current_category_tpl" );
}

// path
$pathArray = $category->path();

$t->set_var( "path_item", "" );
foreach ( $pathArray as $path )
{
    $t->set_var( "category_id", $path[0] );

    $t->set_var( "category_name", $path[1] );
    
    $t->parse( "path_item", "path_item_tpl", true );
}


// Print out all the categories
$categoryList =& $category->getByParent( $category );

$i=0;
foreach ( $categoryList as $categoryItem )
{
    $t->set_var( "category_name", $categoryItem->name() );
    $t->set_var( "category_id", $categoryItem->id() );
    $t->set_var( "category_description", $categoryItem->description() );

    $t->set_var( "category_read", "" );
    $t->set_var( "category_write", "" );

    // Check if user have read permission
    if ( eZObjectPermission::hasPermission( $categoryItem->id(), "imagecatalogue_category", "r", $user ) )
    {
        $t->parse( "category_read", "category_read_tpl" );
    }

    // Check if user have write permission
    if ( ( eZObjectPermission::hasPermission( $categoryItem->id(), "imagecatalogue_category", "w", $user ) ) && $user )
    {
        $t->parse( "category_write", "category_write_tpl" );
        $t->parse( "delete_categories_button", "delete_categories_button_tpl" );
        $t->parse( "default_delete", "default_delete_tpl" );
        $t->parse( "write_menu", "write_menu_tpl" );
    }

    $t->parse( "category", "category_tpl", true );
    $i++;
}

if ( count( $categoryList ) > 0 )
{
    $t->parse( "category_list", "category_list_tpl" );
}
else
{
    $t->set_var( "category_list", "" );
}


// Print out all the images
$imageList =& $category->images();

//$i=0;
foreach ( $imageList as $image )
{
    if ( ( $i % 4 ) == 0 )
    {
        $t->set_var( "begin_tr", "<tr>" );
        $t->set_var( "end_tr", "" );        
    }
    else if ( ( $i % 4 ) == 3 )
    {
        $t->set_var( "begin_tr", "" );
        $t->set_var( "end_tr", "</tr>" );
    }
    else
    {
        $t->set_var( "begin_tr", "" );
        $t->set_var( "end_tr", "" );        
    }


    $t->set_var( "image_id", $image->id() );
    $t->set_var( "original_image_name", $image->originalFileName() );
    $t->set_var( "image_name", $image->name() );
    $t->set_var( "image_caption", $image->name() );
    $t->set_var( "image_url", $image->name() );

    $width =& $ini->read_var( "eZImageCatalogueMain", "ThumbnailViewWidth" );
    
    $height =& $ini->read_var( "eZImageCatalogueMain", "ThumbnailViewHight" );
    
    $variation =& $image->requestImageVariation( $width, $height );

    $t->set_var( "image_description",$image->description() ); 
    $t->set_var( "image_alt", $image->name() );
    $t->set_var( "image_src", "/" .$variation->imagePath() );
    $t->set_var( "image_width", $variation->width() );
    $t->set_var( "image_height", $variation->height() );
    $t->set_var( "image_file_name", $image->originalFileName() );

    $imagePath =& $variation->imagePath( true );

    $size = filesize( $imagePath );

    if ( $size == 0 )
    {
        $t->set_var( "image_size", 0 );
    }
    else
    {
        $t->set_var( "image_size", $size );
    }


    $t->set_var( "read", "" );
    $t->set_var( "write", "" );

    // Check if user have read permission
    $t->set_var( "detail_read", "" );
    if ( eZObjectPermission::hasPermission( $image->id(), "imagecatalogue_image", "r", $user ) )
    {
        if ( isSet ( $DetailView ) )
        {
            $t->parse( "detail_read", "detail_read_tpl" );
        }
        else
        {
            $t->parse( "read", "read_tpl" );
        }
    }

    // Check if user have write permission
    if ( ( eZObjectPermission::hasPermission( $image->id(), "imagecatalogue_image", "w", $user ) ) && $user )
    {
        if ( isSet ( $DetailView ) )
        {
            $deleteImage = true;
            $t->parse( "detail_write", "detail_write_tpl" );

            $t->parse( "delete_images_button", "delete_images_button_tpl" );
            $t->parse( "default_delete", "default_delete_tpl" );
            $t->parse( "write_menu", "write_menu_tpl" );
        }
        else
        {
            $t->parse( "write", "write_tpl" );
        }
    }
    else
    {
        $t->set_var( "detail_write", "" );
    }

    // Set the detail or normail view
    if ( isSet ( $DetailView ) )
    {
        $t->set_var( "is_detail_view", "true" );
        $t->set_var( "detail_button", "" );
        $t->set_var( "image", "" );
    
        $t->parse( "detail_view", "detail_view_tpl", true );
        $t->parse( "normal_button", "normal_view_button" );
    }
    else
    {
        $t->set_var( "is_detail_view", "" );
        $t->set_var( "detail_view", "" );
        $t->set_var( "normal_button", "" );
    
        $t->parse( "image", "image_tpl", true );
        $t->parse( "detail_button", "detail_view_button" );
    }
    $i++;
}

// Print out the category/image menu
if ( $category->id() != 0 )
{
    if ( eZObjectPermission::hasPermission( $category->id(), "imagecatalogue_category", "w", $user ) && $user )
    {
        $t->parse( "default_new", "default_new_tpl" );
        $t->parse( "write_menu", "write_menu_tpl" );
    }
}
else
{
    if ( eZPermission::checkPermission( $user, "eZImageCatalogue", "WriteToRoot" ) )
    {
        $t->parse( "default_new", "default_new_tpl" );
        $t->parse( "write_menu", "write_menu_tpl" );
    }
}

if ( count( $imageList ) > 0 )
{
    $t->parse( "image_list", "image_list_tpl" );
}
else
{
    $t->set_var( "normal_button", "" );
    $t->set_var( "detail_button", "" );
    $t->set_var( "image_list", "" );
}

$t->set_var( "image_dir", $ImageDir );

$t->set_var( "main_category_id", $CategoryID );

if ( $error == false )
{
    $t->pparse( "output", "image_list_page_tpl" );
}
else
{
    eZHTTPTool::header( "Location: /error/403/" );
    exit();
}


?>

