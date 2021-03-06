<?
// 
// $Id: articlelist.php,v 1.11 2000/10/26 11:07:13 bf-cvs Exp $
//
// 
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Oct-2000 14:41:37 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );

include_once( "ezarticle/classes/ezarticlecategory.php" );
include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezarticlerenderer.php" );


$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZArticleMain", "Language" );
$ImageDir = $ini->read_var( "eZArticleMain", "ImageDir" );

$t = new eZTemplate( "ezarticle/user/" . $ini->read_var( "eZArticleMain", "TemplateDir" ),
                     "ezarticle/user/intl/", $Language, "articlelist.php" );

$t->setAllStrings();

$t->set_file( array(
    "article_list_page_tpl" => "articlelist.tpl"
    ) );

// path
$t->set_block( "article_list_page_tpl", "path_item_tpl", "path_item" );

// article
$t->set_block( "article_list_page_tpl", "category_list_tpl", "category_list" );
$t->set_block( "category_list_tpl", "category_item_tpl", "category_item" );

// product
$t->set_block( "article_list_page_tpl", "article_list_tpl", "article_list" );
$t->set_block( "article_list_tpl", "article_item_tpl", "article_item" );

$t->set_block( "article_item_tpl", "article_image_tpl", "article_image" );

// image dir
$t->set_var( "image_dir", $ImageDir );

$category = new eZArticleCategory( $CategoryID );

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

$categoryList = $category->getByParent( $category );


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




if ( $CategoryID == 0 )
{
    $article = new eZArticle();
    $articleList = $article->articles( $SortMode, false );
} 
else
{
    $articleList = $category->articles( $SortMode, false );
}

$locale = new eZLocale( $Language );
$i=0;
$t->set_var( "article_list", "" );
foreach ( $articleList as $article )
{
    $t->set_var( "article_id", $article->id() );
    $t->set_var( "article_name", $article->name() );
    
    // preview image
    $thumbnailImage = $article->thumbnailImage();
    if ( $thumbnailImage )
    {
        $variation =& $thumbnailImage->requestImageVariation( $ini->read_var( "eZArticleMain", "ThumbnailImageWidth" ),
                                                              $ini->read_var( "eZArticleMain", "ThumbnailImageHeight" ));
    
        $t->set_var( "thumbnail_image_uri", "/" . $variation->imagePath() );
        $t->set_var( "thumbnail_image_width", $variation->width() );
        $t->set_var( "thumbnail_image_height", $variation->height() );
        $t->set_var( "thumbnail_image_caption", $thumbnailImage->caption() );

        $t->parse( "article_image", "article_image_tpl" );
    }
    else
    {
        $t->set_var( "article_image", "" );    
    }
    

    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }

    $renderer = new eZArticleRenderer( $article );

    $t->set_var( "article_intro", $renderer->renderIntro(  ) );

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

