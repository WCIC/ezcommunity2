<?
// 
// $Id: ezorderstatus.php,v 1.4 2000/10/25 19:49:33 bf-cvs Exp $
//
// Definition of eZOrderStatus class
//
// B�rd Farstad <bf@ez.no>
// Created on: <02-Oct-2000 15:06:32 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZTrade
//! eZOrderStatus handles order status.
/*!

  \sa eZOrder
*/

/*!TODO
  Add documentation.    
*/

include_once( "classes/ezdb.php" );

include_once( "classes/ezdatetime.php" );

class eZOrderStatus
{
    /*!
      Constructs a new eZOrder object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZOrderStatus( $id="", $fetch=true )
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
      Stores a order status  to the database.
    */
    function store()
    {
        $this->dbInit();
        
        if ( !isset( $this->ID ) )
        {
            $this->Database->query( "INSERT INTO eZTrade_OrderStatus SET
		                         StatusID='$this->StatusID',
		                         AdminID='$this->AdminID',
		                         Comment='$this->Comment',
		                         OrderID='$this->OrderID'
                                 " );

            $this->ID = mysql_insert_id();

            $this->State_ = "Coherent";
        }
        else
        {
            $this->Database->query( "UPDATE eZTrade_OrderStatus SET
		                         StatusID='$this->StatusID',
		                         AdminID='$this->AdminID',
		                         Comment='$this->Comment',
		                         OrderID='$this->OrderID'
                                 WHERE ID='$this->ID'
                                 " );

            $this->State_ = "Coherent";
        }
        
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
            $this->Database->array_query( $order_array, "SELECT * FROM eZTrade_OrderStatus WHERE ID='$id'" );
            if ( count( $order_array ) > 1 )
            {
                die( "Error: Order's with the same ID was found in the database. This shouldent happen." );
            }
            else if( count( $order_array ) == 1 )
            {
                $this->ID =& $order_array[0][ "ID" ];
                $this->StatusID =& $order_array[0][ "StatusID" ];
                $this->Altered =& $order_array[0][ "Altered" ];
                $this->AdminID =& $order_array[0][ "AdminID" ];
                $this->OrderID =& $order_array[0][ "OrderID" ];
                $this->Comment =& $order_array[0][ "Comment" ];

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
      Returns the object id.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the altered timestamp as a eZDateTime object.
    */
    function altered( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       
       $dateTime = new eZDateTime();

       $dateTime->setMySQLTimeStamp( $this->Altered );

       return $dateTime;
    }

    /*!
      Returns the status type as a eZOrderStatusType object.
    */
    function type( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $ret = new eZOrderStatusType( $this->StatusID );

       return $ret;
    }

    /*!
      Returns the comment.
    */
    function comment()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       return $this->Comment;
    }

    /*!
      Returns the admin user.
    */
    function admin()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = new eZUser( $this->AdminID );
        return $ret;
    }
    

    /*!
      Sets status type.
    */
    function setType( $type )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       if ( get_class( $type ) == "ezorderstatustype" )
       {
           $this->StatusID = $type->id();
       }
    }

    /*!
      Sets the admin.
    */
    function setAdmin( $user )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       if ( get_class( $user ) == "ezuser" )
       {
           $this->AdminID = $user->id();
       }
    }

    /*!
      Sets the order ID.
    */
    function setOrderID( $order )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $this->OrderID = $order;
    }

    /*!
      Sets the comment.
    */
    function setComment( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $this->Comment = $value;
    }    

    /*!
      \private
      Open the database for read and write. Gets all the database information from site.ini.
    */
    function dbInit()
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = new eZDB( "site.ini", "site" );
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $StatusID;
    var $Altered;
    var $AdminID;
    var $OrderID;
    var $Comment;
    
    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}

?>
