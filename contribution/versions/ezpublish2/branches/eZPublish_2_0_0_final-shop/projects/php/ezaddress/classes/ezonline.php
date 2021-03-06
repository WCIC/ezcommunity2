<?
// 
// $Id: ezonline.php,v 1.3 2001/02/14 10:30:38 bf Exp $
//
// Definition of eZOnline class
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <09-Nov-2000 18:05:07 ce>
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


//!! eZAddress
//! eZOnline handles onlinees.
/*!

  Example code:
  \code
  $online = new eZOnline();
  $online->setURL( "domain.com/a/path" );
  $online->setOnlineTypeID( 43 ); // What type of online, reads out from eZAddress_OnlineType
  $online->store(); // Store or updates to the database.
  \code
  \sa eZOnlineType eZCompany eZPerson eZAddress eZPhone eZAddress
  
*/

include_once( "classes/ezdb.php" );
include_once( "ezaddress/classes/ezonlinetype.php" );

class eZOnline
{
    /*!
      Constructs a new eZOnline object.
    */
    function eZOnline( $id="", $fetch=true )
    {
        if ( !empty( $id ) )
        {
            $this->ID = $id;
            if ( $fetch == true )
            {
                $this->get( $this->ID );
            }
        }
    }

    /*!
      Stores a eZOnline
    */  
    function store()
    {
        $db =& eZDB::globalDatabase();

        $ret = false;
        
        if ( !isset( $this->ID ) )
        {
            $db->query( "INSERT INTO eZAddress_Online SET
                    URL='$this->URL',
                    OnlineTypeID='$this->OnlineTypeID'" );

            $this->ID = mysql_insert_id();

            $ret = true;
        }
        else
        {
            $db->query( "UPDATE eZAddress_Online SET
                    URL='$this->URL',
                    OnlineTypeID='$this->OnlineTypeID'
                    WHERE ID='$this->ID'" );            

            $ret = true;            
        }        

        
        return $ret;
    }

    /*!
      Deletes the online where id = $this->ID
    */
    function delete( $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();

        $db->query( "DELETE FROM eZAddress_Online WHERE ID='$id'" );
    }    


    /*!
      Fetches an online with object id==$id;
    */  
    function get( $id=-1 )
    {
        $db =& eZDB::globalDatabase();
        if ( $id != "" )
        {
            $db->array_query( $online_array, "SELECT * FROM eZAddress_Online WHERE ID='$id'" );
            if ( count( $online_array ) > 1 )
            {
                die( "Feil: Flere onlineer med samme ID funnet i database, dette skal ikke v�re mulig. " );
            }
            else if ( count( $online_array ) == 1 )
            {
                $this->ID =& $online_array[ 0 ][ "ID" ];
                $this->URL =& $online_array[ 0 ][ "URL" ];
                $this->OnlineTypeID =& $online_array[ 0 ][ "OnlineTypeID" ];
            }
        }
    }

    /*!
      Fetches out all the onlines thats stored in the database.
    */
    function getAll( )
    {
        $db =& eZDB::globalDatabase();
        $online_array = 0;

        $online_array = array();
        $return_array = array();
    
        $db->array_query( $online_array, "SELECT ID FROM eZAddress_Online" );

        foreach ( $online_array as $addresItem )
        {
            $return_array[] = new eZOnline( $onlineItem["ID"] );
        }
    
        return $online_array;
    }

    /*!
      Returns the object ID.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the URL of the object.
    */
    function url()
    {
        return $this->URL;
    }

    /*!
    /*!
      Returns the OnlineTypeID of the object.
    */
    function onlineTypeID()
    {
        return $this->OnlineTypeID;
    }

    /*!
      Returns the OnlineType of the object.
    */
    function onlineType()
    {
        $onlineType = new eZOnlineType( $this->OnlineTypeID );
        return $onlineType;
    }

    /*!
      Sets the URL of the object.
    */
    function setURL( $value )
    {
        $this->URL= $value;
    }
    
    /*!
      Sets the OnlineTypeID of the object.
    */
    function setOnlineTypeID( $value )
    {
        if( is_numeric( $value ) )
        {
            $this->OnlineTypeID= $value;
        }
        
        if( get_class( $value ) == "ezonlinetype" )
        {
            $this->OnlineTypeID = $value->id();
        }
    }

    /*!
      Sets the OnlineType of the object.
    */
    function setOnlineType( $value )
    {
        if( is_numeric( $value ) )
        {
            $this->OnlineTypeID= $value;
        }
        
        if( get_class( $value ) == "ezonlinetype" )
        {
            $this->OnlineTypeID = $value->id();
        }
    }

//      /*!
//          Returns the url type options
//       */
//      function workStatusTypes()
//      {
//          $db =& eZDB::globalDatabase();
//          $db->array_query( $itemArray, $query="SHOW COLUMNS FROM eZAddress_Online LIKE 'URLType'" );
//          $items=preg_split( "/'|\,/", $itemArray[0]["Type"], 0, PREG_SPLIT_NO_EMPTY );
        
//          $count=count( $items );
        
//          for( $i=1; $i < $count - 1; $i++ )
//          {
//              $returnArray[]=$items[$i];
//          }
        
//          return $returnArray;
//      }

    var $ID;
    var $URL;
    var $OnlineTypeID;

    /// Relation to an eZOnlineType
    var $OnlineTypeID;
}

?>
