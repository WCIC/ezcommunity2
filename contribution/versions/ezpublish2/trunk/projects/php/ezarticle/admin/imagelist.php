<?
// 
// $Id: imagelist.php,v 1.9 2001/01/22 14:42:59 jb Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <21-Sep-2000 10:32:19 bf>
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
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZArticleMain", "Language" );

include_once( "ezarticle/classes/ezarticlecategory.php" );
include_once( "ezarticle/classes/ezarticle.php" );


$t = new eZTemplate( "ezarticle/admin/" . $ini->read_var( "eZArticleMain", "AdminTemplateDir" ),
                     "ezarticle/admin/intl/", $Language, "imagelist.php" );

$t->setAllStrings();

$t->set_file( array(
    "image_list_page_tpl" => "imagelist.tpl"
    ) );

$t->set_block( "image_list_page_tpl", "image_tpl", "image" );

$article = new eZArticle( $ArticleID );


$thumbnail = $article->thumbnailImage();

$t->set_var( "article_name", $article->name() );

$images = $article->images();

$i=0;
$t->set_var( "image", "" );
foreach ( $images as $image )
{
    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }

    $t->set_var( "main_image_checked", "" );
    if ( $main != 0 )
    {
        if ( $main->id() == $image->id() )
        {
            $t->set_var( "main_image_checked", "checked" );
        }
    }

    $t->set_var( "thumbnail_image_checked", "" );
    if ( $thumbnail != 0 )
    {
        if ( $thumbnail->id() == $image->id() )
        {
            $t->set_var( "thumbnail_image_checked", "checked" );
        }
    }
    
    $t->set_var( "image_number", $i + 1 );

    if ( $image->caption() == "" )
        $t->set_var( "image_name", "&nbsp;" );
    else
        $t->set_var( "image_name", $image->caption() );
    $t->set_var( "image_id", $image->id() );
    $t->set_var( "article_id", $ArticleID );

    $variation =& $image->requestImageVariation( 150, 150 );
    
    $t->set_var( "image_url", "/" .$variation->imagePath() );
    $t->set_var( "image_width", $variation->width() );
    $t->set_var( "image_height",$variation->height() );
    
//      $t->set_var( "image_url", $image->filePath() );

    $t->parse( "image", "image_tpl", true );
    
    $i++;
}


$t->set_var( "article_id", $article->id() );

$t->pparse( "output", "image_list_page_tpl" );

?>
