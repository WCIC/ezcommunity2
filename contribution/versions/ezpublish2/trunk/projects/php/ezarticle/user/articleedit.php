<?
// 
// $Id: articleedit.php,v 1.9 2001/02/23 14:25:00 gl Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Oct-2000 15:04:39 bf>
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
include_once( "classes/ezmail.php" );
include_once( "classes/ezcachefile.php" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "classes/ezhttptool.php" );

include_once( "ezarticle/classes/ezarticlecategory.php" );
include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezarticlegenerator.php" );
include_once( "ezarticle/classes/ezarticlerenderer.php" );


$ini =& $GLOBALS["GlobalSiteIni"];

$PublishNoticeReceiver = $ini->read_var( "eZArticleMain", "PublishNoticeReceiver" );
$PublishNoticeSender = $ini->read_var( "eZArticleMain", "PublishNoticeSender" );


// insert a new article in the database
if ( $Action == "Insert" )
{
    $user = eZUser::currentUser();
        
    $article = new eZArticle();
    $article->setName( $Name );
    
    $article->setAuthor( $user );

    $generator = new eZArticleGenerator();

    $contents = $generator->generateXML( $Contents );
    $article->setContents( $contents );

    $article->setPageCount( $generator->pageCount() );
    
    $article->setAuthorText( $AuthorText );
    
    $article->setLinkText( $LinkText );

// DO SOMETHING ABOUT THIS
//    $ownerGroup = new eZUserGroup( $OwnerGroupID );
//    $article->setOwnerGroup( $ownerGroup );

    // Read permission: Everyone
    $article->setReadPermission( 0 );

    // user-submitted articles are never directly published
    $article->setIsPublished( false );

    // check if the contents is parseable
    if ( xmltree( $contents ) )
    {
        // generate keywords
        $contents = strip_tags( $contents );
        $contents = ereg_replace( "#\n#", "", $contents );
        $contents_array =& split( " ", $contents );
        $contents_array = array_unique( $contents_array );

        $keywords = "";
        foreach ( $contents_array as $word )
        {
            
            $keywords .= $word . " ";
        }

        $article->setKeywords( $keywords );
        
        $article->store();
    

        // add to categories
        $category = new eZArticleCategory( $CategoryIDSelect );
        $category->addArticle( $article );

        $article->setCategoryDefinition( $category );


        // get the category to redirect to
        $category = $article->categoryDefinition();
        $categoryID = $category->id();

        eZHTTPTool::header( "Location: /article/archive/$categoryID/" );
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

    $category = $article->categoryDefinition( );
    
    if ( $category )
    {
        $categoryID = $category->id();
    }

    eZHTTPTool::header( "Location: /article/archive/$categoryID/" );
    exit();
}


$Language = $ini->read_var( "eZArticleMain", "Language" );

$t = new eZTemplate( "ezarticle/user/" . $ini->read_var( "eZArticleMain", "AdminTemplateDir" ),
                     "ezarticle/user/intl/", $Language, "articleedit.php" );

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

$t->set_var( "article_id", "" );
$t->set_var( "article_name", stripslashes( $Name ) );
$t->set_var( "article_contents_0", stripslashes( $Contents[0] ) );
$t->set_var( "article_contents_1", stripslashes( $Contents[1] ) );
$t->set_var( "author_text", stripslashes( $AuthorText ) );
$t->set_var( "link_text", stripslashes( $LinkText  ) );

$t->set_var( "action_value", "insert" );

if ( $Action == "New" )
{
    $user = eZUser::currentUser();
    $t->set_var( "author_text", $user->firstName() . " " . $user->lastName());    
}


// category select
$tree = new eZArticleCategory();
$treeArray = $tree->getTree();

foreach ( $treeArray as $catItem )
{
    $t->set_var( "selected", "" );

    if ( $CategoryIDSelect == $catItem[0]->id() )
    {
        $t->set_var( "selected", "selected" );
    }

    $t->set_var( "option_value", $catItem[0]->id() );
    $t->set_var( "option_name", $catItem[0]->name() );

    if ( $catItem[1] > 0 )
        $t->set_var( "option_level", str_repeat( "&nbsp;", $catItem[1] ) );
    else
        $t->set_var( "option_level", "" );

    $t->parse( "value", "value_tpl", true );    
}


$t->pparse( "output", "article_edit_page_tpl" );

?>
