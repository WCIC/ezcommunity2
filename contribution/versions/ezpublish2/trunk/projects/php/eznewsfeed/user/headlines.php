<?
// 
// $Id: headlines.php,v 1.3 2000/11/25 15:57:33 bf-cvs Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <16-Nov-2000 10:51:34 bf>
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

include_once( "eznewsfeed/classes/eznews.php" );

include_once( "classes/ezdatetime.php" );

$news = new eZNews( );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZNewsFeedMain", "Language" );
$ImageDir = $ini->read_var( "eZNewsFeedMain", "ImageDir" );

$t = new eZTemplate( "eznewsfeed/user/" . $ini->read_var( "eZNewsFeedMain", "TemplateDir" ),
                     "eznewsfeed/user/intl/", $Language, "headlines.php" );

$t->setAllStrings();

$t->set_file( array(
    "headlines_page_tpl" => "headlines.tpl"
    ) );

$t->set_block( "headlines_page_tpl", "head_line_item_tpl", "head_line_item" );

$newsList = $news->newsList();

$locale = new eZLocale();

foreach ( $newsList as $newsItem )
{
    $t->set_var( "head_line", $newsItem->name() );
    $t->set_var( "head_line_url", $newsItem->url() );

    $t->set_var( "head_line_origin", $newsItem->origin() );
    $published = $newsItem->originalPublishingDate();

    $t->set_var( "head_line_date", $locale->format( $published ) );


    $t->parse( "head_line_item", "head_line_item_tpl", true );
}


$t->pparse( "output", "headlines_page_tpl" );

?>
