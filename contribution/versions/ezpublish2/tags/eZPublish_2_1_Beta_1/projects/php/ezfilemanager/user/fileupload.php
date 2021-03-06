<?
// 
// $Id: fileupload.php,v 1.28 2001/04/20 15:17:45 ce Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <10-Dec-2000 15:49:57 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2001 eZ systems as
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
include_once( "classes/ezfile.php" );
include_once( "classes/ezhttptool.php" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );
include_once( "ezfilemanager/classes/ezvirtualfile.php" );
include_once( "ezfilemanager/classes/ezvirtualfolder.php" );

if ( isSet ( $NewFile ) )
{
    $Action = "New";
}
if ( isSet ( $NewFolder ) )
{
    eZHTTPTool::header( "Location: /filemanager/folder/new/$FolderID" );
    exit();
}

if ( isSet( $DeleteFiles ) )
{
    $Action = "DeleteFiles";
}

if ( isSet( $Delete ) )
{
    $Action = "Delete";
}

if ( isSet( $DeleteFolders ) )
{
    $Action = "DeleteFolders";
}

if ( isSet ( $Cancel ) )
{
    eZHTTPTool::header( "Location: /filemanager/list/" . $parentID );
    exit();
}

if ( isSet ( $Download ) )
{
    $file = new eZVirtualFile( $FileID );
    $fileName = $file->originalFileName();

    eZHTTPTool::header( "Location: /filemanager/download/$FileID/$fileName/" );
    exit();
}

$user = eZUser::currentUser();

if ( !$user )
{
    eZHTTPTool::header( "Location: /error/403/" );
    exit();
}

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZFileManagerMain", "Language" );


$t = new eZTemplate( "ezfilemanager/user/" . $ini->read_var( "eZFileManagerMain", "TemplateDir" ),
                     "ezfilemanager/user/intl/", $Language, "fileupload.php" );

$t->set_file( "file_upload_tpl", "fileupload.tpl" );

$t->setAllStrings();

$t->set_block( "file_upload_tpl", "value_tpl", "value" );
$t->set_block( "file_upload_tpl", "errors_tpl", "errors" );

$t->set_block( "file_upload_tpl", "write_group_item_tpl", "write_group_item" );
$t->set_block( "file_upload_tpl", "read_group_item_tpl", "read_group_item" );

$t->set_var( "errors", "&nbsp;" );

$t->set_var( "name_value", "$Name" );
$t->set_var( "description_value", "$Description" );

$error = false;
$nameCheck = true;
$descriptionCheck = false;
$folderPermissionCheck = true;
$readCheck = true;
$fileCheck = true;

$t->set_block( "errors_tpl", "error_write_permission", "write_permission" );
$t->set_var( "write_permission", "&nbsp;" );

$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
$t->set_var( "error_name", "&nbsp;" );

$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
$t->set_var( "error_file_upload", "&nbsp" );

$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
$t->set_var( "error_description", "&nbsp;" );

$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
$t->set_var( "error_read_everybody_permission", "&nbsp;" );

$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
$t->set_var( "error_write_everybody_permission", "&nbsp;" );


if ( $Action == "Insert" || $Action == "Update" )
{
    if ( $folderPermissionCheck )
    {
        $folder = new eZVirtualFolder( $FolderID );
        
        if ( eZObjectPermission::hasPermission( $folder, "filemanager_folder", "w", $user ) )
        {
            $t->parse( "write_permission", "error_write_permission" ); 
            $error = true;
        }
    }

    if ( count ( $ReadGroupArrayID ) > 1 )
    {
        foreach ( $ReadGroupArrayID as $Read )
        {
            if ( $Read == 0 )
            {
                $t->parse( "error_read_everybody_permission", "error_read_everybody_permission_tpl" );
                $error = true;
            }
        }
    }

    if ( count ( $WriteGroupArrayID ) > 1 )
    {
        foreach ( $WriteGroupArrayID as $Write )
        {
            if ( $Write == 0 )
            {
                $t->parse( "error_write_everybody_permission", "error_write_everybody_permission_tpl" );
                $error = true;
            }
        }
    }

    if ( $nameCheck )
    {
        
        if ( empty ( $Name ) )
        {
            $t->parse( "error_name", "error_name_tpl" );
            $error = true;
        }
    }

    if ( $descriptionCheck )
    {
        if ( empty ( $Description ) )
        {
            $t->parse( "error_description", "error_description_tpl" );
            $error = true;
        }
    }

    if ( $fileCheck )
    {
        $file = new eZFile();
        if ( $file->getUploadedFile( "userfile" ) == false )
        {
            if ( $Action == "Insert" )
            {
                $error = true;
                $t->parse( "error_file_upload", "error_file_upload_tpl" );
            }
        }
    }

    if ( $error )
    {
        $t->parse( "errors", "errors_tpl" );
    }
}

if ( $Action == "Insert" && $error == false )
{
    $uploadedFile = new eZVirtualFile();
    $uploadedFile->setName( $Name );
    $uploadedFile->setDescription( $Description );
    
    $uploadedFile->setUser( $user );
    
    $uploadedFile->setFile( &$file );
    
    $uploadedFile->store();

    if ( count ( $ReadGroupArrayID ) > 0 )
    {
        foreach ( $ReadGroupArrayID as $Read )
        {
            if ( $Read == 0 )
                $group = -1;
            else
                $group = new eZUserGroup( $Read );

            eZObjectPermission::setPermission( $group, $uploadedFile->id(), "filemanager_file", "r" );
        }
    }

    if ( count ( $WriteGroupArrayID ) > 0 )
    {
        foreach ( $WriteGroupArrayID as $Write )
        {
            if ( $Write == 0 )
                $group = -1;
            else
                $group = new eZUserGroup( $Write );
            
            eZObjectPermission::setPermission( $group, $uploadedFile->id(), "filemanager_file", "w" );
        }
    }
    
    $folder = new eZVirtualFolder( $FolderID );
    
    $folder->addFile( $uploadedFile );
    
    eZLog::writeNotice( "File added to file manager from IP: $REMOTE_ADDR" );
    eZHTTPTool::header( "Location: /filemanager/list/$FolderID/" );
    exit();
}

if ( $Action == "Update" && $error == false )
{
    $file = new eZFile( );

    $uploadedFile = new eZVirtualFile( $FileID );

    $uploadedFile->setName( $Name );
    $uploadedFile->setDescription( $Description );
    
    if ( $file->getUploadedFile( "userfile" ) )
    {
        $uploadedFile->setFile( $file );
    }    

    $uploadedFile->store();

    eZObjectPermission::removePermissions( $FileID, "filemanager_file", 'r' );    
    if ( count ( $ReadGroupArrayID ) > 0 )
    {
        foreach ( $ReadGroupArrayID as $Read )
        {
            if ( $Read == 0 )
                $group = -1;
            else
                $group = new eZUserGroup( $Read );

            eZObjectPermission::setPermission( $group, $uploadedFile->id(), "filemanager_file", "r" );
        }
    }

    eZObjectPermission::removePermissions( $FileID, "filemanager_file", 'w' );
    if ( count ( $WriteGroupArrayID ) > 0 )
    {
        foreach ( $WriteGroupArrayID as $Write )
        {
            if ( $Write == 0 )
                $group = -1;
            else
                $group = new eZUserGroup( $Write );
            
            eZObjectPermission::setPermission( $group, $uploadedFile->id(), "filemanager_file", "w" );
        }
    }

    $folder = new eZVirtualFolder( $FolderID );

    $uploadedFile->removeFolders();
    
    $folder->addFile( $uploadedFile );

    eZLog::writeNotice( "File added to file manager from IP: $REMOTE_ADDR" );
    eZHTTPTool::header( "Location: /filemanager/list/$FolderID/" );
}

if ( $Action == "DeleteFiles" )
{
    $oldFolder = 0;
    if ( count ( $FileArrayID ) != 0 )
    {
        foreach( $FileArrayID as $ID )
        {
            $file = new eZVirtualFile( $ID );
            $oldParent = $file->folder();

            if( $oldParent )
                $oldFolder = $oldParent->id();

            $file->delete();
        }
    }

    eZHTTPTool::header( "Location: /filemanager/list/$oldFolder/" );
    exit();
}

if ( $Action == "Delete" )
{
    $file = new eZVirtualFile( $FileID );
    $oldParent = $file->folder();
    
    if( $oldParent )
        $oldFolder = $oldParent->id();

    $file->delete();

    eZHTTPTool::header( "Location: /filemanager/list/$oldFolder/" );
    exit();
}

if ( $Action == "DeleteFolders" )
{
    $oldFolder = 0;
    if ( count ( $FolderArrayID ) > 0 )
    {
        foreach ( $FolderArrayID as $FolderID )
        {
            $folder = new eZVirtualFolder( $FolderID );
            $oldParent = $folder->parent();

            if( $oldParent )
                $oldFolder = $oldParent->id();

            $folder->delete();
        }
    }

    eZHTTPTool::header( "Location: /filemanager/list/$oldFolder/" );
    exit();
}


$t->set_var( "write_everybody", "" );
$t->set_var( "read_everybody", "" );
if ( $Action == "New" || $error )
{
    $t->set_var( "action_value", "insert" );
    $t->set_var( "file_id", "" );
    $t->set_var( "write_everybody", "selected" );
    $t->set_var( "read_everybody", "selected" );
}

if ( $Action == "Edit" )
{
    $file = new eZVirtualFile( $FileID );

    $t->set_var( "name_value", $file->name() );
    $t->set_var( "description_value", $file->description() );
    $t->set_var( "file_id", $file->id() );

    $folder = $file->folder();

    if ( $folder )
        $FolderID = $folder->id();

    $readGroupArrayID =& eZObjectPermission::getGroups( $file->id(), "filemanager_file", "r", false );
    $writeGroupArrayID =& eZObjectPermission::getGroups( $file->id(), "filemanager_file", "w", false );

    $t->set_var( "action_value", "update" );
}

// Print out all the groups.

$group = new eZUserGroup();
$groups = $group->getAll();

foreach ( $groups as $group )
{
    $t->set_var( "group_id", $group->id() );
    $t->set_var( "group_name", $group->name() );

    $t->set_var( "is_read_selected1", "" );
    $t->set_var( "is_write_selected1", "" );
    
    if ( $readGroupArrayID )
    {
        foreach ( $readGroupArrayID as $readGroup )
        {
            if ( $readGroup == $group->id() )
            {
                $t->set_var( "is_read_selected1", "selected" );
            }
            elseif ( $readGroup == -1 )
            {
                $t->set_var( "read_everybody", "selected" );                    
            }
            else
            {
                $t->set_var( "is_read_selected", "" );
            }
        }
           
    }

    if ( $writeGroupArrayID )
    {
        foreach ( $writeGroupArrayID as $writeGroup )
        {
            if ( $writeGroup == $group->id() )
            {
                $t->set_var( "is_write_selected1", "selected" );
            }
            elseif ( $writeGroup == -1 )
            {
                $t->set_var( "write_everybody", "selected" );                    
            }
            else
            {
                $t->set_var( "is_write_selected", "" );
            }
        }
    }

    $t->parse( "write_group_item", "write_group_item_tpl", true );
    $t->parse( "read_group_item", "read_group_item_tpl", true );
}


$folder = new eZVirtualFolder() ;

$folderList = $folder->getTree( );

foreach ( $folderList as $folderItem )
{
    if( eZObjectPermission::hasPermission( $folderItem[0]->id(), "filemanager_folder", 'w' ) ||
        eZVirtualFolder::isOwner( eZUser::currentUser(), $folderItem[0]->id() ) )
    {
        $t->set_var( "option_name", $folderItem[0]->name() );
        $t->set_var( "option_value", $folderItem[0]->id() );

        if ( $folderItem[1] > 0 )
            $t->set_var( "option_level", str_repeat( "&nbsp;", $folderItem[1] ) );
        else
            $t->set_var( "option_level", "" );

        $t->set_var( "selected", "" );
    
        if ( $folder && !$FolderID )
        {
            $FolderID = $folder->id();
        }

        if ( $FolderID )
        {
            if ( $folderItem[0]->id() == $FolderID )
            {
                $t->set_var( "selected", "selected" );
            }
        }

        $t->parse( "value", "value_tpl", true );
    }
}

$t->pparse( "output", "file_upload_tpl" );
?>

