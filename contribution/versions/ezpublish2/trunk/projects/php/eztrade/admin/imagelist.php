<?
// 
// $Id: imagelist.php,v 1.1 2000/09/21 12:42:24 bf-cvs Exp $
//
// Definition of eZCompany class
//
// B�rd Farstad <bf@ez.no>
// Created on: <21-Sep-2000 10:32:19 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZTradeMain", "Language" );
$DOC_ROOT = $ini->read_var( "eZTradeMain", "DocumentRoot" );

include_once( $DOC_ROOT . "/classes/ezproductcategory.php" );
include_once( $DOC_ROOT . "/classes/ezproduct.php" );
include_once( $DOC_ROOT . "/classes/ezoption.php" );

$t = new eZTemplate( $DOC_ROOT . "/admin/" . $ini->read_var( "eZTradeMain", "TemplateDir" ) . "/imagelist/",
                     $DOC_ROOT . "/admin/intl/", $Language, "imagelist.php" );

$t->setAllStrings();

$t->set_file( array(
    "image_list_page" => "imagelist.tpl",
    "image_item" => "imageitem.tpl"
    ) );

$product = new eZProduct( $ProductID );
    
$t->set_var( "product_name", $product->name() );


$t->pparse( "output", "image_list_page" );

?>
