<?php
// 
// $Id: reportsuccess.php,v 1.4 2001/07/29 23:31:02 kaid Exp $
//
// Created on: <27-Nov-2000 21:55:26 bf>
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

include_once( "classes/INIFile.php" );
// $ini = new INIFile( "site.ini" );
$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZBugMain", "Language" );

include_once( "classes/eztemplate.php" );

$t = new eZTemplate( "ezbug/user/" . $ini->read_var( "eZBugMain", "TemplateDir" ),
                     "ezbug/user/intl", $Language, "reportsuccess.php" );

$t->setAllStrings();

$t->set_file( array(
    "report_success_tpl" => "reportsuccess.tpl"
    ) );

$t->set_var( "site_style", $SiteStyle );

$t->pparse( "output", "report_success_tpl" );

?>
