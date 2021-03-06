<?
/*!
    $Id: norights.php,v 1.1 2000/10/20 15:43:38 ce-cvs Exp $

    Author: Lars Wilhelmsen <lw@ez.no>
    
    Created on: Created on: <14-Jul-2000 13:41:35 lw>
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/

include_once( "classes/INIFile.php" );

$ini = new INIFile( "site.ini" );
$DOC_ROOT = $ini->read_var( "eZLinkMain", "DocumentRoot" );
$Language = $ini->read_var( "eZLinkMain", "Language" );

include_once( "classes/ezdb.php" );
include_once( "classes/eztemplate.php" );

$t = new eZTemplate( "ezlink/admin/" . $ini->read_var( "eZLinkMain", "TemplateDir" ),
"ezlink/admin/" . "/intl", $Language, "noright.php" );
$t->setAllStrings();

$t->set_file( array( "norights" => "norights.tpl"
                     ) );

$t->pparse( "output", "norights" );
?>
