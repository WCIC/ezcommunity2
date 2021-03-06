<?
// 
// $Id: ezcountry.php,v 1.2 2000/11/01 09:35:23 ce-cvs Exp $
//
// Definition of eZCountry class
//
// B�rd Farstad <bf@ez.no>
// Created on: <31-Oct-2000 11:49:30 bf>
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
//!! eZContact
//! eZCountry handles countries.
/*!
  
*/

include_once( "classes/ezdb.php" );

class eZCountry
{
    /*!
      Constructs a new eZCountry object.
    */
    function eZCountry( $id="", $fetch=true )
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
      Stores a country to the database.
    */  
    function store()
    {
        $this->dbInit();

        $ret = false;
        
        if ( !isset( $this->ID ) )
        {
            $this->Database->query( "INSERT INTO eZContact_Country
                    SET ISO='$this->ISO',
                    Name='$this->Name'" );            

            $this->ID = mysql_insert_id();

            $this->State_ = "Coherent";
            $ret = true;
        }
        else
        {
            $this->Database->query( "UPDATE eZContact_Country
                    SET ISO='$this->ISO',
                    Name='$this->Name'
                    WHERE ID='$this->ID'" );            

            $this->State_ = "Coherent";
            $ret = true;            
        }        

        
        return $ret;
    }

  
    /*!
      Fetches an country with object id==$id;
    */  
    function get( $id="" )
    {
        $this->dbInit();    
        if ( $id != "" )
        {
            $this->Database->array_query( $country_array, "SELECT * FROM eZContact_Country WHERE ID='$id'" );
            if ( count( $country_array ) > 1 )
            {
                die( "Feil: Flere countryer med samme ID funnet i database, dette skal ikke v�re mulig. " );
            }
            else if ( count( $country_array ) == 1 )
            {
                $this->ID =& $country_array[ 0 ][ "ID" ];
                $this->ISO =& $country_array[ 0 ][ "ISO" ];
                $this->Name =& $country_array[ 0 ][ "Name" ];
            }
        }
    }

    /*!
      Returns every country as a eZCountry object
    */
    function &getAll( )
    {
        $this->dbInit();    
        $country_array = 0;
        $return_array = array();
    
        $this->Database->array_query( $country_array, "SELECT * FROM eZContact_Country ORDER BY Name" );

        foreach ( $country_array as $country )
        {
            $return_array[] = new eZCountry( $country["ID"] );
        }
    
        return $return_array;
    }

    /*!
      Returns every country as an array. This function is faster then the one above.
    */
    function &getAllArray( )
    {
        $this->dbInit();    
        $country_array = 0;
    
        $this->Database->array_query( $country_array, "SELECT * FROM eZContact_Country ORDER BY Name" );
    
        return $country_array;
    }
    
    /*!
      Sletter adressen med ID == $id;
     */
    function delete()
    {
        $this->dbInit();
        
        $this->Database->query( "DELETE FROM eZContact_Country WHERE ID='$this->ID'" );
    }    
    

    /*!
      Returns the object ID.
    */
    function id( )
    {
        return $this->ID;
    }
    
    /*!
      Returns the ISO code of the country.
    */
    function iso( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->ISO;
    }

    /*!
      Returns the name of the country.
    */
    function name( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        return $this->Name;
    }


    /*!
      Sets the ISO code of the country.
    */
    function setISO( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $this->ISO = $value;
    }

    /*!
      Sets the name of the country.
    */
    function setName( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $this->Name = $value;
    }

    /*!
      \private
      Open the database.
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
    var $ISO;
    var $Name;

    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}

?>
