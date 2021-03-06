<?
// 
// $Id: ezoption.php,v 1.13 2000/11/01 09:24:18 ce-cvs Exp $
//
// Definition of eZOption class
//
// B�rd Farstad <bf@ez.no>
// Created on: <12-Sep-2000 15:01:53 bf>
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
//! The eZOption class handles options for products and product categories.
/*!
  eZOption class handles product options. The class has functions for storing
  to the database and fetching from the database.

  
  You can add option values to options, and get the values for a option.

  Example code:
  \code
  // Create a new option object
  $option = new eZOption();

  $option->setName( "Color" );
  $option->setDescription( "The color of the product" );

  // Store the option to the database
  $option->store();

  // Fetch all the options from the database.
  $optionArray = $option->getAll();

  //print out all the options.
  foreach ( $optionArray as $optionItem )
  {
    print( "Option: " . $optionItem->name() . "<br>" );
  }

  // Create some values
  $value1 = new eZOptionValue();
  $value1->setName( "Red" );

  $value2 = new eZOptionValue();
  $value2->setName( "Green" );

  $value3 = new eZOptionValue();
  $value3->setName( "Blue" );

  // Add them to the option
  $option->addValue( $value1 );
  $option->addValue( $value2 );
  $option->addValue( $value3 );  

  \endcode  
  \sa eZProductCategory eZOptionValue
*/

include_once( "classes/ezdb.php" );
include_once( "eztrade/classes/ezoptionvalue.php" );

class eZOption
{
    /*!
      Constructs a new eZOption object. Retrieves the data from the database
      if a valid id is given as an argument.
    */
    function eZOption( $id=-1, $fetch=true )
    {
        $this->IsConnected = false;
        if ( $id != -1 )
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
      Stores a eZOption object to the database.
    */
    function store()
    {
        $this->dbInit();

        if ( !isset( $this->ID ) )
        {
            $this->Database->query( "INSERT INTO eZTrade_Option SET
		                         Name='$this->Name',
                                 Description='$this->Description'" );
        
            $this->ID = mysql_insert_id();
            $this->State_ = "Coherent";
        }
        else
        {
            $this->Database->query( "UPDATE eZTrade_Option SET
		                         Name='$this->Name',
                                 Description='$this->Description' WHERE ID='$this->ID'" );
            $this->State_ = "Coherent";
        }
        
        return true;
    }

    /*!
      Fetches the option object values from the database.
    */
    function get( $id=-1 )
    {
        $this->dbInit();
        
        if ( $id != -1  )
        {
            $this->Database->array_query( $option_array, "SELECT * FROM eZTrade_Option WHERE ID='$id'" );
            
            if ( count( $option_array ) > 1 )
            {
                die( "Error: Option's with the same ID was found in the database. This shouldent happen." );
            }
            else if( count( $option_array ) == 1 )
            {
                $this->ID =& $option_array[0][ "ID" ];
                $this->Name =& $option_array[0][ "Name" ];
                $this->Description =& $option_array[0][ "Description" ];
                
                $this->State_ = "Coherent";                
            }
        }
        else
        {
            $this->State_ = "Dirty";
        }
    }

    /*!
      Retrieves every option from the database.
    */
    function &getAll()
    {
        $this->dbInit();
        
        $return_array = array();
        $option_array = array();
        
        $this->Database->array_query( $option_array, "SELECT ID FROM eZTrade_Option ORDER BY Name" );
        
        for ( $i=0; $i<count($option_array); $i++ )
        {
            $return_array[$i] = new eZOption( $option_array[$i]["ID"], 0 );
        }
        
        return $return_array;
    }

    /*!
      Deletes a option from the database.
    */
    function delete()
    {
        $this->dbInit();
        
        $this->Database->array_query( $option_array, "DELETE FROM eZTrade_OptionValue WHERE OptionID='$this->ID'" );
        $this->Database->array_query( $option_array, "DELETE FROM eZTrade_ProductOptionLink WHERE OptionID='$this->ID'" );
        $this->Database->array_query( $option_array, "DELETE FROM eZTrade_Option WHERE ID='$this->ID'" );
    }

    /*!
      Returns the object ID to the option. This is the unique ID stored in the database.
    */
    function id()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
       
       return $this->ID;
    }

    /*!
      Returns the name of the option.
    */
    function name()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
 
        return $this->Name;
    }

    /*!
      Returns the option description.
    */
    function description()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        return $this->Description;
    }

    /*!
      Returns all the values to the current option.

      The values are returned as an array of eZOptionValue objects.
    */
    function &values( )
    {
        $value = new eZOptionValue();
        return $value->getByOption( $this );
    }

    /*!
      Sets the name of the option.
    */
    function setName( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->Name = $value;
    }

    /*!
      Sets the description of the option.
    */
    function setDescription( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->Description = $value;
    }

    /*!
      Adds a value to the option. The value must be of eZOptionValue type.
      
      NOTE: It stores the value object to the database. 
    */
    function addValue( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        if ( get_class( $value ) == "ezoptionvalue" )
        {
            $this->dbInit();

            $value->setOptionID( $this->ID );
            
            $value->store();            
        }
    }
    
    /*!
      Private function.
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
    var $Name;
    var $Description;

    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}

?>
