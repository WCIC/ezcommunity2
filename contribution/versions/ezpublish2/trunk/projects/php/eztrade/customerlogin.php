<?
// 
// $Id: customerlogin.php,v 1.1 2000/10/06 13:46:24 bf-cvs Exp $
//
// 
//
// B�rd Farstad <bf@ez.no>
// Created on: <03-Oct-2000 16:45:30 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZTradeMain", "Language" );

include_once( "ezuser/classes/ezuser.php" );

if ( eZUser::currentUser() )
{
    print( "user logged in, redirect if the've got addresses" );
    Header( "Location: /trade/checkout/" );
}
else
{
    $t = new eZTemplate( "eztrade/" . $ini->read_var( "eZTradeMain", "TemplateDir" ) . "/customerlogin/",
                         "eztrade/intl/", $Language, "customerlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(        
        "customer_login_tpl" => "customerlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/trade/customerlogin/" );
    $t->pparse( "output", "customer_login_tpl" );
}

?>
