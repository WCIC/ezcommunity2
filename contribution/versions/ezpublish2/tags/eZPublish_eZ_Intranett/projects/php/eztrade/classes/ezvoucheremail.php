<?
// 
// $Id: ezvoucheremail.php,v 1.1 2001/08/24 14:01:49 ce Exp $
//
// eZVoucherEMail class
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

//!! ezquizvoucher smail
//! ezquizvoucher smail documentation.
/*!

  Example code:
  \code
  \endcode

*/

include_once( "eztrade/classes/ezvoucher.php" );
include_once( "eztrade/classes/ezpreorder.php" );

include_once( "ezaddress/classes/ezaddress.php" );

class eZVoucherEMail
{

    /*!
      Constructs a new eZVoucherEMail object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZVoucherEMail( $id=-1 )
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
      Stores a eZVoucherEMail object to the database.
    */
    function store()
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        
        $description =& addslashes( $this->Description );
        
        if ( !isset( $this->ID ) )
        {
            $db->lock( "eZTrade_VoucherEMail" );
            $nextID = $db->nextID( "eZTrade_VoucherEMail", "ID" );            
            $timeStamp =& eZDateTime::timeStamp( true );
            $password = md5( $this->Password );

            $res = $db->query( "INSERT INTO eZTrade_VoucherEMail
                      ( ID, VoucherID, Email, Description, PreOrderID )
                      VALUES
                      ( '$nextID',
                        '$this->VoucherID',
                        '$this->Email',
                        '$description',
                        '$this->PreOrderID'
                         )
                     " );

			$this->ID = $nextID;
        }
        elseif ( is_numeric( $this->ID ) )
        {
            $res = $db->query( "UPDATE eZTrade_VoucherEMail SET
                                     VoucherID='$this->VoucherID',
                                     Email=$this->Email,
                                     Description='$this->Description',
                                     PreOrderID='$this->PreOrderID'
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
      Deletes a eZVoucherEMail object from the database.
    */
    function delete( $catID=-1 )
    {
        if ( $catID == -1 )
            $catID = $this->ID;

        $db =& eZDB::globalDatabase();
        $db->begin();
        
        $res = $db->query( "DELETE FROM eZTrade_VoucherEMail WHERE ID='$this->ID'" );
    
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
            $db->array_query( $quizArray, "SELECT * FROM eZTrade_VoucherEMail WHERE ID='$id'",
                              0, 1 );
            if( count( $quizArray ) == 1 )
            {
                $this->fill( &$quizArray[0] );
                $ret = true;
            }
            elseif( count( $quizArray ) == 1 )
            {
                $this->ID = 0;
            }
        }
        return $ret;
    }

    /*!
      Fills in information to the object taken from the array.
    */
    function fill( &$value )
    {
        $this->ID =& $value[ "ID" ];
        $this->Description =& $value[ "Description" ];
        $this->Email =& $value[ "Email" ];
        $this->VoucherID =& $value[ "VoucherID" ];
        $this->PreOrderID =& $value[ "PreOrderID" ];
    }

    /*!
      Returns all the categories found in the database.

      The categories are returned as an array of eZVoucherEMail objects.
    */
    function &getAll( $offset=0, $limit=20 )
    {
        $db =& eZDB::globalDatabase();
        
        $returnArray = array();
        $quizArray = array();

        if ( $limit == false )
        {
            $db->array_query( $quizArray, "SELECT ID
                                           FROM eZTrade_VoucherEMail
                                           ORDER BY StartDate DESC
                                           " );

        }
        else
        {
            $db->array_query( $quizArray, "SELECT ID
                                           FROM eZTrade_VoucherEMail
                                           ORDER BY StartDate DESC
                                           LIMIT $offset, $limit" );
        }

        for ( $i=0; $i < count($quizArray); $i++ )
        {
            $returnArray[$i] = new eZVoucherEMail( $quizArray[$i][$db->fieldName( "ID" )] );
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
                                     FROM eZTrade_VoucherEMail" );
        $ret = $result[$db->fieldName( "Count" )];
        return $ret;
    }

    /*!
      Returns the object ID to the voucher smail. This is the unique ID stored in the database.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the description of the voucher smail.
    */
    function &description()
    {
        return htmlspecialchars( $this->Description );
    }

    /*!
      Sets the description.
    */
    function setDescription( &$value )
    {
        $this->Description = $value;
    }

    /*!
      Sets the email for the voucher smail.
    */
    function setEmail( &$value )
    {
        $this->Email = $value;
    }

    /*!
      Sets the voucher for the voucher smail.
    */
    function setVoucher( &$value )
    {
        if ( get_class ( $value ) == "ezvoucher" )
            $this->VoucherID = $value->id();
        else
            $this->VoucherID = $value;
    }

    /*!
      Sets the preorder for the voucher smail.
    */
    function setPreOrder( &$value )
    {
        if ( get_class ( $value ) == "ezpreorder" )
            $this->PreOrderID = $value->id();
        else
            $this->PreOrderID = $value;
    }

    /*!
      Returns the email
    */
    function email( )
    {
        return $this->EMail;
    }

    /*!
      Returns the voucher
    */
    function voucher( $as_object=true )
    {
        if ( $as_object )
            return eZVoucher( $this->VoucherID );
        else
            return $this->VoucherID;
    }

    /*!
      Returns the pre order
    */
    function preOrderID( $as_object=true )
    {
        if ( $as_object )
            return eZPreOrderID( $this->PreOrderID );
        else
            return $this->PreOrderID;
    }


    var $ID;
    var $Description;
    var $PreOrderID;
    var $Email;
    var $VoucherID;
}

?>
