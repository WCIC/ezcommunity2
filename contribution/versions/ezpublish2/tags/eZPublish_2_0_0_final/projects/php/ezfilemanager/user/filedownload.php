<?php
// 
// $Id: filedownload.php,v 1.10 2001/03/09 10:12:02 fh Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <10-Dec-2000 16:39:10 bf>
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

// clear what might be in the output buffer and stop the buffer.
ob_end_clean();


include_once( "ezfilemanager/classes/ezvirtualfile.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );

$user = eZUser::currentUser();

$file = new eZVirtualFile( $FileID );

//if ( eZObjectPermission::hasPermission( $file->id(), "filemanager_file", "r", $user ) == false )
//{
//    eZHTTPTool::header( "Location: /error/403/" );
//    exit();
//}

$fileName = $file->name();
$originalFileName = $file->originalFileName();
$filePath = $file->filePath( true );

// store the statistics
$file->addPageView( $GlobalPageView );
 
$filePath = preg_replace( "#.*/(.*)#", "\\1", $filePath );
 
//  print( $filePath );

$originalFileName = str_replace( " ", "%20", $originalFileName );
                                
//print( "Location: /filemanager/filedownload/$filePath/$originalFileName"  );
//exit();
 
Header( "Location: /filemanager/filedownload/$filePath/$originalFileName" );

exit();

?> 
