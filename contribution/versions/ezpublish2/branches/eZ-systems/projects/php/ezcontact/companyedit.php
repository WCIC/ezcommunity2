<?
include  "template.inc";
require "ezphputils.php";
require "ezperson.php";
require "ezpersontype.php";
require "ezcompany.php";
require "ezaddress.php";
require "ezaddresstype.php";
require "ezsession.php";
require "ezuser.php";
require "ezcompanytype.php";

if ( $Insert == "TRUE" )
{
  $newCompany = new eZCompany();
//  print $CompanyName;
  $newCompany->setName( $CompanyName );  
  $newCompany->setContactType( $CompanyType );

  $newCompany->setComment( $Comment );

  { // hente ut gjeldene bruker
    $session = new eZSession();
    $session->get( $AuthenticatedSession ); 
    $usr = new eZUser();
    $usr->get( $session->userID() );
  }
  $newCompany->setOwner( $usr->id() );
  $pid = $newCompany->store();
    
  $newAddress = new eZAddress();
  $newAddress->setStreet1( $Street1 );
  $newAddress->setStreet2( $Street2 );
  $newAddress->setZip( $Zip );
  $newAddress->store();
}

// sjekke session
{
  include( "checksession.php" );
}

$t = new Template( "." );
$t->set_file( array(                    
                    "company_edit" => "templates/companyedit.tpl",
                    "company_type_select" => "templates/companytypeselect.tpl",
                    "address_type_select" => "templates/addresstypeselect.tpl"
                    ) );

$company = new eZCompany();
$companyType = new eZCompanyType();
$company = new eZCompany();
$addressType = new eZAddressType();

$company_type_array = $companyType->getAll( );
$address_type_array = $addressType->getAll( );

// company type selector
for ( $i=0; $i<count( $company_type_array ); $i++ )
{
  $t->set_var( "company_type_id", $company_type_array[$i][ "ID" ] );
  $t->set_var( "company_type_name", $company_type_array[$i][ "Name" ] );
  
  if ( $CompanyType == $company_type_array[$i][ "ID" ] )
  {
    $t->set_var( "is_selected", "selected" );
  }
  else
  {
    $t->set_var( "is_selected", "" );    
  }
  
  $t->parse( "company_type", "company_type_select", true );
}


  $t->set_var( "first_name", $FirstName );
  $t->set_var( "last_name", $LastName );

// address type selector
for ( $i=0; $i<count( $address_type_array ); $i++ )
{
  $t->set_var( "address_type_id", $address_type_array[$i][ "ID" ] );
  $t->set_var( "address_type_name", $address_type_array[$i][ "Name" ] );
  
  if ( $Address_Type == $address_type_array[$i][ "ID" ] )
  {
    $t->set_var( "is_selected", "selected" );
  }
  else
  {
    $t->set_var( "is_selected", "" );    
  }
  
  $t->parse( "address_type", "address_type_select", true );
}

$t->set_var( "comment", $Comment );

$t->set_var( "street_1", $Street1 );
$t->set_var( "street_2", $Street2 );
$t->set_var( "zip_code", $Zip );

if ( $EditMode == "edit" )
{  
  $t->set_var( "submit_text", "lagre endringer" );
}
else
{
 $t->set_var( "submit_text", "lagre" );
}

$t->set_var( "message", "Nytt kontakfirma" );

$t->pparse( "output", "company_edit"  );


?>
