<?php
// 
// $Id: cacheadmin.php,v 1.3 2001/07/29 23:31:10 kaid Exp $
//
// Created on: <05-Jul-2001 14:40:06 bf>
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
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlist.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZSiteManagerMain", "Language" );
$Limit = $ini->read_var( "eZSiteManagerMain", "AdminListLimit" );

include_once( "ezsitemanager/classes/ezsection.php" );


$t = new eZTemplate( "ezsitemanager/admin/" . $ini->read_var( "eZSiteManagerMain", "AdminTemplateDir" ),
                     "ezsitemanager/admin/" . "/intl", $Language, "cacheadmin.php" );
$t->setAllStrings();

$t->set_file( "cache_admin_tpl", "cacheadmin.tpl" );

$t->set_block( "cache_admin_tpl", "cache_results_tpl", "cache_results" ); 



$t->set_var( "cache_results", "" );
if ( isset( $ClearCache ) )
{    
    // save the buffer contents
    $buffer =& ob_get_contents();
    ob_end_clean();

    // fetch the system printout
    ob_start();
    system( $siteDir . "./clearcache.sh" );    
    $ret = ob_get_contents();
    ob_end_clean();

    // fill the buffer with the old values
    ob_start();
    print( $buffer );
    
    $t->set_var( "cache_return", $ret );

    $t->parse( "cache_results", "cache_results_tpl" );
}



$t->pparse( "output", "cache_admin_tpl" );

?>
