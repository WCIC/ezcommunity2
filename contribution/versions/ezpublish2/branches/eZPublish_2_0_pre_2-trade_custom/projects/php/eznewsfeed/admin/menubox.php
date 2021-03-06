<?
// 
// $Id: menubox.php,v 1.4 2001/01/28 10:57:13 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <23-Oct-2000 17:53:46 bf>
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

// Supply $menuItems to get a menubox

$menuItems = array(
    array( "/newsfeed/archive/", "{intl-news_archive}" ),
    array( "/newsfeed/news/new", "{intl-new_news}" ),
    array( "/newsfeed/category/new", "{intl-new_category}" ),
    array( "/newsfeed/unpublished/", "{intl-unpublished_news}" ),
    array( "/newsfeed/importnews/", "{intl-import_news}" ),
    array( "/newsfeed/sourcesite/new/", "{intl-newsourcesite}" )
    );

?>

