<?php
// 
// $Id: ezauthor.php,v 1.2 2001/06/12 14:32:33 ce Exp $
//
// Definition of eZAuthor class
//
// B�rd Farstad <bf@ez.no>
// Created on: <31-May-2001 13:52:30 bf>
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

//!! eZUser
//! eZAuthor handles authors/photographers.
/*!
  \sa eZUser eZUserGroup eZPermission eZModule eZForgot
*/

include_once( "classes/ezdb.php" );


class eZAuthor
{
    /*!
      Constructs a new eZAuthor object.
    */
    function eZAuthor( $id=-1 )
    {
        if ( $id != -1 )
        {
            $this->ID = $id;
            $this->get( $this->ID );
        }
    }

    /*!
      Stores or updates a eZAuthor object in the database.
    */
    function store()
    {
        $db =& eZDB::globalDatabase();

        $name = addslashes( $this->Name );
        $email = addslashes( $this->EMail );

        if ( !isset( $this->ID ) )
        {
            $db->query( "INSERT INTO eZUser_Author SET
		                 Name='$name',
                         EMail='$email'
                       " );
			$this->ID = $db->insertID();
        }
        else
        {
            $db->query( "UPDATE eZUser_Author SET
		                 Name='$name',
                         EMail='$email'
                        WHERE ID='$this->ID'" );
        }

        return true;
    }

    /*!
      Deletes a eZAuthor object from the database.
    */
    function delete( $id )
    {
        $db =& eZDB::globalDatabase();

        if ( is_numeric( $id ) )
        {
            $db->query( "DELETE FROM eZUser_Author WHERE ID='$id'" );
        }
        else if ( isset( $this->ID ) )
        {
            $db->query( "DELETE FROM eZUser_Author WHERE ID='$this->ID'" );
        }
        
        return true;
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
            $db->array_query( $author_array, "SELECT * FROM eZUser_Author WHERE ID='$id'" );
            if( count( $author_array ) == 1 )
            {
                $this->ID =& $author_array[0][ "ID" ];
                $this->Name =& $author_array[0][ "Name" ];
                $this->EMail =& $author_array[0][ "EMail" ];
                $ret = true;
            }
            elseif( count( $author_array ) == 1 )
            {
                $this->ID = 0;
            }
        }
        return $ret;
    }


    /*!
      Fetches the user id from the database. And returns a array of eZAuthor objects.
    */
    function &getAll(  )
    {
        $db =& eZDB::globalDatabase();

        $return_array = array();
        $author_array = array();


        $db->array_query( $author_array, "SELECT ID FROM eZUser_Author
                                        ORDER By Name" );

        foreach ( $author_array as $author )
        {
            $return_array[] = new eZAuthor( $author[0] );
        }
        return $return_array;
    }
    /*!
      Returns the object id.
    */
    function id()
    {
        return $this->ID;
    }
    
    /*!
      Returns the name.
    */
    function name( $html = true )
    {
        if( $html )
            return htmlspecialchars( $this->Name );
        return $this->Name;
    }


    /*!
      Returns the authors e-mail address.
    */
    function email( )
    {
       return $this->EMail;
    }

    /*!
      Sets the name.
    */
    function setName( $value )
    {
       $this->Name = $value;
    }
    
    /*!
      Sets the email address to the user.
    */
    function setEmail( $value )
    {
       $this->EMail = $value;
    }
    
    var $ID;
    var $Name;
    var $EMail;
}

?>
