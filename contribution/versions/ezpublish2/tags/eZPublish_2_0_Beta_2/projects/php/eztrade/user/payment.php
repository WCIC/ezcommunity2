<?
// 
// $Id: payment.php,v 1.9 2001/02/09 15:49:50 ce Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <02-Feb-2001 16:31:53 bf>
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

unset( $PaymentSuccess );

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );

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


include_once( "eztrade/classes/ezcheckout.php" );

include_once( "ezsession/classes/ezsession.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "classes/ezmail.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTradeMain", "Language" );
$OrderSenderEmail = $ini->read_var( "eZTradeMain", "OrderSenderEmail" );
$OrderReceiverEmail = $ini->read_var( "eZTradeMain", "OrderReceiverEmail" );
$ShippingCost = $ini->read_var( "eZTradeMain", "ShippingCost" );

// fetch the cart
$cart = new eZCart();
$cart = $cart->getBySession( $session, "Cart" );

$items = $cart->items();

$order = new eZOrder( $session->variable( "OrderID" ) );

$ChargeTotal = $order->totalPrice() + $ShippingCost;

$checkout = new eZCheckout();
$instance =& $checkout->instance();

$billingAddress = $order->billingAddress();
$shippingAddress = $order->shippingAddress();

$paymentMethod = $session->variable( "PaymentMethod" );

include( $instance->paymentFile( $paymentMethod ) );

// create an order and empty the cart.
// only do this if the payment was OK.
if ( $PaymentSuccess == "true" ) 
{
    $locale = new eZLocale( $Language );
    $currency = new eZCurrency();
    
    $user = eZUser::currentUser();

    // Setup the template for email
    $mailTemplate = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                                        "eztrade/user/intl", $Language, "mailorder.php" );

    $mailTemplateIni = new INIFile( "eztrade/user/intl/" . $Language . "/mailorder.php.ini", false );
    $mailTemplate->set_file( "mail_order_tpl", "mailorder.tpl" );
    $mailTemplate->setAllStrings();

    // subject
    $mailTemplate->set_block( "mail_order_tpl", "subject_admin_tpl", "subject_admin" );
    $mailTemplate->set_block( "mail_order_tpl", "subject_user_tpl", "subject_user" );    
    
    $mailTemplate->set_block( "mail_order_tpl", "order_item_tpl", "order_item" );
    $mailTemplate->set_block( "order_item_tpl", "option_item_tpl", "option_item" );

    $mailTemplate->set_block( "mail_order_tpl", "billing_address_tpl", "billing_address" );
    $mailTemplate->set_block( "mail_order_tpl", "shipping_address_tpl", "shipping_address" );
    
    // fetch the cart items
    $items = $order->items( );

    // Get the strings for the headers

    $headProduct = $mailTemplateIni->read_var( "strings", "product" );
    $headCount = $mailTemplateIni->read_var( "strings", "count" );
    $headPrice = $mailTemplateIni->read_var( "strings", "price" );
    $footTotal = $mailTemplateIni->read_var( "strings", "total" );
    $footSandH = $mailTemplateIni->read_var( "strings", "ship_hand" );
    $footSubT = $mailTemplateIni->read_var( "strings", "sub_total" );

    $productString = substr( $headProduct, 0, 56 );
    $productString = $productString . ": ";
    $productString = str_pad( $productString, 58, " " );

    $countString = substr( $headCount, 0, 5 );
    $countString = $countString . ": ";
    $countString = str_pad( $countString, 7, " ", STR_PAD_LEFT );

    $priceString = substr( $headPrice, 0, 13 );
    $priceString = $priceString . ": ";
    $priceString = str_pad( $priceString, 15, " ", STR_PAD_LEFT );

    $totalString = substr( $footTotal, 0, 56 );
    $totalString = $totalString . ": ";
    $totalString = str_pad( $totalString, 58, " ", STR_PAD_LEFT );
    
    $tshString = substr( $footSandH, 0, 56 );
    $tshString = $tshString . ": ";
    $tshString = str_pad( $tshString, 58, " ", STR_PAD_LEFT );

    $subTotalString = substr( $footSubT, 0, 56 );
    $subTotalString = $subTotalString . ": ";
    $subTotalString = str_pad( $subTotalString, 58, " ", STR_PAD_LEFT );
    
    $lineString = str_pad( $lineString, 78, "-");
    
    $mailTemplate->set_var( "product_string", $productString );
    $mailTemplate->set_var( "count_string", $countString );
    $mailTemplate->set_var( "price_string", $priceString );
    $mailTemplate->set_var( "stringline", $lineString );
    $mailTemplate->set_var( "product_total_string", $totalString );
    $mailTemplate->set_var( "product_sub_total_string", $subTotalString );
    $mailTemplate->set_var( "product_ship_hand_string", $tshString );
    
    $user = $order->user();

    $mailTemplate->set_var( "user_first_name", $user->firstName() );
    $mailTemplate->set_var( "user_last_name", $user->lastName() );

   // print out the addresses

    $mailTemplate->set_var( "billing_street1", $billingAddress->street1() );
    $mailTemplate->set_var( "billing_street2", $billingAddress->street2() );
    $mailTemplate->set_var( "billing_zip", $billingAddress->zip() );
    $mailTemplate->set_var( "billing_place", $billingAddress->place() );
    
    $country = $billingAddress->country();
    $mailTemplate->set_var( "billing_country", $country->name() );

    if ( $ini->read_var( "eZTradeMain", "ShowBillingAddress" ) == "enabled" )
        $mailTemplate->parse( "billing_address", "billing_address_tpl" );
    else
        $mailTemplate->set_var( "billing_address", "" );

   
    $mailTemplate->set_var( "shipping_street1", $shippingAddress->street1() );
    $mailTemplate->set_var( "shipping_street2", $shippingAddress->street2() );
    $mailTemplate->set_var( "shipping_zip", $shippingAddress->zip() );
    $mailTemplate->set_var( "shipping_place", $shippingAddress->place() );
    
    $country = $shippingAddress->country();
    $mailTemplate->set_var( "shipping_country", $country->name() );
    
    $mailTemplate->parse( "shipping_address", "shipping_address_tpl" );

    foreach( $items as $item )
    {
        $product = $item->product();

        $price = $product->price() * $item->count();
        $currency->setValue( $price );

        $mailTemplate->set_var( "debug", $debug );
        
        $nameString = substr(  $product->name(), 0, 56 );
        $nameString = str_pad( $nameString, 58, " " );
        
        $countString = substr(  $item->count(), 0, 5 );
        $countString = str_pad( $countString, 7, " ", STR_PAD_LEFT );
        
        $priceString = substr(  $locale->format( $currency ), 0, 13 );
        $priceString = str_pad( $priceString, 15, " ", STR_PAD_LEFT );

        $mailTemplate->set_var( "order", $nameString );
        $mailTemplate->set_var( "count", $countString );
        $mailTemplate->set_var( "price", $priceString );

        $optionValues =& $item->optionValues();

        $mailTemplate->set_var( "cart_item_option", "" );
        $mailTemplate->set_var( "option_item", "" );

        $optionNameLength = 0;

        $optionValues =& $item->optionValues();
        
        foreach ( $optionValues as $optionValue )
        {
            $option =& $optionValue->option();
            $value =& $optionValue->optionValue();

            $optionString = substr( $option->name(), 0, 35 );
            $optionString = str_pad( $optionString, 36, " ", STR_PAD_LEFT );
            $valueString = substr( $value->name(), 0, 38 );
            $valueString = str_pad( $valueString, 39, " " );
    
            $mailTemplate->set_var( "name", $optionString );
            $mailTemplate->set_var( "value", $valueString );
            $mailTemplate->parse( "option_item", "option_item_tpl", true );
        }

        $mailTemplate->parse( "order_item", "order_item_tpl", true );
    }

    $totalPrice = $order->totalPrice();
    $currency->setValue( $totalPrice );
    
    $priceString = substr(  $locale->format( $currency ), 0, 13 );
    $priceString = str_pad( $priceString, 15, " ", STR_PAD_LEFT );
    $mailTemplate->set_var( "product_sub_total", $priceString );

    $shippinglPrice = $order->shippingCharge();
    $currency->setValue( $shippinglPrice );
    
    $shippingPriceString = substr(  $locale->format( $currency ), 0, 13 );
    $shippingPriceString = str_pad( $shippingPriceString, 15, " ", STR_PAD_LEFT );
    $mailTemplate->set_var( "product_ship_hand", $shippingPriceString );

    $grandTotal = $order->totalPrice() + $order->shippingCharge();
    $currency->setValue( $grandTotal );

    $grandTotalString = substr(  $locale->format( $currency ), 0, 13 );
    $grandTotalString = str_pad( $grandTotalString, 15, " ", STR_PAD_LEFT );
    $mailTemplate->set_var( "product_total", $grandTotalString );
   
    $mailTemplate->set_var( "order_number", $order->id() );

    $checkout = new eZCheckout();
    $instance =& $checkout->instance();
    $paymentMethod = $instance->paymentName( $order->paymentMethod() );
    
    $mailTemplate->set_var( "payment_method", $paymentMethod );

    // get the subjects
    $mailSubjectUser = $mailTemplate->parse( "subject_user", "subject_user_tpl" );
    $mailTemplate->set_var( "subject_user", "" );

    $mailSubjectAdmin = $mailTemplate->parse( "subject_admin", "subject_admin_tpl" );
    $mailTemplate->set_var( "subject_admin", "" );


    // payment method
    $checkout = new eZCheckout();
    $instance =& $checkout->instance();
    $paymentMethod = $instance->paymentName( $order->paymentMethod() );
    
    $mailTemplate->set_var( "payment_method", $paymentMethod );
    
    
    // Send E-mail    
    $mail = new eZMail();
    $mailToAdmin = $ini->read_var( "eZTradeMain", "mailToAdmin" );    
    
    $mailBody = $mailTemplate->parse( "dummy", "mail_order_tpl" );
    $mail->setFrom( $OrderSenderEmail );
    
    $mail->setTo( $user->email() );
    $mail->setSubject( $mailSubjectUser );
    $mail->setBody( $mailBody );
    $mail->send();
    
    $mail->setSubject( $mailSubjectAdmin );
    $mail->setTo( $mailToAdmin );

    $mail->send();

    // get the cart or create it
    $cart = new eZCart();
    $cart = $cart->getBySession( $session, "Cart" );

    foreach( $cart->items() as $item )
    {
        // set the wishlist item to bought if the cart item is
        // fetched from a wishlist
        $wishListItem = $item->wishListItem();
        if ( $wishListItem )
        {
            $wishListItem->setIsBought( true );
            $wishListItem->store();
        }
    }

    $order->setIsActive( true );
    $order->store();
    $cart->clear();

    $orderID = $order->id();
    
    Header( "Location: /trade/ordersendt/$orderID/" );
    exit();
}

?>
