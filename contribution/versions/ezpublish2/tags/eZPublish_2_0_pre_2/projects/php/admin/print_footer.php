<?
// 
// $Id: print_footer.php,v 1.1 2001/03/04 13:10:12 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <04-Mar-2001 13:57:25 bf>
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
$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZArticleMain", "Language" );

include_once( "classes/template.inc" );

$t = new Template( "templates/" . $SiteStyle );

$t->set_file( array(
    "print_footer_tpl" => "print_footer.tpl"
    ) );

$t->set_var( "site_style", $SiteStyle );
$t->set_var( "module_dir", $moduleName );


$t->pparse( "output", "print_footer_tpl" );
    

?>

