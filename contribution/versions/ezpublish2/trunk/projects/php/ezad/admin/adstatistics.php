<?php
// 
// $Id: adstatistics.php,v 1.3 2000/11/27 11:54:12 bf-cvs Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <26-Nov-2000 11:47:03 bf>
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


$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZAdMain", "Language" );
$ImageDir = $ini->read_var( "eZAdMain", "ImageDir" );

$t = new eZTemplate( "ezad/admin/" . $ini->read_var( "eZAdMain", "AdminTemplateDir" ),
                     "ezad/admin/intl/", $Language, "adstatistics.php" );

$t->setAllStrings();

$t->set_file( array(
    "ad_edit_page_tpl" => "adstatistics.tpl"
    ) );

$t->set_block( "ad_edit_page_tpl", "image_tpl", "image" );

$ad = new eZAd( $AdID );

$t->set_var( "ad_title", $ad->name() );
$t->set_var( "ad_description", $ad->description() );
$t->set_var( "ad_url", $ad->url() );
$t->set_var( "ad_id", $ad->id() );

$t->set_var( "ad_view_count", $ad->viewCount() );
$t->set_var( "ad_click_count", $ad->clickCount() );

$t->set_var( "ad_click_percent", ( $ad->clickCount() / $ad->viewCount() ) * 100 );

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


$t->pparse( "output", "ad_edit_page_tpl" );

?>

