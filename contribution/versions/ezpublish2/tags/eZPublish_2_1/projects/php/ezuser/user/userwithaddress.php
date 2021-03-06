<?
// 
// $Id: userwithaddress.php,v 1.53 2001/05/14 15:31:15 fh Exp $
//
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <10-ct-2000 12:52:42 bf>
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
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
include_once( "classes/ezhttptool.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZUserMain", "Language" );
$SelectCountry = $ini->read_var( "eZUserMain", "SelectCountry" );
$AnonymousUserGroup = $ini->read_var( "eZUserMain", "AnonymousUserGroup" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezaddress/classes/ezaddress.php" );
include_once( "ezaddress/classes/ezcountry.php" );
include_once( "ezmail/classes/ezmail.php" );

$t = new eZTemplate( "ezuser/user/" . $ini->read_var( "eZUserMain", "TemplateDir" ),
                     "ezuser/user/intl/", $Language, "userwithaddress.php" );

$t->setAllStrings();

$t->set_file( "user_edit_tpl", "userwithaddress.tpl" );

$t->set_block( "user_edit_tpl", "required_fields_error_tpl", "required_fields_error" );
$t->set_block( "user_edit_tpl", "user_exists_error_tpl", "user_exists_error" );
$t->set_block( "user_edit_tpl", "password_error_tpl", "password_error" );
$t->set_block( "user_edit_tpl", "missing_address_error_tpl", "missing_address_error" );

$t->set_block( "user_edit_tpl", "address_tpl", "address" );
$t->set_block( "address_tpl", "delete_address_tpl", "delete_address" );
$t->set_block( "address_tpl", "country_tpl", "country" );
$t->set_block( "country_tpl", "country_option_tpl", "country_option" );

$t->set_block( "user_edit_tpl", "errors_item_tpl", "errors_item" );
$t->set_var( "errors_item", "" );

$t->set_block( "user_edit_tpl", "login_item_tpl", "login_item" );
$t->set_block( "user_edit_tpl", "disabled_login_item_tpl", "disabled_login_item" );

// Info templates
$t->set_block( "user_edit_tpl", "info_item_tpl", "info_item" );
$t->set_block( "info_item_tpl", "info_updated_tpl", "info_updated" );

// Error templates
$t->set_block( "errors_item_tpl", "error_login_tpl", "error_login" );
$t->set_block( "errors_item_tpl", "error_login_exists_tpl", "error_login_exists" );
$t->set_block( "errors_item_tpl", "error_first_name_tpl", "error_first_name" );
$t->set_block( "errors_item_tpl", "error_last_name_tpl", "error_last_name" );
$t->set_block( "errors_item_tpl", "error_email_tpl", "error_email" );
$t->set_block( "errors_item_tpl", "error_email_not_valid_tpl", "error_email_not_valid" );
$t->set_block( "errors_item_tpl", "error_password_match_tpl", "error_password_match" );
$t->set_block( "errors_item_tpl", "error_password_too_short_tpl", "error_password_too_short" );

$t->set_block( "errors_item_tpl", "error_address_street1_tpl", "error_address_street1" );
$t->set_block( "errors_item_tpl", "error_address_street2_tpl", "error_address_street2" );
$t->set_block( "errors_item_tpl", "error_address_zip_tpl", "error_address_zip" );
$t->set_block( "errors_item_tpl", "error_address_place_tpl", "error_address_place" );

$t->set_block( "errors_item_tpl", "error_missing_address_tpl", "error_missing_address" );
$t->set_block( "errors_item_tpl", "error_missing_country_tpl", "error_missing_country" );

$t->set_block( "user_edit_tpl", "new_user_tpl", "new_user" );
$t->set_block( "user_edit_tpl", "edit_user_tpl", "edit_user" );
$t->set_block( "user_edit_tpl", "edit_user_info_tpl", "edit_user_info" );

$t->set_block( "user_edit_tpl", "ok_button_tpl", "ok_button" );
$t->set_block( "user_edit_tpl", "submit_button_tpl", "submit_button" );

$t->set_var( "error_login", "" );
$t->set_var( "error_login_exists", "" );
$t->set_var( "error_first_name", "" );
$t->set_var( "error_last_name", "" );
$t->set_var( "error_email", "" );
$t->set_var( "error_email_not_valid", "" );
$t->set_var( "error_password_match", "" );
$t->set_var( "error_password_too_short", "" );
$t->set_var( "error_missing_country", "" );

$t->set_var( "error_address_place", "" );
$t->set_var( "error_address_zip", "" );
$t->set_var( "error_address_street1", "" );
$t->set_var( "error_address_street2", "" );

$t->set_var( "first_name_value", "$FirstName" );
$t->set_var( "last_name_value", "$LastName" );
$t->set_var( "login_value", "$Login" );
$t->set_var( "email_value", "$Email" );
$t->set_var( "password_value", "$Password" );
$t->set_var( "verify_password_value", "$VerifyPassword" );

if ( $AutoCookieLogin == "on" )
    $t->set_var( "is_cookie_selected", "checked" );

$user = eZUser::currentUser();

$t->set_var( "ok_button", "" );
$t->set_var( "submit_button", "" );
$t->set_var( "new_user", "" );
$t->set_var( "edit_user", "" );
$t->set_var( "edit_user_info", "" );
if ( get_class( $user ) == "ezuser" )
{
    $t->parse( "submit_button", "submit_button_tpl" );
    $t->parse( "edit_user", "edit_user_tpl" );
    $t->parse( "edit_user_info", "edit_user_info_tpl" );
}
else
{
    $t->parse( "ok_button", "ok_button_tpl" );
    $t->parse( "new_user", "new_user_tpl" );
}

// Set error checking.
$error = false;
$nameCheck = true;
$emailCheck = true;
$firstNameCheck = true;
$lastNameCheck = true;
$loginCheck = true;
$passwordCheck = true;
$street1Check = true;
$street2Check = false;
$zipCheck = true;
$placeCheck = true;

// If the user is trying to buy without having a address
if ( $MissingAddress == true )
{
    $t->parse( "error_missing_address", "error_missing_address_tpl" );

    $t->parse( "errors_item", "errors_item_tpl" );

    $t->set_var( "action_value", "update" );
}
else
{
    $t->set_var( "error_missing_address", "" );
    $t->set_var( "action_value", "update" );
}

// If the user is trying to buy without having a address
if ( $MissingCountry == true )
{
    $t->parse( "error_missing_country", "error_missing_country_tpl" );

    $t->parse( "errors_item", "errors_item_tpl" );

    $t->set_var( "action_value", "update" );
}
else
{
    $t->set_var( "error_missing_country", "" );
    $t->set_var( "action_value", "update" );
}

// Check for errors when inserting, updating and inserting a new address
if ( isset( $OK ) )
{
    if ( $loginCheck )
    {
        if ( get_class( $user ) != "ezuser" and $Login == "" )
        {
            $t->parse( "error_login", "error_login_tpl" );
            $error = true;
        }
        else if ( get_class( $user ) != "ezuser" )
        {
            if ( eZUser::exists( $Login ) == true )
            {
                $t->parse( "error_login_exists", "error_login_exists_tpl" );
                $error = true;
            }
        }
    }

    if ( $firstNameCheck and $FirstName == "" )
    {
        $t->parse( "error_first_name", "error_first_name_tpl" );
        $error = true;
    }

    if ( $lastNameCheck and $LastName == "" )
    {
        $t->parse( "error_last_name", "error_last_name_tpl" );
        $error = true;
    }

    if ( $emailCheck )
    {
        if( $Email == "" )
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
            $t->parse( "error_password_match", "error_password_match_tpl" );
            $error = true;
            
        }
        if ( strlen( $VerifyPassword ) < 2 )
        {
            $t->parse( "error_password_too_short", "error_password_too_short_tpl" );
            $error = true;
        }
    }

    if ( $addressCheck )
    {
        for( $i=0; $i < count ( $AddressID ); $i++ )
        {
            if ( $street1Check )
            {
                if ( $Street1[$i] == "" )
                {
                    $t->parse( "error_address_street1", "error_address_street1_tpl" );
                    $error = true;
                }
            }

            if ( $street2Check )
            {
                if ( $Street2[$i] == "" )
                {
                    $t->parse( "error_address_street2", "error_address_street2_tpl" );
                    $error = true;
                }
            }

            if ( $zipCheck )
            {
                if ( $Zip[$i] == "" )
                {
                    $t->parse( "error_address_zip", "error_address_zip_tpl" );
                    $error = true;
                }
            }

            if ( $placeCheck )
            {
                if ( $Place[$i] == "" )
                {
                    $t->parse( "error_address_place", "error_address_place_tpl" );
                    $error = true;
                }
            }
        }
    }

    if( $error == true )
    {
        $t->parse( "errors_item", "errors_item_tpl" );
    }
}

// Add a new address
if ( isset( $NewAddress ) )
{
    if ( count( $AddressID ) > 0 )
        $AddressID[] = $AddressID[count( $AddressID ) - 1] + 1;
    else
        $AddressID = array( 1 );
    $Street1[] = "";
    $Street2[] = "";
    $Zip[] = "";
    $Place[] = "";
    $country_id = $ini->read_var( "eZUserMain", "DefaultCountry" );
    if ( count( $CountryID ) > 0 and is_numeric( $CountryID[count($CountryID)-1] ) )
        $CountryID[] = $CountryID[count($CountryID)-1];
    else
        $CountryID[] = $country_id;
}

// Insert a user with address
if ( isset( $OK ) and $error == false )
{
    $new_user = false;
    if ( get_class( $user ) != "ezuser" )
        $new_user = true;

    if ( $new_user )
    {
        $user_insert = new eZUser();
        $user_insert->setLogin( $Login );
    }
    else
    {
        $user_insert = $user;
    }

    if ( $new_user or $Password != "dummy" )
        $user_insert->setPassword( $Password );

    $user_insert->setEmail( $Email );
    $user_insert->setFirstName( $FirstName );
    $user_insert->setLastName( $LastName );
    $user_insert->setSignature( $Signature );


    if ( $AutoCookieLogin == "on" )
        $user_insert->setCookieLogin( true );
    else
        $user_insert->setCookieLogin( false );

    $user_insert->store();

    // add user to usergroup
    setType( $AnonymousUserGroup, "integer" );

    $group = new eZUserGroup( $AnonymousUserGroup );
    $group->addUser( $user_insert );

    if ( !$new_user )
        $user_insert->removeAddresses();

    for( $i = 0; $i < count( $AddressID ); ++$i )
    {
        $address_id = $AddressID[$i];
        $address = new eZAddress();

        $address->setStreet1( $Street1[$i] );
        $address->setStreet2( $Street2[$i] );
        $address->setZip( $Zip[$i] );
        $address->setPlace( $Place[$i] );

        if ( $SelectCountry == "enabled" and isset( $CountryID[$i] ) )
        {
            $address->setCountry( $CountryID[$i] );
        }
        else
        {
            $CountryID = $ini->read_var( "eZUserMain", "DefaultCountry" );
            $address->setCountry( $CountryID );
        }
        $address->store();
        if ( $MainAddressID == $AddressID[$i] )
            $main_id = $address->id();

        // add the address to the user.
        $user_insert->addAddress( $address );
    }
    eZAddress::setMainAddress( $main_id, $user_insert );

    $user_insert->loginUser( $user_insert );

    if ( !$new_user )
        $Updated = true;

    $RedirectURL = $session->variable( "RedirectURL" );

    if ( isSet( $RedirectURL )  && ( $RedirectURL != "" ) )
    {
        $session->setVariable( "RedirectURL", "" );
        eZHTTPTool::header( "Location: $RedirectURL" );
        exit();
    }
    if ( get_class( $user ) != "ezuser" )
    {
        eZHTTPTool::header( "Location: /" );
        exit();
    }
}

$info_array = array();
$t->set_var( "info_item", "" );
if ( isset( $Updated ) )
{
    $info_array[] = "info_updated";
}

if ( count( $info_array ) > 0 )
{
    foreach( $info_array as $info )
    {
        $t->parse( $info, $info . "_tpl" );
    }
    $t->parse( "info_item", "info_item_tpl" );
}

$t->set_var( "readonly", "" );

// Fill in variables which are not set for current user,
// this is done the first the page loads
if ( get_class( $user ) == "ezuser" )
{
    if ( !isset( $UserID ) )
        $UserID = $user->id();
    if ( !isset( $Login ) )
        $Login = $user->Login();
    if ( !isset( $Email ) )
        $Email = $user->Email();
    if ( !isset( $FirstName ) )
        $FirstName = $user->FirstName();
    if ( !isset( $LastName ) )
         $LastName = $user->LastName();

    if ( $user->cookieLogin() == true )
        $cookieCheck = "checked";
    else
        $cookieCheck = "";
    
    if ( !isset( $AddressID ) )
    {
        if ( !isset( $AddressID ) )
            $AddressID = array();
        if ( !isset( $Street1 ) )
            $Street1 = array();
        if ( !isset( $Street2 ) )
            $Street2 = array();
        if ( !isset( $Zip ) )
            $Zip = array();
        if ( !isset( $Place ) )
            $Place = array();
        if ( !isset( $CountryID ) )
            $CountryID = array();

        $mainAddress = eZAddress::mainAddress( $user );

        $addressArray = $user->addresses();

        $i = 0;
        foreach ( $addressArray as $address )
        {
            if ( ( get_class( $mainAddress ) == "ezaddress" ) and ( $address->id() == $mainAddress->id()  ) and !isset( $MainAddressID ) )
                $MainAddressID = $i + 1;
            if ( !isset( $AddressID[$i] ) )
                $AddressID[$i] = $i + 1;
            if ( !isset( $Street1[$i] ) )
                $Street1[$i] = $address->street1();
            if ( !isset( $Street2[$i] ) )
                $Street2[$i] = $address->street2();
            if ( !isset( $Zip[$i] ) )
                $Zip[$i] = $address->zip();
            if ( !isset( $Place[$i] ) )
                $Place[$i] = $address->place();
            if ( !isset( $CountryID[$i] ) )
            {
                $country = $address->country();
                if ( $country )
                {
                    $CountryID[$i] = $country->id();
                }
            }
            ++$i;
        }
    }
}
else
{
    if ( !isset( $AddressID ) )
        $AddressID = array( 1 );
    if ( !isset( $Street1 ) )
        $Street1 = array( "" );
    if ( !isset( $Street2 ) )
        $Street2 = array( "" );
    if ( !isset( $Zip ) )
        $Zip = array( "" );
    if ( !isset( $Place ) )
        $Place = array( "" );
    if ( !isset( $CountryID ) )
        $CountryID = array( $ini->read_var( "eZUserMain", "DefaultCountry" ) );
    if ( !isset( $MainAddressID ) )
         $MainAddressID = 1;
}

if ( !isset( $DeleteAddressArrayID ) )
    $DeleteAddressArrayID = array();

// Set the values to the user when editing
$t->parse( "delete_address", "delete_address_tpl" );

$t->set_var( "login_value", $Login );
$t->set_var( "disabled_login_item", "" );
$t->set_var( "login_item", "" );
if ( get_class( $user ) == "ezuser" )
{
    $t->parse( "disabled_login_item", "disabled_login_item_tpl" );
}
else
{
    $t->parse( "login_item", "login_item_tpl" );
}

if ( get_class( $user ) == "ezuser" and $Password == "" )
    $Password = "dummy";
if ( get_class( $user ) == "ezuser" and $VerifyPassword == "" )
    $VerifyPassword = "dummy";
$t->set_var( "password_value", $Password );
$t->set_var( "verify_password_value", $VerifyPassword );
$t->set_var( "email_value", $Email );

$t->set_var( "first_name_value", $FirstName );
$t->set_var( "last_name_value", $LastName );

$t->set_var( "is_cookie_selected", $cookieCheck );

if ( get_class( $user ) == "ezuser" )
    $t->set_var( "readonly", "disabled" );

$t->set_var( "address", "" );

if ( $SelectCountry == "enabled" )
    $countryList =& eZCountry::getAllArray();

// Make sure the MainAddressID is set to something sensible
$deleted = false;
for ( $i = 0; $i < count( $AddressID ); ++$i )
{
    $address_id = $AddressID[$i];
    $variable = "DeleteAddressButton$address_id";
    if ( in_array( $AddressID[$i], $DeleteAddressArrayID ) or isset( $$variable ) )
    {
        if ( $AddressID[$i] == $MainAddressID )
            $deleted = true;
    }
}

if ( $deleted )
{
    for ( $i = 0; $i < count( $AddressID ); ++$i )
    {
        $address_id = $AddressID[$i];
        $variable = "DeleteAddressButton$address_id";
        if ( !in_array( $AddressID[$i], $DeleteAddressArrayID ) and !isset( $$variable ) )
        {
            $MainAddressID = $AddressID[$i];
            break;
        }
    }
}

// Render addresses

for ( $i = 0; $i < count( $AddressID ); ++$i )
{
    $address_id = $AddressID[$i];
    $variable = "DeleteAddressButton$address_id";
    if ( !in_array( $AddressID[$i], $DeleteAddressArrayID ) and !isset( $$variable ) )
    {
        $t->set_var( "address_id", $AddressID[$i] );

        $t->set_var( "street1_value", $Street1[$i] );
        $t->set_var( "street2_value", $Street2[$i] );

        if ( is_numeric( $MainAddressID ) )
        {
            $t->set_var( "is_checked", $AddressID[$i] == $MainAddressID ? "checked" : "" );
        }

        $t->set_var( "zip_value", $Zip[$i] );
        $t->set_var( "place_value", $Place[$i] );

        $t->set_var( "country", "" );
        if ( $SelectCountry == "enabled" )
        {
            $t->set_var( "country_option", "" );
            foreach ( $countryList as $country )
            {
                $t->set_var( "is_selected", $country["ID"] == $CountryID[$i] ? "selected" : "" );

                $t->set_var( "country_id", $country["ID"] );
                $t->set_var( "country_name", $country["Name"] );
                $t->parse( "country_option", "country_option_tpl", true );
            }
            $t->parse( "country", "country_tpl" );
        }
        $t->set_var( "address_number", $i + 1 );

        $t->parse( "address", "address_tpl", true );
    }
}

$t->set_var( "user_id", $UserID );

$t->set_var( "redirect_url", $RedirectURL );

$t->pparse( "output", "user_edit_tpl" );

?>
