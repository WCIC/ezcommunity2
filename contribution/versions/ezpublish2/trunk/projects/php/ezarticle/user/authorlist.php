<?php
// 
// $Id: authorlist.php,v 1.1 2001/02/16 16:06:17 jb Exp $
//
// Jan Borsodi <jb@ez.no>
// Created on: <16-Feb-2001 14:54:04 amos>
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

include_once( "ezarticle/classes/ezarticle.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZArticleMain", "Language" );

$t = new eZTemplate( "ezarticle/user/" . $ini->read_var( "eZArticleMain", "TemplateDir" ),
                     "ezarticle/user/intl/", $Language, "authorlist.php" );

$t->setAllStrings();

$t->set_file( "author_list_tpl", "authorlist.tpl" );

$t->set_block( "author_list_tpl", "author_item_tpl", "author_item" );

if ( !isset( $Offset ) )
    $Offset = 0;
if ( !isset( $Limit ) )
    $Limit = 5;
if ( !isset( $SortOrder ) )
    $SortOrder = "name";

$authors = eZArticle::authorList( $Offset, $Limit, $SortOrder );

$t->set_var( "author_item", "" );
$i = 0;
foreach( $authors as $author )
{
    $t->set_var( "td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );
    $t->set_var( "author_id", $author["AuthorID"] );
    $user = new eZUser( $author["AuthorID"] );
    $t->set_var( "author_firstname", $user->firstName() );
    $t->set_var( "author_lastname", $user->lastName() );
    $t->set_var( "article_count", $author["Count"] );
    $t->parse( "author_item", "author_item_tpl", true );
    $i++;
}

$t->pparse( "output", "author_list_tpl" );

?>
