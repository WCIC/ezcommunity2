<?
// 
// $Id: headlines.php,v 1.8 2001/03/17 12:39:22 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <30-Nov-2000 14:35:24 bf>
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

include_once( "ezarticle/classes/ezarticlecategory.php" );
include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezarticlerenderer.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZArticleMain", "Language" );
$ImageDir = $ini->read_var( "eZArticleMain", "ImageDir" );

$t = new eZTemplate( "ezarticle/user/" . $ini->read_var( "eZArticleMain", "TemplateDir" ),
                     "ezarticle/user/intl/", $Language, "headlines.php" );

$t->setAllStrings();

$t->set_file( array(
    "article_list_page_tpl" => "headlines.tpl"
    ) );



// product
$t->set_block( "article_list_page_tpl", "article_list_tpl", "article_list" );
$t->set_block( "article_list_tpl", "article_item_tpl", "article_item" );

// image dir
$t->set_var( "image_dir", $ImageDir );

$category = new eZArticleCategory( $CategoryID );

// should we allow currentuser to go get articles with permissions or should we not??
$articleList = $category->articles( $SortMode, false, true, 0, 5 );

$locale = new eZLocale( $Language );
$i=0;
$t->set_var( "article_list", "" );
foreach ( $articleList as $article )
{
    $t->set_var( "article_id", $article->id() );
    $t->set_var( "article_name", $article->name() );
    

    $published =& $article->published();
    $date =& $published->date();

    $t->set_var( "article_published", $locale->format( $date ) );    

    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }

    if ( $article->linkText() != "" )
    {
        $t->set_var( "article_link_text", $article->linkText() );
    }
    else
    {
        $t->set_var( "article_link_text", "more" );
    }

    $t->parse( "article_item", "article_item_tpl", true );
    $i++;
}

if ( count( $articleList ) > 0 )    
    $t->parse( "article_list", "article_list_tpl" );
else
    $t->set_var( "article_list", "" );


$t->pparse( "output", "article_list_page_tpl" );


?>

