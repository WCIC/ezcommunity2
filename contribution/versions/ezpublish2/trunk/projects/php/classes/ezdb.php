<?php
// 
// $Id: ezdb.php,v 1.21 2001/01/04 19:24:13 jb Exp $
//
// Definition of eZDB class
//
// B�rd Farstad <bf@ez.no>
// Created on: Created on: <14-Jul-2000 13:01:15 bf>
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

//!! eZCommon
//! The eZDB class provides database functions.
/*!
  The eZDB class hanles database connections and is a wrapper
  to query functions.

   
*/

/*!TODO
  Add a generic query builder for use with search. A more advanced version of the query
  class found in ezlink/class/ezquery.
*/

class eZDB
{
    /*!
      Constructs a new eZDB object, connects to the database and
      selects the desired table.

      The eZDB constructor takes a .ini file as an argument.
      The second argument defines under what category in the .ini
      file the database information is located.
    */
    function eZDB( $iniFile, $category )
    {
        $ini =& $GLOBALS["GlobalSiteIni"];
        
        $this->Server =& $ini->read_var( "site", "Server" );
        $this->DB =& $ini->read_var( "site", "Database" );
        $this->User =& $ini->read_var( "site", "User" );
        $this->Password =& $ini->read_var( "site", "Password" );
        
        mysql_pconnect( $this->Server, $this->User, $this->Password )
            or print( "Error: could not connect to the database." );
        
        mysql_select_db( $this->DB )
            or print( "Error: could not connect to the database." );;
    }

    /*!
      Execute a query on the global MySQL database link.  If it returns an error,
      the script is halted and the attempted SQL query and MySQL error message are printed.
    */
    function &query( $sql )
    {
        $result = mysql_query( $sql );
  
        if ( $result )
        {
            return $result;
        }
        else
        {
            $this->Error = "<code>" . htmlentities( $sql ) . "</code><br>\n<b>" . htmlentities(mysql_error()) . "</b>\n" ;
            return false;
        }
    }

    /*!
      Executes a SELECT query that returns multiple rows and puts the results into the passed
      array as an indexed associative array.  The array is cleared first.  The results start with
      the array start at 0, and the number of results can be found with the count() function.
    */
    function array_query( &$array, $sql )
    {
        $array = array();
        $result =& $this->query( $sql );

        if ( $result == false )
        {
            print( $this->Error );
            return false;
        }

        if ( mysql_num_rows( $result ) > 0 )
        {
            for($i = 0; $i < mysql_num_rows( $result ); $i++ )
                $array[$i] =& mysql_fetch_array( $result );
        }
    }

    /*!
      Differs from the above function only by not creating av empty array,
      but simply appends to the array passed as an argument.
     */    
    function array_query_append( &$array, $sql)
    {
        $result =& query($sql);

        if ( $result == false )
        {
            print( $this->Error );
            return false;
        }

        $offset = count( $array );
        if ( count( $result ) > 0 )
        { 
            for($i = 0; $i < mysql_num_rows($result); $i++)
                $array[$i + $offset] =& mysql_fetch_array($result);
        }
    }

    /*!
      Returns the last error message.
    */
    function error()
    {
        return $this->Error;
    }

    /*!
      \static
      Returns the global database object, if it doesn't exists it is initialized.
      This is safe to call without an object since it does not access member variables.
    */
    function globalDatabase()
    {
        if ( get_class( $eZDB ) != "ezdb" )
        {
            $eZDB = new eZDB( "site.ini", "site" );
        }
        return $eZDB;
    }

    /// the server to connect to
    var $Server;
    /// the database to use
    var $DB;
    /// the username to use
    var $User;
    /// the password to use
    var $Password;

    // the last error message
    var $Error;
}

?>
