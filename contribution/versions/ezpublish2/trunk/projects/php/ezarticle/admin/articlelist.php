<?
// 
// $Id: articlelist.php,v 1.12 2001/01/24 11:53:53 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Oct-2000 14:41:37 bf>
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

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZArticleMain", "Language" );
$AdminListLimit = $ini->read_var( "eZArticleMain", "AdminListLimit" );

$t = new eZTemplate( "ezarticle/admin/" . $ini->read_var( "eZArticleMain", "AdminTemplateDir" ),
                     "ezarticle/admin/intl/", $Language, "articlelist.php" );

$t->setAllStrings();

$t->set_file( array(
    "article_list_page_tpl" => "articlelist.tpl"
    ) );

// path
$t->set_block( "article_list_page_tpl", "path_item_tpl", "path_item" );

// category
$t->set_block( "article_list_page_tpl", "category_list_tpl", "category_list" );
$t->set_block( "category_list_tpl", "category_item_tpl", "category_item" );

// article
$t->set_block( "article_list_page_tpl", "article_list_tpl", "article_list" );
$t->set_block( "article_list_tpl", "article_item_tpl", "article_item" );

$t->set_block( "article_item_tpl", "article_is_published_tpl", "article_is_published" );
$t->set_block( "article_item_tpl", "article_not_published_tpl", "article_not_published" );

// move up / down
$t->set_block( "article_list_tpl", "absolute_placement_header_tpl", "absolute_placement_header" );
$t->set_block( "article_item_tpl", "absolute_placement_item_tpl", "absolute_placement_item" );


// prev/next
$t->set_block( "article_list_page_tpl", "previous_tpl", "previous" );
$t->set_block( "article_list_page_tpl", "next_tpl", "next" );


$category = new eZArticleCategory( $CategoryID );


// move articles up / down

if ( $category->sortMode() == "absolute_placement" )
{
    if ( is_numeric( $MoveUp ) )
    {
        $category->moveUp( $MoveUp );
    }

    if ( is_numeric( $MoveDown ) )
    {
        $category->moveDown( $MoveDown );
    }
}

$t->set_var( "current_category_id", $category->id() );
$t->set_var( "current_category_name", $category->name() );
$t->set_var( "current_category_description", $category->description() );

// path
$pathArray = $category->path();

$t->set_var( "path_item", "" );
foreach ( $pathArray as $path )
{
    $t->set_var( "category_id", $path[0] );

    $t->set_var( "category_name", $path[1] );
    
    $t->parse( "path_item", "path_item_tpl", true );
}



$categoryList =& $category->getByParent( $category, true );


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


// set the offset/limit
if ( !isset( $Offset ) )
    $Offset = 0;

if ( !isset( $Limit ) )
    $Limit = $AdminListLimit;

// articles
$articleList =& $category->articles( $category->sortMode(), true, true, $Offset, $Limit );
$articleCount = $category->articleCount( true, true );

$locale = new eZLocale( $Language );
$i=0;
$t->set_var( "article_list", "" );

if ( $category->sortMode() == "absolute_placement" )
{
    $t->parse( "absolute_placement_header", "absolute_placement_header_tpl" );
}
else
{
    $t->set_var( "absolute_placement_header", "" );
}

foreach ( $articleList as $article )
{
    if ( $article->name() == "" )
        $t->set_var( "article_name", "&nbsp;" );
    else
        $t->set_var( "article_name", $article->name() );

    $t->set_var( "article_id", $article->id() );

    if ( $article->isPublished() == true )
    {
        $t->parse( "article_is_published", "article_is_published_tpl" );
        $t->set_var( "article_not_published", "" );        
    }
    else
    {
        $t->set_var( "article_is_published", "" );
        $t->parse( "article_not_published", "article_not_published_tpl" );
    }

    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }

    if ( $category->sortMode() == "absolute_placement" )
    {
        $t->parse( "absolute_placement_item", "absolute_placement_item_tpl" );
    }
    else
    {
        $t->set_var( "absolute_placement_item", "" );
    }
    

    $t->parse( "article_item", "article_item_tpl", true );
    $i++;
}

$prevOffs = $Offset - $Limit;
$nextOffs = $Offset + $Limit;
        
if ( $prevOffs >= 0 )
{
    $t->set_var( "prev_offset", $prevOffs  );
    $t->parse( "previous", "previous_tpl" );
}
else
{
    $t->set_var( "previous", "" );
}
        
if ( $nextOffs <= $articleCount )
{
    $t->set_var( "next_offset", $nextOffs  );
    $t->parse( "next", "next_tpl" );
}
else
{
    $t->set_var( "next", "" );
}

if ( count( $articleList ) > 0 )    
    $t->parse( "article_list", "article_list_tpl" );
else
    $t->set_var( "article_list", "" );


$t->pparse( "output", "article_list_page_tpl" );






?>
