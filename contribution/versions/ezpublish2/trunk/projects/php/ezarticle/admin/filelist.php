<?
// 
// $Id: filelist.php,v 1.2 2001/01/22 14:42:59 jb Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <21-Dec-2000 17:43:40 bf>
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
                     "ezarticle/admin/intl/", $Language, "filelist.php" );

$t->setAllStrings();

$t->set_file( array(
    "file_list_page_tpl" => "filelist.tpl"
    ) );

$t->set_block( "file_list_page_tpl", "file_tpl", "file" );

$article = new eZArticle( $ArticleID );


$t->set_var( "article_name", $article->name() );

$files = $article->files();

$i=0;
$t->set_var( "file", "" );
foreach ( $files as $file )
{
    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }


    $t->set_var( "file_number", $i + 1 );

    $t->set_var( "file_name", $file->name() );
    
    $t->parse( "file", "file_tpl", true );
    
    $i++;
}


$t->set_var( "article_id", $article->id() );

$t->pparse( "output", "file_list_page_tpl" );

?>
