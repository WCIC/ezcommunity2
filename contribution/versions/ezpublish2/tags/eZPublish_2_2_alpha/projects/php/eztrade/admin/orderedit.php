<?php
// 
// $Id: orderedit.php,v 1.25 2001/08/10 12:15:26 jhe Exp $
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

if ( isSet( $Cancel ) )
{
    Header( "Location: /trade/orderlist/" );
    exit();
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );
include_once( "ezcontact/classes/ezperson.php" );
include_once( "ezcontact/classes/ezcompany.php" );


$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZTradeMain", "Language" );
$ShowPriceGroups = $ini->read_var( "eZTradeMain", "PriceGroupsEnabled" ) == "true";

$languageINI = new INIFIle( "eztrade/admin/intl/" . $Language . "/orderedit.php.ini", false );


include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezorder.php" );
include_once( "eztrade/classes/ezcheckout.php" );
include_once( "eztrade/classes/ezpricegroup.php" );

include_once( "eztrade/classes/ezorderstatustype.php" );

if ( $Action == "newstatus" )
{
    $status = new eZOrderStatus();
    
    // store the status
    $statusType = new eZOrderStatusType( $StatusID );

    $status = new eZOrderStatus();
    $status->setType( $statusType );
    $status->setComment( $StatusComment );
    $status->setOrderID( $OrderID );

    $user =& eZUser::currentUser();

    $status->setAdmin( $user );
    $status->store();            

    Header( "Location: /trade/orderlist/" );
    exit();
}

if ( $Action == "delete" )
{
    $order = new eZOrder( $OrderID );
    $order->delete();
    
    Header( "Location: /trade/orderlist/" );
    exit();
}

$t = new eZTemplate( "eztrade/admin/" . $ini->read_var( "eZTradeMain", "AdminTemplateDir" ),
                     "eztrade/admin/intl/", $Language, "orderedit.php" );

$t->setAllStrings();

$t->set_file( "order_edit_tpl", "orderedit.tpl" );

$t->set_block( "order_edit_tpl", "visa_tpl", "visa" );
$t->set_block( "order_edit_tpl", "mastercard_tpl", "mastercard" );
$t->set_block( "order_edit_tpl", "cod_tpl", "cod" );
$t->set_block( "order_edit_tpl", "invoice_tpl", "invoice" );

$t->set_block( "order_edit_tpl", "order_status_option_tpl", "order_status_option" );
$t->set_block( "order_edit_tpl", "order_status_history_tpl", "order_status_history" );

$t->set_block( "order_edit_tpl", "order_item_list_tpl", "order_item_list" );
$t->set_block( "order_item_list_tpl", "order_item_tpl", "order_item" );

$t->set_block( "order_item_tpl", "order_item_option_tpl", "order_item_option" );

$order = new eZOrder( $OrderID );

// get the customer

$user = $order->user();

if ( $user )
{
    if ( $order->personID() == 0 && $order->companyID() == 0 )
    {
        $t->set_var( "customer_email", $user->email() );    
        $t->set_var( "customer_first_name", $user->firstName() );
        $t->set_var( "customer_last_name", $user->lastName() );
    }
    else
    {
        if ( $order->personID() > 0 )
        {
            $customer = new eZPerson( $order->personID() );
            $t->set_var( "customer_first_name", $customer->firstName() );
            $t->set_var( "customer_last_name", $customer->lastName() );
        }
        else
        {
            $customer = new eZCompany( $order->companyID() );
            $t->set_var( "customer_first_name", $customer->name() );
            $t->set_var( "customer_last_name", "" );
        }
        $emailList = $customer->emailAddress();
        $t->set_var( "customer_email", $emailList[0] );
    }

    // print out the addresses
    $shippingAddress =& $order->shippingAddress();

    $t->set_var( "shipping_street1", $shippingAddress->street1() );
    $t->set_var( "shipping_street2", $shippingAddress->street2() );
    $t->set_var( "shipping_zip", $shippingAddress->zip() );
    $t->set_var( "shipping_place", $shippingAddress->place() );

    if ( $order->personID() == 0 && $order->companyID() == 0 )
    {    
        $shippingUser = $order->shippingUser();

        if ( $shippingUser )
        {
            $t->set_var( "shipping_first_name", $shippingUser->firstName() );
            $t->set_var( "shipping_last_name", $shippingUser->lastName() );   
        }
    }
    else
    {
        if ( $order->personID() > 0 )
        {
            $customer = new eZPerson( $order->personID() );
            $t->set_var( "shipping_first_name", $customer->firstName() );
            $t->set_var( "shipping_last_name", $customer->lastName() );
        }
        else
        {
            $customer = new eZCompany( $order->companyID() );
            $t->set_var( "shipping_first_name", $customer->name() );
            $t->set_var( "shipping_last_name", "" );
        }
    }

    $country = $shippingAddress->country();
    if ( is_object( $country ) )
    {
        $t->set_var( "shipping_country", $country->name() );
    }
    else
    {
        $t->set_var( "shipping_country", "" );
    }
    $billingAddress =& $order->billingAddress();

    $t->set_var( "billing_street1", $billingAddress->street1() );
    $t->set_var( "billing_street2", $billingAddress->street2() );
    $t->set_var( "billing_zip", $billingAddress->zip() );
    $t->set_var( "billing_place", $billingAddress->place() );
    
    $PriceGroup = eZPriceGroup::correctPriceGroup( $user->groups( true ) );

    $country = $billingAddress->country();
    if ( is_object( $country ) )
    {
        $t->set_var( "billing_country", $country->name() );
    }
    else
    {
        $t->set_var( "billing_country", "" );
    }
}

// fetch the order items
$items = $order->items( $OrderType );
$locale = new eZLocale( $Language );
$currency = new eZCurrency();

$i = 0;
$sum = 0.0;
foreach ( $items as $item )
{
    $product =& $item->product();

    $image = $product->thumbnailImage();
    
    if ( $image )
    {
        $thumbnail =& $image->requestImageVariation( 35, 35 );
        
        $t->set_var( "product_image_path", "/" . $thumbnail->imagePath() );
        $t->set_var( "product_image_width", $thumbnail->width() );
        $t->set_var( "product_image_height", $thumbnail->height() );
        $t->set_var( "product_image_caption", $image->caption() );
    }

    $priceobj = new eZCurrency();
    
    if ( $product->showPrice() == true and $product->hasPrice() )
    {
        $found_price = false;
        if ( $ShowPriceGroups and $PriceGroup > 0 )
        {
            $price = eZPriceGroup::correctPrice( $product->id(), $PriceGroup );
            if ( $price )
            {
                $found_price = true;
                $priceobj->setValue( $price * $item->count() );
            }
        }
        if ( !$found_price )
        {
            $priceobj->setValue( $product->price() * $item->count() );
        }
        $t->set_var( "product_price", $locale->format( $priceobj ) );
    }
    else
    {
        $priceobj->setValue( $product->price() * $item->count() );
        $priceArray = "";
        $options =& $product->options();
        if ( count( $options ) == 1 )
        {
            $option = $options[0];
            if ( get_class( $option ) == "ezoption" )
            {
                $optionValues =& $option->values();
                if ( count( $optionValues ) > 1 )
                {
                    $i=0;
                    foreach ( $optionValues as $optionValue )
                    {
                        $found_price = false;
                        if ( $ShowPriceGroups and $PriceGroup > 0 )
                        {
                            $priceArray[$i] = eZPriceGroup::correctPrice( $product->id(), $PriceGroup, $option->id(), $optionValue->id() );
                            if ( $priceArray[$i] )
                            {
                                $found_price = true;
                                $priceArray[$i] = $priceArray[$i];
                            }
                        }
                        if ( !$found_price )
                        {
                            $priceArray[$i] = $optionValue->price();
                        }
                        $i++;
                    }
                    $high = new eZCurrency( max( $priceArray ) );
                    $low = new eZCurrency( min( $priceArray ) );
                }
            }
        }
        else
        {
            $t->set_var( "product_price", "" );
        }
    }
    
    $price = $priceobj->value();    

    $currency->setValue( $price );

    $sum += $price;
    $t->set_var( "product_name", $product->name() );
    $t->set_var( "product_id", $product->id() );
    $t->set_var( "product_number", $product->productNumber() );
    $t->set_var( "product_price", $locale->format( $currency ) );

    $t->set_var( "order_item_count", $item->count() );
    
    if ( ( $i % 2 ) == 0 )
        $t->set_var( "td_class", "bglight" );
    else
        $t->set_var( "td_class", "bgdark" );

    $optionValues =& $item->optionValues();

    $t->set_var( "order_item_option", "" );
    foreach ( $optionValues as $optionValue )
    {
        $t->set_var( "option_name", $optionValue->optionName() );
        $t->set_var( "option_value", $optionValue->valueName() );
            
        $t->parse( "order_item_option", "order_item_option_tpl", true );
    }
    $t->parse( "order_item", "order_item_tpl", true );
        
    $i++;
}
$shippingCost = $order->shippingCharge();

$currency->setValue( $shippingCost );
$t->set_var( "shipping_cost", $locale->format( $currency ) );

$totalVAT = $order->totalVAT();
$currency->setValue( $totalVAT );
$t->set_var( "vat_cost", $locale->format( $currency ) );
if ( $order->isVATInc() )
{
    $sum = $order->totalPriceIncVAT() + $shippingCost;
    $currency->setValue( $sum );
    $t->set_var( "order_sum", $locale->format( $currency ) );
}
else
{
    $sum += $shippingCost;
    $currency->setValue( $sum );
    $t->set_var( "order_sum", $locale->format( $currency ) );
}
$statusType = new eZOrderStatusType();
$statusTypeArray = $statusType->getAll();
foreach ( $statusTypeArray as $status )
{
    $statusName = preg_replace( "#intl-#", "", $status->name() );
    $statusName =  $languageINI->read_var( "strings", $statusName );
    
    $t->set_var( "option_name", $statusName );
    $t->set_var( "option_id", $status->id() );
    $t->parse( "order_status_option", "order_status_option_tpl", true );
}


$historyArray = $order->statusHistory();
$i=0;
foreach ( $historyArray as $status )
{
    if ( ( $i % 2 ) == 0 )
        $t->set_var( "td_class", "bglight" );
    else
        $t->set_var( "td_class", "bgdark" );

    $admin =  $status->admin();
    
    $statusType = $status->type();

    $statusName = preg_replace( "#intl-#", "", $statusType->name() );
    $statusName =  $languageINI->read_var( "strings", $statusName );
    
    
    $t->set_var( "status_date", $locale->format( $status->altered() ) );
    $t->set_var( "status_name", $statusName );
    $t->set_var( "status_comment", $status->comment() );
    $t->set_var( "admin_login", $admin->login() );
    $t->parse( "order_status_history", "order_status_history_tpl", true );
    $i++;
}


$checkout = new eZCheckout();
$instance =& $checkout->instance();
$paymentMethod = $instance->paymentName( $order->paymentMethod() );

$t->set_var( "payment_method", $paymentMethod );

$shippingType = $order->shippingType();
if ( $shippingType )
{    
    $t->set_var( "shipping_method", $shippingType->name() );
}

$t->set_var( "order_id", $order->id() );

$t->parse( "order_item_list", "order_item_list_tpl" );

$t->pparse( "output", "order_edit_tpl" );

?>
