<?
// 
// $Id: grouplist.php,v 1.1 2000/10/02 15:46:42 ce-cvs Exp $
//
// Definition of eZUser class
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <20-Sep-2000 13:32:11 ce>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZUserMain", "Language" );
$DOC_ROOT = $ini->read_var( "eZUserMain", "DocumentRoot" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );

$t = new eZTemplate( $DOC_ROOT . "/admin/" . $ini->read_var( "eZUserMain", "TemplateDir" ) . "/grouplist/",
                     $DOC_ROOT . "/admin/" . "/intl", $Language, "grouplist.php" );
$t->setAllStrings();

$t->set_file( array(
    "group_list_page" => "grouplist.tpl",
    "group_item" => "groupitem.tpl"
    ) );

$group = new eZUserGroup();

$groupList = $group->getAll();

foreach( $groupList as $groupItem )
{
    $t->set_var( "group_id", $groupItem->id() );
    $t->set_var( "group_name", $groupItem->name() );

    $t->parse( "group_list", "group_item", true );
}

$t->pparse( "output", "group_list_page" );

?>
