<?
// 
// $Id: ezcompany.php,v 1.65 2001/03/09 11:17:30 jb Exp $
//
// Definition of eZProduct class
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

//!! eZContact
//! eZCompany handles company information.
/*!

  Example code:
  \code
  $company = new eZCompany();
  $company->setName( "Company name" );
  $company->store();

  \endcode

  \sa eZPerson eZAddress
*/

//require "ezphputils.php";

include_once( "ezaddress/classes/ezaddress.php" );
include_once( "ezcontact/classes/ezcompanytype.php" );
include_once( "ezaddress/classes/ezphone.php" );
include_once( "classes/ezimagefile.php" );
include_once( "ezimagecatalogue/classes/ezimage.php" );
// include_once( "ezclassified/classes/ezclassified.php" );

// include_once( "ezaddress/classes/ezonline.php" );

class eZCompany
{
    /*!
      Constructs a new eZCompany object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZCompany( $id="-1", $fetch=true )
    {
        if ( is_numeric( $id ) and $id != -1 )
        {
            $this->ID = $id;
            if ( $fetch == true )
            {
                $this->get( $this->ID );
            }
        }
    }

    /*!
      Stores a company to the database
    */
    function store( )
    {
        $db =& eZDB::globalDatabase();

        if ( !isset( $this->ID ) or !is_numeric( $this->ID ) )
        {
            $query_type = "INSERT INTO";
        }
        else
        {
            $query_type = "UPDATE";
            $query_cond = "WHERE ID='$this->ID'";
        }
        $type = $this->ContactType == "ezperson" ? 2 : 1;
        $db->query( "$query_type eZContact_Company set Name='$this->Name',
                                 Comment='$this->Comment',
                                 CompanyNo='$this->CompanyNo',
                                 ContactID='$this->ContactID',
                                 ContactType='$type',
                                 CreatorID='$this->CreatorID'
                                 $query_cond" );
        if ( !isset( $this->ID ) or !is_numeric( $this->ID ) )
            $this->ID = mysql_insert_id();

        return true;
    }

    /*!
      Deletes a eZCompany object  from the database.
    */
    function delete( $id = false )
    {
        $db =& eZDB::globalDatabase();

        if ( !$id )
            $id = $this->ID;

        if( isset( $id ) && is_numeric( $id ) )
        {
            // Delete real world addresses

            $db->array_query( $address_array, "SELECT eZContact_CompanyAddressDict.AddressID AS 'DID'
                                               FROM eZAddress_Address, eZContact_CompanyAddressDict
                                               WHERE eZAddress_Address.ID=eZContact_CompanyAddressDict.AddressID
                                                     AND eZContact_CompanyAddressDict.CompanyID='$id' " );

            foreach( $address_array as $addressItem )
            {
                $addressDictID = $addressItem["DID"];
                eZAddress::delete( $addressDictID );
            }
            $db->query( "DELETE FROM eZContact_CompanyAddressDict WHERE CompanyID='$id'" );
           
            // Delete phone numbers.

            $db->array_query( $phone_array, "SELECT eZContact_CompanyPhoneDict.PhoneID AS 'DID'
                                     FROM eZAddress_Phone, eZContact_CompanyPhoneDict
                                     WHERE eZAddress_Phone.ID=eZContact_CompanyPhoneDict.PhoneID
                                       AND eZContact_CompanyPhoneDict.CompanyID='$id' " );

            foreach( $phone_array as $phoneItem )
            {
                $phoneDictID = $phoneItem["DID"];
                eZPhone::delete( $phoneDictID );
            }
            $db->query( "DELETE FROM eZContact_CompanyPhoneDict WHERE CompanyID='$id'" );

            // Delete online address.

            $db->array_query( $online_array, "SELECT eZContact_CompanyOnlineDict.OnlineID AS 'DID'
                                     FROM eZAddress_Online, eZContact_CompanyOnlineDict
                                     WHERE eZAddress_Online.ID=eZContact_CompanyOnlineDict.OnlineID
                                       AND eZContact_CompanyOnlineDict.CompanyID='$id' " );

            foreach( $online_array as $onlineItem )
            {
                $onlineDictID = $onlineItem["DID"];
                eZPhone::delete( $onlineDictID );
            }
            $db->query( "DELETE FROM eZContact_CompanyOnlineDict WHERE CompanyID='$id'" );

            $db->query( "DELETE FROM eZContact_CompanyTypeDict WHERE CompanyID='$id'" );
            $db->query( "DELETE FROM eZContact_Company WHERE ID='$id'" );

            $db->query( "DELETE FROM eZContact_CompanyPersonDict WHERE CompanyID='$id'" );
        }
        eZCompany::removePersons( $id );
        return true;
    }

  
    /*!
      Fetches the object information from the database.
    */
    function get( $id=-1 )
    {
        $db =& eZDB::globalDatabase();
        $ret = false;

        if ( $id != "" )
        {
            $db->array_query( $company_array, "SELECT * FROM eZContact_Company WHERE ID='$id'" );
            if ( count( $company_array ) > 1 )
            {
                die( "Error: More than one company with the same id was found. " );
            }
            else if ( count( $company_array ) == 1 )
            {
                $this->ID = $company_array[0]["ID"];
                $this->Name = $company_array[0]["Name"];
                $this->Comment = $company_array[0]["Comment"];
                $this->CreatorID = $company_array[0]["CreatorID" ];        
                $this->CompanyNo = $company_array[0]["CompanyNo"];
                $this->ContactID = $company_array[0]["ContactID"];
                $type = $company_array[0]["ContactType"];
                $this->ContactType = $type == 2 ? "ezperson" : "ezuser";
                $ret = true;
            }
        }
        return $ret;
    }

    /*!
      Returns all companies found in the database.
      
      The companies are returned as an array of eZCompany objects.
    */
    function &getAll( )
    {
        $db =& eZDB::globalDatabase();

        $company_array = array();
        $return_array = array();

        $db->array_query( $company_array, "SELECT ID FROM eZContact_Company ORDER BY Name" );

        foreach( $company_array as $companyItem )
        {
            $return_array[] =& new eZCompany( $companyItem["ID"] );
        }
        return $return_array;
    }

    /*!
      Returns all the companies found in the database in a specific category.
      
      The companies are returned as an array of eZCompany objects.
    */
    function &getByCategory( $categoryID )
    {
        $db =& eZDB::globalDatabase();

        $company_array = array();
        $return_array = array();

        $db->array_query( $company_array, "SELECT CompanyID FROM eZContact_CompanyTypeDict, eZContact_Company
                                           WHERE eZContact_CompanyTypeDict.CompanyTypeID='$categoryID'
                                           AND eZContact_Company.ID = eZContact_CompanyTypeDict.CompanyID
                                           ORDER BY eZContact_Company.Name" );

        foreach( $company_array as $companyItem )
        {
            $return_array[] =& new eZCompany( $companyItem["CompanyID"] );
        }

        return $return_array;
    }

    /*!
      Search the company database in a single category, using query as the search string in company name.
    */
    function &searchByCategory( $categoryID, $query )
    {
        $db =& eZDB::globalDatabase();
        
        $company_array = array();
        $return_array = array();
        if( !empty( $query ) )
        {
            $db->array_query( $company_array, "
                SELECT 
                    Comp.ID 
                FROM
                    eZContact_CompanyTypeDict as Dict,
                    eZContact_Company as Comp
                WHERE
                    Comp.Name LIKE '%$query%'
                AND
                    Dict.CompanyTypeID = '$categoryID'
                AND
                    Comp.ID = Dict.CompanyID
                ORDER BY Name" );

            foreach( $company_array as $companyItem )
            {
                $return_array[] =& new eZCompany( $companyItem["ID"] );
            }
        }
        
        return $return_array;
    }


    /*!
      Henter ut alle firma i databasen som inneholder s�kestrengen.
    */
    function &search( $query )
    {
        $db =& eZDB::globalDatabase();
        $company_array = array();
        $return_array = array();
    
        $db->array_query( $company_array, "SELECT ID FROM eZContact_Company WHERE Name LIKE '%$query%' ORDER BY Name" );

        foreach( $company_array as $companyItem )
        {
            $return_array[] =& new eZCompany( $companyItem["ID"] );
        }
        return $return_array;
    }


    /*!
      Removes the company from every user category.
    */
    function removeCategories()
    {
        $db =& eZDB::globalDatabase();
       
        $db->query( "DELETE FROM eZContact_CompanyTypeDict
                                WHERE CompanyID='$this->ID'" );
    }

    /*!
      Returns the categories that belong to this eZCompany object.
    */
    function &categories( $companyID = false, $as_object = true, $limit = -1 )
    {
        if ( !$companyID )
            $companyID = $this->ID;

        $return_array = array();
        $db =& eZDB::globalDatabase();

        if ( $limit != -1 )
        {
            $limit_qry = "LIMIT $limit";
        }

        $db->array_query( $categories_array, "SELECT CompanyTypeID
                                              FROM eZContact_CompanyTypeDict
                                              WHERE CompanyID='$companyID'
                                              $limit_qry" );

        foreach( $categories_array as $categoriesItem )
        {
            if ( $as_object )
                $return_array[] =& new eZCompanyType( $categoriesItem["CompanyTypeID"] );
            else
                $return_array[] =& $categoriesItem["CompanyTypeID"];
        }
        return $return_array;
    }
   

    /*!
      Returns the address that belong to this eZCompany object.
    */
    function &addresses( $companyID = false )
    {
        if ( !$companyID )
            $companyID = $this->ID;
        
        $return_array = array();
        $db =& eZDB::globalDatabase();

        $db->array_query( $address_array, "SELECT CAD.AddressID
                                           FROM eZContact_CompanyAddressDict AS CAD, eZAddress_Address AS A,
                                           eZAddress_AddressType as AT
                                           WHERE CAD.AddressID = A.ID AND A.AddressTypeID = AT.ID
                                                 AND CAD.CompanyID='$companyID' AND AT.Removed=0" );

        foreach( $address_array as $addressItem )
        {
            $return_array[] =& new eZAddress( $addressItem["AddressID"] );
        }

        return $return_array;
    }

    /*!
      Adds an address to the current Company.
    */
    function addAddress( &$address )
    {
        $ret = false;
        $db =& eZDB::globalDatabase();
        if ( get_class( $address ) == "ezaddress" )
        {
            $addressID = $address->id();

            $db->query( "INSERT INTO eZContact_CompanyAddressDict
                                SET CompanyID='$this->ID', AddressID='$addressID'" );

            $ret = true;
        }
        return $ret;
    }

    /*!
      Delete all address and the relation to the eZContact_Company
    */
    function removeAddresses()
    {
        $db =& eZDB::globalDatabase();

        $db->array_query( $address_array, "SELECT AddressID FROM eZContact_CompanyAddressDict
                                           WHERE CompanyID='$this->ID' " );

        foreach( $address_array as $addressItem )
        {
            $addressID =& $addressItem["AddressID"];
            eZAddress::delete( $addressID );
        }
        $db->query( "DELETE FROM eZContact_CompanyAddressDict WHERE CompanyID='$this->ID'" );
    }

    /*!
      Returns the phones that belong to this eZCompany object.
    */
    function &phones( $companyID = false )
    {
        if ( !$companyID )
            $companyID = $this->ID;

        $return_array = array();
        $db =& eZDB::globalDatabase();

        $db->array_query( $phone_array, "SELECT CPD.PhoneID
                                         FROM eZContact_CompanyPhoneDict AS CPD, eZAddress_Phone AS P,
                                              eZAddress_PhoneType AS PT
                                         WHERE CPD.PhoneID = P.ID AND P.PhoneTypeID = PT.ID
                                               AND CPD.CompanyID='$companyID' AND PT.Removed=0" );

        foreach( $phone_array as $phoneItem )
        {
            $return_array[] =& new eZPhone( $phoneItem["PhoneID"] );
        }

        return $return_array;
    }

    /*!
      Adds an phone to the current Company.
    */
    function addPhone( &$phone )
    {
        $ret = false;
        $db =& eZDB::globalDatabase();
        if ( get_class( $phone ) == "ezphone" )
        {
            $phoneID =& $phone->id();

            $db->query( "INSERT INTO eZContact_CompanyPhoneDict
                                SET CompanyID='$this->ID', PhoneID='$phoneID'" );

            $ret = true;
        }
        return $ret;
    }

    /*!
      Delete all phones and the relation to the eZContact_Company
    */
    function removePhones()
    {
        $db =& eZDB::globalDatabase();
        $db->array_query( $phone_array, "SELECT PhoneID FROM
                                         eZContact_CompanyPhoneDict WHERE CompanyID='$this->ID' " );

        foreach( $phone_array as $phoneItem )
        {
            $phoneID =& $phoneItem["PhoneID"];
            eZPhone::delete( $phoneID );
        }
        $db->query( "DELETE FROM eZContact_CompanyPhoneDict WHERE CompanyID='$this->ID'" );
    }

    /*!
      Returns the onlines that belong to this eZCompany object.
    */
    function &onlines( $onlineID = false )
    {
        if ( !$onlineID )
            $onlineID = $this->ID;

        $return_array = array();
        $db =& eZDB::globalDatabase();

        $db->array_query( $online_array, "SELECT COD.OnlineID
                                          FROM eZContact_CompanyOnlineDict AS COD, eZAddress_Online AS O,
                                               eZAddress_OnlineType AS OT
                                          WHERE COD.OnlineID = O.ID AND O.OnlineTypeID = OT.ID
                                                AND COD.CompanyID='$this->ID' AND OT.Removed=0" );

        foreach( $online_array as $onlineItem )
        {
            $return_array[] =& new eZOnline( $onlineItem["OnlineID"] );
        }

        return $return_array;
    }

    /*!
      Adds an online to the current Company.
    */
    function addOnline( &$online )
    {
        $ret = false;
        $db =& eZDB::globalDatabase();

        if ( get_class( $online ) == "ezonline" )
        {
            $onlineID =& $online->id();

            $db->query( "INSERT INTO eZContact_CompanyOnlineDict
                                SET CompanyID='$this->ID', OnlineID='$onlineID'" );

            $ret = true;
        }
        return $ret;
    }

    /*!
      Delete all onlines and the relation to the eZContact_Company
    */
    function removeOnlines()
    {
        $db =& eZDB::globalDatabase();
        $db->array_query( $online_array, "SELECT OnlineID FROM eZContact_CompanyOnlineDict
                                          WHERE CompanyID='$this->ID' " );

        foreach( $online_array as $onlineItem )
        {
            $onlineID =& $onlineItem["OnlineID"];
            eZOnline::delete( $onlineID );
        }
        $db->query( "DELETE FROM eZContact_CompanyOnlineDict WHERE CompanyID='$this->ID'" );
    }
    
    /*!
      Adds a image to the current 
     */
    function addImage( &$image )
    {
        $ret = false;
        $db =& eZDB::globalDatabase();

        if ( get_class ( $image ) == "ezimage" )
        {
            $imageID =& $image->id();

            $db->query( "INSERT INTO eZContact_CompanyImageDict
                                     SET CompanyID='$this->ID', ImageID='$imageID'" );
        }
    }

    /*!
      Returns every image to a product as a array of eZImage objects.
    */
    function &images()
    {
        $db =& eZDB::globalDatabase();
       
        $return_array = array();
        $image_array = array();

        $db->array_query( $image_array, "SELECT ImageID FROM eZContact_CompanyImageDict WHERE CompanyID='$this->ID'" );

        for ( $i=0; $i < count($image_array); $i++ )
        {
            $return_array[$i] =& new eZImage( $image_array[$i]["ImageID"], false );
        }
       
        return $return_array;
    }

    /*!
      Delete all images for this company.
    */
    function removeImages()
    {
        $db =& eZDB::globalDatabase();
        
        $db->query( "DELETE FROM eZContact_CompanyImageDefinition WHERE CompanyID='$this->ID'" );
    }

    /*!
      Returns the logo image of the company as a eZImage object.
    */
    function logoImage( $id = false )
    {
        if ( !$id )
            $id = $this->ID;

        $ret = false;
        $db =& eZDB::globalDatabase();

        $db->array_query( $res_array, "SELECT * FROM eZContact_CompanyImageDefinition
                                     WHERE
                                     CompanyID='$id'
                                   " );

        if ( count( $res_array ) == 1 )
        {
            if ( $res_array[0]["LogoImageID"] != "NULL" and $res_array[0]["LogoImageID"] != "0" )
            {
                $ret = new eZImage( $res_array[0]["LogoImageID"], false );
            }
        }
        return $ret;
    }

    /*!
      Sets the logo image for the company.

      The argument must be a eZImage object.
    */
    function setLogoImage( &$image, $id = false )
    {
        if ( !$id )
            $id = $this->ID;

        if ( get_class( $image ) == "ezimage" )
        {
            $db =& eZDB::globalDatabase();

            $imageID =& $image->id();

            $db->array_query( $res_array, "SELECT COUNT(*) AS Number FROM eZContact_CompanyImageDefinition
                                     WHERE
                                     CompanyID='$id'" );

            if ( $res_array[0]["Number"] == "1" )
            {            
                $db->query( "UPDATE eZContact_CompanyImageDefinition
                                     SET
                                     LogoImageID='$imageID'
                                     WHERE
                                     CompanyID='$id'" );
            }
            else
            {
                $db->query( "INSERT INTO eZContact_CompanyImageDefinition
                                     SET
                                     CompanyID='$id',
                                     LogoImageID='$imageID'" );
            }
        }
    }

    /*!
      Deletes the image for the company.
    */
    function deleteImage( $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();
        $db->query( "UPDATE eZContact_CompanyImageDefinition
                     SET CompanyImageID='0' WHERE CompanyID='$id'" );
    }

    /*!
      Deletes the logo for the company.
    */
    function deleteLogo( $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();
        $db->query( "UPDATE eZContact_CompanyImageDefinition
                     SET LogoImageID='0' WHERE CompanyID='$id'" );
    }


    /*!
      Sets the company image for the company.

      The argument must be a eZImage object.
    */
    function setCompanyImage( &$image, $id = false )
    {
        if ( !$id )
            $id = $this->ID;

        if ( get_class( $image ) == "ezimage" )
        {
            $db =& eZDB::globalDatabase();
            $imageID =& $image->id();

            $db->array_query( $res_array, "SELECT COUNT(*) AS Number FROM eZContact_CompanyImageDefinition
                                     WHERE
                                     CompanyID='$id'" );

            if ( $res_array[0]["Number"] == "1" )
            {            
                $db->query( "UPDATE eZContact_CompanyImageDefinition
                                     SET
                                     CompanyImageID='$imageID'
                                     WHERE
                                     CompanyID='$id'" );
            }
            else
            {
                $db->query( "INSERT INTO eZContact_CompanyImageDefinition
                                     SET
                                     CompanyID='$id',
                                     CompanyImageID='$imageID'" );
            }
        }
    }


    /*!
      Returns the image of the company as a eZImage object.
    */
    function companyImage( $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $ret = false;
        $db =& eZDB::globalDatabase();

        $db->array_query( $res_array, "SELECT * FROM eZContact_CompanyImageDefinition
                                       WHERE CompanyID='$id'" );

        if ( count( $res_array ) == 1 )
        {
            if ( $res_array[0]["CompanyImageID"] != "NULL" and $res_array[0]["CompanyImageID"] != "0" )
            {
                $ret = new eZImage( $res_array[0]["CompanyImageID"], false );
            }
        }

        return $ret;
    }


    /*!
      Sets the name of the company.
    */
    function setName( $value )
    {
        $this->Name = $value;
    }

    /*!
      Sets the comment of the company.
    */
    function setComment( $value )
    {
        $this->Comment = $value;
    }

    /*!
      Sets the creatorID of the company.
    */
    function setCreatorID( &$user )
    {
        if ( get_class( $user ) == "ezuser" )
        {
            $userID =& $user->id();
            $this->CreatorID = $userID;
        }
    }

    /*!
      Sets the contact type of the company.
    */
    function setCompanyNo( $value )
    {
        $this->CompanyNo = $value;
    }

    /*!
      Returnerer ID.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returnerer firmanavn.
    */
    function name()
    {
        return $this->Name;
    }

    /*!
      Returnerer ID til eier av firma ( brukeren som opprettet det ).
    */
    function creatorID()
    {
        return $this->CreatorID;
    }
    
    /*!
      Returnerer kommentar.
    */
    function comment()
    {
        return $this->Comment;
    }  
    
    /*!
      Returns Company no.
    */
    function companyNo()
    {
        return $this->CompanyNo;
    }


    /*!
        Set the contact for this object to $value.
     */
    function setContact( $value )
    {
        $this->ContactID = $value;
        $this->ContactType = "ezuser";
    }

    /*!
      Returns the contact for this company.
    */
    function contact( )
    {
        return $this->ContactID;
        $this->ContactType = "ezuser";
    }

    /*!
        Set the person contact for this object to $value.
     */
    function setPersonContact( $value )
    {
        $this->ContactID = $value;
        $this->ContactType = "ezperson";
    }

    /*!
      Returns the contact for this company.
    */
    function contactType()
    {
        return $this->ContactType;
    }

    /*
      Henter ut alle firma i databasen hvor en eller flere tilh�rende personer    
      inneholder s�kestrengen.
    */
    function &searchByPerson( $query )
    {
        $db =& eZDB::globalDatabase();
        $company_array = array();
        $return_array = array();
    
        $db->array_query( $company_array,
                          "SELECT eZContact_Company.ID as ID
                           FROM eZContact_Company, eZContact_Person
                           WHERE ((eZContact_Person.FirstName LIKE '%$query%' OR eZContact_Person.LastName LIKE '%$query%')
                           AND eZContact_Company.ID=eZContact_Person.Company) GROUP BY eZContact_Company.ID ORDER BY eZContact_Company.ID" );

        foreach( $company_array as $companyItem )
        {
            $return_array[] =& new eZCompany( $companyItem["ID"] );
        }
        return $return_array;
    }    

    /*!
      Returns the project state of this company.
    */
    function &projectState()
    {
        $ret = "";

        $db =& eZDB::globalDatabase();

        $checkQuery = "SELECT ProjectID FROM eZContact_CompanyProjectDict WHERE CompanyID='$this->ID'";
        $db->array_query( $array, $checkQuery, 0, 1 );

        if( count( $array ) == 1 )
        {
            $ret =& $array[0]["ProjectID"];
        }

        return $ret;
    }

    /*!
      Returns the project state of this company.
    */
    function setProjectState( $value )
    {
        $db =& eZDB::globalDatabase();
        $db->query( "DELETE FROM eZContact_CompanyProjectDict WHERE CompanyID='$this->ID'" );

        if ( is_numeric( $value )  )
        {
            if ( $value > 0 )
            {
                $checkQuery = "INSERT INTO eZContact_CompanyProjectDict
                               SET CompanyID='$this->ID', ProjectID='$value'";
                $db->query( $checkQuery );
            }
        }
    }

    /*!
      Makes the person a part of the company.
    */
    function removePersons( $companyid = false )
    {
        $db =& eZDB::globalDatabase();
        if ( !$companyid )
            $companyid = $this->ID;

        $db->query( "DELETE FROM eZContact_CompanyPersonDict
                     WHERE CompanyID='$companyid'" );
    }

    /*!
      Makes the person a part of the company.
    */
    function addPerson( $personid, $companyid = false )
    {
        $db =& eZDB::globalDatabase();
        if ( get_class( $personid ) == "ezperson" )
            $personid = $personid->id();
        if ( !$companyid )
            $companyid = $this->ID;

        $db->query( "DELETE FROM eZContact_CompanyPersonDict
                     WHERE CompanyID='$companyid' AND PersonID='$personid'" );
        $db->query( "INSERT INTO eZContact_CompanyPersonDict
                     SET PersonID='$personid', CompanyID='$companyid'" );
    }

    /*!
      Returns the number of persons related to this company
    */
    function personCount( $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();
        $db->query_single( $arr, "SELECT count( PersonID ) AS Count
                                  FROM eZContact_CompanyPersonDict
                                  WHERE CompanyID='$id'" );
        return $arr["Count"];
    }

    /*!
      Returns an array of persons related to this company
    */
    function persons( $id = false, $as_object = true, $limit = -1, $offset = 0 )
    {
        if ( !$id )
            $id = $this->ID;
        if ( $limit >= 0 )
        {
            $limit_text = "LIMIT $offset, $limit";
        }
        $db =& eZDB::globalDatabase();
        $db->array_query( $arr, "SELECT CPD.PersonID
                                 FROM eZContact_CompanyPersonDict AS CPD, eZContact_Person AS P
                                 WHERE CPD.CompanyID='$id' AND CPD.PersonID=P.ID
                                 ORDER BY P.LastName, P.FirstName $limit_text" );
        $ret = array();
        if ( $as_object )
        {
            foreach( $arr as $row )
            {
                $ret[] = new eZPerson( $row["PersonID"] );
            }
        }
        else
        {
            foreach( $arr as $row )
            {
                $ret[] = $row["PersonID"];
            }
        }
        return $ret;
    }

    var $ID;
    var $CreatorID;
    var $Name;
    var $Comment;
    var $Online;
    var $ContactID;
    var $PersonContactID;
    var $CompanyNo;
}

?>
