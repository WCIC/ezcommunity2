<?
// 
// $Id: ezorder.php,v 1.17 2001/01/18 14:18:26 ce Exp $
//
// Definition of eZOrder class
//
// B�rd Farstad <bf@ez.no>
// Created on: <28-Sep-2000 16:40:01 bf>
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

//!! eZTrade
//! eZOrder handles orders.
/*!

  \sa eZOrderItem eZOrderOptionValue eZDateTime
*/

/*!TODO
  Fix address, should take an object as parameter.
    
*/

include_once( "classes/ezdb.php" );

include_once( "eztrade/classes/ezorderstatustype.php" );
include_once( "eztrade/classes/ezorderstatus.php" );
include_once( "eztrade/classes/ezorderitem.php" );

include_once( "ezuser/classes/ezuser.php" );

class eZOrder
{
    /*!
      Constructs a new eZOrder object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZOrder( $id="", $fetch=true )
    {
        $this->IsConnected = false;

        if ( $id != "" )
        {
            $this->ID = $id;
            if ( $fetch == true )
            {
                
                $this->get( $this->ID );
            }
            else
            {
                $this->State_ = "Dirty";
            }
        }
        else
        {
            $this->State_ = "New";
        }
    }

    /*!
      Stores a order to the database.
    */
    function store()
    {
        $this->dbInit();

        if ( !isset( $this->ID ) )
        {
            $this->Database->query( "INSERT INTO eZTrade_Order SET
		                         UserID='$this->UserID',
		                         ShippingAddressID='$this->ShippingAddressID',
		                         BillingAddressID='$this->BillingAddressID',
		                         PaymentMethod='$this->PaymentMethod',
		                         ShippingCharge='$this->ShippingCharge'
                                 " );

            $this->ID = mysql_insert_id();

            // store the status
            $statusType = new eZOrderStatusType( );
            $statusType = $statusType->getByName( "intl-initial" );

            $status = new eZOrderStatus();
            $status->setType( $statusType );

            $status->setOrderID( $this->ID );

//              $user = eZUser::currentUser();
//              print( $user->id() );
            
            $status->setAdmin( $user );
            $status->store();            

            $this->State_ = "Coherent";
        }
        else
        {
            $this->Database->query( "UPDATE eZTrade_Order SET
		                         UserID='$this->UserID',
		                         ShippingAddressID='$this->ShippingAddressID',
		                         BillingAddressID='$this->BillingAddressID',
		                         PaymentMethod='$this->PaymentMethod',
		                         ShippingCharge='$this->ShippingCharge'
                                 WHERE ID='$this->ID'
                                 " );

            $this->State_ = "Coherent";
        }
        
        return true;
    }

    /*!
      Deletes a eZOrder object from the database.
    */
    function delete()
    {
        $this->dbInit();

        $items = $this->items();

        if  ( $items )
        {
            $i = 0;
            foreach ( $items as $item )
            {
                $item->delete();
            }
        }

        $this->Database->query( "DELETE FROM eZTrade_OrderStatus WHERE OrderID='$this->ID'" );

        
        $this->Database->query( "DELETE FROM eZTrade_Order WHERE ID='$this->ID'" );
            
        return true;
    }
    

    /*!
      Fetches the object information from the database.
    */
    function get( $id="" )
    {
        $this->dbInit();
        $ret = false;
        
        if ( $id != "" )
        {
            $this->Database->array_query( $cart_array, "SELECT * FROM eZTrade_Order WHERE ID='$id'" );
            if ( count( $cart_array ) > 1 )
            {
                die( "Error: Cart's with the same ID was found in the database. This shouldent happen." );
            }
            else if( count( $cart_array ) == 1 )
            {
                $this->ID = $cart_array[0][ "ID" ];
                $this->UserID = $cart_array[0][ "UserID" ];
                $this->ShippingAddressID = $cart_array[0][ "ShippingAddressID" ];
                $this->BillingAddressID = $cart_array[0][ "BillingAddressID" ];
                $this->ShippingCharge = $cart_array[0][ "ShippingCharge" ];
                $this->PaymentMethod = $cart_array[0][ "PaymentMethod" ];

                $this->State_ = "Coherent";
                $ret = true;
            }
        }
        else
        {
            $this->State_ = "Dirty";
        }
        return $ret;
    }

    /*!
      Fetches all the orders.

      Note: Default limit is 20.
    */
    function getAll( $offset=0, $limit=20 )
    {
        $this->dbInit();

        $return_array = array();
        $order_array = array();

        $this->Database->array_query( $order_array, "SELECT ID FROM eZTrade_Order
                                                     LIMIT $offset, $limit" );

        for ( $i=0; $i<count( $order_array ); $i++ )
        {
            $return_array[$i] = new eZOrder( $order_array[$i][ "ID" ], 0 );
        }

        return $return_array;
    }

    /*!
      Does a search in the order database.

      Note: Default limit is 20.
    */
    function search( $queryText, $offset=0, $limit=20 )
    {
        $this->dbInit();

        $return_array = array();
        $order_array = array();

        $this->Database->array_query( $order_array, "SELECT ID
                                                     FROM eZTrade_Order
                                                     WHERE ID LIKE '%$queryText%'
                                                      LIMIT $offset, $limit" );

        for ( $i=0; $i<count( $order_array ); $i++ )
        {
            $return_array[$i] = new eZOrder( $order_array[$i][ "ID" ], 0 );
        }

        return $return_array;
    }

    /*!
      Returns the total count of a query.
    */
    function getSearchCount( $queryText )
    {
        $this->dbInit();

        $this->Database->array_query( $order_array, "SELECT count(ID) as Count
                                                     FROM eZTrade_Order
                                                     WHERE ID LIKE '%$queryText%'" );

        $ret = 0;
        if ( count( $order_array ) == 1 )
            $ret = $order_array[0]["Count"];

        return $ret;
    }

    /*!
      Returns the total count of orders.
    */
    function getTotalCount(   )
    {
        $this->dbInit();

        $this->Database->array_query( $order_array, "SELECT count(ID) as Count
                                                     FROM eZTrade_Order" );

        $ret = 0;
        if ( count( $order_array ) == 1 )
            $ret = $order_array[0]["Count"];

        return $ret;
    }
    
    
    /*!
      Returns the object id.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the user as a eZUser object.

      false (0) is returned if unsuccessful.
    */
    function user()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $ret = false;
       
       $user = new eZUser( );
       if ( $user->get( $this->UserID ) )
           $ret = $user;

       return $ret;
    }

    /*!
      Returns the shipping charge.
    */
    function shippingCharge()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       return $this->ShippingCharge;
    }

    /*!
      Returns the shipping address.
    */
    function shippingAddress()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $shippingAddress = new eZAddress( $this->ShippingAddressID );
       
       return $shippingAddress;
    }

    /*!
      Returns the billing address.
    */
    function billingAddress()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $billingAddress = new eZAddress( $this->BillingAddressID );
       
       return $billingAddress;
    }

    /*!
      Returns the payment method text.
    */
    function paymentMethod()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       return $this->PaymentMethod;
    }

    /*!
      Sets the payment method.
    */
    function setPaymentMethod( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $this->PaymentMethod = $value;
    }
    

    /*!
      Sets the user.
    */
    function setUser( $user )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       if ( get_class( $user ) == "ezuser" )
       {
           $this->UserID = $user->id();
       }
    }

    /*!
      Sets the shipping address.
    */
    function setShippingAddress( $shippingAddress )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       if ( get_class( $shippingAddress ) == "ezaddress" )
       {
           $this->ShippingAddressID = $shippingAddress->id();
       }
    }

    /*!
      Sets the billing address.
    */
    function setBillingAddress( $billingAddress )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       if ( get_class( $billingAddress ) == "ezaddress" )
       {
           $this->BillingAddressID = $billingAddress->id();
       }
    }

    /*!
      Sets the shipping charge.
    */
    function setShippingCharge( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       
       $this->ShippingCharge = $value;

       setType( $this->ShippingCharge, "double" );       
    }

    /*!
      Sets the status of the order.
    */
    function setStatus( $type )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       if ( get_class( $type ) == "ezorderstatustype" )
       {
           $this->OrderStatus_ = $type->id();
       }
    }

    /*!
      Returns the initial status as a eZOrderStatus object.
    */
    function initialStatus( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $statusType = new eZOrderStatusType();
       
       $statusType->getByName( "Initial" );
       
       $this->Database->array_query( $status_array, "SELECT ID FROM eZTrade_OrderStatus
                                                    WHERE OrderID='$this->ID'
                                                    ORDER BY Altered" );
       $ret = false;
       if ( count( $status_array ) )
       {
           $ret = new eZOrderStatus( $status_array[0]["ID"] );
       }
       return $ret;
    }

    /*!
      Returns the last status change  as a eZOrderStatus object.
    */
    function lastStatus( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $statusType = new eZOrderStatusType();
       
       $statusType->getByName( "Initial" );
       
       $this->Database->array_query( $status_array, "SELECT ID FROM eZTrade_OrderStatus
                                                    WHERE OrderID='$this->ID'
                                                    ORDER BY Altered DESC" );
       $ret = false;
       if ( count( $status_array ) )
       {
           $ret = new eZOrderStatus( $status_array[0]["ID"] );
       }
       return $ret;
    }

    /*!
      Returns the status history as an array of eZOrderStatus object.
    */
    function statusHistory()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $statusType = new eZOrderStatusType();
       
       $statusType->getByName( "Initial" );
       
       $this->Database->array_query( $status_array, "SELECT ID FROM eZTrade_OrderStatus
                                                    WHERE OrderID='$this->ID'
                                                    ORDER BY Altered" );
       $ret = array();
       foreach ( $status_array as $status )
       {
           $ret[] = new eZOrderStatus( $status["ID"] );
       }
       return $ret;

    }

    /*!
      Returns all the order items.
    */
    function items()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $ret = array();
       
       $this->dbInit();

       $this->Database->array_query( $order_array, "SELECT * FROM
                                                    eZTrade_OrderItem
                                                    WHERE OrderID='$this->ID'" );

       if ( count( $order_array ) > 0 )
       {
           $return_array = array();
           foreach ( $order_array as $item )
           {
               $return_array[] = new eZOrderItem( $item["ID"] );               
           }
           $ret = $return_array;
       }

       return $ret;       
       
    }


    /*!
      Returns the total price on an order. Without the shipping charge.
    */
    function totalPrice()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $retPrice = 0;
       $this->dbInit();

       $this->Database->array_query( $order_item_array, "SELECT Price, Count FROM
                                                    eZTrade_OrderItem
                                                    WHERE OrderID='$this->ID'" );

       foreach ( $order_item_array as $item )
       {
           $price = $item["Price"];

           $price = $price * $item["Count"];

           $retPrice += $price;
       }

       return $retPrice;       
    }

    /*!
      Returns the most request bought products.
    */
    function mostPopularProduct()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $ret = array();
       $this->dbInit();

       $this->Database->array_query( $product_array,
       "SELECT ProductID, Count(ProductID) AS Count, Sum( Count ) AS RealCount
        FROM eZTrade_OrderItem GROUP BY ProductID
        ORDER BY RealCount DESC" );
       
       foreach ( $product_array as $item )
       {
           $ret[] = array( "ProductID" => $item["ProductID"],
                           "Count" => $item["Count"],
                           "RealCount" => $item["RealCount"] );
       }
       
        return $ret;        
    }
    
    /*!
      \private
      Open the database for read and write. Gets all the database information from site.ini.
    */
    function dbInit()
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = eZDB::globalDatabase();
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $UserID;
    var $ShippingAddressID;
    var $BillingAddressID;
    var $ShippingCharge;
    var $PaymentMethod;

    var $OrderStatus_;
    
    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}

?>
