<?
// 
// $Id: rfp_cron.php,v 1.14.2.8 2003/12/17 18:21:00 ghb Exp $
//
// Created on: <04-Dec-2003 04:20:42 ghb>
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

//------------------------------------------------
// Index Placement Header
// Find out, where our files are.
//------------------------------------------------

set_time_limit( 0 );

if ( ereg( "(.*/)([^\/]+\.php)$", $SCRIPT_FILENAME, $regs ) )
    $siteDir = $regs[1];
elseif ( ereg( "(.*/)([^\/]+\.php)/?", $PHP_SELF, $regs ) )
    $siteDir = $DOCUMENT_ROOT . $regs[1];
else
//	$siteDir = "./";
      $siteDir = "/home/www/web/";

if ( substr( php_uname(), 0, 7) == "Windows" )
    $separator = ";";
else
    $separator = ":";

$includePath = ini_get( "include_path" );
if ( trim( $includePath ) != "" )
    $includePath .= $separator . $siteDir;
else
    $includePath = $siteDir;
ini_set( "include_path", $includePath );

// print( $includePath );
// print ( $siteDir . "\n\n" );
//------------------------------------------------

 include_once( "classes/INIFile.php" );
 $ini = new INIFile( "site.ini" );
 $GlobalSiteIni =& $ini;

//------------------------------------------------
// Send Email Deadline Reminders to Rfp Holders

 include_once( "ezrfp/classes/ezrfp.php" );
 include_once( "ezrfp/classes/ezrfpcategory.php" );

 $rfp = new eZRfp();
// $rfps =& $rfp->getAll();


//    	print( "rfp: " .  $rfp->name()."\n" );
	$rfp->emailRfpReminders();

//------------------------------------------------




//------------------------------------------------
// print date to log file (basic trigger log)

print("\n");
$today = date("F j, Y, g:i a"); 
// print('outputing date to file via cron . . .' );
// print("\n" .' creating /home/www/web/rfp_cron_log.txt' ."\n".' date: '. $today . "\n");
system('/bin/date >> /home/www/web/rfp_cron_log.txt');
// print("#---------------------------------------------- \n");

//------------------------------------------------
// print / output log text
// system('/usr/bin/cat /home/www/web/rfp_cron_log.txt');
//------------------------------------------------

?>
