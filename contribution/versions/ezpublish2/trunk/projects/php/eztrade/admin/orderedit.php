<?
// 
// $Id: orderedit.php,v 1.15 2001/03/12 10:34:38 bf Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <30-Sep-2000 13:03:13 bf>
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

if ( isset( $Cancel ) )
{
    Header( "Location: /trade/orderlist/" );
    exit();
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZTradeMain", "Language" );

$languageINI = new INIFIle( "eztrade/admin/intl/" . $Language . "/orderedit.php.ini", false );


include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezorder.php" );
include_once( "eztrade/classes/ezcheckout.php" );

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

    $user = eZUser::currentUser();

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

$t = new eZTemplate( "eztrade/admin/" . $ini->read_var( "eZTradeMain", "AdminTemplateDir" ) . "/orderedit/",
                     "eztrade/admin/intl/", $Language, "orderedit.php" );

$t->setAllStrings();

$t->set_file( array(
    "order_edit_tpl" => "orderedit.tpl",
    ) );

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
    $t->set_var( "customer_email", $user->email() );    
    $t->set_var( "customer_first_name", $user->firstName() );
    $t->set_var( "customer_last_name", $user->lastName() );

    // print out the addresses
    $shippingAddress =& $order->shippingAddress();

    $t->set_var( "shipping_street1", $shippingAddress->street1() );
    $t->set_var( "shipping_street2", $shippingAddress->street2() );
    $t->set_var( "shipping_zip", $shippingAddress->zip() );
    $t->set_var( "shipping_place", $shippingAddress->place() );

    $shippingUser = $order->shippingUser();

    if ( $shippingUser )
    {
        $t->set_var( "shipping_first_name", $shippingUser->firstName() );
        $t->set_var( "shipping_last_name", $shippingUser->lastName() );
        
    }
    $country = $shippingAddress->country();
    $t->set_var( "shipping_country", $country->name() );

    $billingAddress =& $order->billingAddress();

    $t->set_var( "billing_street1", $billingAddress->street1() );
    $t->set_var( "billing_street2", $billingAddress->street2() );
    $t->set_var( "billing_zip", $billingAddress->zip() );
    $t->set_var( "billing_place", $billingAddress->place() );
    
    $country = $billingAddress->country();
    $t->set_var( "billing_country", $country->name() );
    
}


// fetch the order items
$items = $order->items( $OrderType );

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
    }
        
    $price = $item->price() * $item->count();
    $currency->setValue( $price );

    $sum += $price;
    $t->set_var( "product_name", $product->name() );
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

$sum += $shippingCost;
$currency->setValue( $sum );
$t->set_var( "order_sum", $locale->format( $currency ) );

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

$t->set_var( "order_id", $order->id() );

$t->parse( "order_item_list", "order_item_list_tpl" );

$t->pparse( "output", "order_edit_tpl" );

?>
