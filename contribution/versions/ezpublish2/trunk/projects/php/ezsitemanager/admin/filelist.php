<?php
// 
// $Id: filelist.php,v 1.3 2001/07/29 23:31:10 kaid Exp $
//
// Created on: <11-Jul-2001 15:37:33 bf>
//
// This source file is part of eZ publish, publishing software.
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
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
include_once( "classes/ezhttptool.php" );
include_once( "classes/ezfile.php" );


if ( isset( $Delete ) )
{
    foreach ( $FileDeleteArray as $file )
    {
        if ( eZFile::file_exists( "ezsitemanager/staticfiles/$file" ) )
        {
            eZFile::unlink( "ezsitemanager/staticfiles/$file" );
        }
    }

    foreach ( $ImageDeleteArray as $file )
    {
        if ( eZFile::file_exists( "ezsitemanager/staticfiles/images/$file" ) )
        {
            eZFile::unlink( "ezsitemanager/staticfiles/images/$file" );
        }
    }
}

if ( isset( $Upload ) )
{
    // upload file, if supplied
    $file = new eZFile();
    if ( $file->getUploadedFile( "userfile" ) == true )
    {
        $file->copy( "ezsitemanager/staticfiles/" . $file->name() );
    }

    // upload image, if supplied
    $image = new eZFile();
    
    if ( $image->getUploadedFile( "userimage" ) == true )
    {
        $image->copy( "ezsitemanager/staticfiles/images/" . $image->name() );
    }
}


$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZSiteManagerMain", "Language" );

$t = new eZTemplate( "ezsitemanager/admin/" . $ini->read_var( "eZSiteManagerMain", "AdminTemplateDir" ),
                     "ezsitemanager/admin/" . "/intl", $Language, "filelist.php" );
$t->setAllStrings();

$t->set_file( "file_list_tpl", "filelist.tpl" );

$t->set_block( "file_list_tpl", "file_tpl", "file" );
$t->set_block( "file_list_tpl", "image_tpl", "image" );

$t->set_var( "file", "" );
$t->set_var( "image", "" );
$dir = eZFile::dir( "ezsitemanager/staticfiles/" );
$ret = array();
while ( $entry = $dir->read() )
{
    if ( $entry != "." && $entry != ".." && $entry != "images" )
    {
        $t->set_var( "file_name", $entry );
        $t->parse( "file", "file_tpl", true );
    }
}

$dir = eZFile::dir( "ezsitemanager/staticfiles/images" );
$ret = array();
while ( $entry = $dir->read() )
{
    if ( $entry != "." && $entry != ".." )
    {
        $t->set_var( "file_name", $entry );
        $t->parse( "image", "image_tpl", true );
    }
}
$t->pparse( "output", "file_list_tpl" );

?>
