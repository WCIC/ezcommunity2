<?
// 
// $Id: userwithaddress.php,v 1.18 2001/01/18 19:23:27 bf Exp $
//
// 
//
// B�rd Farstad <bf@ez.no>
// Created on: <10-Oct-2000 12:52:42 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
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

require( "ezuser/user/usercheck.php" );

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezmail.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZUserMain", "Language" );
$SelectCountry = $ini->read_var( "eZUserMain", "SelectCountry" );
$AnonymousUserGroup = $ini->read_var( "eZUserMain", "AnonymousUserGroup" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezcontact/classes/ezaddress.php" );
include_once( "ezcontact/classes/ezcountry.php" );

$t = new eZTemplate( "ezuser/user/" . $ini->read_var( "eZUserMain", "TemplateDir" ),
                     "ezuser/user/intl/", $Language, "userwithaddress.php" );

$t->setAllStrings();

$t->set_file( array(        
    "user_edit_tpl" => "userwithaddress.tpl"
    ) );

$t->set_block( "user_edit_tpl", "required_fields_error_tpl", "required_fields_error" );
$t->set_block( "user_edit_tpl", "user_exists_error_tpl", "user_exists_error" );
$t->set_block( "user_edit_tpl", "password_error_tpl", "password_error" );
$t->set_block( "user_edit_tpl", "missing_address_error_tpl", "missing_address_error" );

$t->set_block( "user_edit_tpl", "address_tpl", "address" );
$t->set_block( "address_tpl", "country_tpl", "country" );
$t->set_block( "country_tpl", "country_option_tpl", "country_option" );

$t->set_block( "user_edit_tpl", "errors_item_tpl", "errors_item" );
$t->set_var( "errors_item", "&nbsp;" );

$t->set_block( "errors_item_tpl", "error_login_tpl", "error_login" );
$t->set_block( "errors_item_tpl", "error_login_exists_tpl", "error_login_exists" );
$t->set_block( "errors_item_tpl", "error_first_name_tpl", "error_first_name" );
$t->set_block( "errors_item_tpl", "error_last_name_tpl", "error_last_name" );
$t->set_block( "errors_item_tpl", "error_email_tpl", "error_email" );
$t->set_block( "errors_item_tpl", "error_email_not_valid_tpl", "error_email_not_valid" );
$t->set_block( "errors_item_tpl", "error_password_match_tpl", "error_password_match" );
$t->set_block( "errors_item_tpl", "error_password_to_short_tpl", "error_password_to_short" );

$t->set_var( "error_login", "" );
$t->set_var( "error_login_exists", "" );
$t->set_var( "error_first_name", "" );
$t->set_var( "error_last_name", "" );
$t->set_var( "error_email", "" );
$t->set_var( "error_email_not_valid", "" );
$t->set_var( "error_password", "" );

$t->set_var( "first_name_value", "$FirstName" );
$t->set_var( "last_name_value", "$LastName" );
$t->set_var( "login_value", "$Login" );
$t->set_var( "email_value", "$Email" );
$t->set_var( "password_value", "$Password" );
$t->set_var( "verify_password_value", "$VerifyPassword" );


print_r( $AddressArrayID );
if ( count ( $AddressArrayID ) != 0 )
{
    $t->set_block( "errors_item_tpl", "error_address_street1_tpl", "error_address_street1" );
    $t->set_block( "errors_item_tpl", "error_address_street2_tpl", "error_address_street2" );
    $t->set_block( "errors_item_tpl", "error_address_zip_tpl", "error_address_zip" );
    $t->set_block( "errors_item_tpl", "error_address_place_tpl", "error_address_place" );

    $t->set_var( "error_address_place", "" );
    $t->set_var( "error_address_zip", "" );
    $t->set_var( "error_address_street1", "" );
    $t->set_var( "error_address_street2", "" );

    print( "hm" );

    $addressCheck = true;
}


$user = eZUser::currentUser();

$error = false;
$nameCheck = true;
$emailCheck = true;
$firstNameCheck = true;
$lastNameCheck = true;
$loginCheck = true;
$passordCheck = true;
$street1Check = true;
$street2Check = true;
$zipCheck = true;
$placeCheck = true;

if ( $Action == "Insert" || $Action == "Update" )
{
    if ( $loginCheck )
    {
        if ( empty ( $Login ) )
        {
            $t->parse( "error_login", "error_login_tpl" );
            $error = true;
        }
        else
        {
            
            $user = new eZUser();
            if ( $user->exists( $Login ) == false )
            {
                $t->parse( "error_login_exits", "error_login_exists_tpl" );
                $error = true;
            }
        }
    }

    if ( $firstNameCheck )
    {
        if ( empty ( $FirstName ) )
        {
            $t->parse( "error_first_name", "error_first_name_tpl" );
            $error = true;
        }
    }

    if ( $lastNameCheck )
    {
        if ( empty ( $LastName ) )
        {
            $t->parse( "error_last_name", "error_last_name_tpl" );
            $error = true;
        }
    }

    if ( $emailCheck )
    {
        if( empty( $Email ) )
        {
            $t->parse( "error_email", "error_email_tpl" );
            $error = true;
        }
        else
        {
            if( eZMail::validate( $Email ) == false )
            {
                $t->parse( "error_email_not_valid", "error_email_not_valid_tpl" );
                $error = true;
            }
        }        
    }

    if ( $passwordCheck )
    {
        if ( $Password != $VerifyPassword )
        {
            $t->parse( "error_passwword_match", "error_pasword_match_tpl" );
            $error = true;
            
        }
        if ( strlen( $VerifyPassword ) < 2 )
        {
            $t->parse( "error_passwword_to_short", "error_pasword_to_short_tpl" );
            $error = true;
        }
    }

    if ( $street1Check && $addressCheck )
    {
        if ( empty ( $Street1 ) )
        {
            $t->parse( "error_address_street1", "error_address_street1_tpl" );
        }
    }

    if ( $street2Check && $addressCheck )
    {
        if ( empty ( $Street2 ) )
        {
            $t->parse( "error_address_street2", "error_address_street2_tpl" );
        }
    }

    if ( $zipCheck && $addressCheck )
    {
        if ( empty ( $ZipCheck ) )
        {
            $t->parse( "error_address_zip", "error_address_zip_tpl" );
        }
    }

    if ( $placeCheck && $addressCheck )
    {
        if ( empty ( $placeCheck ) )
        {
            $t->parse( "error_address_place", "error_address_place_tpl" );
        }
    }

    if( $error == true )
    {
        $t->parse( "errors_item", "errors_item_tpl" );
        $Action = "New";
    }
}

if ( isset( $NewAddress ) )
{
    $address = new eZAddress();
    $country = new eZCountry( $CountryID );    
    $address->setCountry( 0 );    
    $address->store();
                
    // add the address to the user.
    $user->addAddress( $address );
    
    $Action = "Edit";
}

if ( isset( $DeleteAddress ) )
{            
    if ( count ( $AddressArrayID ) != 0 )
    {
        foreach( $AddressArrayID as $ID )
        {
            $address = new eZAddress( $ID );
            $user->removeAddress( $address );
        }
    }
    $Action = "Edit";
}

if ( $Action == "Insert" && $error == false )
{
    $user->setLogin( $Login );
    $user->setPassword( $Password );
    $user->setEmail( $Email );
    $user->setFirstName( $FirstName );
    $user->setLastName( $LastName );
    
    $user->store();

    // add user to usergroup
    setType( $AnonymousUserGroup, "integer" );
    
    $group = new eZUserGroup( $AnonymousUserGroup );
    $group->addUser( $user );
    
    
    $address = new eZAddress();
    $address->setStreet1( $Street1 );
    $address->setStreet2( $Street2 );
    $address->setZip( $Zip );
    $address->setPlace( $Place );
    
    if ( isset( $CountryID ) )
    {
        $country = new eZCountry( $CountryID );
        $address->setCountry( $country );
        
    }
    
    $address->store();
    
    // add the address to the user.
    $user->addAddress( $address );
    
    $user->loginUser( $user );
    
    if ( isSet( $RedirectURL )  && ( $RedirectURL != "" ) )
    {
        Header( "Location: $RedirectURL" );
        exit();
    }
    Header( "Location: /" );
    exit();
}

if ( $Action == "Update" )
{
    $user = new eZUser();
    $user->get( $UserID );

    $user->setPassword( $Password );
    
    $user->setEmail( $Email );
    $user->setFirstName( $FirstName );
    $user->setLastName( $LastName );
    
    for ( $i=0; $i<count($AddressID); $i++ )
    {
        
        if ( $addressID == -1 )
        {
            $address = new eZAddress();
        }
        else
        {
            $address = new eZAddress( $AddressID[$i] );
        }

        $address->setStreet1( $Street1[$i] );
        $address->setStreet2( $Street2[$i] );
        $address->setZip( $Zip[$i] );
        $address->setPlace( $Place[$i] );
                
        if ( isset( $CountryID[$i] ) )
        {
            $country = new eZCountry( $CountryID[$i] );
            $address->setCountry( $country );
        }
                
        $address->store();
    }

    $user->store();


    if ( isSet( $RedirectURL )  && ( $RedirectURL != "" ) )
    {
        Header( "Location: $RedirectURL" );
        exit();
    }
    
    Header( "Location: /" );
    exit();
}

$t->set_var( "readonly", "" );

if ( $Action == "Update" )
    $action_value = "update";

if ( $Action == "New" )
{
    $t->set_var( "address_number", 1 );
    $t->set_var( "address_id", "-1" );
    $t->set_var( "action_value", "insert" );

    $t->set_var( "street1_value", "" );
    $t->set_var( "street2_value", "" );
            
    $t->set_var( "zip_value", "" );
            
    $t->set_var( "place_value", "" );
            
    if ( $SelectCountry == "enabled" )
    {
        $countryList = "";

        $ezcountry = new eZCountry();
        $countryList =& $ezcountry->getAllArray();

        $t->set_var( "country_option", "" );

        foreach ( $countryList as $country )
        {
            // add default country
            $t->set_var( "is_selected", "" );
                        
            $t->set_var( "country_id", $country["ID"] );
            $t->set_var( "country_name", $country["Name"] );
            $t->parse( "country_option", "country_option_tpl", true );
        }

        $t->parse( "country", "country_tpl" );
    }
    else
    {
        $t->set_var( "country", "" );
    }

    $t->parse( "address", "address_tpl" );
    
}

if ( $MissingAddress == true )
{
    $t->parse( "missing_address_error", "missing_address_error_tpl" );
}
else
{
    $t->set_var( "missing_address_error", "" );
}

if ( $Action == "Edit" )
{
    $user = eZUser::currentUser();
    if ( !$user )
        Header( "Location: /" );
    
    $UserID = $user->id();
    $user->get( $user->id() );
    
    $Login = $user->Login( );
    $Email = $user->Email(  );
    $FirstName = $user->FirstName(  );
    $LastName = $user->LastName(  );

    $t->set_var( "login_value", $Login );
    $t->set_var( "password_value", $Password );
    $t->set_var( "verify_password_value", $VerifyPassword );
    $t->set_var( "email_value", $Email );

    $t->set_var( "first_name_value", $FirstName );
    $t->set_var( "last_name_value", $LastName );
    
    $t->set_var( "readonly", "readonly" );

    $addressArray = "";
    $addressArray = $user->addresses();

    $i = 0;

    foreach ( $addressArray as $address )
    {
        $Street1 =  $address->street1();
        $Street2 = $address->street2();
        $Zip = $address->zip();
        $Place = $address->place();
            
        $t->set_var( "address_id", $address->id() );
            
        $t->set_var( "street1_value", $Street1 );
        $t->set_var( "street2_value", $Street2 );
            
        $t->set_var( "zip_value", $Zip );
            
        $t->set_var( "place_value", $Place );
            
        if ( $SelectCountry == "enabled" )
        {
            $countryList = "";

            $ezcountry = new eZCountry();
            $countryList =& $ezcountry->getAllArray();

            $t->set_var( "country_option", "" );
            foreach ( $countryList as $country )
                {
                    if ( $Action == "Edit" )
                    {
                        if ( $address )
                        {
                            $countryID = $address->country();
                
                            if ( $country["ID"] == $countryID->id() )
                            {
                                $t->set_var( "is_selected", "selected" );
                            }
                            else
                                $t->set_var( "is_selected", "" );
                        }
                    }
                        
                    $t->set_var( "country_id", $country["ID"] );
                    $t->set_var( "country_name", $country["Name"] );
                    $t->parse( "country_option", "country_option_tpl", true );
                }
            $t->parse( "country", "country_tpl" );
        }
        else
        {
            $t->set_var( "country", "" );
        }

        $t->set_var( "address_number", $i );
        
        $i++;
        $t->parse( "address", "address_tpl", true );
    }

    $action_value = "update";
}

$t->set_var( "user_id", $UserID );

$t->set_var( "redirect_url", $RedirectURL );

$t->pparse( "output", "user_edit_tpl" );

?>




