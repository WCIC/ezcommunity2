<?
// 
// $Id: welcome.php,v 1.7 2001/03/08 19:02:43 ce Exp $
//
// Christoffer A. Elo <bf@ez.no>
// Created on: <13-Nov-2000 10:57:15 bf>
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZUserMain", "Language" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezuser/classes/ezmodule.php" );
include_once( "ezuser/classes/ezpermission.php" );
include_once( "ezsession/classes/ezsession.php" );


// Template
$t = new eZTemplate( "ezuser/admin/" . $ini->read_var( "eZUserMain", "AdminTemplateDir" ),
                     "ezuser/admin/intl", $Language, "welcome.php" );
$t->setAllStrings();



$t->set_file( array(
    "welcome_tpl" => "welcome.tpl"
    ) );

$t->set_block( "welcome_tpl", "error_tpl", "error" );

$t->set_block( "error_tpl", "libxml_error_tpl", "libxml_error" );
$t->set_block( "error_tpl", "qtdom_error_tpl", "qtdom_error" );
$t->set_block( "error_tpl", "convert_error_tpl", "convert_error" );

$t->set_var( "error", "" );
$t->set_var( "libxml_error", "" );
$t->set_var( "qtdom_error", "" );
$t->set_var( "convert_error", "" );

$user = eZUser::currentUser();

if ( $user )
{
    $t->set_var( "first_name", $user->firstName() );
    $t->set_var( "last_name", $user->lastName() );
}

if ( $ini->read_var( "site", "CheckDependes" ) == "enabled" )
{
    if ( function_exists( "xmltree" ) == false )
    {
        $t->set_var( "libxml_location", "http://xmlsoft.org/#Downloads" );
        $t->parse( "libxml_error", "libxml_error_tpl" );
        $error = true;
    }

    if ( function_exists( "qdom_tree" ) == false )
    {
        $t->set_var( "qtdom_location", "http://www.trolltech.com" );
        $t->parse( "qtdom_error", "qtdom_error_tpl" );
        $error = true;
    }

    $check = system( "convert > /dev/null", $ret );

    if ( $ret == "127" )
    {
        $t->set_var( "convert_location", "http://www.imagemagick.org/www/archives.html" );
        $t->parse( "convert_error", "convert_error_tpl" );
        $error = true;
    }


    if ( $error )
    {
        $t->parse( "error", "error_tpl" );
    }
}

$t->pparse( "output", "welcome_tpl" );

?>
