<?
// 
// $Id: ezcompany.php,v 1.53 2001/01/20 23:18:55 jb Exp $
//
// Definition of eZProduct class
//
// <real-name><<email-name>>
// Created on: <09-Nov-2000 14:52:40 ce>
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

//!! eZCompany
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

include_once( "ezcontact/classes/ezaddress.php" );
include_once( "ezcontact/classes/ezcompanytype.php" );
include_once( "ezcontact/classes/ezphone.php" );
include_once( "classes/ezimagefile.php" );
include_once( "ezimagecatalogue/classes/ezimage.php" );
// include_once( "ezclassified/classes/ezclassified.php" );

// include_once( "ezcontact/classes/ezonline.php" );

class eZCompany
{
    /*!
      Constructs a new eZCompany object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZCompany( $id="-1", $fetch=true )
    {
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
      Stores a company to the database
    */
    function store( )
    {
        $db = eZDB::globalDatabase();

        if ( !isSet( $this->ID ) )
        {
        
            $db->query( "INSERT INTO eZContact_Company set Name='$this->Name',
	                                              Comment='$this->Comment',
                                                  CompanyNo='$this->CompanyNo',
                                                  ContactType='$this->ContactType',
	                                              CreatorID='$this->CreatorID'" );
            $this->ID = mysql_insert_id();
            
            $this->State_ = "Coherent";
        }
        else
        {
            $db->query( "UPDATE eZContact_Company set Name='$this->Name',
                                            	 Comment='$this->Comment',
                                                 CompanyNo='$this->CompanyNo',
                                                 ContactType='$this->ContactType',
                                               	 CreatorID='$this->CreatorID' WHERE ID='$this->ID'" );
            $this->State_ = "Coherent";
        }

        return true;
    }

    /*
      Deletes a eZCompany object  from the database.
    */
    function delete( $id = false )
    {
        $db = eZDB::globalDatabase();

        if ( !$id )
            $id = $this->ID;

        if( isset( $id ) && is_numeric( $id ) )
        {
            // Delete real world addresses

            $db->array_query( $address_array, "SELECT eZContact_CompanyAddressDict.AddressID AS 'DID'
                                               FROM eZContact_Address, eZContact_CompanyAddressDict
                                               WHERE eZContact_Address.ID=eZContact_CompanyAddressDict.AddressID
                                                     AND eZContact_CompanyAddressDict.CompanyID='$id' " );

            foreach( $address_array as $addressItem )
            {
                $addressDictID = $addressItem["DID"];
                $db->query( "DELETE FROM eZContact_Address WHERE ID='$addressDictID'" );
            }
            $db->query( "DELETE FROM eZContact_CompanyAddressDict WHERE CompanyID='$id'" );
           
            // Delete phone numbers.

            $db->array_query( $phone_array, "SELECT eZContact_CompanyPhoneDict.PhoneID AS 'DID'
                                     FROM eZContact_Phone, eZContact_CompanyPhoneDict
                                     WHERE eZContact_Phone.ID=eZContact_CompanyPhoneDict.PhoneID
                                       AND eZContact_CompanyPhoneDict.CompanyID='$id' " );

            foreach( $phone_array as $phoneItem )
            {
                $phoneDictID = $phoneItem["DID"];
                $db->query( "DELETE FROM eZContact_Phone WHERE ID='$phoneDictID'" );
            }
            $db->query( "DELETE FROM eZContact_CompanyPhoneDict WHERE CompanyID='$id'" );

            // Delete online address.

            $db->array_query( $online_array, "SELECT eZContact_CompanyOnlineDict.OnlineID AS 'DID'
                                     FROM eZContact_Online, eZContact_CompanyOnlineDict
                                     WHERE eZContact_Online.ID=eZContact_CompanyOnlineDict.OnlineID
                                       AND eZContact_CompanyOnlineDict.CompanyID='$id' " );

            foreach( $online_array as $onlineItem )
            {
                $onlineDictID = $onlineItem["DID"];
                $db->query( "DELETE FROM eZContact_Online WHERE ID='$onlineDictID'" );
            }
            $db->query( "DELETE FROM eZContact_CompanyOnlineDict WHERE CompanyID='$id'" );

            $db->query( "DELETE FROM eZContact_CompanyTypeDict WHERE CompanyID='$this->ID'" );
            $db->query( "DELETE FROM eZContact_Company WHERE ID='$this->ID'" );
        }
        return true;
    }

  
    /*!
      Fetches the object information from the database.
    */
    function get( $id=-1 )
    {
        $db = eZDB::globalDatabase();
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
                $this->ContactType = $company_array[0]["ContactType"];
                     
                $ret = true;
            }
            $this->State_ = "Coherent";
        }
        else
        {
            $this->State_ = "Dirty";
        }
        return $ret;
    }
    

    /*
      Returns all the company found in the database.
      
      The company are returned as an array of eZCompany objects.
    */
    function getAll( )
    {
        $db = eZDB::globalDatabase();

        $company_array = array();
        $return_array = array();
    
        $db->array_query( $company_array, "SELECT ID FROM eZContact_Company ORDER BY Name" );

        foreach( $company_array as $companyItem )
            {
                $return_array[] = new eZCompany( $companyItem["ID"] );
            }
        return $return_array;
    }

    /*
      Returns all the companies found in the database.
      
      The company are returned as an array of eZCompany objects.
    */
    function getByCategory( $categoryID )
    {
        $db = eZDB::globalDatabase();

        $company_array = array();
        $return_array = array();

        $db->array_query( $company_array, "SELECT CompanyID FROM eZContact_CompanyTypeDict, eZContact_Company
                                                       WHERE eZContact_CompanyTypeDict.CompanyTypeID='$categoryID'
                                                       AND eZContact_Company.ID = eZContact_CompanyTypeDict.CompanyID
                                                       ORDER BY eZContact_Company.Name" );

            foreach( $company_array as $companyItem )
            {
                $return_array[] = new eZCompany( $companyItem["CompanyID"] );
            }

        return $return_array;
    }

    /*
      Search the company database in a single category, using query as the search string in company name.
    */
    function searchByCategory( $categoryID, $query )
    {
        $db = eZDB::globalDatabase();
        
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
                $return_array[] = new eZCompany( $companyItem["ID"] );
            }
        }
        
        return $return_array;
    }


    /*
      Henter ut alle firma i databasen som inneholder søkestrengen.
    */
    function search( $query )
    {
        $db = eZDB::globalDatabase();
        $company_array = array();
        $return_array = array();
    
        $db->array_query( $company_array, "SELECT ID FROM eZContact_Company WHERE Name LIKE '%$query%' ORDER BY Name" );

        foreach( $company_array as $companyItem )
        {
            $return_array[] = new eZCompany( $companyItem["ID"] );
        }
        return $return_array;
    }


    /*!
      Removes the company from every user category.
    */
    function removeCategories()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
       
       $db->query( "DELETE FROM eZContact_CompanyTypeDict
                                WHERE CompanyID='$this->ID'" );
    }

    /*!
      Returns the categories that belong to this eZCompany object.
    */
    function categories( $companyID = false, $as_object = true )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        if ( !$companyID )
            $companyID = $this->ID;

        $return_array = array();
        $db = eZDB::globalDatabase();

        $db->array_query( $categories_array, "SELECT CompanyTypeID
                                                 FROM eZContact_CompanyTypeDict
                                                 WHERE CompanyID='$companyID'" );

        foreach( $categories_array as $categoriesItem )
        {
            if ( $as_object )
                $return_array[] = new eZCompanyType( $categoriesItem["CompanyTypeID"] );
            else
                $return_array[] = $categoriesItem["CompanyTypeID"];
        }
        return $return_array;
    }
   

    /*!
      Returns the address that belong to this eZCompany object.
    */
    function addresses( $companyID = false )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        if ( !$companyID )
            $companyID = $this->ID;
        
        $return_array = array();
        $db = eZDB::globalDatabase();

        $db->array_query( $address_array, "SELECT AddressID
                                                 FROM eZContact_CompanyAddressDict
                                                 WHERE CompanyID='$companyID'" );

        foreach( $address_array as $addressItem )
            {
                $return_array[] = new eZAddress( $addressItem["AddressID"] );
            }

        return $return_array;
    }

    /*!
      Adds an address to the current Company.
    */
    function addAddress( $address )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;
       
        $db = eZDB::globalDatabase();
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
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();

        $db->array_query( $address_array, "SELECT eZContact_Address.ID AS 'AID', eZContact_CompanyAddressDict.CompanyID AS 'DID'
                                               FROM eZContact_Address, eZContact_CompanyAddressDict
                                               WHERE eZContact_Address.ID=eZContact_CompanyAddressDict.AddressID AND eZContact_CompanyAddressDict.CompanyID='$this->ID' " );
        
        foreach( $address_array as $addressItem )
        {
            $addressID = $addressItem["AID"];
            $addressDictID = $addressItem["DID"];
            $db->query( "DELETE FROM eZContact_Address WHERE ID='$addressID'" );
            $db->query( "DELETE FROM eZContact_CompanyAddressDict WHERE CompanyID='$this->ID'" );
        }
    }

    /*!
      Returns the phones that belong to this eZCompany object.
    */
    function phones( $companyID = false )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        if ( !$companyID )
            $companyID = $this->ID;

        $return_array = array();
        $db = eZDB::globalDatabase();

        $db->array_query( $phone_array, "SELECT PhoneID
                                                 FROM eZContact_CompanyPhoneDict
                                                 WHERE CompanyID='$companyID'" );

        foreach( $phone_array as $phoneItem )
        {
            $return_array[] = new eZPhone( $phoneItem["PhoneID"] );
        }

        return $return_array;
    }

    /*!
      Adds an phone to the current Company.
    */
    function addPhone( $phone )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;
       
        $db = eZDB::globalDatabase();
        if ( get_class( $phone ) == "ezphone" )
        {
            $phoneID = $phone->id();

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
        $db = eZDB::globalDatabase();
        $db->array_query( $phone_array, "SELECT eZContact_Phone.ID AS 'PID', eZContact_CompanyPhoneDict.CompanyID AS 'DID'
                                     FROM eZContact_Phone, eZContact_CompanyPhoneDict
                                     WHERE eZContact_Phone.ID=eZContact_CompanyPhoneDict.PhoneID AND eZContact_CompanyPhoneDict.CompanyID='$this->ID' " );
        
        foreach( $phone_array as $phoneItem )
        {
            $phoneID = $phoneItem["PID"];
            $phoneDictID = $phoneItem["DID"];
            $db->query( "DELETE FROM eZContact_Phone WHERE ID='$phoneID'" );
            $db->query( "DELETE FROM eZContact_CompanyPhoneDict WHERE CompanyID='$this->ID'" );
        }
    }

    /*!
      Returns the onlines that belong to this eZCompany object.
    */
    function onlines( $onlineID = false )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        if ( !$onlineID )
            $onlineID = $this->ID;

        $return_array = array();
        $db = eZDB::globalDatabase();

        $db->array_query( $online_array, "SELECT OnlineID
                                                 FROM eZContact_CompanyOnlineDict
                                                 WHERE CompanyID='$this->ID'" );

        foreach( $online_array as $onlineItem )
        {
            $return_array[] = new eZOnline( $onlineItem["OnlineID"] );
        }

        return $return_array;
    }

    /*!
      Adds an online to the current Company.
    */
    function addOnline( $online )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;
       
        $db = eZDB::globalDatabase();

        if ( get_class( $online ) == "ezonline" )
        {
            $onlineID = $online->id();

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
        $db = eZDB::globalDatabase();
        $db->array_query( $online_array, "SELECT eZContact_Online.ID AS 'OID', eZContact_CompanyOnlineDict.CompanyID AS 'DID'
                                     FROM eZContact_Online, eZContact_CompanyOnlineDict
                                     WHERE eZContact_Online.ID=eZContact_CompanyOnlineDict.OnlineID AND eZContact_CompanyOnlineDict.CompanyID='$this->ID' " );
        
        foreach( $online_array as $onlineItem )
        {
            $onlineID = $onlineItem["OID"];
            $onlineDictID = $onlineItem["DID"];
            $db->query( "DELETE FROM eZContact_Online WHERE ID='$onlineID'" );
            $db->query( "DELETE FROM eZContact_CompanyOnlineDict WHERE CompanyID='$this->ID'" );
        }
    }
    
    /*!
      Adds a image to the current 
     */
    function addImage( $image )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;
       
        $db = eZDB::globalDatabase();

        if ( get_class ( $image ) == "ezimage" )
        {
            $imageID = $image->id();

            $db->query( "INSERT INTO eZContact_CompanyImageDict
                                     SET CompanyID='$this->ID', ImageID='$imageID'" );
        }
    }

    /*!
      Returns every image to a product as a array of eZImage objects.
    */
    function images()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
       
       $return_array = array();
       $image_array = array();
       
       $db->array_query( $image_array, "SELECT ImageID FROM eZContact_CompanyImageDict WHERE CompanyID='$this->ID'" );
       
       for ( $i=0; $i<count($image_array); $i++ )
       {
           $return_array[$i] = new eZImage( $image_array[$i]["ImageID"], false );
       }
       
       return $return_array;
    }

    /*!
      Delete all onlines and the relation to the eZContact_Company
    */
    function removeImages()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
        
       $db->query( "DELETE FROM eZContact_CompanyImageDefinition WHERE CompanyID='$this->ID'" );
    }

    /*!
      Returns the logo image of the company as a eZImage object.
    */
    function logoImage( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $ret = false;
        $db = eZDB::globalDatabase();
       
       $db->array_query( $res_array, "SELECT * FROM eZContact_CompanyImageDefinition
                                     WHERE
                                     CompanyID='$this->ID'
                                   " );

       if ( count( $res_array ) == 1 )
       {
           if ( $res_array[0]["LogoImageID"] != "NULL" )
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
    function setLogoImage( $image )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        if ( get_class( $image ) == "ezimage" )
        {
            $db = eZDB::globalDatabase();

            $imageID = $image->id();

            $db->array_query( $res_array, "SELECT COUNT(*) AS Number FROM eZContact_CompanyImageDefinition
                                     WHERE
                                     CompanyID='$this->ID'" );

            if ( $res_array[0]["Number"] == "1" )
            {            
                $db->query( "UPDATE eZContact_CompanyImageDefinition
                                     SET
                                     LogoImageID='$imageID'
                                     WHERE
                                     CompanyID='$this->ID'" );
            }
            else
            {
                $db->query( "INSERT INTO eZContact_CompanyImageDefinition
                                     SET
                                     CompanyID='$this->ID',
                                     LogoImageID='$imageID'" );
            }
        }
    }

    function deleteImage( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
       $db->query( "UPDATE eZContact_CompanyImageDefinition SET CompanyImageID='0' WHERE CompanyID='$this->ID'" );
    }

    function deleteLogo( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $db = eZDB::globalDatabase();
       
       $db->query( "UPDATE eZContact_CompanyImageDefinition SET LogoImageID='0' WHERE CompanyID='$this->ID'" );
    }


    /*!
      Sets the company image for the company.

      The argument must be a eZImage object.
    */
    function setCompanyImage( $image )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        if ( get_class( $image ) == "ezimage" )
        {
            $db = eZDB::globalDatabase();
            $imageID = $image->id();

            $db->array_query( $res_array, "SELECT COUNT(*) AS Number FROM eZContact_CompanyImageDefinition
                                     WHERE
                                     CompanyID='$this->ID'" );

            if ( $res_array[0]["Number"] == "1" )
            {            
                $db->query( "UPDATE eZContact_CompanyImageDefinition
                                     SET
                                     CompanyImageID='$imageID'
                                     WHERE
                                     CompanyID='$this->ID'" );
            }
            else
            {
                $db->query( "INSERT INTO eZContact_CompanyImageDefinition
                                     SET
                                     CompanyID='$this->ID',
                                     CompanyImageID='$imageID'" );
            }
        }
    }


    /*!
      Returns the logo image of the company as a eZImage object.
    */
    function companyImage( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $ret = false;
        $db = eZDB::globalDatabase();

       $db->array_query( $res_array, "SELECT * FROM eZContact_CompanyImageDefinition
                                     WHERE
                                     CompanyID='$this->ID'
                                   " );

       if ( count( $res_array ) == 1 )
       {
           if ( $res_array[0]["CompanyImageID"] != "NULL" )
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
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Name = $value;
    }

    /*!
      Sets the comment of the company.
    */
    function setComment( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Comment = $value;
    }

    /*!
      Sets the creatorID of the company.
    */
    function setCreatorID( $user )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        if ( get_class( $user ) == "ezuser" )
        {
            $userID = $user->id();

            $this->CreatorID = $userID;
        }
    }
    /*!
      Sets the contact type of the company.
    */
    function setCompanyNo( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

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
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Name;
    }

    /*!
      Returnerer ID til eier av firma ( brukeren som opprettet det ).
    */
    function creatorID()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->CreatorID;
    }
    
    /*!
      Returnerer kommentar.
    */
    function comment()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Comment;
    }  
    
    /*!
      Returns Company no.
    */
    function companyNo()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->CompanyNo;
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
      Returns the contact for this company.
    */
    function contact( )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->ContactType;
    }

    /*
      Henter ut alle firma i databasen hvor en eller flere tilhørende personer    
      inneholder søkestrengen.
    */
    function searchByPerson( $query )
    {
        $db = eZDB::globalDatabase();
        $company_array = array();
        $return_array = array();
    
        $db->array_query( $company_array, "SELECT eZContact_Company.ID as ID
                                      FROM eZContact_Company, eZContact_Person
                                      WHERE ((eZContact_Person.FirstName LIKE '%$query%' OR eZContact_Person.LastName LIKE '%$query%')
                                      AND eZContact_Company.ID=eZContact_Person.Company) GROUP BY eZContact_Company.ID ORDER BY eZContact_Company.ID" );

        foreach( $company_array as $companyItem )
            {
                $return_array[] = new eZCompany( $companyItem["ID"] );
            }
        return $return_array;
    }    

    /*!
      Returns the project state of this company.
    */
    function projectState()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = "";

        $db = eZDB::globalDatabase();

        $checkQuery = "SELECT ProjectID FROM eZContact_CompanyProjectDict WHERE CompanyID='$this->ID'";
        $db->array_query( $array, $checkQuery, 0, 1 );

        if( count( $array ) == 1 )
        {
            $ret = $array[0]["ProjectID"];
        }

        return $ret;
    }

    /*!
      Returns the project state of this company.
    */
    function setProjectState( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $db = eZDB::globalDatabase();
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

    var $ID;
    var $CreatorID;
    var $Name;
    var $Comment;
    var $Online;
    var $ContactType;
    var $CompanyNo;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
}

?>
