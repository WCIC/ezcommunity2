<?
// 
// $Id: imageview.php,v 1.3 2000/11/01 09:32:55 ce-cvs Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <26-Oct-2000 19:40:18 bf>
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

include_once( "ezimagecatalogue/classes/ezimage.php" );
include_once( "ezimagecatalogue/classes/ezimagevariation.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZImageCatalogueMain", "Language" );


$t = new eZTemplate( "ezimagecatalogue/user/" . $ini->read_var( "eZImageCatalogueMain", "TemplateDir" ),
                     "ezimagecatalogue/user/intl/", $Language, "imageview.php" );

$t->set_file( "image_view_tpl", "imageview.tpl" );

$t->setAllStrings();

$image = new eZImage( $ImageID );

$variation =& $image->requestImageVariation( $ini->read_var( "eZImageCatalogueMain", "ImageViewWidth" ),
                                             $ini->read_var( "eZImageCatalogueMain", "ImageViewHeight" ) );

$t->set_var( "referer_url", $RefererURL );

$t->set_var( "image_uri", "/" . $variation->imagePath() );
$t->set_var( "image_width", $variation->width() );
$t->set_var( "image_height", $variation->height() );
$t->set_var( "image_caption", $image->caption() );

$t->pparse( "output", "image_view_tpl" );


?>
