<?
// 
// $Id: articleedit.php,v 1.15 2000/10/25 18:19:55 bf-cvs Exp $
//
// 
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Oct-2000 15:04:39 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "ezuser/classes/ezuser.php" );

include_once( "ezarticle/classes/ezarticlecategory.php" );
include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezarticlegenerator.php" );

if ( $Action == "Insert" )
{
    $user = eZUser::currentUser();
    $category = new eZArticleCategory( $CategoryID );
        
    $article = new eZArticle( );
    $article->setName( $Name );
    
    $article->setAuthor( $user );

    $generator = new eZArticleGenerator();

    $contents = $generator->generateXML( $Contents );
    $article->setContents( $contents );

    $article->setPageCount( $generator->pageCount() );
    
    $article->setAuthorText( $AuthorText );
    
    $article->setLinkText( $LinkText );

    // add check for publishing rights here
    if ( $IsPublished == "on" )
    {
        $article->setIsPublished( true );
    }
    else
    {
        $article->setIsPublished( false );
    }
    
    // check if the contents is parseable
    if ( xmltree( $contents ) )
    {
        $article->store();
    
        $category->addArticle( $article );

        $articleID = $article->id();

    // add images
        if ( isset( $Image ) )
        {
            Header( "Location: /article/articleedit/imagelist/$articleID/" );
            exit();
        }

        // preview
        if ( isset( $Preview ) )
        {
            Header( "Location: /article/articlepreview/$articleID/" );
            exit();
        }


        // get the category to redirect to
        $categories = $article->categories();    
        $categoryID = $categories[0]->id();

    
        Header( "Location: /article/archive/$categoryID/" );
        exit();
    }
    else
    {
        $Action = "New";
        $ErrorParsing = true;
    }
}


if ( $Action == "Cancel" )
{
    $article = new eZArticle( $ArticleID );

    $categories = $article->categories();

    $categoryID = $categories[0]->id();

    Header( "Location: /article/archive/$categoryID/" );
    exit();
}


if ( $Action == "Update" )
{
    $category = new eZArticleCategory( $CategoryID );
    
    $article = new eZArticle( $ArticleID );
    $article->setName( $Name );

    $generator = new eZArticleGenerator();

    $contents = $generator->generateXML( $Contents );
    
    $article->setContents( $contents  );

//    print( $contents );

    $article->setPageCount( $generator->pageCount() );
    
    $article->setAuthorText( $AuthorText );
    
    $article->setLinkText( $LinkText );

    print( "ispublished: " . $IsPublished . "<br>" );
    // add check for publishing rights here
    if ( $IsPublished == "on" )
    {
        $article->setIsPublished( true );
    }
    else
    {
        $article->setIsPublished( false );
    }
        
    // check if the contents is parseable
    if ( xmltree( $contents ) )
    {
        $article->store();

    // remove all category references
        $article->removeFromCategories();
        $category->addArticle( $article );

    // add images
        if ( isset( $Image ) )
        {
            Header( "Location: /article/articleedit/imagelist/$ArticleID/" );
            exit();
        }

        // preview
        if ( isset( $Preview ) )
        {
            Header( "Location: /article/articlepreview/$ArticleID/" );
            exit();
        }

        // get the category to redirect to
        $categories = $article->categories();    
        $categoryID = $categories[0]->id();
    
        Header( "Location: /article/archive/$categoryID/" );
        exit();
    }
    else
    {
        $Action = "Edit";
        $ErrorParsing = true;        
    }
}


if ( $Action == "Delete" )
{
    $article = new eZArticle( $ArticleID );


    // get the category to redirect to
    $categories = $article->categories();    
    $categoryID = $categories[0]->id();

    $article->delete();    
    
    Header( "Location: /article/archive/$categoryID/" );
    exit();
}

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZArticleMain", "Language" );

$t = new eZTemplate( "ezarticle/admin/" . $ini->read_var( "eZArticleMain", "AdminTemplateDir" ),
                     "ezarticle/admin/intl/", $Language, "articleedit.php" );

$t->setAllStrings();

$t->set_file( array(
    "article_edit_page_tpl" => "articleedit.tpl"
    ) );



$t->set_block( "article_edit_page_tpl", "value_tpl", "value" );

$t->set_block( "article_edit_page_tpl", "error_message_tpl", "error_message" );

if ( $ErrorParsing == true )
{
    $t->parse( "error_message", "error_message_tpl" );
}
else
{
    $t->set_var( "error_message", "" );
}

$t->set_var( "article_is_published", "" );

$t->set_var( "article_id", "" );
$t->set_var( "article_name", stripslashes( $Name ) );
$t->set_var( "article_contents_0", stripslashes( $Contents[0] ) );
$t->set_var( "article_contents_1", stripslashes($Contents[1] ) );
$t->set_var( "article_contents_2", stripslashes($Contents[2] ) );
$t->set_var( "article_contents_3", stripslashes($Contents[3] ) );
$t->set_var( "author_text", stripslashes($AuthorText ) );
$t->set_var( "link_text", stripslashes($LinkText  ));

$t->set_var( "action_value", "insert" );

if ( $Action == "New" )
{
    $user = eZUser::currentUser();
    $t->set_var( "author_text", $user->firstName() . " " . $user->lastName());    

}


$article = new eZArticle( $ArticleID );

if ( $Action == "Edit" )
{
    $t->set_var( "article_id", $ArticleID );

    if (  $article->isPublished() )
    {
        $t->set_var( "article_is_published", "checked" );
    }
    else
    {
        $t->set_var( "article_is_published", "" );
    }
    
    if ( !isset( $Name ) )        
         $t->set_var( "article_name", $article->name() );

    $generator = new eZArticleGenerator();
    
    $contentsArray = $generator->decodeXML( $article->contents() );
    
    $i=0;
    foreach ( $contentsArray as $content )
    {
        if ( !isset( $Contents[$i] ) )
        {
            $t->set_var( "article_contents_$i", $content );
        }
        $i++;
    }
    
    $t->set_var( "author_text", $article->authorText() );
    $t->set_var( "link_text", $article->linkText() );
    
    $t->set_var( "action_value", "update" );
}

// category select
$category = new eZArticleCategory();
$categoryArray = $category->getAll( );

foreach ( $categoryArray as $catItem )
{
    if ( $Action == "Edit" )
    {
        if ( $article->existsInCategory( $catItem ) )
        {
            $t->set_var( "selected", "selected" );
        }
        else
            $t->set_var( "selected", "" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }    
        
    
    $t->set_var( "option_value", $catItem->id() );
    $t->set_var( "option_name", $catItem->name() );

    $t->parse( "value", "value_tpl", true );    
}


$t->pparse( "output", "article_edit_page_tpl" );

?>
