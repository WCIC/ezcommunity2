<?
/*!
    $Id: main.php,v 1.5 2000/10/17 13:43:58 ce-cvs Exp $

    Author: Lars Wilhelmsen <lw@ez.no>
    
    Created on: <28-Jul-2000 13:49:14 lw>
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/
include( "ezforum/dbsettings.php" );
include_once( "template.inc" );

$t = new Template( "$DOCROOT/admin/templates/" );

$t->set_file( Array( "main" => "main.tpl" ) );

$t->set_var( "docroot", $DOCROOT);


$t->pparse( "output", "main" );
