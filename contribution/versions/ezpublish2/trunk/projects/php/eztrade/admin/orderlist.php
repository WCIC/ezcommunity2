<?php
// 
// $Id: orderlist.php,v 1.19 2001/08/08 12:34:57 jhe Exp $
//
// Created on: <30-Sep-2000 13:03:13 bf>
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
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZTradeMain", "Language" );

include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezorder.php" );

include_once( "eztrade/classes/ezorderstatustype.php" );

if( isSet( $Delete ) && count( $OrderArrayID ) > 0 )
{
    foreach( $OrderArrayID as $orderid )
    {
        $order = new eZOrder( $orderid );
        $order->delete();
    }
}

$t = new eZTemplate( "eztrade/admin/" . $ini->read_var( "eZTradeMain", "AdminTemplateDir" ),
                     "eztrade/admin/intl/", $Language, "orderlist.php" );

$languageINI = new INIFile( "eztrade/admin/intl/" . $Language . "/orderlist.php.ini", false );

$t->setAllStrings();

$t->set_file( "order_list_tpl", "orderlist.tpl" );

$t->set_block( "order_list_tpl", "order_item_list_tpl", "order_item_list" );
$t->set_block( "order_item_list_tpl", "order_item_tpl", "order_item" );

// next prvious
$t->set_block( "order_list_tpl", "previous_tpl", "previous" );
$t->set_block( "order_list_tpl", "next_tpl", "next" );

$t->set_var( "site_style", $SiteStyle );

if ( isSet( $URLQueryText ) )
{
    $QueryText = urldecode( $URLQueryText );
}

$t->set_var( "query_string", $QueryText );

$t->set_var( "previous", "" );
$t->set_var( "next", "" );

if ( !isSet( $OrderBy ) )
    $OrderBy = "Date";

if ( !isSet( $Offset ) )
    $Offset = 0;

if ( !isSet( $Limit ) )
    $Limit = 15;

$t->set_var( "current_offset", $Offset );

$order = new eZOrder();

// perform search
if ( isSet( $QueryText ) )
{
    print( "Search for: ". $QueryText );
    $orderArray = $order->search( $QueryText, $Offset, $Limit );
    $total_count = $order->getSearchCount( $QueryText );
}
else
{
    $orderArray = $order->getAll( $Offset, $Limit, $OrderBy );
    $total_count = $order->getTotalCount( );
}

$prevOffs = $Offset - $Limit;
$nextOffs = $Offset + $Limit;
                
if ( $prevOffs >= 0 )
{
    $t->set_var( "prev_offset", $prevOffs  );
    $t->parse( "previous", "previous_tpl" );
}
else
{
    $t->set_var( "previous", "" );
}
                
if ( $nextOffs <= $total_count )
{
    $t->set_var( "next_offset", $nextOffs  );
    $t->parse( "next", "next_tpl" );
}
else
{
    $t->set_var( "next", "" );
}
                
$t->set_var( "limit", $Limit );
$t->set_var( "query_text", $QueryText );

if ( !$orderArray )
    $t->set_var( "order_item", "" );

$locale = new eZLocale( $Language );
$currency = new eZCurrency();
$i = 0;

foreach ( $orderArray as $order )
{
    if ( ( $i % 2 ) == 0 )
        $t->set_var( "td_class", "bgdark" );
    else
        $t->set_var( "td_class", "bglight" );
    
    $t->set_var( "order_id", $order->id() );
    $status = $order->initialStatus( );
    $dateTime = $status->altered();
    $t->set_var( "order_date", $locale->format( $dateTime ) );
    
    $status = $order->lastStatus( );
    $dateTime = $status->altered();
    $t->set_var( "altered_date", $locale->format( $dateTime ) );
    
    $statusType = $status->type();
    $statusName = preg_replace( "#intl-#", "", $statusType->name() );
    $statusName =  $languageINI->read_var( "strings", $statusName );
    $t->set_var( "order_status", $statusName );
    
    if ( $order->isVATInc() == true )
        $currency->setValue( $order->totalPriceIncVAT() + $order->shippingCharge());
    else
        $currency->setValue( $order->totalPrice() + $order->shippingCharge() );
    $t->set_var( "order_price", $locale->format( $currency ) );
    
    $t->parse( "order_item", "order_item_tpl", true );
    $i++;
}

$t->set_var( "url_query_string", urlencode( $QueryText ) );
$t->parse( "order_item_list", "order_item_list_tpl" );
$t->pparse( "output", "order_list_tpl" );

?>
