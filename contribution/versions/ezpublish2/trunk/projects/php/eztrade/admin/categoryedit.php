<?
// 
// $Id: categoryedit.php,v 1.17 2001/07/12 12:19:28 ce Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Sep-2000 14:46:19 bf>
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

include_once( "classes/ezhttptool.php" );

if ( isset( $Cancel ) )
{
    eZHTTPTool::header( "Location: /trade/categorylist/" );
    exit();
}

if ( isset ( $DeleteCategories ) )
{
    $Action = "DeleteCategories";
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZTradeMain", "Language" );

include_once( "eztrade/classes/ezproductcategory.php" );

// Get images from the image browse function.
if ( ( isSet ( $AddImages ) ) and ( is_numeric( $CategoryID ) ) and ( is_numeric ( $ImageID ) ) )
{
    $image = new eZImage( $ImageID );
    $category = new eZProductCategory( $CategoryID );
    $category->setImage( $image );
    $category->store();
    $Action = "Edit";
}

// Direct actions
if ( $Action == "Insert" )
{
    $parentCategory = new eZProductCategory();
    $parentCategory->get( $ParentID );
    
    $category = new eZProductCategory();
    $category->setName( $Name );
    $category->setParent( $parentCategory );
    $category->setDescription( $Description );

    $category->setSortMode( $SortMode );

    $file = new eZImageFile();
    if ( $file->getUploadedFile( "ImageFile" ) )
    {
        $image = new eZImage( );
        $image->setName( "Image" );
        $image->setImage( $file );
        
        $image->store();
        $category->setImage( $image );
    }

    $category->store();

    include_once( "classes/ezcachefile.php" );
    $files = eZCacheFile::files( "eztrade/cache/", array( "productlist",
                                                          array( $ParentID, $category->id() ),
                                                          NULL, NULL ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }

    if ( isSet ( $Browse ) )
    {
        $categoryID = $category->id();
        $session = eZSession::globalSession();
        $session->setVariable( "SelectImages", "single" );
        $session->setVariable( "ImageListReturnTo", "/trade/categoryedit/edit/$categoryID/" );
        $session->setVariable( "NameInBrowse", $category->name() );
        eZHTTPTool::header( "Location: /imagecatalogue/browse/" );
        exit();
    }

    eZHTTPTool::header( "Location: /trade/categorylist/" );
    exit();
}

if ( $Action == "Update" )
{
    $parentCategory = new eZProductCategory();
    $parentCategory->get( $ParentID );

    $category = new eZProductCategory();
    $category->get( $CategoryID );
    $category->setName( $Name );
    $category->setParent( $parentCategory );
    $category->setDescription( $Description );

    $category->setSortMode( $SortMode );

    $file = new eZImageFile();
    if ( $file->getUploadedFile( "ImageFile" ) )
    {
        $image = new eZImage( );
        $image->setName( "Image" );
        $image->setImage( $file );
        
        $image->store();
        $category->setImage( $image );
    }

    if ( $DeleteImage == "on" )
        $category->setImage( 0 );

    $category->store();

    include_once( "classes/ezcachefile.php" );
    $files = eZCacheFile::files( "eztrade/cache/", array( "productlist",
                                                          array( $ParentID, $CategoryID ), NULL, NULL ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }

    if ( isSet ( $Browse ) )
    {
        $categoryID = $category->id();
        $session = eZSession::globalSession();
        $session->setVariable( "SelectImages", "single" );
        $session->setVariable( "ImageListReturnTo", "/trade/categoryedit/edit/$categoryID/" );
        $session->setVariable( "NameInBrowse", $category->name() );
        eZHTTPTool::header( "Location: /imagecatalogue/browse/" );
        exit();
    }

    eZHTTPTool::header( "Location: /trade/categorylist/" );
    exit();
}

if ( $Action == "Delete" )
{
    $category = new eZProductCategory();
    $category->get( $CategoryID );

    if ( file_exists( "ezarticle/cache/menubox.cache" ) )
        unlink( "ezarticle/cache/menubox.cache" );

    include_once( "classes/ezcachefile.php" );

    $parent = $category->parent();
    if ( get_class( $parent ) == "ezproductcategory" )
        $parent = $parent->id();
    $files = eZCacheFile::files( "eztrade/cache/",
                                 array( "productlist",
                                        array( $category->id(), $parent ),
                                        NULL, NULL ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }

    $category->delete();
    
    eZHTTPTool::header( "Location: /trade/categorylist/" );
    exit();
}

if ( $Action == "DeleteCategories" )
{
    if ( count ( $CategoryArrayID ) != 0 )
    {
        if ( file_exists( "ezarticle/cache/menubox.cache" ) )
            unlink( "ezarticle/cache/menubox.cache" );

        include_once( "classes/ezcachefile.php" );
        foreach( $CategoryArrayID as $ID )
        {
            $category = new eZProductCategory();
            $category->get( $ID );

            $parent = $category->parent();
            if ( get_class( $parent ) == "ezproductcategory" )
                $parent = $parent->id();
            $files = eZCacheFile::files( "eztrade/cache/",
                                         array( "productlist",
                                                array( $category->id(), $parent ),
                                                NULL, NULL ),
                                         "cache", "," );
            foreach( $files as $file )
            {
                $file->delete();
            }

            $category->delete();
        }
    }

    eZHTTPTool::header( "Location: /trade/categorylist/" );
    exit();
}


$t = new eZTemplate( "eztrade/admin/" . $ini->read_var( "eZTradeMain", "AdminTemplateDir" ),
                     "eztrade/admin/intl/", $Language, "categoryedit.php" );

$t->setAllStrings();

$t->set_file( array( "category_edit_tpl" => "categoryedit.tpl" ) );


$t->set_block( "category_edit_tpl", "value_tpl", "value" );
$t->set_block( "category_edit_tpl", "image_item_tpl", "image_item" );

$headline = new INIFIle( "eztrade/admin/intl/" . $Language . "/categoryedit.php.ini", false );
$t->set_var( "head_line", $headline->read_var( "strings", "head_line_insert" ) );

$category = new eZProductCategory();

$categoryArray = $category->getTree( );

$t->set_var( "description_value", "" );
$t->set_var( "name_value", "" );
$t->set_var( "action_value", "insert" );

$t->set_var( "1_selected", "" );
$t->set_var( "2_selected", "" );
$t->set_var( "3_selected", "" );
$t->set_var( "4_selected", "" );
$t->set_var( "image_item", "" );

// edit
if ( $Action == "Edit" )
{
    $category = new eZProductCategory();
    $category->get( $CategoryID );

    $t->set_var( "name_value", $category->name() );
    $t->set_var( "description_value", $category->description() );
    $t->set_var( "action_value", "update" );
    $t->set_var( "category_id", $category->id() );

    $parent = $category->parent();
    switch ( $category->sortMode() )
    {
        case "time" :
        {
            $t->set_var( "1_selected", "selected" );
        }
        break;

        case "alpha" :
        {
            $t->set_var( "2_selected", "selected" );
        }
        break;

        case "alphadesc" :
        {
            $t->set_var( "3_selected", "selected" );
        }
        break;

        case "absolute_placement" :
        {
            $t->set_var( "4_selected", "selected" );
        }
        break;
    }

    $image =& $category->image( true );
    if ( get_class( $image ) == "ezimage" && $image->id() != 0 )
    {
        $imageWidth =& $ini->read_var( "eZTradeMain", "CategoryImageWidth" );
        $imageHeight =& $ini->read_var( "eZTradeMain", "CategoryImageHeight" );
        
        $variation =& $image->requestImageVariation( $imageWidth, $imageHeight );
        
        $imageURL = "/" . $variation->imagePath();
        $imageWidth = $variation->width();
        $imageHeight = $variation->height();
        $imageCaption = $image->caption();
        
        $t->set_var( "image_width", $imageWidth );
        $t->set_var( "image_height", $imageHeight );
        $t->set_var( "image_url", $imageURL );
        $t->set_var( "image_caption", $imageCaption );
        $t->parse( "image_item", "image_item_tpl" );
    }
    else
    {

    }

    $headline = new INIFIle( "eztrade/admin/intl/" . $Language . "/categoryedit.php.ini", false );
    $t->set_var( "head_line", $headline->read_var( "strings", "head_line_edit" ) );
}

foreach ( $categoryArray as $catItem )
{
    if ( $CategoryID != $catItem[0]->id() )
    {
        $t->set_var( "option_value", $catItem[0]->id() );
        $t->set_var( "option_name", $catItem[0]->name() );

        if ( $Action == "Edit" )
        {
		    if ( $parent )
			{
            	if ( $catItem[0]->id() == $parent->id() )
            	{
                	$t->set_var( "selected", "selected" );
            	}
            	else
            	{            
               		$t->set_var( "selected", "" );
            	}
			}
            
        }
        else
        {            
            $t->set_var( "selected", "" );
        }

        if ( $catItem[1] > 0 )
            $t->set_var( "option_level", str_repeat( "&nbsp;", $catItem[1] ) );
        else
            $t->set_var( "option_level", "" );


        $t->parse( "value", "value_tpl", true );
    }
}

$t->pparse( "output", "category_edit_tpl" );

?>
