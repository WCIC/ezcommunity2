<?php
// 
// $Id: adedit.php,v 1.7 2001/01/02 18:10:22 ce Exp $
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
include_once( "classes/ezimagefile.php" );
include_once( "classes/ezlog.php" );

include_once( "classes/ezdatetime.php" );

include_once( "ezad/classes/ezad.php" );
include_once( "ezad/classes/ezadcategory.php" );

if ( isSet ( $Preview ) )
{
    if ( is_numeric ( $AdID ) && ( $AdID != 0 ) )
    {
        $Action = "Update";
    }
    else
    {
        $Action = "Insert";
    }
}

if ( $Action == "Insert" )
{
    $category = new eZAdCategory( $CategoryID );
    
    $ad = new eZAd( );

    $ad->setName( $AdTitle );
    $ad->setDescription( $AdDescription );
    
    if ( $IsActive == "on" )
    {
        $ad->setIsActive( true );
    }
    else
    {
        $ad->setIsActive( false );
    }

    $ad->setURL( $AdURL );
    
    $ad->setClickPrice( $ClickPrice );
    $ad->setViewPrice( $ViewPrice );

    $file = new eZImageFile();

    if ( $file->getUploadedFile( "AdImage" ) )
    { 
        $image = new eZImage();
        $image->setName( $Name );
        $image->setCaption( $Caption );

        $image->setImage( $file );
        
        $image->store();
        
        $ad->setImage( $image );

        eZLog::writeNotice( "Picture added to ad: $AdID  from IP: $REMOTE_ADDR" );
    }

//      $dateTime = new eZDateTime( 2000, 11, 13, 14, 0, 15 );
//      $ad->setOriginalPublishingDate( $dateTime );

    $ad->store();

    $category->addAd( $ad );

    
    if ( isset( $Preview ) )
    {
        $Action = "Edit";
        $AdID = $ad->id();
    }
    else
    {        
        Header( "Location: /ad/archive/$CategoryID/" );
        exit();
    }
    
}

if ( $Action == "Update" )
{
    $category = new eZAdCategory( $CategoryID );
    
    $ad = new eZAd( $AdID );

    $ad->setName( $AdTitle );
    $ad->setDescription( $AdDescription );

    if ( $IsActive == "on" )
    {
        $ad->setIsActive( true );
    }
    else
    {
        $ad->setIsActive( false );
    }

    $ad->setURL( $AdURL );

    $ad->setClickPrice( $ClickPrice );
    $ad->setViewPrice( $ViewPrice );
    
//      $dateTime = new eZDateTime( 2000, 11, 13, 14, 0, 15 );
//      $ad->setOriginalPublishingDate( $dateTime );

    $file = new eZImageFile();

    if ( $file->getUploadedFile( "AdImage" ) )
    { 
        $image = new eZImage();
        $image->setName( $Name );
        $image->setCaption( $Caption );

        $image->setImage( $file );
        
        $image->store();
        
        $ad->setImage( $image );

        eZLog::writeNotice( "Picture added to ad: $AdID  from IP: $REMOTE_ADDR" );
    }

    $ad->store();

    $ad->removeFromCategories();
    $category->addAd( $ad );
    
    if ( isset( $Preview ) )
    {
        $Action = "Edit";        
    }
    else
    {        
        Header( "Location: /ad/archive/$CategoryID/" );
        exit();
    }
}

if ( $Action == "Delete" )
{
    $ad = new eZAd( $AdID );
    $ad->delete();
    
    Header( "Location: /ad/archive/$CategoryID/" );
    exit();    
}


$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZAdMain", "Language" );
$ImageDir = $ini->read_var( "eZAdMain", "ImageDir" );

$t = new eZTemplate( "ezad/admin/" . $ini->read_var( "eZAdMain", "AdminTemplateDir" ),
                     "ezad/admin/intl/", $Language, "adedit.php" );

$t->setAllStrings();

$t->set_file( array(
    "ad_edit_page_tpl" => "adedit.tpl"
    ) );

$t->set_block( "ad_edit_page_tpl", "value_tpl", "value" );
$t->set_block( "ad_edit_page_tpl", "image_tpl", "image" );

$t->set_var( "action_value", "Insert" );

$t->set_var( "ad_title_value", "" );
$t->set_var( "ad_date_value", "" );
$t->set_var( "ad_description_value", "" );
$t->set_var( "ad_url_value", "" );
$t->set_var( "ad_click_price_value", "" );
$t->set_var( "ad_view_price_value", "" );
$t->set_var( "ad_id", "" );
$t->set_var( "image", "" );

if ( $Action == "Edit" )
{
    $ad = new eZAd( $AdID );

    $t->set_var( "ad_title_value", $ad->name() );
    $t->set_var( "ad_description_value", $ad->description() );
    $t->set_var( "ad_url_value", $ad->url() );
    $t->set_var( "ad_id", $ad->id() );
    $t->set_var( "action_value", "Update" );

    $t->set_var( "ad_click_price_value", $ad->clickPrice() );
    $t->set_var( "ad_view_price_value", $ad->viewPrice() );
    
    if ( $ad->isActive() == true )
    {
        $t->set_var( "ad_is_active", "checked" );
    }
    else
    {
        $t->set_var( "ad_is_active", "" );
    }

    $image = $ad->image();

    if ( $image )
    {
        $t->set_var( "image_src",  $image->filePath() );
        $t->set_var( "image_width", $image->width() );
        $t->set_var( "image_height", $image->height() );
        $t->set_var( "image_file_name", $image->originalFileName() );
        $t->parse( "image", "image_tpl" );
    }
    else
    {
        $t->set_var( "image", "" );
    }
        
    
    
    $cats = $ad->categories();

    $defCat = $cats[0];
}


// category select
$category = new eZAdCategory();
$categoryArray = $category->getTree();

foreach ( $categoryArray as $catItem )
{
    if ( $Action == "Edit" )
    {
        if ( $defCat->id() == $catItem[0]->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
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
    
    $t->set_var( "option_value", $catItem[0]->id() );
    $t->set_var( "option_name", $catItem[0]->name() );

    $t->parse( "value", "value_tpl", true );    
}


$t->pparse( "output", "ad_edit_page_tpl" );

?>
