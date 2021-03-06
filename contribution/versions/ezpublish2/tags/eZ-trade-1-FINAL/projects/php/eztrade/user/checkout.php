<?php
// 
// $Id: checkout.php,v 1.18 2000/11/03 10:38:03 bf-cvs Exp $
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

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZTradeMain", "Language" );
$OrderSenderEmail = $ini->read_var( "eZTradeMain", "OrderSenderEmail" );
$OrderReceiverEmail = $ini->read_var( "eZTradeMain", "OrderReceiverEmail" );
$ShippingCost = $ini->read_var( "eZTradeMain", "ShippingCost" );

$VISAShopping = $ini->read_var( "eZTradeMain", "VISAShopping" );
$MasterCardShopping = $ini->read_var( "eZTradeMain", "MasterCardShopping" );
$CODShopping = $ini->read_var( "eZTradeMain", "CODShopping" );
$InvoiceShopping = $ini->read_var( "eZTradeMain", "InvoiceShopping" );

include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezoption.php" );
include_once( "eztrade/classes/ezoptionvalue.php" );
include_once( "eztrade/classes/ezcart.php" );
include_once( "eztrade/classes/ezcartitem.php" );
include_once( "eztrade/classes/ezcartoptionvalue.php" );
include_once( "eztrade/classes/ezorder.php" );
include_once( "eztrade/classes/ezorderitem.php" );
include_once( "eztrade/classes/ezorderoptionvalue.php" );

include_once( "ezsession/classes/ezsession.php" );

include_once( "classes/ezmail.php" );


$cart = new eZCart();
$session = new eZSession();

// if no session exist create one.
if ( !$session->fetch() )
{
    $session->store();
}

// get the cart or create it
$cart = $cart->getBySession( $session, "Cart" );
if ( !$cart )
{
    Header( "Location: /trade/cart/" );
}

$t = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                     "eztrade/user/intl/", $Language, "checkout.php" );

$t->setAllStrings();

$t->set_file( array(
    "checkout_tpl" => "checkout.tpl"
    ) );

$t->set_block( "checkout_tpl", "visa_tpl", "visa" );
$t->set_block( "checkout_tpl", "mastercard_tpl", "mastercard" );
$t->set_block( "checkout_tpl", "cod_tpl", "cod" );
$t->set_block( "checkout_tpl", "invoice_tpl", "invoice" );

$t->set_block( "checkout_tpl", "cart_item_list_tpl", "cart_item_list" );
$t->set_block( "cart_item_list_tpl", "cart_item_tpl", "cart_item" );
$t->set_block( "cart_item_tpl", "cart_item_option_tpl", "cart_item_option" );
$t->set_block( "cart_item_tpl", "cart_image_tpl", "cart_image" );

$t->set_block( "checkout_tpl", "address_tpl", "address" );


if ( $VISAShopping == "enabled" )
{
    $t->parse( "visa", "visa_tpl" );
}
else
{
    $t->set_var( "visa", "" );
}

if ( $MasterCardShopping == "enabled" )
{
    $t->parse( "mastercard", "mastercard_tpl" );
}
else
{
    $t->set_var( "mastercard", "" );
}

if ( $CODShopping == "enabled" )
{
    $t->parse( "cod", "cod_tpl" );
}
else
{
    $t->set_var( "cod", "" );
}

if ( $InvoiceShopping == "enabled" )
{
    $t->parse( "invoice", "invoice_tpl" );
}
else
{
    $t->set_var( "invoice", "" );
}

    
//  $t->set_block( "cart_page", "cart_header_tpl", "cart_header" );


// create an order and empty the cart.
if ( $SendOrder == "true" ) 
{
    $locale = new eZLocale( $Language );
    $currency = new eZCurrency();
    // create a new order
    $order = new eZOrder();
    $user = eZUser::currentUser();
    $order->setUser( $user );
    $order->setAddress( 42 );
    $order->setShippingCharge( $ShippingCost );
    $order->setPaymentMethod( $PaymentMethod );
    $order->store();

    $order_id = $order->id();

    // Setup the template for email
    $mailTemplate = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                                        "eztrade/user/intl", $Language, "mailorder.php" );

    $mailTemplateIni = new INIFile( "eztrade/user/intl/" . $Language . "/mailorder.php.ini", false );
    $mailTemplate->set_file( "mail_order_tpl", "mailorder.tpl" );
    $mailTemplate->setAllStrings();

    $mailTemplate->set_block( "mail_order_tpl", "order_item_tpl", "order_item" );
    $mailTemplate->set_block( "order_item_tpl", "option_item_tpl", "option_item" );
    
    // fetch the cart items
    $items = $cart->items( $CartType );

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

    $addressArray = $user->addresses();

        $mailTemplate->set_var( "user_street", "" );
        $mailTemplate->set_var( "user_street2", "" );
        $mailTemplate->set_var( "user_city", "" );
        $mailTemplate->set_var( "user_zip", "" );

        $mailTemplate->set_var( "user_country", "" );

    foreach ( $addressArray as $address )
    {
        // Select correct later PKEJ
        $mailTemplate->set_var( "user_street", $address->street1() );
        $mailTemplate->set_var( "user_street2", $address->street2() );
        $mailTemplate->set_var( "user_city", $address->place() );
        $mailTemplate->set_var( "user_zip", $address->zip() );

        $country = $address->country();
        $mailTemplate->set_var( "user_country", $country->name() );
    }

    foreach( $items as $item )
    {        
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

            $orderOptionValue = new eZOrderOptionValue();
            $orderOptionValue->setOrderItem( $orderItem );
            $orderOptionValue->setOptionName( $option->name() );
            $orderOptionValue->setValueName( $value->name() );
            $orderOptionValue->store();

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
    
    // Send E-mail
    
    $mail = new eZMail();
    $mailToAdmin = $ini->read_var( "eZTradeMain", "mailToAdmin" );
    
    $mailSubjectAdmin = $mailTemplateIni->read_var( "strings", "mail_subject_admin" );
    $mailSubjectUser = $mailTemplateIni->read_var( "strings", "mail_subject_user" );

    $mailBody = $mailTemplate->parse( "dummy", "mail_order_tpl" );
    $mail->setFrom( $OrderSenderEmail );
    
    $mail->setTo( $user->email() );
    $mail->setSubject( $mailSubjectUser );
    $mail->setBody( $mailBody );
    $mail->send();
    
    $mail->setSubject( $mailSubjectAdmin );
    $mail->setTo( $mailToAdmin );
    $mail->send();

    $cart->clear();
    Header( "Location: /trade/ordersendt/$order_id/" );
}

// print the cart contents

{
// fetch the cart items
    $items = $cart->items( $CartType );

    $locale = new eZLocale( $Language );
    $currency = new eZCurrency();
    
    $i = 0;
    $sum = 0.0;
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
        
        $price = $product->price() * $item->count();
        $currency->setValue( $price );

        $sum += $price;
        
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
            $t->set_var( "option_value", $value->name() );
            
            $t->parse( "cart_item_option", "cart_item_option_tpl", true );
        }
        
        $t->parse( "cart_item", "cart_item_tpl", true );
        
        $i++;
    }

    $shippingCost = $ShippingCost;
    $currency->setValue( $shippingCost );
    $t->set_var( "shipping_cost", $locale->format( $currency ) );

    $sum += $shippingCost;
    $currency->setValue( $sum );
    $t->set_var( "cart_sum", $locale->format( $currency ) );
}

$t->parse( "cart_item_list", "cart_item_list_tpl" );

$user = eZUser::currentUser();

$t->set_var( "customer_first_name", $user->firstName() );
$t->set_var( "customer_last_name", $user->lastName() );

// print out the addresses

$addressArray = $user->addresses();

foreach ( $addressArray as $address )
{
    $t->set_var( "street1", $address->street1() );
    $t->set_var( "street2", $address->street2() );
    $t->set_var( "zip", $address->zip() );
    $t->set_var( "place", $address->place() );
    $country = $address->country();
    $t->set_var( "country", $country->name() );
    
    $t->parse( "address", "address_tpl"  );
}

$t->pparse( "output", "checkout_tpl" );

?>
