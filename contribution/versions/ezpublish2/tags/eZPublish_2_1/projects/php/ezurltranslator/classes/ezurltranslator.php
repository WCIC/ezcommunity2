<?php
// 
// $Id: ezurltranslator.php,v 1.3 2001/05/05 11:16:05 bf Exp $
//
// Definition of eZURLTranslator class
//
// B�rd Farstad <bf@ez.no>
// Created on: <22-Apr-2001 18:38:38 bf>
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

//!! eZURLTranslator
//! The eZURLTranslator class provides URL translation functions.
/*!
   
*/


include_once( "classes/ezdb.php" );

class eZURLTranslator
{
    /*!

     */
    function eZURLTranslator( $id=-1 )
    {
        if ( $id != -1 )
        {
            $this->ID = $id;
            $this->get( $this->ID );
        }
    }

    /*!
      \static
    */
    function translate( $url )
    {
        $ret = "/error/404";
        
        $db =& eZDB::globalDatabase();
        
        $db->array_query( $url_array,
            "SELECT Dest FROM eZURLTranslator_URL
             WHERE Source='$url'" );

        
        if ( count( $url_array ) > 0 )
        {                
            $ret = $url_array[0]["Dest"];
        }

        return $ret;
    }

    /*!
      Stores/updates the URL translation.
    */
    function store()
    {
        $db =& eZDB::globalDatabase();

        if ( !isset( $this->ID ) )
        {
            $db->query( "INSERT INTO eZURLTranslator_URL SET
		                 Source='$this->Source',
		                 Dest='$this->Dest',
		                 Created=now()
                          " );
        
			$this->ID = $db->insertID();
        }
        else
        {
            $db->query( "UPDATE eZURLTranslator_URL SET
		                 Source='$this->Source',
		                 Dest='$this->Dest',
		                 Created=now()
                        WHERE ID='$this->ID'" );
        }
        
        return true;
    }

    /*!
      Fetches the URL translation from the database.
    */
    function get( $id=-1 )
    {
        $db =& eZDB::globalDatabase();
        
        if ( $id != -1  )
        {
            $db->array_query( $url_array, "SELECT * FROM eZURLTranslator_URL WHERE ID='$id'" );
            
            if ( count( $url_array ) > 1 )
            {
                die( "Error: Url translations's with the same ID was found in the database. This shouldn't happen." );
            }
            else if ( count( $url_array ) == 1 )
            {
                $this->ID =& $url_array[0][ "ID" ];
                $this->Source =& $url_array[0][ "Source" ];
                $this->Dest =& $url_array[0][ "Dest" ];
            }
        }
    }

    /*!
      Retrieves all the URL translations from the database.
    */
    function &getAll()
    {
        $db =& eZDB::globalDatabase();
        
        $return_array = array();
        $url_array = array();
        
        $db->array_query( $url_array, "SELECT ID FROM eZURLTranslator_URL ORDER BY Created" );
        
        for ( $i=0; $i<count($url_array); $i++ )
        {
            $return_array[$i] = new eZURLTranslator( $url_array[$i]["ID"], 0 );
        }
        
        return $return_array;
    }

    /*!
      Deletes a URL translation from the database.
    */
    function delete()
    {
        $db =& eZDB::globalDatabase();

        $db->query( "DELETE FROM eZURLTranslator_URL WHERE ID='$this->ID'" );
    }

    /*!
      Returns the object ID to the option. This is the unique ID stored in the database.
    */
    function id()
    {
       return $this->ID;
    }

    /*!
      Returns the source of the url translation.
    */
    function source()
    {
        return $this->Source;
    }

    /*!
      Returns the destination of the url translation.
    */
    function dest()
    {
        return $this->Dest;
    }


    /*!
      Sets the source of the URL translation.
    */
    function setSource( $value )
    {
        $this->Source = $value;
    }

    /*!
      Sets the destination of the URL translation.
    */
    function setDest( $value )
    {
        $this->Dest = $value;
    }
    
    var $ID;
    var $Source;
    var $Dest;
    
}

?>
