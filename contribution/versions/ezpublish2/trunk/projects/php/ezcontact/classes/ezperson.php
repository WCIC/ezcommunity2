<?
// 
// $Id: ezperson.php,v 1.41 2001/01/25 17:11:34 jb Exp $
//
// Definition of eZPerson class
//
// <real-name><<email-name>>
// Created on: <09-Nov-2000 14:52:40 ce>
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

//!! eZPerson
//! eZPerson handles a persons belonging in contacts information.

//!! eZPerson
//!
/*!
  Denne klassen h�ndterer personer i eZ contact. Disse lagres og hentes ut fra databasen.
*/

include_once( "ezuser/classes/ezuser.php" );
include_once( "classes/ezdb.php" );
include_once( "classes/ezquery.php" );
include_once( "ezaddress/classes/ezaddress.php" );
include_once( "ezaddress/classes/ezphone.php" );
include_once( "ezaddress/classes/ezonline.php" );

class eZPerson
{
    /*!
      Constructs a new eZPerson object.
      
      If $id is set, the object's values are fetched from the
      database.
    */
    function eZPerson( $id="", $fetch=true  )
    {
        if( !empty( $id ) )
        {
            $this->ID = $id;
            if( $fetch == true )
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
      Stores a person to the database. 
    */  
    function store()
    {
        $db = eZDB::globalDatabase();
        if( !isSet( $this->ID ) )
        {
        
            $db->query( "INSERT INTO eZContact_Person set
                                                    FirstName='$this->FirstName',
                                                    LastName='$this->LastName',
	                                                Comment='$this->Comment',
	                                                BirthDate='$this->BirthDate',
                                                    ContactTypeID='$this->ContactType'" );
            $this->ID = mysql_insert_id();            
            $this->State_ = "Coherent";
        }
        else
        {
            $db->query( "UPDATE eZContact_Person set
                                                    FirstName='$this->FirstName',
                                                    LastName='$this->LastName',
	                                                Comment='$this->Comment',
	                                                BirthDate='$this->BirthDate',
                                                    ContactTypeID='$this->ContactType'
                                                    WHERE ID='$this->ID'" );
            $this->State_ = "Coherent";
        }
    }


    /*!
      Deletes an eZPerson from the database.
    */
    function delete( $id = false )
    {
        $db = eZDB::globalDatabase();

        if ( !$id )
            $id = $this->ID;

        if( isset( $id ) && is_numeric( $id ) )
        {
            // Delete project state
            $this->setProjectState( false );

            // Delete real world addresses

            $db->array_query( $address_array, "SELECT eZContact_PersonAddressDict.AddressID AS 'DID'
                                               FROM eZContact_Address, eZContact_PersonAddressDict
                                               WHERE eZContact_Address.ID=eZContact_PersonAddressDict.AddressID
                                                     AND eZContact_PersonAddressDict.PersonID='$id' " );

            foreach( $address_array as $addressItem )
            {
                $addressDictID = $addressItem["DID"];
                $db->query( "DELETE FROM eZContact_Address WHERE ID='$addressDictID'" );
            }
            $db->query( "DELETE FROM eZContact_PersonAddressDict WHERE PersonID='$id'" );
           
            // Delete phone numbers.

            $db->array_query( $phone_array, "SELECT eZContact_PersonPhoneDict.PhoneID AS 'DID'
                                     FROM eZContact_Phone, eZContact_PersonPhoneDict
                                     WHERE eZContact_Phone.ID=eZContact_PersonPhoneDict.PhoneID
                                       AND eZContact_PersonPhoneDict.PersonID='$id' " );

            foreach( $phone_array as $phoneItem )
            {
                $phoneDictID = $phoneItem["DID"];
                $db->query( "DELETE FROM eZContact_Phone WHERE ID='$phoneDictID'" );
            }
            $db->query( "DELETE FROM eZContact_PersonPhoneDict WHERE PersonID='$id'" );

            // Delete online address.

            $db->array_query( $online_array, "SELECT eZContact_PersonOnlineDict.OnlineID AS 'DID'
                                     FROM eZContact_Online, eZContact_PersonOnlineDict
                                     WHERE eZContact_Online.ID=eZContact_PersonOnlineDict.OnlineID
                                       AND eZContact_PersonOnlineDict.PersonID='$id' " );

            foreach( $online_array as $onlineItem )
            {
                $onlineDictID = $onlineItem["DID"];
                $db->query( "DELETE FROM eZContact_Online WHERE ID='$onlineDictID'" );
            }
            $db->query( "DELETE FROM eZContact_PersonOnlineDict WHERE PersonID='$id'" );

            $db->query( "DELETE FROM eZContact_Person WHERE ID='$id'" );
        }
        return true;
    }


    /*
      Henter ut person med ID == $id
    */
    function get( $id )
    {
        $db = eZDB::globalDatabase();
        if( $id != "" )
        {
            $db->array_query( $person_array, "SELECT * FROM eZContact_Person WHERE ID='$id'" );
            if( count( $person_array ) > 1 )
            {
                die( "Feil: Flere personer med samme ID funnet i database, dette skal ikke v�re mulig. " );
            }
            else if( count( $person_array ) == 1 )
            {
                $this->ID = $person_array[ 0 ][ "ID" ];
                $this->FirstName = $person_array[ 0 ][ "FirstName" ];
                $this->LastName = $person_array[ 0 ][ "LastName" ];
                $this->ContactType = $person_array[ 0 ][ "ContactTypeID" ];
                $this->BirthDate = $person_array[ 0 ][ "BirthDate" ];
                $this->Comment = $person_array[ 0 ][ "Comment" ];
            }
        }
    }

    /*
        Fetches the person with the USER ID == $id
     */
    function getByUserID( $id )
    {
        $db = eZDB::globalDatabase();
        
        $query = "SELECT PersonID FROM eZContact_UserPersonDict WHERE UserID='$id'";

        $return_item = 0;

        $db->array_query( $person_array, $query );
        foreach( $person_array as $personItem )
        {
            $return_item = new eZPerson( $personItem["PersonID"], false );
        }
        
        return $return_item;
    }
    
    function getAllCount( $search_types = "" )
    {
        $db = eZDB::globalDatabase();

        if ( empty( $search_types ) )
        {
            $qry = "SELECT count( ID ) AS Count FROM eZContact_Person ORDER BY LastName, FirstName";
            $db->query_single( $persons, $qry );
            return $persons["Count"];
        }
        else
        {
            $query = new eZQuery( array( "A.FirstName", "A.LastName",
                                         "C.Number",
                                         "E.Street1", "E.Street2", "E.Place", "E.Zip",
                                         "G.Url" ), $search_types );
            $qry = "SELECT A.ID FROM
                    eZContact_Person AS A,
                    eZContact_PersonPhoneDict as B, eZContact_Phone AS C,
                    eZContact_PersonAddressDict as D, eZContact_Address AS E,
                    eZContact_PersonOnlineDict as F, eZContact_Online AS G
                    WHERE A.ID = B.PersonID AND B.PhoneID = C.ID AND
                          A.ID = D.PersonID AND D.AddressID = E.ID AND
                          A.ID = F.PersonID AND F.OnlineID = G.ID AND
                    (" .
                    $query->buildQuery() .
                    ")
                    GROUP BY A.ID
                    ORDER BY A.LastName, A.FirstName";
            $db->array_query( $persons, $qry );
            return count( $persons );
        }
    }

    /*!
      Fetches all persons in the database.
    */
    function getAll( $search_types = "", $limit_index = 0, $limit = 1 )
    {
        $db = eZDB::globalDatabase();
        $person_array = 0;

        if ( empty( $search_types ) )
        {
            $qry = "SELECT ID FROM eZContact_Person ORDER BY LastName, FirstName
                    LIMIT $limit_index, $limit";
            $db->array_query( $person_array, $qry );
        }
        else
        {
            $query = new eZQuery( array( "A.FirstName", "A.LastName",
                                         "C.Number",
                                         "E.Street1", "E.Street2", "E.Place", "E.Zip",
                                         "G.Url" ), $search_types );
            $qry = "SELECT A.ID as ID FROM
                    eZContact_Person AS A,
                    eZContact_PersonPhoneDict as B, eZContact_Phone AS C,
                    eZContact_PersonAddressDict as D, eZContact_Address AS E,
                    eZContact_PersonOnlineDict as F, eZContact_Online AS G
                    WHERE A.ID = B.PersonID AND B.PhoneID = C.ID AND
                          A.ID = D.PersonID AND D.AddressID = E.ID AND
                          A.ID = F.PersonID AND F.OnlineID = G.ID AND
                    (" .
                    $query->buildQuery() .
                    ")
                    GROUP BY A.ID
                    ORDER BY A.LastName, A.FirstName
                    LIMIT $limit_index, $limit";
            $db->array_query( $person_array, $qry );
        }

        foreach( $person_array as $personItem )
        {
            $return_array[] = new eZPerson( $personItem["ID"] );
        }
        return $return_array;
    }

    /*!
      Fetches all persons whith first name or last name equal to the query string.
    */
    function search( $query )
    {
        $db = eZDB::globalDatabase();
        $person_array = 0;
    
        $db->array_query( $person_array, "SELECT * FROM eZContact_Person
                                          WHERE FirstName LIKE '%$query%' OR
                                                LastName LIKE '%$query%' ORDER BY LastName" );
    
        foreach( $person_array as $personItem )
        {
            $return_array[] = new eZPerson( $personItem["ID"] );
        }
        return $return_array;
    }
    
    /*!
      Returns the address that belong to this eZPerson object.
    */
    function addresses()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $return_array = array();
        $db = eZDB::globalDatabase();

        $PersonID = $this->ID;


        $db->array_query( $address_array, "SELECT PAD.AddressID
                                           FROM eZContact_PersonAddressDict AS PAD, eZAddress_Address AS A,
                                                eZAddress_AddressType AS AT
                                           WHERE PAD.AddressID = A.ID AND A.AddressTypeID = AT.ID
                                                 AND PAD.PersonID='$PersonID' AND AT.Removed=0" );

        foreach( $address_array as $addressItem )
        {
            $return_array[] = new eZAddress( $addressItem["AddressID"] );
        }

        return $return_array;
    }

    /*!
      Adds an address to the current Person.
    */
    function addAddress( $address )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;
       
        $db = eZDB::globalDatabase();
        if( get_class( $address ) == "ezaddress" )
        {
            $addressID = $address->id();

            $checkQuery = "SELECT PersonID FROM eZContact_PersonAddressDict WHERE AddressID='$addressID'";

            $db->array_query( $address_array, $checkQuery );

            $count = count( $address_array );

            if( $count == 0 )
            {
                $db->query( "INSERT INTO eZContact_PersonAddressDict
                                SET PersonID='$this->ID', AddressID='$addressID'" );
            }
            $ret = true;
        }
        return $ret;
    }

    /*!
      Remove all addresses to the current Person.
    */
    function removeAddresses()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
        $db->array_query( $address_array, "SELECT AddressID FROM eZContact_PersonAddressDict
                                           WHERE PersonID='$this->ID'" );
        foreach( $address_array as $address )
        {
            $id = $address["AddressID"];
            eZAddress::delete( $id );
//              $db->query( "DELETE FROM eZContact_Address WHERE PersonID='$id'" );
        }
        $db->query( "DELETE FROM eZContact_PersonAddressDict WHERE PersonID='$this->ID'" );
    }

    /*!
      Returns the phones that belong to this eZPerson object.
    */
    function phones( $personID = false )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        if ( !is_numeric( $personID ) )
            $personID = $this->ID;

        $return_array = array();
        $db = eZDB::globalDatabase();

        $PersonID = $this->ID;

        $db->array_query( $phone_array, "SELECT PPD.PhoneID
                                         FROM eZContact_PersonPhoneDict AS PPD, eZAddress_Phone AS P,
                                              eZAddress_PhoneType AS PT
                                         WHERE PPD.PhoneID = P.ID AND P.PhoneTypeID = PT.ID
                                               AND PersonID='$PersonID' AND PT.Removed=0" );

        foreach( $phone_array as $phoneItem )
        {
            $return_array[] = new eZPhone( $phoneItem["PhoneID"] );
        }

        return $return_array;
    }

    /*!
      Adds an phone to the current Person.
    */
    function addPhone( $phone )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;
       
        $db = eZDB::globalDatabase();
        if( get_class( $phone ) == "ezphone" )
        {
            $phoneID = $phone->id();

            $checkQuery = "SELECT PersonID FROM eZContact_PersonPhoneDict WHERE PhoneID='$phoneID'";

            $db->array_query( $phone_array, $checkQuery );

            $count = count( $phone_array );
            if( $count == 0 )
            {
                $db->query( "INSERT INTO eZContact_PersonPhoneDict
                                SET PersonID='$this->ID', PhoneID='$phoneID'" );
            }

            $ret = true;
        }
        return $ret;
    }

    /*!
      Remove all phones to the current Person.
    */
    function removePhones()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
        $db->array_query( $phone_array, "SELECT PhoneID FROM
                                         eZContact_PersonPhoneDict WHERE PersonID='$this->ID'" );
        foreach( $phone_array as $phone )
        {
            $id = $phone["PhoneID"];
            eZPhone::delete( $id );
//              $db->query( "DELETE FROM eZContact_Phone WHERE PersonID='$id'" );
        }
        $db->query( "DELETE FROM eZContact_PersonPhoneDict WHERE PersonID='$this->ID'" );
    }

    /*!
      Returns the onlines that belong to this eZPerson object.
    */
    function onlines()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $return_array = array();
        $db = eZDB::globalDatabase();

        $PersonID = $this->ID;

        $db->array_query( $online_array, "SELECT POD.OnlineID
                                          FROM eZContact_PersonOnlineDict AS POD, eZAddress_Online AS O,
                                               eZAddress_OnlineType AS OT
                                          WHERE POD.OnlineID = O.ID AND O.OnlineTypeID = OT.ID
                                                AND PersonID='$PersonID' AND OT.Removed=0" );

        foreach( $online_array as $onlineItem )
        {
            $return_array[] = new eZOnline( $onlineItem["OnlineID"] );
        }

        return $return_array;
    }

    /*!
      Remove all onlines to the current Person.
    */
    function removeOnlines()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
        $db->array_query( $online_array, "SELECT OnlineID FROM eZContact_PersonOnlineDict
                                          WHERE PersonID='$this->ID'" );
        foreach( $online_array as $online )
        {
            $id = $online["OnlineID"];
            eZOnline::delete( $id );
//              $db->query( "DELETE FROM eZContact_Online WHERE PersonID='$id'" );
        }
        $db->query( "DELETE FROM eZContact_PersonOnlineDict WHERE PersonID='$this->ID'" );
    }

    /*!
      Returns the email address of the person, returns false if none exists.
    */
    function emailAddress()
    {
        $onlines = $this->onlines();
        if ( count( $onlines ) >= 1 )
        {
            $found_mail = false;
            foreach ( $onlines as $online )
                {
                    if ( $online->urlType() == "mailto" )
                    {
                        return $online->url();
                    }
                }
        }
        return false;
    }

    /*!
      Returns the work phone of the person, returns false if none exists.
    */
    function workPhone()
    {
        $phones = $this->phones( 0 );
        if ( count( $phones ) >= 1 )
        {
            foreach ( $phones as $phone )
                {
                    if ( $phone->phoneTypeID() == 4 ) // 4 = Work phone
                    {
                        return $phone->number();
                    }
                }
        }
        return false;
    }

    /*!
      Returns the fax phone of the person, returns false if none exists.
    */
    function faxPhone()
    {
        $phones = $this->phones( 0 );
        if ( count( $phones ) >= 1 )
        {
            foreach ( $phones as $phone )
                {
                    if ( $phone->phoneTypeID() == 8 ) // 4 = Fax
                    {
                        return $phone->number();
                    }
                }
        }
        return false;
    }

    /*!
      Returns the title of the user related to a given company ($companyID)
    */
    function hasTitle( $companyID )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
        $checkQuery = "SELECT Title FROM eZContact_CompanyPersonDict
                                    WHERE CompanyID='$companyID' and PersonID='$this->ID'";

        $title_array = array();

        $db->array_query( $title_array, $checkQuery );

        return count( $title_array ) > 0;
    }

    /*!
      Returns the title of the user related to a given company ($companyID)
    */
    function title( $companyID )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;

        $db = eZDB::globalDatabase();
        $checkQuery = "SELECT Title FROM eZContact_CompanyPersonDict
                                    WHERE CompanyID='$companyID' and PersonID='$this->ID'";

        $title_array = array();

        $db->array_query( $title_array, $checkQuery );

        $title = false;

        if ( count( $title_array ) == 1 )
        {
            $title = $title_array[0]["Title"];
        }
        else
        {
            die( "eZPerson::title(): Found " . count( $title_array ) . " titles, expected 1" );
        }
        return $title;
    }

    /*!
      Adds an online to the current Person.
    */
    function addOnline( $online )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;
       
        $db = eZDB::globalDatabase();

        if( get_class( $online ) == "ezonline" )
        {
            $onlineID = $online->id();

            $checkQuery = "SELECT PersonID FROM eZContact_PersonOnlineDict WHERE OnlineID='$onlineID'";
            
            $db->array_query( $online_array, $checkQuery );

            $count = count( $online_array );

            if( $count == 0 )
            {
                $db->query( "INSERT INTO eZContact_PersonOnlineDict
                                SET PersonID='$this->ID', OnlineID='$onlineID'" );
            }

            $ret = true;
        }
        return $ret;
    }

    /*!
      Returns the user that belong to this eZPerson object.
    */
    function user()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $return_array = array();
        $db = eZDB::globalDatabase();

        $db->array_query( $user_array, "SELECT UserID
                                                 FROM eZContact_UserPersonDict
                                                 WHERE PersonID='$this->ID'" );

        foreach( $user_array as $userItem )
        {
            $return_array[] = new eZUser( $userItem["UserID"] );
        }

        return $return_array;
    }

    /*!
      Adds a user to the current Person.
    */
    function addUser( $user )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;
        
        $db = eZDB::globalDatabase();

        if( get_class( $user ) == "ezuser" )
        {
            $userID = $user->id();
            
            $checkQuery = "SELECT PersonID FROM eZContact_UserPersonDict WHERE UserID=$userID";
            $db->array_query( $user_array, $checkQuery );
            
            $count = count( $user_array );
            
            if( $count == 0 )
            {
                $db->query( "INSERT INTO eZContact_UserPersonDict
                                SET PersonID='$this->ID', UserID='$userID'" );
            }
            $ret = true;
        }
        return $ret;
    }

    /*!
        Set the first name of this object to $value.
     */
    function setFirstName( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->FirstName = $value;
    }

    /*!
        Set the last name of this object to $value.
     */
    function setLastName( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->LastName = $value;
    }

    /*!
        Set the comment of this object to $value.
     */
    function setComment( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Comment = $value;
    }

    /*!
        Set the contact for this object to $value.
     */
    function setContact( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->ContactType = $value;
    }

    /*!
        Set the birth day of this object to $value.
    */
    function setBirthDay( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->BirthDate = $value;
    }
  
  
    /*!
      Returns the ID of the person.
    */
    function id()
    {
        return $this->ID;
    }
  
    /*!
      Returns the first name and the last name of the person.
    */
    function name()
    {
        return $this->firstName() . " " . $this->lastName();
    }

    /*!
      Returns the first name of the person.
    */
    function firstName()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->FirstName;
    }

    /*!
      Returns the last name of the person.
    */
    function lastName()
    {    
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->LastName;
    }

    /*!
      Returns the first name and last name of the person concated together.
    */
    function fullName()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->FirstName . " " . $this->LastName;
    }

    /*!
      Returns the comment for this person.
    */
    function comment( )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Comment;
    }

    /*!
      Returns the contact for this person.
    */
    function contact( )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->ContactType;
    }

    /*!
      Returns the project state of this person.
    */
    function projectState()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = "";

        $db = eZDB::globalDatabase();

        $checkQuery = "SELECT ProjectID FROM eZContact_PersonProjectDict WHERE PersonID='$this->ID'";
        $db->array_query( $array, $checkQuery, 0, 1 );

        if( count( $array ) == 1 )
        {
            $ret = $array[0]["ProjectID"];
        }

        return $ret;
    }

    /*!
      Returns the project state of this person.
    */
    function setProjectState( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
        $db->query( "DELETE FROM eZContact_PersonProjectDict WHERE PersonID='$this->ID'" );

        if ( is_numeric( $value )  )
        {
            if ( $value > 0 )
            {
                $checkQuery = "INSERT INTO eZContact_PersonProjectDict
                               SET PersonID='$this->ID', ProjectID='$value'";
                $db->query( $checkQuery );
            }
        }
    }

    /*!
      Returns the birthday of this person.
    */
    function birthDate( )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->BirthDate;
    }

    var $ID;
    var $FirstName;
    var $LastName;
    var $BirthDate;  
    var $ContactType;
    var $Comment;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
};

?>
