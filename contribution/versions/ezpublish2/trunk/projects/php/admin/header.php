<?
// 
// $Id: header.php,v 1.23 2001/01/24 16:27:12 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <23-Jan-2001 16:06:07 bf>
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

include_once( "classes/eztemplate.php" );

$ini =& $GlobalSiteIni;
$Language =& $ini->read_var( "eZUserMain", "Language" );


$t = new eZTemplate( "templates/" . $SiteStyle,
                     "intl/", $Language, "header.php" );


$t->set_file( array(
    "header_tpl" => "header.tpl"
    ) );

$SiteURL =& $ini->read_var( "site", "SiteURL" );

$user =& eZUser::currentUser();

if ( $user )
{
    $t->set_var( "first_name", $user->firstName() );
    $t->set_var( "last_name", $user->lastName() );
}


$t->set_var( "site_url", $SiteURL );

$t->set_var( "site_style", $SiteStyle );

$t->set_var( "module_name", $moduleName );


$t->setAllStrings();

$t->pparse( "output", "header_tpl" );
    

?>
