<?
// 
// $Id: bugreport.php,v 1.9 2001/02/19 13:43:43 fh Exp $
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

include_once( "classes/ezmail.php" );

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

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


// new inserts new bug
// update, updates the bug with new values.
// when updating and inserting values must be checked.
// you must save the bug before you can add images/files.
$successfull = 0;
$actionValue == "New";
if( $Action == "New" )
{
    /*   $user = eZUser::currentUser();

    if( ( $Name != "" ) && ( $Description != "" ) )
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

            $successfull = 1;
            $actionValue = "Update";
        }
        else
        {
            if ( $bug->setUserEmail( $Email ) )
            {
                $bug->setIsHandled( false );
                $bug->store();
                
                $category->addBug( $bug );
                $module->addBug( $bug );
                
                $successfull = 2;
                $actionValue = "Update";
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
    */
    $bug = new eZBug();
    $bug->store();
    $BugID = $bug->id();
}

if( $Action == "Update" )
{
    $bug = new eZBug( $BugID );
    $bug->setName( $Name );
    $bug->setDescription( $Description );

    $category = new eZBugCategory( $CategoryID );
    $module = new eZBugModule( $ModuleID );
    $bug->removeFromCategories();
    $bug->removeFromModules();

    if( $category )
        $category->addBug( $bug );
    if( $module )
        $module->addBug( $bug );
    
    $user = eZUser::currentUser();
    if( $user )
        $bug->setUser( $user );
    else
        $bug->setUserEmail( $Email );
    
    $bug->setIsHandled( false );
    $bug->store();
    
    $actionValue = "Update";
}


/* bug is now allways saved... lets check what the user really wanted to do..*/
if( isset( $Ok ) ) // here check for errors. and display them if nescacary
{
    $user = eZUser::currentUser();
    if( ( $Name != "" ) && ( $Description != "" ) )
    {
        if ( $user )
        {
            $successfull = 1;
            send_email( $bug, $ini, $Language );            
            header( "Location: /bug/reportsuccess/" );
            exit();                
        }
        else
        {
            if( $Email != "" )
            {
                $successfull = 2;
                send_email( $bug, $ini, $Language );            
                header( "Location: /bug/reportsuccess/" );
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

if( isset( $InsertFile ) ) 
{
    $Action = "";
//    $BugID = $bug->id();
    include( "ezbug/user/fileedit.php" );
    exit();
}

if( isset( $InsertImage ) )
{
    $Action = "";
//    $BugID = $bug->id();
    include( "ezbug/user/imageedit.php" );
    exit();
}

/* user didn't press any buttons.. lets set up the view correctly then..*/
$catName = "";
$modName = "";
$t->set_var( "description_value", "" );
$t->set_var( "title_value", "" );
if( $Action == "Edit" ) // load values from database
{
    $bug = new eZBug( $BugID );
    $module = $bug->module();
    if( $module )
        $modName = $module->name();

    $category = $bug->category();
    if( $category )
        $catName = $category->name();

    $t->set_var( "description_value", $bug->description() );
    $t->set_var( "title_value", $bug->name() );
}

// if any errors are set, lets display them to the user.
if ( $AllFieldsError == true )
{
    $t->parse( "all_fields_error", "all_fields_error_tpl" );
}
else
{
    $t->set_var( "all_fields_error", "" );
}

if( $EmailError == true )
{
    $t->parse( "email_error", "email_error_tpl" );
}
else
{
    $t->set_var( "email_error", "" );
}


// insert values into fields.
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

    if( $category->name() == $catName )
        $t->set_var( "selected", "selected" );
    else
        $t->set_var( "selected", "" );
        
    $t->parse( "category_item", "category_item_tpl", true );
}

// list the categories
$modules = $module->getAll();
foreach ( $modules as $module )
{
    $t->set_var( "module_id", $module->id() );
    $t->set_var( "module_name", $module->name() );

    if( $module->name() == $modName )
        $t->set_var( "selected", "selected" );
    else
        $t->set_var( "selected", "" );

    
    $t->parse( "module_item", "module_item_tpl", true );
}

//$t->set_var( "action_value", $actionValue );
$t->set_var( "bug_id", $BugID );

$t->pparse( "output", "bug_report_tpl" );


function send_email( $bug, $ini, $Language )
{
    // set up some values that we need
    $module = $bug->module();
    
    // find the owners of the group, if their is now owner group, no need to send mail.
    $ownerGroup = $module->ownerGroup();
    if( get_class( $ownerGroup ) != "ezusergroup" )
    {
        Header( "Location: /bug/reportsuccess/" );
        exit();                
    }
    $users = $ownerGroup->users();
    $userEmail = "";
    foreach( $users as $userItem )
    {
        if( $userEmail = "" )
            $userEmail = $userItem->email();
        else
            $userEmail = $userEmail . ", " . $userItem->email();
    }

    $mail = new eZMail();
    if( $succesfull == 1 )
        $mail->setFrom( $user->email() );
    else
        $mail->setFrom( $bug->userEmail );

    $mailTemplate = new eZTemplate( "ezbug/user/" . $ini->read_var( "eZBugMain", "TemplateDir" ),
                                    "ezbug/user/intl", $Language, "mailnewbug.php" );
    $headerInfo = ( getallheaders() );

    $mailTemplate->set_file( "mailnewbug", "mailnewbug.tpl" );
    $mailTemplate->setAllStrings();

    $host = preg_replace( "/^admin\./", "", $headerInfo["Host"] );
            
    $mailTemplate->set_var( "bug_url", "http://" . $host . "/bug/bugview/" . $bug->id() );
    $mailTemplate->set_var( "bug_id", $bug->id() );
    $mailTemplate->set_var( "bug_title", $bug->name() );
    $mailTemplate->set_var( "bug_module", $module->name() );
    if( $user )
        $mailTemplate->set_var( "bug_reporter", $user->namedEmail() );
    else
        $mailTemplate->set_var( "bug_reporter", $Email );
    $mailTemplate->set_var( "bug_description", $bug->description() );
        
    $mail->setSubject( "[Bug][" . $bug->id() ."] " . $bug->name() );
    $bodyText = ( $mailTemplate->parse( "dummy", "mailnewbug" ) );
    $mail->setBody( $bodyText );

    if( $userEmail != "" )
    {
        $mail->setTo( $userEmail  );
        print( $userEmail );
        $mail->send();
    }

}

?>


