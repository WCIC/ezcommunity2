<?php
//
// $Id: cron.php,v 1.2.2.2.2.1 2002/06/03 10:44:27 pkej Exp $
//
// Created on: <08-Jun-2001 13:16:33 ce>
//
// This source file is part of eZ publish, publishing software.
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
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

include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezarticletool.php" );

$article = new eZArticle();
$articleValidArray =& $article->getAllValid();
$articleUnValid =& $article->getAllUnValid();

if ( count ( $articleValidArray ) > 0 )
{
    foreach ( $articleValidArray as $article )
    {
	$article->setIsPublished( true );
	$d = 0;
	$article->setStartDate( $d );
	$article->store();

    if ( $article->isPublished() == 1 )
        eZArticleTool::notificationMessage( $article );

	$catDef = $article->categoryDefinition();

	$cats = $article->categories( false ) ;
	// clear the cache files.
	eZArticleTool::deleteCache( $article->id(), $catDef, $cats);

	print( "Publishing article: " . $article->name() . "\n" );
    }
}

if ( count ( $articleUnValid ) > 0 )
{
    foreach( $articleUnValid as $article )
    {
	    $d  = 0;

	    $cats = $article->categories( false ) ;
	    // clear the cache files.
	    eZArticleTool::deleteCache( $article->id(), $catDef, $cats  );
	    $article->removeFromCategories( false ) ;

        $category = $article->categoryDefinition();
        $categories = $category->categories();
        $categoryDef = $category->categoryDefinition();
        
        foreach( $categories as $tmpCategory )
        {
             $tmpCategory->addArticle( $article );
        }

        $article->setCategoryDefinition( $categoryDef );

	    $article->setStopDate( $d );
	    $article->store();
	    print( "UnPublishing article: " .$article->id() . " " . $article->name() . "\n" );
    }
}

// Old expire.
// if ( count ( $articleUnValid ) > 0 )
// {
//     foreach( $articleUnValid as $article )
//     {
//         $article->setIsPublished( false );
// 	    $d  = 0;
// 	    $article->setStopDate( $d );
//         $article->store();
//         print( "UnPublishing article: " . $article->name() . "\n" );
//     }
// }


?>
