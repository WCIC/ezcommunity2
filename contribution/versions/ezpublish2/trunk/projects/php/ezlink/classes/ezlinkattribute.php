<?php
// 
// $Id: ezlinkattribute.php,v 1.1 2001/06/30 11:29:40 bf Exp $
//
// Definition of eZLinkAttribute class
//
// Jo Henrik Endrerud <jhe@ez.no>
// Created on: <29-Jun-2001 11:09:22 jhe>
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

include_once( "classes/ezdb.php" );
include_once( "ezlink/classes/ezlinktype.php" );

class eZLinkAttribute
{
    /*!
      Constructs a new eZLinkAttribute object. Retrieves the data from the database
      if a valid id is given as an argument.
    */
    function eZLinkAttribute( $id=-1 )
    {
        if ( $id != -1 )
        {
            $this->ID = $id;
            $this->get( $this->ID );
        }
    }

    /*!
      Stores a eZLinkAttribute object to the database.
    */
    function store()
    {
        $db =& eZDB::globalDatabase();

        $db->begin( );

        if ( !isset( $this->ID ) )
        {

            $db->lock( "eZLink_Attribute" );

            $db->array_query( $attribute_array, "SELECT Placement FROM eZLink_Attribute" );

            if ( count ( $attribute_array ) > 0 )
            {
                $place = max( $attribute_array );
                $place = $place[$db->fieldName("Placement")];
                $place++;
            }

            $timestamp = eZDateTime::timeStamp( true );
			$this->ID = $db->nextID( "eZLink_Attribute", "ID" );
            
            $res = $db->query( "INSERT INTO eZLink_Attribute
                                     (ID, Name, TypeID, Placement, Created )
                                     VALUES ( '$this->ID',
                                              '$this->Name',
                                              '$this->TypeID',
		                                      '$place',
		                                      '$timestamp')" );
        }
        else
        {
            $res = $db->query( "UPDATE eZLink_Attribute SET
		                         Name='$this->Name',
		                         Created=Created,
		                         TypeID='$this->TypeID' WHERE ID='$this->ID'" );
        }

        $db->unlock();
    
        if ( $res == false )
            $db->rollback( );
        else
            $db->commit();
        
        return true;
    }

    /*!
      Fetches the link attribute object values from the database.
    */
    function get( $id=-1 )
    {
        $db =& eZDB::globalDatabase();
        
        if ( $id != -1  )
        {
            $db->array_query( $attribute_array, "SELECT * FROM eZLink_Attribute WHERE ID='$id'" );
            
            if ( count( $attribute_array ) > 1 )
            {
                die( "Error: Link attributes with the same ID was found in the database. This shouldent happen." );
            }
            else if( count( $attribute_array ) == 1 )
            {
                $this->ID =& $attribute_array[0][$db->fieldName("ID")];
                $this->Name =& $attribute_array[0][$db->fieldName("Name")];
                $this->TypeID =& $attribute_array[0][$db->fieldName("TypeID")];
                $this->Placement =& $attribute_array[0][$db->fieldName("Placement")];
            }
        }
    }

    /*!
      Retrieves every option from the database.
    */
    function &getAll()
    {
        $db =& eZDB::globalDatabase();
        
        $return_array = array();
        $attribute_array = array();
        
        $db->array_query( $attribute_array, "SELECT ID FROM eZLink_Attribute ORDER BY Created" );
        
        for ( $i = 0; $i < count( $attribute_array ); $i++ )
        {  
            $return_array[$i] = new eZLinkAttribute( $attribute_array[$i][$db->fieldName("ID")] ); 
        }
        
        return $return_array;
    }

    /*!
      Deletes a option from the database.
    */
    function delete()
    {
        $db =& eZDB::globalDatabase();

        $db->query( "DELETE FROM eZLink_AttributeValue WHERE AttributeID='$this->ID'" );
        
        $tdb->query( "DELETE FROM eZLink_Attribute WHERE ID='$this->ID'" );
    }

    /*!
      Returns the object ID to the option. This is the unique ID stored in the database.
    */
    function id()
    {
       return $this->ID;
    }

    /*!
      Returns the name of the attribute.
    */
    function name()
    {
        return $this->Name;
    }

    /*!
      Returns the type of the attribute.
    */
    function type()
    {
       $type = new eZLinkType( $this->TypeID );
 
       return $type;
    }


    /*!
      Sets the name of the attribute.
    */
    function setName( $value )
    {
        $this->Name = $value;
    }

    /*!
      Sets the type of the attribute.
    */
    function setType( $type )
    {
       if ( get_class( $type ) == "ezlinktype" )
       {
           $this->TypeID = $type->id();
       }
    }

    /*!
      Sets the attribute value for the given link.
    */
    function setValue( $link, $value )
    {
        if ( get_class( $link ) == "ezlink" )
        {
            $db =& eZDB::globalDatabase();

            $db->begin( );

            $linkID = $link->id();
            
            // check if the attribute is already set, if so update
            $db->array_query( $value_array,
            "SELECT ID FROM eZLink_AttributeValue WHERE LinkID='$linkID' AND AttributeID='$this->ID'" );

            
            if ( count( $value_array ) > 0 )
            {
                $valueID = $value_array[0][$db->fieldName("ID")];
                
                $res = $db->query( "UPDATE eZLink_AttributeValue SET 
                                 Value='$value'
                                 WHERE ID='$valueID'" );
            }
            else
            {
                $db->lock( "eZLink_AttributeValue" );
                
                $nextID = $db->nextID( "eZLink_AttributeValue", "ID" );
                
                $res = $db->query( "INSERT INTO eZLink_AttributeValue
                                 ( ID, LinkID, AttributeID, Value )
                             VALUES
                                 ( '$nextID',
		                           '$linkID',
                                   '$this->ID',
                                   '$value' )" );
            }

            $db->unlock();
    
            if ( $res == false )
                $db->rollback( );
            else
                $db->commit();
            
        }
    }

    /*!
      Returns the attribute value to the given link.
    */
    function value( $link )
    {
       $ret = "";
       if ( get_class( $link ) == "ezlink" )
       {
           $db =& eZDB::globalDatabase();

           $linkID = $link->id();

           // check if the attribute is already set, if so update
           $db->array_query( $value_array,
           "SELECT Value FROM eZLink_AttributeValue WHERE LinkID='$linkID'
           AND AttributeID='$this->ID'" );

           if ( count( $value_array ) > 0 )
           {
               $ret = $value_array[0][$db->fieldName("Value")];
           }
       }
       return $ret;
    }

    /*!
      Moves this item up one step in the order list, this means that it will swap place with the item above.
    */
    function moveUp()
    {
        $db =& eZDB::globalDatabase();
        $db->query_single( $qry, "SELECT ID, Placement FROM eZLink_Attribute
                                  WHERE Placement < '$this->Placement' ORDER BY Placement DESC",
                           array( "Limit" => 1, "Offset" => 0 ) );
        
        $listorder = $qry[$db->fieldName("Placement")];
        $listid = $qry[$db->fieldName("ID")];
        $db->query( "UPDATE eZLink_Attribute SET Placement='$listorder' WHERE ID='$this->ID'" );
        $db->query( "UPDATE eZLink_Attribute SET Placement='$this->Placement' WHERE ID='$listid'" );
    }

    /*!
      Moves this item down one step in the order list, this means that it will swap place with the item below.
    */
    function moveDown()
    {
        $db =& eZDB::globalDatabase();
        
        $db->query_single( $qry, "SELECT ID, Placement FROM eZLink_Attribute
                                  WHERE Placement > '$this->Placement' ORDER BY Placement ASC",
                           array( "Limit" => 1, "Offset" => 0 ) );
        $listorder = $qry[$db->fieldName("Placement")];
        $listid = $qry[$db->fieldName("ID")];
        $db->query( "UPDATE eZLink_Attribute SET Placement='$listorder' WHERE ID='$this->ID'" );
        $db->query( "UPDATE eZLink_Attribute SET Placement='$this->Placement' WHERE ID='$listid'" );
    }

    var $ID;
    var $TypeID;
    var $Name;
    var $AttributeType;
    var $Placement;
}

?>
