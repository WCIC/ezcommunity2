<?
// 
// $Id: ezuser.php,v 1.50 2001/02/06 15:46:30 jb Exp $
//
// Definition of eZCompany class
//
// B�rd Farstad <bf@ez.no>
// Created on: <26-Sep-2000 18:47:27 bf>
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
//! eZUser handles users.
/*!
  
  Example code:
  \code
  // create a new user and set some variables.
  $user = new eZUser();
  $user->setLogin( "bf" );
  $user->setPassword( "secret" );
  $user->setEmail( "bf@ez.no" );
  $user->setFirstName( "B�rd" );
  $user->setLastName( "Farstad" );

  // check if a user with that username exists
  if ( !$user->exists( $user->login() ) )
  {
      // Store the user to the database.
      echo "Username is not used, creating user.<br>";      
      $user->store();        
  }

  // validate a userlogin and password.
  $user = $user->validateUser( "bf", "secret" );

  if ( $user )
  {
      print( "Password and username are ok!<br>" );
      print( $user->firstName() );
      print( $user->lastName() );      
  }
  
  \endcode

  \sa eZUserGroup eZPermission eZModule eZForgot
*/

include_once( "classes/ezdb.php" );

include_once( "ezaddress/classes/ezaddress.php" );
include_once( "classes/ezdatetime.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once( "ezuser/classes/ezusergroup.php" );

class eZUser
{
    /*!
      Constructs a new eZUser object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZUser( $id=-1, $fetch=true )
    {
        if ( is_array( $id ) )
        {
            $this->fill( $id );
        }
        else if ( $id != -1 )
        {
            $this->ID = $id;
            if ( $fetch == true )
            {
                $this->get( $this->ID );
            }
        }
    }

    /*!
      Stores or updates a eZUser object in the database.
    */
    function store()
    {
        $db =& eZDB::globalDatabase();

        $GLOBALS["DEBUG"] = true;
        
        if ( !isset( $this->ID ) )
        {
            $db->query( "INSERT INTO eZUser_User SET
		                         Login='$this->Login',
                                 Password=PASSWORD('$this->Password'),
                                 Email='$this->Email',
                                 InfoSubscription='$this->InfoSubscription',
                                 FirstName='$this->FirstName',
                                 LastName='$this->LastName',
                                 Signature='$this->Signature'" );
            $this->ID = mysql_insert_id();
        }
        else
        {
            $db->query( "UPDATE eZUser_User SET
		                         Login='$this->Login',
                                 Email='$this->Email',
                                 InfoSubscription='$this->InfoSubscription',
                                 FirstName='$this->FirstName',
                                 Signature='$this->Signature',
                                 LastName='$this->LastName'
                                 WHERE ID='$this->ID'" );

            // update password if set.
            if ( isset( $this->Password ) )
            {
                $db->query( "UPDATE eZUser_User SET
                                 Password=PASSWORD('$this->Password')
                                 WHERE ID='$this->ID'" );
            }
            
        }
        
        return true;
    }

    /*!
      Deletes a eZUser object from the database.

    */
    function delete()
    {
        $db =& eZDB::globalDatabase();

        if ( isset( $this->ID ) )
        {
            $db->query( "DELETE FROM eZUser_UserGroupLink WHERE UserID='$this->ID'" );
            $db->query( "DELETE FROM eZUser_UserAddressLink WHERE UserID='$this->ID'" );

            $db->query( "DELETE FROM eZUser_User WHERE ID='$this->ID'" );
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
            $db->array_query( $user_array, "SELECT * FROM eZUser_User WHERE ID='$id'",
                              0, 1 );
            if( count( $user_array ) == 1 )
            {
                $this->fill( $user_array[0] );
                $ret = true;
            }
        }
        return $ret;
    }

    /*!
      Fills in information to the object taken from the array.
    */
    function fill( &$user_array )
    {
        $this->ID =& $user_array[ "ID" ];
        $this->Login =& $user_array[ "Login" ];
        $this->Email =& $user_array[ "Email" ];
        $this->InfoSubscription =& $user_array[ "InfoSubscription" ];
        $this->FirstName =& $user_array[ "FirstName" ];
        $this->LastName =& $user_array[ "LastName" ];
        $this->Signature =& $user_array[ "Signature" ];
    }

    /*!
      Returns the number of rows a getAll with the same search param would give.
    */
    function &getAllCount( $search = false )
    {
        $db =& eZDB::globalDatabase();

        $query = new eZQuery( array( "FirstName", "LastName",
                                     "Login", "Email" ), $search );

        $db->query_single( $user_array, "SELECT count( ID ) AS Count FROM eZUser_User
                                         WHERE " . $query->buildQuery() );

        return $user_array["Count"];
    }

    /*!
      Fetches the user id from the database. And returns a array of eZUser objects.
    */
    function &getAll( $order="Login", $as_object = true, $search = false, $max = -1, $index = 0 )
    {
        $db =& eZDB::globalDatabase();

        switch ( $order )
        {
            case "name" :
            {
                $orderBy = "LastName, FirstName";
            }
            break;
            
            case "lastname" :
            {
                $orderBy = "LastName";
            }
            break;

            case "firstname" :
            {
                $orderBy = "FirstName";
            }
            break;
            
            case "lastname" :
            {
                $orderBy = "LastName";
            }
            break;

            case "email" :
            {
                $orderBy = "Email";
            }
            break;
            
            default :
                $orderBy = "Login";
            break;
        }
                
        $return_array = array();
        $user_array = array();

        if ( $as_object )
            $select = "*";
        else
            $select = "ID";

        if ( $max >= 0 )
        {
            $limit = "LIMIT $index, $max";
        }

        $query = new eZQuery( array( "FirstName", "LastName",
                                     "Login", "Email" ), $search );

        $db->array_query( $user_array, "SELECT $select FROM eZUser_User
                                        WHERE " . $query->buildQuery() . "
                                        ORDER By $orderBy $limit" );

        if ( $as_object )
        {
            foreach ( $user_array as $user )
            {
                $return_array[] = new eZUser( $user );
            }
        }
        else
        {
            foreach ( $user_array as $user )
            {
                $return_array[] = $user[ "ID" ];
            }
        }
        return $return_array;
    }

    /*!
      Returns the eZUser object where login = $login.

      False (0) is returned if the users isn't validated.
    */
    function getUser( $login )
    {
        $db =& eZDB::globalDatabase();
        $ret = false;
        
        $db->array_query( $user_array, "SELECT * FROM eZUser_User
                                                    WHERE Login='$login'" );
        if ( count( $user_array ) == 1 )
        {
            $ret = new eZUser( $user_array[0]["ID"] );
        }
        return $ret;
    }

    /*!
      Returns the correct eZUser object if the user is validated.

      False (0) is returned if the users isn't validated.
    */
    function validateUser( $login, $password )
    {
        $db =& eZDB::globalDatabase();
        $ret = false;
        
        $db->array_query( $user_array, "SELECT * FROM eZUser_User
                                                    WHERE Login='$login'
                                                    AND Password=PASSWORD('$password')" );
        if ( count( $user_array ) == 1 )
        {
            $ret = new eZUser( $user_array[0]["ID"] );
        }
        return $ret;
    }


    /*!
      Returns the eZUser object if a user with that login exits.

      Falst (0) is returned if not.
    */
    function exists( $login )
    {
        $db =& eZDB::globalDatabase();
        $ret = false;
        
        $db->array_query( $user_array, "SELECT * FROM eZUser_User
                                                    WHERE Login='$login'" );

        if ( count( $user_array ) == 1 )
        {
            $ret = new eZUser( $user_array[0]["ID"] );
        }

        return $ret;        
    }

    /*!
      Returns the object id.
    */
    function id()
    {
        return $this->ID;
    }
    
    /*!
      Returns the signature.
    */
    function signature()
    {
        return $this->Signature;
    }

    /*!
      Returns the users login.
    */
    function login( )
    {
       return $this->Login;
    }

    
    /*!
      Returns the users InfoSubscription.
    */
    function infoSubscription( )
    {
       $ret = false;
       
       if ( $this->InfoSubscription == "true" )
       {
           $ret = true;
       }
       return $ret;
    }


    /*!
      Returns the users e-mail address.
    */
    function email( )
    {
       return $this->Email;
    }

    /*!
      Returns the users name and email where the email is surrounded by <>.
    */
    function namedEmail()
    {
        return $this->name() . " <" . $this->email() . ">";
    }

    /*!
      Returns the users first and last name with a space in between.
    */
    function name()
    {
        return $this->firstName() . " " . $this->lastName();
    }

    /*!
      Returns the users first name.
    */
    function firstName( )
    {
        return $this->FirstName;
    }

    /*!
      Returns the users last name.
    */
    function lastName( )
    {
        return $this->LastName;
    }
    
    /*!
      Sets the signature.
    */
    function setSignature( $value )
    {
       $this->Signature = $value;
    }

    /*!
      Sets the login.
    */
    function setLogin( $value )
    {
       $this->Login = $value;
    }

    /*!
      Sets the password.
    */
    function setPassword( $value )
    {
       $this->Password = $value;
    }
    
    /*!
      Sets the email address to the user.
    */
    function setEmail( $value )
    {
       $this->Email = $value;
    }

    /*!
      Sets the infoSubscription to the user.

      This value indicates if the user wants to receive updates
      from the site. true and false are valid arguments.
    */
    function setInfoSubscription( $value )
    {
       if ( $value == true )
       {
           $this->InfoSubscription = "true";
       }
       else
       {
           $this->InfoSubscription = "false";
       }
    }

    /*!
      Sets the users first name.
    */
    function setFirstName( $value )
    {
       $this->FirstName = $value;
    }

    /*!
      Sets the users last name.
    */
    function setLastName( $value )
    {
       $this->LastName = $value;
    }

    /*!
      \static
      Logs in the user given argument. The $user argument must be a eZUser object.

      Returns false if unsuccess ful, true if successful.
    */
    function loginUser( $user )
    {
        $ret = false;

        if ( get_class( $user ) == "ezuser" )
        {
            $session =& eZSession::globalSession();

            if ( !$session->fetch() )
            {
                $session->store();
            }
            
            $session->refresh();
            $session->refresh();
            
            $session->setVariable( "AuthenticatedUser", $user->id() );
            $ret = true;            
        }
        return $ret;
    }

    /*!
      \static
      Logs out a user.
    */
    function logout( )
    {
        $session =& eZSession::globalSession();
        if ( $session->fetch() )
        {
            $session->setVariable( "AuthenticatedUser", "" );
        }
    }

    

    /*!
      \static
      Returns the current logged on user as a eZUser object. If the user's session timeout
      has timedout the user is logged out.

      If the session timeout is set to 0 it is disabled.

      False is returned if unseccessful.
    */
    function currentUser()
    {
        $user =& $GLOBALS["eZCurrentUserObject"];

        if ( ( get_class( $user ) == "ezuser" ) and ( is_numeric( $user->id() ) ) )
        {
            return $user;
        }
        

        $session =& eZSession::globalSession();

        $returnValue = false;
        
        if ( $session->fetch( false ) )
        {
            $val =& $session->variable( "AuthenticatedUser" );

            $user = new eZUser( $val );

//              print( $session->variable( "AuthenticatedUser" ) );

            $idle = $session->idle();
            $idle = $idle / 60;
       
            if ( ( $idle > $user->timeoutValue() ) && ( $user->timeoutValue() != 0 ) )
            {
                $user->logout();
                $user = false;
            }
            else            
            {
                if ( ( $user->id() != 0 ) && ( $user->id() != "" ) )
                {
                    $session->refresh( );
                    $returnValue = $user;
                }
            }            
        }

        return $returnValue;
    }


    /*!
      \static
      Returns the currently logged in users with sessions. It is returned as an
      array of eZUser and eZSession objects.
    */
    function currentUsers()
    {
        $session =& eZSession::globalSession();

        $ret = array();

        $sessionIDArray =& $session->getByVariable( "AuthenticatedUser" );

        foreach ( $sessionIDArray as $sessionID )
        {
            $session = new eZSession( $sessionID );
            $user = new eZUser( $session->variable( "AuthenticatedUser" ) );

            $idle = $session->idle();
            $idle = $idle / 60;

            if ( ( $idle > $user->timeoutValue() ) && ( $user->timeoutValue() != 0  ) )
            {
                $session->delete( );                
            }
            else            
            {
                if ( ( $user->id() != 0 ) && ( $user->id() != "" ) )
                {

                    
                    $ret[] = array( $user, $session );
                }
            }
        }
        
        return $ret;
    }

    /*!
      Returns the user groups the current user is a member of.

      The result is returned as an array of eZUserGroup objects.
    */
    function groups()
    {
        $ret = array();
        $db =& eZDB::globalDatabase();
        
        $db->array_query( $user_group_array, "SELECT * FROM eZUser_UserGroupLink
                                                    WHERE UserID='$this->ID'" );

        foreach ( $user_group_array as $group )
        {
            $ret[] = new eZUserGroup( $group["GroupID"] );
        }

        return $ret;
    }

    /*!
      Removes the user from every user group.
    */
    function removeGroups()
    {
        $db =& eZDB::globalDatabase();
       
        $db->query( "DELETE FROM eZUser_UserGroupLink
                                WHERE UserID='$this->ID'" );
    }

    /*!
      Adds an address to the current User.
    */
    function addAddress( $address )
    {
        $db =& eZDB::globalDatabase();
        if ( get_class( $address ) == "ezaddress" )
        {
            $addressID = $address->id();
            $db->query( "INSERT INTO eZUser_UserAddressLink
                                SET UserID='$this->ID', AddressID='$addressID'" );   
        }
    }

    /*!
      Remove addreses from a user. The function also remove the address from the database.
    */
    function removeAddress( $address )
    {
        $db =& eZDB::globalDatabase();
        if ( get_class( $address ) == "ezaddress" )
        {
            $addressID = $address->id();

            $db->query( "DELETE FROM eZUser_UserAddressLink
                                WHERE AddressID='$addressID'" );
            eZAddress::delete( $addressID );
//              $db->query( "DELETE FROM eZContact_Address
//                                  WHERE ID='$addressID'" );
        }
    }
    

    /*!
      Returns the addresses a user has. It is returned as an array of eZAddress objects.      
    */
    function addresses()
    {
       $ret = array();
       
        $db =& eZDB::globalDatabase();

       $db->array_query( $address_array, "SELECT AddressID FROM eZUser_UserAddressLink
                                WHERE UserID='$this->ID'" );

       foreach ( $address_array as $address )
       {
           $ret[] = new eZAddress( $address["AddressID"] );
       }

       return $ret;
    }

    /*!
      Returns the timeout for the user. If the user is not a member of any user groups the timeout
      is set to 30. If the user is a member of several user groups, the fastest timeout is returned.
    */
    function timeoutValue()
    {
        if ( is_numeric( $this->StoredTimeout ) )
            return $this->StoredTimeout;

        $ret = 30;

        $db =& eZDB::globalDatabase();
        $db->array_query( $timeout_array, "SELECT eZUser_Group.SessionTimeout
                                                      FROM eZUser_User, eZUser_UserGroupLink, eZUser_Group
                                                      WHERE eZUser_User.ID=eZUser_UserGroupLink.UserID
                                                      AND eZUser_Group.ID=eZUser_UserGroupLink.GroupID
                                                      AND eZUser_User.ID='$this->ID'
                                                      ORDER BY eZUser_Group.SessionTimeout ASC
                                                      LIMIT 1" );

       if ( count( $timeout_array ) == 1 )
       {
           $ret = $timeout_array[0]["SessionTimeout"];
           $this->StoredTimeout = $ret;
       }

       return $ret;
    }

    var $ID;
    var $Login;
    var $Password;
    var $Email;
    var $FirstName;
    var $LastName;
    var $InfoSubscription;
    var $Signature;
    var $StoredTimeout;
}

?>
