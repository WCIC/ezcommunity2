<?
// 
// $Id: bugedit.php,v 1.27 2001/03/09 10:11:06 fh Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <28-Nov-2000 19:45:35 bf>
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
include_once( "classes/ezmail.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/eztexttool.php" );
include_once( "ezfilemanager/classes/ezvirtualfile.php" );
include_once( "ezimagecatalogue/classes/ezimage.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZBugMain", "Language" );

include_once( "ezbug/classes/ezbug.php" );
include_once( "ezbug/classes/ezbugcategory.php" );
include_once( "ezbug/classes/ezbugmodule.php" );
include_once( "ezbug/classes/ezbugpriority.php" );
include_once( "ezbug/classes/ezbugstatus.php" );
include_once( "ezbug/classes/ezbuglog.php" );

$session = new eZSession();

if ( isSet ( $Cancel ) )
{
    $bug = new eZBug( $BugID );

    if ( $bug->IsHandled() )
    {
        eZHTTPTool::header( "Location: /bug/archive/$ModuleID/" );
        exit();
    }
    else
    {
        eZHTTPTool::header( "Location: /bug/unhandled/" );
        exit();
    }
}

$t = new eZTemplate( "ezbug/admin/" . $ini->read_var( "eZBugMain", "TemplateDir" ),
                     "ezbug/admin/intl", $Language, "bugedit.php" );
$t->setAllStrings();
$t->set_var( "site_style", $SiteStyle );

$t->set_file( array(
    "bug_edit_tpl" => "bugedit.tpl"
    ) );

$t->set_block( "bug_edit_tpl", "module_item_tpl", "module_item" );
$t->set_block( "bug_edit_tpl", "category_item_tpl", "category_item" );
$t->set_block( "bug_edit_tpl", "priority_item_tpl", "priority_item" );
$t->set_block( "bug_edit_tpl", "status_item_tpl", "status_item" );
$t->set_block( "bug_edit_tpl", "owner_item_tpl", "owner_item" );

$t->set_block( "bug_edit_tpl", "log_item_tpl", "log_item" );
$t->set_block( "bug_edit_tpl", "file_headers_tpl", "file_headers" );
$t->set_block( "file_headers_tpl", "file_tpl", "file" );
$t->set_block( "bug_edit_tpl", "image_headers_tpl", "image_headers" );
$t->set_block( "image_headers_tpl", "image_tpl", "image" );


if ( $Action == "Insert" )
{
    $user = eZUser::currentUser();

    if ( $user )
    {
        $category = new eZBugCategory( $CategoryID );
        $module = new eZBugModule( $ModuleID );
        
        $bug = new eZBug();
        $bug->setName( $Name );
        $bug->setDescription( $Description );
        $bug->setUser( $user );
        $bug->setIsHandled( false );
        if ( $IsClosed == 'on' )
            $bug->setIsClosed( true );
        else
            $bug->setIsClosed( false );

        if( $IsPrivate == 'on' )
            $bug->setIsPrivate( true );
        else
            $bug->setIsPrivate( false );
        
        $bug->store();
        Header( "Location: /bug/archive/" );
        exit();
    }
}

if ( $Action == "Update" )
{
    $user = eZUser::currentUser();

    if ( $user )
    {
        if ( isset( $Update ) )
        {        
            $category = new eZBugCategory( $CategoryID );
            $module = new eZBugModule( $ModuleID );
            
            $priority = new eZBugPriority( $PriorityID );
            $status = new eZBugStatus( $StatusID );

            if( $OwnerID != -1 )
                $owner = new eZUser( $OwnerID );
            else
                $owner = NULL;
            
            $bug = new eZBug( $BugID );

            if ( $bug->IsHandled() )
                $isHandled = true;
            else
                $isHandled = false;
            
            $bug->setIsHandled( true );
            
            $bug->setPriority( $priority );
            $bug->setStatus( $status );
            // check if owner is actually among the valid owners
            $bug->setOwner( $owner );
            
            if ( $IsClosed == 'on' )
            {
                $bug->setIsClosed( true );
            }
            else
            {
                $bug->setIsClosed( false );
            }

            if( $IsPrivate == 'on'  )
            {
                $bug->setIsPrivate( true );
            }
            else
            {
                $bug->setIsPrivate( false );
            }

            $bug->setName( addSlashes( $bug->name() ) );
            $bug->setDescription( addSlashes( $bug->description() ) );
            

            $bug->removeFromModules();
            $bug->removeFromCategories();
            $bug->store();

                        
            $category->addBug( $bug );
            $module->addBug( $bug );

            $log = new eZBugLog();
            $log->setDescription( $LogMessage );
            $log->setUser( eZUser::currentUser() );
            $log->setBug( $bug );
            $log->store();

            // check if the owner has changed
            if( get_class( $owner ) == "ezuser" && $OwnerID != $CurrentOwnerID )
            {
                sendAssignedMail( $bug, $owner->email(), $ini, $Language );
            }
            
            if ( $MailReporter == "on" )
            {            
                // send email notice to the reporter            
                if ( $bug->user() )
                {
                    $reporter = $bug->user();
                    $reporter_email = $reporter->email();
                }
                else
                {
                    $reporter_email = $bug->userEmail();
                }

                $mail = new eZMail();
                $mail->setFrom( $user->email() );

                $locale = new eZLocale( $Language );
    
                $mailTemplate = new eZTemplate( "ezbug/admin/" . $ini->read_var( "eZBugMain", "AdminTemplateDir" ),
                                                "ezbug/admin/intl", $Language, "mailreply.php" );
            
                $headerInfo = ( getallheaders() );

                $mailTemplate->set_file( "mailreply", "mailreply.tpl" );
                $mailTemplate->setAllStrings();

                $host = preg_replace( "/^admin\./", "", $headerInfo["Host"] );
            
                $mailTemplate->set_var( "bug_url", "http://" . $host . "/bug/bugview/" . $bug->id() );
                $mailTemplate->set_var( "log_message", $LogMessage );
                $mailTemplate->set_var( "bug_id", $bug->id() );
                $mailTemplate->set_var( "bug_report", $bug->description() );
                $mailTemplate->set_var( "bug_title", $bug->name() );
                
                $bodyText = ( $mailTemplate->parse( "dummy", "mailreply" ) );

                $languageIni = new INIFile( "ezbug/admin/" . "intl/" . $Language . "/mailreply.php.ini", false );
                $msg =  $languageIni->read_var( "strings", "bug_handled" );

                $mail->setSubject( "[" . $msg . "]" . $bug->name() );
                $mail->setTo( $reporter_email );
                $mail->setBody( $bodyText );

                $mail->send();
            }

            $Action = "Edit";
            if( !isset( $InsertImage) && !isset( $InsertFile ) && !isset( $DeleteSelected ) )
            {
                if ( $isHandled )
                {
                    eZHTTPTool::header( "Location: /bug/archive/$ModuleID/" );
                    exit();

                }
                else
                {
                    eZHTTPTool::header( "Location: /bug/unhandled/" );
                    exit();
                }
            }
        }
        else
        {
            if( !isset( $InsertImage) && !isset( $InsertFile ) && !isset( $DeleteSelected ) )
            {
                Header( "Location: /bug/archive/" );
                exit();
            }
            
        }
    }
}

$t->set_var( "bug_date", "" );    
$t->set_var( "action_value", "Insert" );

if( isset( $InsertFile ) ) 
{
    $Action = "";
    eZHTTPTool::header( "Location: /bug/report/fileedit/new/$BugID" );
    exit();
}

if( isset( $InsertImage ) )
{
    $Action = "";
    eZHTTPTool::header( "Location: /bug/report/imageedit/new/$BugID" );
    exit();
}

if( isset( $DeleteSelected ) )
{
    $bug = new eZBug( $BugID );
    if( count( $ImageArrayID ) > 0 )
    {
        foreach( $ImageArrayID as $imageID )
        {
            $image = new eZImage( $imageID );
            $bug->deleteImage( $image );
        }
    }

    if( count( $FileArrayID ) > 0 )
    {
        foreach( $FileArrayID as $fileID )
        {
            $file = new eZVirtualFile( $fileID );
            $bug->deleteFile( $file );
        }
    }
    $Action = "Edit";
}

if ( $Action == "Edit" )
{
    $locale = new eZLocale( $Language );

    $bug = new eZBug( $BugID );
    
    $t->set_var( "bug_id", $bug->id() );
    $t->set_var( "name_value", $bug->name() );
    $bug_user = $bug->user();
    if( $bug_user )
    {
        $t->set_var( "reporter_name_value", $bug_user->namedEmail() );
    }
    else
    {
        $t->set_var( "reporter_name_value", "Unknown" );
    }
    $t->set_var( "description_value", eZTextTool::nl2br( htmlspecialchars( $bug->description() ) ) );
    $t->set_var( "action_value", "Update" );
    
    $date =& $bug->created();
    $t->set_var( "bug_date", $locale->format( $date ) );    


    $bugLog = new eZBugLog();
    $logList = $bugLog->getByBug( $bug );

    $cat =& $bug->category();
    if ( $cat )
        $categoryID = $cat->id();

    $module =& $bug->module();
    if ( $module )
        $moduleID = $module->id();

    $pri =& $bug->priority();
    $status =& $bug->status();

    if ( $status )
        $statusID = $status->id();

    if ( $pri )
        $priorityID = $pri->id();
    
    if( $bug->isClosed() == true )
    {
        $t->set_var( "is_closed", "checked" );
    }
    else
    {
        $t->set_var( "isclosed", "" );
    }

    if( $bug->isPrivate() == true )
    {
        $t->set_var( "is_private", "checked" );
    }
    else
    {
        $t->set_var( "is_private", "" );
    }


// get the files
    $files = $bug->files();
    
    if( count( $files ) > 0 )
    {
        $t->parse( "file_headers", "file_headers_tpl" );
        $i = 0;
        foreach( $files as $file )
        {
            if ( ( $i % 2 ) == 0 )
            {
                $t->set_var( "td_class", "bglight" );
            }
            else
            {
                $t->set_var( "td_class", "bgdark" );
            }

            $t->set_var( "file_number", $i + 1 );
            $t->set_var( "file_id", $file->id() );
        
            $t->set_var( "file_name", "<a href=\"/filemanager/download/" . $file->id() . "/" . $file->originalFileName() . "\">" . $file->name() . "</a>" );
    
            $t->parse( "file", "file_tpl", true );
    
            $i++;
        }
    }
    else
    {
        $t->set_var( "file_headers", "" );
        $t->set_var( "file", "" );
    }

    // get the images
    $images = $bug->images();
    if( count( $images ) > 0  )
    {
        $t->parse( "image_headers", "image_headers_tpl" );
        $i = 0;
        foreach( $images as $image )
        {
            if ( ( $i % 2 ) == 0 )
            {
                $t->set_var( "td_class", "bglight" );
            }
            else
            {
                $t->set_var( "td_class", "bgdark" );
            }
            $t->set_var( "image_number", $i + 1 );
            $t->set_var( "image_id", $image->id() );

            $t->set_var( "image_name", "<a href=\"/imagecatalogue/imageview/" . $image->id()  . "\">" . $image->caption() . "</a>" );
            $t->parse( "image", "image_tpl", true );
    
            $i++;

        }
    }
    else
    {
        $t->set_var( "image_headers", "" );
        $t->set_var( "image", "" );
    }
    
    if( count( $logList ) == 0 )
    {
        $t->set_var( "log_item", "" );
    }
    else
    {
        foreach ( $logList as $log )
        {
            $date =& $log->created();
        
            $t->set_var( "log_date", $locale->format( $date ) );
        
        
            $t->set_var( "log_description", $log->description() );
        
            $t->parse( "log_item", "log_item_tpl", true );
        }
    }
}


$category = new eZBugCategory();
$module = new eZBugModule();
$priority = new eZBugPriority();
$status = new eZBugStatus();


// list the categories
$categories = $category->getAll();
foreach ( $categories as $category )
{
    if ( $category->id() == $categoryID )
    {
        $t->set_var( "selected", "selected" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }
    
    $t->set_var( "category_id", $category->id() );
    $t->set_var( "category_name", $category->name() );

    $t->parse( "category_item", "category_item_tpl", true );
}

// list the modules
$modules = $module->getAll();
foreach ( $modules as $module )
{
    if ( $module->id() == $moduleID )
    {
        $t->set_var( "selected", "selected" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }

    $t->set_var( "module_id", $module->id() );
    $t->set_var( "module_name", $module->name() );

    $t->parse( "module_item", "module_item_tpl", true );
}

// list the priorities
$priorities = $priority->getAll();
foreach ( $priorities as $priority )
{
    if ( $priority->id() == $priorityID )
    {
        $t->set_var( "selected", "selected" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }
    
    $t->set_var( "priority_id", $priority->id() );
    $t->set_var( "priority_name", $priority->name() );

    $t->parse( "priority_item", "priority_item_tpl", true );
}

// list the statuses
$statuses = $status->getAll();
foreach ( $statuses as $status )
{
    if ( $status->id() == $statusID )
    {
        $t->set_var( "selected", "selected" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }
    
    $t->set_var( "status_id", $status->id() );
    $t->set_var( "status_name", $status->name() );

    $t->parse( "status_item", "status_item_tpl", true );
}

/*
// list the possible owners
$module = new eZBugModule( $moduleID );
$ownerGroup = $module->ownerGroup();
$owner = $bug->owner();
$currentOwner = -1;
if( get_class( $ownerGroup ) == "ezusergroup" )
{
    $users = $ownerGroup->users( $ownerGroup );
    if( count( $users ) > 0 )
    {
        foreach( $users as $userItem )
        {
            $t->set_var( "owner_id", $userItem->id() );
            $t->set_var( "owner_login", $userItem->login() );
            if( get_class( $owner ) == "ezuser" && $userItem->id() == $owner->id() )
            {
                $t->set_var( "selected", "selected" );
                $currentOwner = $owner->id();
            }
            else
            {
                $t->set_var( "selected", "" );
            }
            $t->parse( "owner_item", "owner_item_tpl" );
        }
    }
    else
        $t->set_var( "owner_item", "" );
}
else
{
    $t->set_var( "owner_item", "" );
}
*/

$t->set_var( "current_owner_id", $currentOwner );
$t->pparse( "output", "bug_edit_tpl" );

/*!
  This function sends a mail to the person whom the bug is assigned to.
  The code could just as well be pasted right in where function call is, but to keep
  things a little bit simpler, ive put it in here.
 */
function sendAssignedMail( $bug, $userEmail, $ini, $Language )
{
    $module = $bug->module();
    $user = $bug->user();
    if( $user )
        $reporter = $user->namedEmail();
    else
        $reporter = $bug->userEmail();

    $mail = new eZMail();
    $mail->setFrom( $user->email() );
    
    $mailTemplate = new eZTemplate( "ezbug/admin/" . $ini->read_var( "eZBugMain", "AdminTemplateDir" ),
                                    "ezbug/admin/intl", $Language, "mailgotbug.php" );
    $headerInfo = ( getallheaders() );

    $mailTemplate->set_file( "mailgotbug", "mailgotbug.tpl" );
    $mailTemplate->setAllStrings();

    $host = preg_replace( "/^admin\./", "", $headerInfo["Host"] );
            
    $mailTemplate->set_var( "bug_url", "http://" . $host . "/bug/bugview/" . $bug->id() );
    $mailTemplate->set_var( "bug_id", $bug->id() );
    $mailTemplate->set_var( "bug_title", $bug->name() );
    $mailTemplate->set_var( "bug_module", $module->name() );
    $mailTemplate->set_var( "bug_reporter", $reporter );
    $mailTemplate->set_var( "bug_description", $bug->description() );
    
    $languageIni = new INIFile( "ezbug/admin/" . "intl/" . $Language . "/mailgotbug.php.ini", false );
    $msg =  $languageIni->read_var( "strings", "assigned_to_you" );

    $mail->setSubject( "[". $msg ."][" . $bug->id() ."] " . $bug->name() );

    $bodyText = ( $mailTemplate->parse( "dummy", "mailgotbug" ) );
    $mail->setBody( $bodyText );

    $mail->setTo( $userEmail  );
    $mail->send();
}

?>
