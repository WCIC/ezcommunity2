<?php
// 
// $Id: checkout.php,v 1.40 2001/03/05 15:06:03 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <28-Sep-2000 15:52:08 bf>
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
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );
include_once( "classes/ezhttptool.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTradeMain", "Language" );
$OrderSenderEmail = $ini->read_var( "eZTradeMain", "OrderSenderEmail" );
$OrderReceiverEmail = $ini->read_var( "eZTradeMain", "OrderReceiverEmail" );
$ForceSSL = $ini->read_var( "eZTradeMain", "ForceSSL" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezoption.php" );
include_once( "eztrade/classes/ezoptionvalue.php" );
include_once( "eztrade/classes/ezcart.php" );
include_once( "eztrade/classes/ezcartitem.php" );
include_once( "eztrade/classes/ezcartoptionvalue.php" );
include_once( "eztrade/classes/ezorder.php" );
include_once( "eztrade/classes/ezorderitem.php" );
include_once( "eztrade/classes/ezorderoptionvalue.php" );
include_once( "eztrade/classes/ezwishlist.php" );

// shipping
include_once( "eztrade/classes/ezshippingtype.php" );
include_once( "eztrade/classes/ezshippinggroup.php" );


include_once( "eztrade/classes/ezcheckout.php" );

include_once( "ezsession/classes/ezsession.php" );
include_once( "ezuser/classes/ezuser.php" );

include_once( "classes/ezmail.php" );

$cart = new eZCart();
$session = new eZSession();

// if no session exist create one.
if ( !$session->fetch() )
{
    $session->store();
}

// set SSL mode and redirect if not already in SSL mode.
if ( $ForceSSL == "enabled" )
{
    $session->setVariable( "SSLMode", "enabled" );
    
    // force SSL if supposed to
    if ( $SERVER_PORT != '443' )
    {
//          print( "<font color=\"#333333\">Start: Location: https://" . $HTTP_HOST . $REQUEST_URI . "</font>" );
        eZHTTPTool::hheader("Location: https://" . $HTTP_HOST . $REQUEST_URI );
        exit;
    }
}


// get the cart or create it
$cart = $cart->getBySession( $session, "Cart" );
if ( !$cart )
{
    eZHTTPTool::header( "Location: /trade/cart/" );
}

$t = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                     "eztrade/user/intl/", $Language, "checkout.php" );

$t->setAllStrings();

$t->set_file( array(
    "checkout_tpl" => "checkout.tpl"
    ) );

$t->set_block( "checkout_tpl", "payment_method_tpl", "payment_method" );

$t->set_block( "checkout_tpl", "cart_item_list_tpl", "cart_item_list" );
$t->set_block( "cart_item_list_tpl", "cart_item_tpl", "cart_item" );
$t->set_block( "cart_item_tpl", "cart_item_option_tpl", "cart_item_option" );
$t->set_block( "cart_item_tpl", "cart_image_tpl", "cart_image" );

                
$t->set_block( "cart_item_list_tpl", "shipping_type_tpl", "shipping_type" );

$t->set_block( "checkout_tpl", "shipping_address_tpl", "shipping_address" );
$t->set_block( "checkout_tpl", "billing_address_tpl", "billing_address" );
$t->set_block( "billing_address_tpl", "billing_option_tpl", "billing_option" );
$t->set_block( "checkout_tpl", "wish_user_tpl", "wish_user" );


if ( isset( $SendOrder ) ) 
{
    $locale = new eZLocale( $Language );
    $currency = new eZCurrency();
    
    // create a new order
    $order = new eZOrder();
    $user = eZUser::currentUser();
    $order->setUser( $user );

    if ( $ini->read_var( "eZTradeMain", "ShowBillingAddress" ) != "enabled" )
    {
        $billingAddressID = $shippingAddressID;
    }
    
    $shippingAddress = new eZAddress( $ShippingAddressID );
    $billingAddress = new eZAddress( $BillingAddressID );

    $order->setShippingAddress( $shippingAddress );
    $order->setBillingAddress( $billingAddress );

    
    $order->setShippingCharge( eZHTTPTool::getVar( "ShippingCost", true ) );
    $order->setPaymentMethod( $PaymentMethod );

    $order->store();

    $order_id = $order->id();

    // fetch the cart items
    $items = $cart->items(  );

    foreach( $items as $item )
    {
        // set the wishlist item to bought if the cart item is
        // fetched from a wishlist

//          $wishListItem = $item->wishListItem();
//          if ( $wishListItem )
//          {
//              $wishListItem->setIsBought( true );
//              $wishListItem->store();
//          }
        
        $product = $item->product();
        // create a new order item
        $orderItem = new eZOrderItem();
        $orderItem->setOrder( $order );
        $orderItem->setProduct( $product );
        $orderItem->setCount( $item->count() );
        $orderItem->setPrice( $product->price() );
        $orderItem->store();
        $price = $product->price() * $item->count();
        $currency->setValue( $price );
        
        $optionValues =& $item->optionValues();

        $optionNameLength = 0;

        $optionValues =& $item->optionValues();
        
        foreach ( $optionValues as $optionValue )
        {
            $option =& $optionValue->option();
            $value =& $optionValue->optionValue();

            $orderOptionValue = new eZOrderOptionValue();
            $orderOptionValue->setOrderItem( $orderItem );
            $orderOptionValue->setOptionName( $option->name() );

            // fix
//            $orderOptionValue->setValueName( $value->name() );
            
            $orderOptionValue->store();
        }
    }
   
//      $cart->clear();

    $session->setVariable( "PaymentMethod", $PaymentMethod );
    $session->setVariable( "OrderID", $order_id );

    Header( "Location: /trade/payment/" );
    exit();
}



// show the shipping types

$type = new eZShippingType();
$types = $type->getAll();

$currentTypeID = eZHTTPTool::getVar( "ShippingTypeID" );
    
$currentShippingType = false;
foreach ( $types as $type )
{
    $t->set_var( "shipping_type_id", $type->id() );
    $t->set_var( "shipping_type_name", $type->name() );

    
    if ( is_numeric( $currentTypeID ) )
    {
        if (  $currentTypeID == $type->id() )
        {
            $currentShippingType = $type;
            $t->set_var( "type_selected", "selected" );
        }
        else
            $t->set_var( "type_selected", "" );            
    }
    else
    {
        if ( $type->isDefault() )
        {
            $currentShippingType = $type;
            $t->set_var( "type_selected", "selected" );
        }
        else
            $t->set_var( "type_selected", "" );
    }

    $t->parse( "shipping_type", "shipping_type_tpl", true );
}



// print the cart contents
{
// fetch the cart items
    $items = $cart->items( );

    $locale = new eZLocale( $Language );
    $currency = new eZCurrency();
    
    $i = 0;
    $sum = 0.0;
    $totalVAT = 0.0;

    $ShippingCostValues = array();
    
    foreach ( $items as $item )
    {
        $product = $item->product();

        $image = $product->thumbnailImage();

        if ( $image )
        {
            $thumbnail =& $image->requestImageVariation( 35, 35 );        

            $t->set_var( "product_image_path", "/" . $thumbnail->imagePath() );
            $t->set_var( "product_image_width", $thumbnail->width() );
            $t->set_var( "product_image_height", $thumbnail->height() );
            $t->set_var( "product_image_caption", $image->caption() );
            
            $t->parse( "cart_image", "cart_image_tpl" );            
        }
        else
        {
            $t->set_var( "cart_image", "" );
        }
        
        $t->set_var( "wish_user", "" );
        
        $wishListItem = $item->wishListItem();
        
        if ( $wishListItem )
        {
            $wishList = $wishListItem->wishList();

            if ( $wishList )
            {
                $wishUser = $wishList->user();

                if ( get_class ( $wishUser ) == "ezuser" )
                {
                    $address = new eZAddress();
                
                    $mainAddress =& $address->mainAddress( $wishUser );

                    if ( get_class ( $mainAddress ) == "ezaddress" )
                    {
                        $t->set_var( "wish_user_address_id", $mainAddress->id() );
                        $t->set_var( "wish_first_name", $wishUser->firstName() );
                        $t->set_var( "wish_last_name", $wishUser->lastName() );
                    
                        $t->parse( "wish_user", "wish_user_tpl" );
                    }
                    else
                    {
                    }
                }
            }
        }

        $price = $product->price() * $item->count();
        $currency->setValue( $price );

        $sum += $price;

        // VAT handling
        $totalVAT += $product->vat() * $item->count();
        
        $t->set_var( "product_name", $product->name() );
        $t->set_var( "product_price", $locale->format( $currency ) );

        $t->set_var( "cart_item_count", $item->count() );
        
        if ( ( $i % 2 ) == 0 )
            $t->set_var( "td_class", "bglight" );
        else
            $t->set_var( "td_class", "bgdark" );

        $optionValues =& $item->optionValues();

        $t->set_var( "cart_item_option", "" );
        foreach ( $optionValues as $optionValue )
        {
            $option =& $optionValue->option();
            $value =& $optionValue->optionValue();
                 
            $t->set_var( "option_name", $option->name() );

            $description = $value->descriptions();
            $t->set_var( "option_value", $description );
            
            $t->parse( "cart_item_option", "cart_item_option_tpl", true );
        }
        
        $t->parse( "cart_item", "cart_item_tpl", true );

        $i++;
    }


    $shippingCost = $cart->shippingCost( $currentShippingType );

    $currency->setValue( $shippingCost );
    $t->set_var( "shipping_cost", $locale->format( $currency ) );

    $sum += $shippingCost;
    $currency->setValue( $sum );
    $t->set_var( "cart_sum", $locale->format( $currency ) );
    
    $currency->setValue( $totalVAT );
    $t->set_var( "cart_vat_sum", $locale->format( $currency ) );
}


$t->parse( "cart_item_list", "cart_item_list_tpl" );

$user = eZUser::currentUser();

$t->set_var( "customer_first_name", $user->firstName() );
$t->set_var( "customer_last_name", $user->lastName() );

// print out the addresses

$addressArray = $user->addresses();

foreach ( $addressArray as $address )
{
    $t->set_var( "address_id", $address->id() );
    $t->set_var( "street1", $address->street1() );
    $t->set_var( "street2", $address->street2() );
    $t->set_var( "zip", $address->zip() );
    $t->set_var( "place", $address->place() );
    $country = $address->country();
    $t->set_var( "country", $country->name() );

    unset( $mainAddress );
    $t->set_var( "is_selected", "" );
    $mainAddress = $address->mainAddress( $user );

    if ( $mainAddress->id() == $address->id() )
    {
        $t->set_var( "is_selected", "selected" );
    }

    if ( $ini->read_var( "eZTradeMain", "ShowBillingAddress" ) == "enabled" )
        $t->parse( "billing_option", "billing_option_tpl", true );
    else
        $t->set_var( "billing_option" );
        
    $t->parse( "shipping_address", "shipping_address_tpl", true );
}

if ( $ini->read_var( "eZTradeMain", "ShowBillingAddress" ) == "enabled" )
    $t->parse( "billing_address", "billing_address_tpl", true );
else
$t->set_var( "billing_address" );


// show the checkout types

$checkout = new eZCheckout();

$instance =& $checkout->instance();

$paymentMethods =& $instance->paymentMethods();

foreach ( $paymentMethods as $paymentMethod )
{
    $t->set_var( "payment_method_id", $paymentMethod["ID"] );
    $t->set_var( "payment_method_text", $paymentMethod["Text"] );

    $t->parse( "payment_method", "payment_method_tpl", true );
}


   
$t->pparse( "output", "checkout_tpl" );


?>
