<?
// 
// $Id: unhandledbugs.php,v 1.2 2000/11/29 16:51:37 bf-cvs Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <27-Nov-2000 22:18:56 bf>
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

include_once( "ezbug/classes/ezbugcategory.php" );
include_once( "ezbug/classes/ezbugmodule.php" );
include_once( "ezbug/classes/ezbug.php" );

include_once( "ezuser/classes/ezuser.php" );

include_once( "classes/eztemplate.php" );

$t = new eZTemplate( "ezbug/admin/" . $ini->read_var( "eZBugMain", "AdminTemplateDir" ),
                     "ezbug/admin/intl", $Language, "unhandledbugs.php" );

$t->set_file( array(
    "unhandled_bugs_tpl" => "unhandledbugs.tpl"
    ) );

$t->setAllStrings();

$t->set_block( "unhandled_bugs_tpl", "bug_tpl", "bug" );

$bug = new eZBug();

$unhandleBugs =& $bug->getUnhandled();

foreach ( $unhandleBugs as $bug )
{
    $t->set_var( "bug_id",  $bug->id() );
    $t->set_var( "bug_name",  $bug->name() );

    $t->parse( "bug", "bug_tpl", true );
}

$t->pparse( "output", "unhandled_bugs_tpl" );

?>
