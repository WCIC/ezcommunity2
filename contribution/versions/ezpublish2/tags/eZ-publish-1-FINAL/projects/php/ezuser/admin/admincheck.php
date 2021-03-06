<?
// 
// $Id: admincheck.php,v 1.4 2000/10/26 13:13:46 ce-cvs Exp $
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <26-Oct-2000 15:11:17 ce>
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

$user = eZUser::currentUser();
if ( !$user )
{
    Header( "Location: /user/login" );
    exit();
}

if ( !eZPermission::checkPermission( $user, "eZUser", "AdminLogin" ) )
{
    eZUser::logout( $user );
    Header( "Location: /user/login" );
    exit();
}
?>

