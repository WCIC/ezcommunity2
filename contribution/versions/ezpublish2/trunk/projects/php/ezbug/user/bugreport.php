<?
// 
// $Id: bugreport.php,v 1.5 2000/12/03 17:16:15 bf-cvs Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <27-Nov-2000 20:31:00 bf>
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

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZBugMain", "Language" );

include_once( "ezbug/classes/ezbug.php" );
include_once( "ezbug/classes/ezbugcategory.php" );
include_once( "ezbug/classes/ezbugmodule.php" );

$t = new eZTemplate( "ezbug/user/" . $ini->read_var( "eZBugMain", "TemplateDir" ),
                     "ezbug/user/intl", $Language, "bugreport.php" );
$t->setAllStrings();

$t->set_file( array(
    "bug_report_tpl" => "bugreport.tpl"
    ) );

$t->set_block( "bug_report_tpl", "module_item_tpl", "module_item" );
$t->set_block( "bug_report_tpl", "category_item_tpl", "category_item" );
$t->set_block( "bug_report_tpl", "email_address_tpl", "email_address" );
$t->set_block( "bug_report_tpl", "all_fields_error_tpl", "all_fields_error" );
$t->set_block( "bug_report_tpl", "email_error_tpl", "email_error" );

if ( $Action == "Insert" )
{
    $user = eZUser::currentUser();

    if ( ( $Name != "" ) && ( $Description != "" ) )
    {
        $category = new eZBugCategory( $CategoryID );
        $module = new eZBugModule( $ModuleID );
        
        $bug = new eZBug();
        $bug->setName( $Name );
        $bug->setDescription( $Description );
        
        if ( $user )
        {
            $bug->setUser( $user );
            
            $bug->setIsHandled( false );
            $bug->store();
            
            $category->addBug( $bug );
            $module->addBug( $bug );
            
            Header( "Location: /bug/reportsuccess/" );
            exit();
        }
        else
        {
            if ( $bug->setUserEmail( $Email ) )
            {
                $bug->setIsHandled( false );
                $bug->store();
                
                $category->addBug( $bug );
                $module->addBug( $bug );
                
                Header( "Location: /bug/reportsuccess/" );
                exit();                
            }
            else
            {
                $EmailError = true;                
            }            
        }       
    }
    else
    {
        $AllFieldsError = true;
    }
}

if ( $AllFieldsError == true )
{
    $t->parse( "all_fields_error", "all_fields_error_tpl" );
}
else
{
    $t->set_var( "all_fields_error", "" );
}

if ( $EmailError == true )
{
    $t->parse( "email_error", "email_error_tpl" );
}
else
{
    $t->set_var( "email_error", "" );
}

$category = new eZBugCategory();
$module = new eZBugModule();

// show email address field if the user is not logged in
$user = eZUser::currentUser();

if ( $user )
{
    $t->set_var( "email_address", "" );
}
else
{
    $t->parse( "email_address", "email_address_tpl" );
}

// list the categories
$categories = $category->getAll();
foreach ( $categories as $category )
{
    $t->set_var( "category_id", $category->id() );
    $t->set_var( "category_name", $category->name() );

    $t->parse( "category_item", "category_item_tpl", true );
}

// list the categories
$modules = $module->getAll();
foreach ( $modules as $module )
{
    $t->set_var( "module_id", $module->id() );
    $t->set_var( "module_name", $module->name() );

    $t->parse( "module_item", "module_item_tpl", true );
}

$t->set_var( "action_value", "Insert" );


$t->pparse( "output", "bug_report_tpl" );

?>

