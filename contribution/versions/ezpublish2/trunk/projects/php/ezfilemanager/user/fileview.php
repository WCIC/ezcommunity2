<?
// 
// $Id: fileview.php,v 1.4 2001/01/30 11:46:12 jb Exp $
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <04-Jan-2001 16:47:23 ce>
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

include_once( "ezfilemanager/classes/ezvirtualfile.php" );
include_once( "ezfilemanager/classes/ezvirtualfolder.php" );
include_once( "ezuser/classes/ezuser.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZFileManagerMain", "Language" );


$t = new eZTemplate( "ezfilemanager/user/" . $ini->read_var( "eZFileManagerMain", "TemplateDir" ),
                     "ezfilemanager/user/intl/", $Language, "fileview.php" );

$t->set_file( "file_view", "fileview.tpl" );

$t->setAllStrings();

// $t->set_block( "file_view", "value_tpl", "value" );
$t->set_block( "file_view", "view_tpl", "view" );
$t->set_block( "file_view", "delete_tpl", "delete" );
$t->set_block( "file_view", "edit_tpl", "edit" );
$t->set_block( "file_view", "download_tpl", "download" );

$t->set_var( "delete", "" );
$t->set_var( "edit", "" );
$t->set_var( "download", "" );

if ( $FileID != 0 )
{
    $file = new eZVirtualFile( $FileID );

    $t->set_var( "file_name", $file->name() );
    $t->set_var( "file_id", $file->id() );
    $t->set_var( "file_description", $file->description() );

    $user = eZUser::currentUser();
    if ( $file->checkReadPermission( $user ) != false )
    {
        $t->parse( "download", "download_tpl" );
    }
    if ( $file->checkWritePermission( $user ) != false )
    {
        $t->parse( "delete", "delete_tpl" );
        $t->parse( "edit", "edit_tpl" );
    }

    $size_short = $file->shortenedFileSize();
    $t->set_var( "size_unit", $size_short["unit"] );
    $t->set_var( "file_size", $size_short["size-string"] );

    $fileOwner = $file->user();

    $t->set_var( "file_owner", $fileOwner->firstName() . " " . $fileOwner->lastName() );




    $t->parse( "view", "view_tpl" );
}


$folder = new eZVirtualFolder( $FolderID );

$folderList =& $folder->getByParent( $folder );

foreach ( $folderList as $folder )
{
    $t->set_var( "option_name", $folder->name() );
    $t->set_var( "option_value", $folder->id() );

    $t->set_var( "selected", "" );
    $t->set_var( "option_level", "" );

//    $t->parse( "value", "value_tpl", true );
}

$t->pparse( "output", "file_view" );


?>

