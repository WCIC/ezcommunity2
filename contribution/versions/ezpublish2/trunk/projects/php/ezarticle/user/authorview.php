<?php
// 
// $Id: authorview.php,v 1.5 2001/03/01 14:06:25 jb Exp $
//
// Jan Borsodi <jb@ez.no>
// Created on: <16-Feb-2001 15:36:13 amos>
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
include_once( "classes/ezdatetime.php" );
include_once( "classes/ezlist.php" );

include_once( "ezarticle/classes/ezarticle.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZArticleMain", "Language" );
$Limit = $ini->read_var( "eZArticleMain", "AuthorArticleLimit" );
$locale = new eZLocale( $Language );

$t = new eZTemplate( "ezarticle/user/" . $ini->read_var( "eZArticleMain", "TemplateDir" ),
                     "ezarticle/user/intl/", $Language, "authorview.php" );

$t->setAllStrings();

$t->set_file( "author_view_tpl", "authorview.tpl" );

$t->set_block( "author_view_tpl", "article_item_tpl", "article_item" );

if ( !isset( $Offset ) or !is_numeric( $Offset ) )
    $Offset = 0;
if ( !isset( $Limit ) or !is_numeric( $Limit ) )
    $Limit = 10;
if ( !isset( $SortOrder ) )
    $SortOrder = "published";

$article_count = eZArticle::authorArticleCount( $AuthorID );

$t->set_var( "article_count", $article_count );
$t->set_var( "article_start", $Offset + 1 );
$t->set_var( "article_end", min( $Offset + $Limit, $article_count ) );

$articles = eZArticle::authorArticleList( $AuthorID, $Offset, $Limit, $SortOrder );

$t->set_var( "author_id", $AuthorID );
$user = new eZUser( $AuthorID );
$t->set_var( "author_firstname", $user->firstName() );
$t->set_var( "author_lastname", $user->lastName() );
$t->set_var( "author_mail", $user->email() );

$t->set_var( "sort", $SortOrder );

$t->set_var( "article_item", "" );
$i = 0;
$dateTime = new eZDateTime();
foreach( $articles as $article )
{
    $t->set_var( "td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );
    $t->set_var( "article_id", $article["ID"] );
    $t->set_var( "article_name", $article["Name"] );
    $t->set_var( "category_id", $article["CategoryID"] );
    $t->set_var( "article_category", $article["CategoryName"] );
    $t->set_var( "author_name", $article["AuthorName"] );
    $dateTime->setMySQLTimeStamp( $article["Published"] );
    $t->set_var( "article_published", $locale->format( $dateTime ) );
    $t->parse( "article_item", "article_item_tpl", true );
    $i++;
}

eZList::drawNavigator( $t, $article_count, $Limit, $Offset, "author_view_tpl" );

$t->pparse( "output", "author_view_tpl" );

?>
