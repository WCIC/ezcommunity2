<?
// 
// $Id: ezvoucher.php,v 1.2 2001/08/24 07:21:07 ce Exp $
//
// eZVoucher class
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <19-Jun-2001 17:41:06 ce>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2001 eZ systems as
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

//!! ezquizgame
//! ezquizgame documentation.
/*!

  Example code:
  \code
  \endcode

*/

include_once( "classes/ezdate.php" );
include_once( "ezquiz/classes/ezquizquestion.php" );
	      
class eZVoucher
{

    /*!
      Constructs a new eZVoucher object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZVoucher( $id=-1 )
    {
        if ( is_array( $id ) )
        {
            $this->fill( $id );
        }
        else if ( $id != -1 )
        {
            $this->ID = $id;
            $this->get( $this->ID );
        }
    }

    /*!
      Stores a eZVoucher object to the database.
    */
    function store()
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        
       
        if ( !isset( $this->ID ) )
        {
            $db->lock( "eZTrade_Voucher" );
            $nextID = $db->nextID( "eZTrade_Voucher", "ID" );            
            $timeStamp =& eZDateTime::timeStamp( true );
            $password = md5( $this->Password );

            $res = $db->query( "INSERT INTO eZTrade_Voucher
                      ( ID, Created, Price, Available, KeyNumber )
                      VALUES
                      ( '$nextID',
                        '$timeStamp',
                        '$this->Price',
                        '$this->Available',
                        '$this->KeyNumber'
                         )
                     " );

			$this->ID = $nextID;
        }
        elseif ( is_numeric( $this->ID ) )
        {
            $res = $db->query( "UPDATE eZTrade_Voucher SET
                                     Created=Created,
                                     Price='$this->Price',
                                     Available='$this->Available'
                                     WHERE ID='$this->ID" );
        }
        $db->unlock();
    
        if ( $res == false )
            $db->rollback( );
        else
            $db->commit();

        return true;
    }

    /*!
      Deletes a eZVoucher object from the database.
    */
    function delete( $catID=-1 )
    {
        if ( $catID == -1 )
            $catID = $this->ID;

        $db =& eZDB::globalDatabase();
        $db->begin();
        
        $res = $db->query( "DELETE FROM eZTrade_Voucher WHERE ID='$this->ID'" );
    
        if ( $ret == false )
            $db->rollback( );
        else
            $db->commit();
    }

    /*!
      Fetches the object information from the database.

      True is retuned if successful, false (0) if not.
    */
    function get( $id=-1 )
    {
        $db =& eZDB::globalDatabase();

        $ret = false;
        if ( $id != "" )
        {
            $db->array_query( $voucherArray, "SELECT * FROM eZTrade_Voucher WHERE ID='$id'",
                              0, 1 );
            if( count( $voucherArray ) == 1 )
            {
                $this->fill( &$voucherArray[0] );
                $ret = true;
            }
            elseif( count( $voucherArray ) == 1 )
            {
                $this->ID = 0;
            }
        }
        return $ret;
    }

    /*!
      Fills in information to the object taken from the array.
    */
    function fill( &$voucherArray )
    {
        $this->ID =& $voucherArray[ "ID" ];
        $this->Created =& $voucherArray[ "Created" ];
        $this->Price =& $voucherArray[ "Price" ];
        $this->Available =& $voucherArray[ "Available" ];
        $this->KeyNumber =& $voucherArray[ "KeyNumber" ];
    }

    /*!
      Returns all the categories found in the database.

      The categories are returned as an array of eZVoucher objects.
    */
    function &getAll( $offset=0, $limit=20 )
    {
        $db =& eZDB::globalDatabase();
        
        $returnArray = array();
        $voucherArray = array();

        if ( $limit == false )
        {
            $db->array_query( $voucherArray, "SELECT ID
                                           FROM eZTrade_Voucher
                                           " );

        }
        else
        {
            $db->array_query( $voucherArray, "SELECT ID
                                           FROM eZTrade_Voucher
                                           ", array( "Limit" => $limit, "Offset" => $offset ) );
        }

        for ( $i=0; $i < count($voucherArray); $i++ )
        {
            $returnArray[$i] = new eZVoucher( $voucherArray[$i][$db->fieldName( "ID" )] );
        }

        return $returnArray;
    }

    /*!
      Returns the total count.
     */
    function count()
    {
        $db =& eZDB::globalDatabase();
        $ret = false;

        $db->query_single( $result, "SELECT COUNT(ID) as Count
                                     FROM eZTrade_Voucher" );
        $ret = $result[$db->fieldName( "Count" )];
        return $ret;
    }

    /*!
      Returns the object ID.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the creation time of the voucher.
    */
    function &created()
    {
        $dateTime = new eZDateTime();
        $dateTime->setTimeStamp( $this->Created );

        return $dateTime;
    }

    /*!
      Sets the login.
    */
    function generateKey( $length=15 )
    {
        $this->KeyNumber = substr( md5( microtime() ), 0, $length );
    }

    /*!
      Returns the keynumber
    */
    function keyNumber( )
    {
        return $this->KeyNumber;
    }

    /*!
      Sets if the voucher is available or not.
    */
    function setAvailable( $value )
    {
        if ( $value == true )
            $this->Available = 1;
        else
            $this->Available = 0;
    }

    /*!
      Returns true if the voucher is avaiable
    */
    function available()
    {
        if ( $this->Available == 1 )
            return true;
        else
            return false;
    }

    /*!
      Sets the voucher price.
    */
    function setPrice( $value )
    {
       $this->Price = $value;
       setType( $this->Price, "double" );
    }

    /*!
      Returns the price of the voucher.
    */
    function &price( )
    {
       return $this->Price;
    }

    /*!
      Get a voucher from a key number.
    */
    function getFromKeyNumber( $key )
    {
        $db =& eZDB::globalDatabase();
        $ret = false;
        if ( !$key )
            return false;
        
        $db->query_single( $res, "SELECT ID FROM eZTrade_Voucher WHERE KeyNumber='$key'" );

        print( $res["ID"] );
        if ( $res["ID"] )
        {
            $ret = new eZVoucher( $res["ID"] );
        }

        return $ret;
    }
  

    var $ID;
    var $KeyNumber;
    var $Created;
    var $Available;
    var $Price;
}

?>
