<?php
// 
// $Id: ezpricegroup.php,v 1.11 2001/07/31 11:33:11 jhe Exp $
//
// Definition of eZPriceGroup class
//
// Created on: <23-Feb-2001 12:57:01 amos>
//
// This source file is part of eZ publish, publishing software.
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
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
//! The class eZPriceGroup handles price groups and prices for products and options
/*!

*/

include_once( "classes/ezdb.php" );

class eZPriceGroup
{
    function eZPriceGroup( $id = false )
    {
        if ( is_array( $id ) )
            $this->fill( $id );
        else if ( $id )
            $this->get( $id );
    }

    function get( $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();
        $db->query_single( $array, "SELECT ID, Name, Description, Placement FROM eZTrade_PriceGroup
                                    WHERE ID='$id'" );
        $this->fill( $array );
    }

    function store()
    {
        $db =& eZDB::globalDatabase();
        $db->begin();

        $this->Name = $db->escapeString( $this->Name );
        $this->Description = $db->escapeString( $this->Description );
        
        if ( !isset( $this->Placement ) or !is_numeric( $this->Placement ) )
        {
            $db->query_single( $place, "SELECT max( Placement ) AS Placement
                                        FROM eZTrade_PriceGroup" );
            $this->Placement = $place[$db->fieldName("Placement")] == "NULL" ? 1 : $place[$db->fieldName("Placement")] + 1;
        }

        if ( is_numeric( $this->ID) )
        {
            $ret[] = $db->query( "UPDATE eZTrade_PriceGroup SET
                     Name='$this->Name',
                     Description='$this->Description',
                     Placement='$this->Placement'
                     WHERE
                     ID='$this->ID'" );
        }
        else
        {
            $db->lock( "eZTrade_PriceGroup" );
            $nextID = $db->nextID( "eZTrade_PriceGroup", "ID" );
            $ret[] = $db->query( "INSERT INTO eZTrade_PriceGroup
                   ( ID,
                     Name,
                     Description,
                     Placement )
                   VALUES
                   ( '$nextID',
                     '$this->Name',
                     '$this->Description',
                     '$this->Placement' )" );
            $db->unlock();
            $this->ID = $nextID;
        }

        eZDB::finish( $ret, $db );
    }

    function delete( $id = false )
    {
        if ( !$id )
            $id = $this->ID;

        $db =& eZDB::globalDatabase();
        $db->begin();
        $ret[] = $db->query( "DELETE FROM eZTrade_ProductPriceLink WHERE PriceID='$id'" );
        $ret[] = $db->query( "DELETE FROM eZTrade_GroupPriceLink WHERE PriceID='$id'" );
        $ret[] = $db->query( "DELETE FROM eZTrade_PriceGroup WHERE ID='$id'" );
        eZDB::finish( $ret, $db );
    }

    function fill( $array )
    {
        $db =& eZDB::globalDatabase();
        $this->ID = $array[$db->fieldName("ID")];
        $this->Name = $array[$db->fieldName("Name")];
        $this->Description = $array[$db->fieldName("Description")];
        $this->Placement = $array[$db->fieldName("Placement")];
    }

    /*!
      Returns all product groups from db.
    */
    function &getAll( $as_object = true )
    {
        $db =& eZDB::globalDatabase();
        $db->array_query( $array, "SELECT ID FROM eZTrade_PriceGroup
                                   ORDER BY Placement" );
        $ret = array();
        foreach( $array as $row )
        {
            $ret[] = $as_object ? new eZPriceGroup( $row[$db->fieldName("ID")] ) : $row[$db->fieldName("ID")];
        }
        return $ret;
    }

    /*!
      Returns all user groups which are connected to a specific price group.
    */
    function &userGroups( $id = false, $as_object = true )
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();
        $db->array_query( $array, "SELECT GroupID AS ID FROM eZTrade_GroupPriceLink
                                   WHERE PriceID='$id'" );
        $ret = array();
        foreach( $array as $row )
        {
            $ret[] = $as_object ? new eZUserGroup( $row[$db->fieldName("ID")] ) : $row[$db->fieldName("ID")];
        }
        return $ret;
    }

    /*!
      Adds a user group to a price group.
    */
    function addUserGroup( $group_id, $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        if ( get_class( $group_id ) == "ezusergroup" )
            $group_id = $group_id->id();

        $db =& eZDB::globalDatabase();
        $db->begin();
        $ret[] = $db->query( "INSERT INTO eZTrade_GroupPriceLink
                   ( GroupID,
                     PriceID )
                   VALUES
                   ( '$group_id',
                     '$id' )" );
        eZDB::finish( $ret, $db );
    }

    /*!
      Removes all user groups from a price group.
    */
    function removeUserGroups( $id = false )
    {
        if ( !$id )
            $id = $this->ID;

        $db =& eZDB::globalDatabase();
        $db->begin();
        $ret[] = $db->query( "DELETE FROM eZTrade_GroupPriceLink WHERE PriceID='$id'" );
        eZDB::finish( $ret, $db );
    }

    /*!
      Returns the price group which the user group is connected to or false if none.
    */
    function correctPriceGroup( $group_id )
    {
        $db =& eZDB::globalDatabase();
        if ( is_array( $group_id ) )
        {
            $first = true;
            foreach( $group_id as $group )
            {
                $first ? $group_text = "GroupID='$group'" : $group_text .= "OR GroupID='$group'";
                $first = false;
            }
            $group_text = "( $group_text )";
        }
        else
        {
            $group_text = "GroupID='$group_id'";
        }
        $db->array_query( $array, "SELECT PriceID
                                   FROM eZTrade_GroupPriceLink, eZTrade_PriceGroup
                                   WHERE PriceID=ID AND $group_text
                                   ORDER BY Placement LIMIT 1" );
        if ( count( $array ) == 1 )
            return $array[0][$db->fieldName("PriceID")];
        return false;
    }

    /*!
      Returns the price of a product according to it's price group, option and value type.
    */
    function correctPrice( $productid, $priceid, $optionid = 0, $valueid = 0 )
    {
        $db =& eZDB::globalDatabase();
        $db->array_query( $array, "SELECT Price FROM eZTrade_ProductPriceLink
                                   WHERE ProductID='$productid' AND PriceID='$priceid'
                                     AND OptionID='$optionid' AND ValueID='$valueid'" );
        if ( count( $array ) == 1 )
            return $array[0][$db->fieldName("Price")];
        return false;
    }

    /*!
      Returns all prices for a specific product with a specific option and value id.
    */
    function prices( $productid, $optionid = 0, $valueid = 0 )
    {
        $db =& eZDB::globalDatabase();
        $db->array_query( $array, "SELECT PriceID, Price FROM eZTrade_ProductPriceLink
                                   WHERE ProductID='$productid' AND OptionID='$optionid'
                                         AND ValueID='$valueid'" );
        return $array;
    }

    /*!
      Adds a price to a product with the specific option and value id.
    */
    function addPrice( $productid, $priceid, $price, $optionid = 0, $valueid = 0 )
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        
        $ret[] = $db->query( "INSERT INTO eZTrade_ProductPriceLink
                     ( PriceID,
                       ProductID,
                       Price,
                       OptionID,
                       ValueID )
                     VALUES
                     ( '$priceid',
                       '$productid',
                       '$price',
                       '$optionid',
                       '$valueid' )" );
        eZDB::finish( $ret, $db );
    }

    /*!
      Removes all prices from a product with the specific option and value id.
    */
    function removePrices( $productid, $optionid = 0, $valueid = 0 )
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        if ( $optionid >= 0 )
            $option_text = "AND OptionID='$optionid'";
        if ( $valueid >= 0 )
            $value_text = "AND ValueID='$valueid'";
        $ret[] = $db->query( "DELETE FROM eZTrade_ProductPriceLink
                     WHERE ProductID='$productid' $option_text $value_text" );
        eZDB::finish( $ret, $db );
    }

    /*!
      Returns the id of the group.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the name of the group.
    */
    function name()
    {
        return $this->Name;
    }

    /*!
      Returns the description of the group.
    */
    function description()
    {
        return $this->Description;
    }

    /*!
      Returns the placement of the group in the list.
    */
    function placement()
    {
        return $this->Placement;
    }

    /*!
      Sets the name of the group.
    */
    function setName( $name )
    {
        $this->Name = $name;
    }

    /*!
      Sets the description of the group.
    */
    function setDescription( $desc )
    {
        $this->Description = $desc;
    }

    /*!
      Sets the placement of the group in the list.
    */
    function setPlacement( $place )
    {
        $this->Placement = $place;
    }

    var $ID;
    var $Name;
    var $Description;
    var $Placement;
}

?>
