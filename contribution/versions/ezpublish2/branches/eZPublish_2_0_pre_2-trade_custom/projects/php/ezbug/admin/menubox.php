<?
// 
// $Id: menubox.php,v 1.3 2001/02/07 14:57:29 fh Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <25-Nov-2000 17:53:52 bf>
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
    array( "/bug/archive/", "{intl-bug_archive}" ),
    array( "/bug/unhandled/", "{intl-unhandled_bugs}" ),
    array( "/bug/priority/list/", "{intl-priority_overview}" ),
    array( "/bug/category/list/", "{intl-category_overview}" ),
    array( "/bug/module/list/0/", "{intl-module_overview}" ),
    array( "/bug/status/list/", "{intl-status_overview}" )
    );

?>
