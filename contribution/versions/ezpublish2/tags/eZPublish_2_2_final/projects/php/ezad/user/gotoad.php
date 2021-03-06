<?php
// 
// $Id: gotoad.php,v 1.6 2001/07/19 11:56:33 jakobn Exp $
//
// Created on: <25-Nov-2000 16:26:08 bf>
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

include_once( "ezuser/classes/ezuser.php" );

include_once( "ezad/classes/ezad.php" );
include_once( "ezad/classes/ezadclick.php" );
include_once( "ezstats/classes/ezpageview.php" );


if ( get_class( $GlobalPageView ) != "ezpageview" )
{
    $GlobalPageView = new eZPageView();
    $GlobalPageView->store();
}


$user =& eZUser::currentUser();

$ad = new eZAd( $AdID );

$click = new eZAdClick();
$click->setAd( $ad );
$click->setPageViewID( $GlobalPageView->id() );
$click->setPrice( $ad->clickPrice() );
$click->store();

$gotoURL = $ad->url();
Header( "Location: $gotoURL" );
exit();

?>
