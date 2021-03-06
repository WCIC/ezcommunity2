<?
// 
// $Id: userwithaddress.php,v 1.9 2000/11/03 10:39:35 ce-cvs Exp $
//
// 
//
// B�rd Farstad <bf@ez.no>
// Created on: <10-Oct-2000 12:52:42 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZUserMain", "Language" );
$SelectCountry = $ini->read_var( "eZUserMain", "SelectCountry" );
$AnonymousUserGroup = $ini->read_var( "eZUserMain", "AnonymousUserGroup" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezcontact/classes/ezaddress.php" );
include_once( "ezcontact/classes/ezcountry.php" );

if ( $Action == "Insert" )
{
    // check for valid data
    if ( $Login != "" &&
    $Email != "" &&
    $FirstName != "" &&
    $LastName != "" &&
    $Street1 != "" &&
    $Zip != "" &&
    $Place != "" )
    {
        $user = new eZUser();

        if ( !$user->exists( $Login ) )
        {
            if ( ( $Password == $VerifyPassword ) && ( strlen( $VerifyPassword ) > 2 ) )
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

                if ( isset( $RedirectURL ) )
                {
                    Header( "Location: $RedirectURL" );
                    exit();
                }
                
                Header( "Location: /" );
                exit();
            }
            else
            {
                $PasswordError = true;
            }
        }
        else
        {
            $UserExistsError = true;
        }
    }
    else
    {
        $Error = true;
    }
}


if ( $Action == "Update" )
{
    // check for valid data
    if ( $Login != "" &&
    $Email != "" &&
    $FirstName != "" &&
    $LastName != "" )
        {
            $user = new eZUser();
            $user->get( $UserID );

            if ( $Password )
            {
                if ( ( $Password == $VerifyPassword ) && ( strlen( $VerifyPassword ) > 2 ) )
                {
                    $user->setPassword( $Password );
                }
                else
                {
                    $PasswordError = true;
                }
            }
    
            $user->setEmail( $Email );
            $user->setFirstName( $FirstName );
            $user->setLastName( $LastName );

            $address = new eZAddress();
            $address->get( $AddressID );
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
            
            if ( !$PasswordError )
                $user->store();


            if ( isSet( $RedirectURL )  && ( $RedirectURL != "" ) )
            {
                Header( "Location: $RedirectURL" );
                exit();
            }
            Header( "Location: /" );
            exit();
        }
    else
    {
        $Error = true;
    }
}



$t = new eZTemplate( "ezuser/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                     "ezuser/user/intl/", $Language, "userwithaddress.php" );

$t->setAllStrings();

$t->set_file( array(        
    "user_edit_tpl" => "userwithaddress.tpl"
    ) );

$t->set_var( "readonly", "" );
$action_value = "insert";

if ( $Action == "Update" )
    $action_value = "update";

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

    $t->set_var( "readonly", "readonly" );
    

// print out the addresses
    // Dosent work with multiplie addresses.

    $addressArray = $user->addresses();

    foreach ( $addressArray as $address )
    {
        $Street1 =  $address->street1();
        $Street2 = $address->street2();
        $Zip = $address->zip();
        $Place = $address->place();

       $t->set_var( "address_id", $address->id() );

//        $country = $address->country();
//        $t->set_var( "country", $country->name() );
    }

    $action_value = "update";

}


$t->set_block( "user_edit_tpl", "required_fields_error_tpl", "required_fields_error" );
$t->set_block( "user_edit_tpl", "user_exists_error_tpl", "user_exists_error" );
$t->set_block( "user_edit_tpl", "password_error_tpl", "password_error" );

$t->set_block( "user_edit_tpl", "country_tpl", "country" );
$t->set_block( "country_tpl", "country_option_tpl", "country_option" );

if ( $Error == true )
{
    $t->parse( "required_fields_error", "required_fields_error_tpl" );
}
else
{
   $t->set_var( "required_fields_error", "" );
}

if ( $UserExistsError == true )
{
    $t->parse( "user_exists_error", "user_exists_error_tpl" );
}
else
{
   $t->set_var( "user_exists_error", "" );
}

if ( $PasswordError == true )
{
    $t->parse( "password_error", "password_error_tpl" );
}
else
{
   $t->set_var( "password_error", "" );
}

$t->set_var( "login_value", $Login );
$t->set_var( "password_value", $Password );
$t->set_var( "verify_password_value", $VerifyPassword );
$t->set_var( "email_value", $Email );

$t->set_var( "first_name_value", $FirstName );
$t->set_var( "last_name_value", $LastName );

$t->set_var( "street1_value", $Street1 );
$t->set_var( "street2_value", $Street2 );

$t->set_var( "zip_value", $Zip );

$t->set_var( "place_value", $Place );

if ( $SelectCountry == "enabled" )
{
    $ezcountry = new eZCountry();
    $countryList =& $ezcountry->getAllArray();
    
    foreach ( $countryList as $country )
    {
        if ( $Action == "Edit" )
        {
            $countryID = $address->country();
            
            if ( $country["ID"] == $countryID->id() )
            {
                $t->set_var( "is_selected", "selected" );
            }
            else
                $t->set_var( "is_selected", "" );
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
    


$t->set_var( "action_value", $action_value );
$t->set_var( "user_id", $UserID );

$t->set_var( "redirect_url", $RedirectURL );

$t->pparse( "output", "user_edit_tpl" );

?>




