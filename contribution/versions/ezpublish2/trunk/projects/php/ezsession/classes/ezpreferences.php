<?
// 
// $Id: ezpreferences.php,v 1.3 2001/01/22 14:43:01 jb Exp $
//
// Definition of eZPreferences class
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Jan-2001 13:05:06 bf>
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

//!! eZSession
//! eZPreferences handles preferences variables.
/*!
  The preferences only works if there is a user logged in.
  
  \code
  // include the code
  include_once( "ezsession/classes/ezpreferences.php" );

  // set the preferences
  $preferences = new eZPreferences();
  $preferences->setVariable( "Color", "Blue" );

  // get the preferences
  $preferences = new eZPreferences();
  $color =& $preferences->variable( "Color" );

  if ( $color )
  {
     print( "the user prefers a color: $color" );
  }
  else
  {
     print( "the user has no preferences" );
  }
  \endcode

*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezdatetime.php" );

class eZPreferences
{
    /*!
      Creates a new eZPreferences object.

      It will automatically fetch the currently logged in user.
    */
    function eZPreferences( )
    {
        $this->UserObject =& eZUser::currentUser();        
    }


    /*!
      Returns the value of a preferences variable.

      If the variable does not exist 0 (false) is returned.
    */
    function variable( $name )
    {
       $ret = false;
       if ( get_class( $this->UserObject ) == "ezuser" )
       {           
           $this->dbInit();
           
           $userID = $this->UserObject->id();
           
           $this->Database->array_query( $value_array, "SELECT Value FROM eZSession_Preferences
                                                    WHERE UserID='$userID' AND Name='$name'" );
           
           if ( count( $value_array ) == 1 )
           {
               $ret = $value_array[0]["Value"];
           }
       }
       return $ret;
    }

    /*!
      Adds or updates a variable to the preferences.

      Returns false if unsuccessful.
    */
    function setVariable( $name, $value )
    {
        $ret = false;
        if ( get_class( $this->UserObject ) == "ezuser" )
        {
            $this->dbInit();
            
            $userID = $this->UserObject->id();
            
            $this->Database->array_query( $value_array, "SELECT ID FROM eZSession_Preferences
                                                    WHERE UserID='$userID' AND Name='$name'" );
            if ( count( $value_array ) == 1 )
            {
                $valueID = $value_array[0]["ID"];
                $this->Database->query( "UPDATE eZSession_Preferences SET
		                         Value='$value' WHERE ID='$valueID'
                                 " );
                $ret = true;
            }
            else
            {
                $this->Database->query( "INSERT INTO eZSession_Preferences SET
		                         UserID='$userID',
		                         Name='$name',
		                         Value='$value'
                                 " );
                $ret = true;                
            }
        }

        return $ret;
    }

    /*!
      \private
      Open the database for read and write. Gets all the database information from site.ini.
    */
    function dbInit()
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = eZDB::globalDatabase();
            $this->IsConnected = true;
        }
    }

    /// copy of the current logged in user 
    var $UserObject;
    

    ///  Variable for keeping the database connection.
    var $Database;

    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}

?>
