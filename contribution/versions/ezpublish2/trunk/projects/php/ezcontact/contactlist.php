<?
include  "template.inc";
require "ezphputils.php";
require "ezperson.php";
require "ezpersontype.php";
require "ezsession.php";
require "ezuser.php";
require "ezcompany.php";

include( "checksession.php" );

$t = new Template( ".");  

$t->set_file( array(
                   "contact_page" =>  "templates/contactpage.tpl",
                   "person_item" =>  "templates/personitem.tpl",
                   "company_item" =>  "templates/companyitem.tpl" ) );

$person = new eZPerson();
$personType = new eZPersonType();

$person_array = $person->getAll( );

for ( $i=0; $i<count( $person_array ); $i++ )
{
  if ( ( $i % 2 ) == 0 )
  {
    $t->set_var( "bg_color", "#eeeeee" );
  }
  else
  {
    $t->set_var( "bg_color", "#dddddd" );
  }
  
  $t->set_var( "person_id", $person_array[$i][ "ID" ] );
  $t->set_var( "first_name", $person_array[$i][ "FirstName" ] );
  $t->set_var( "last_name", $person_array[$i][ "LastName" ] );
  $t->parse( "person_list", "person_item", true );
}

$company = new eZCompany();
$company_array = $company->getAll( );

for ( $i=0; $i<count( $company_array ); $i++ )
{
  if ( ( $i % 2 ) == 0 )
  {
    $t->set_var( "bg_color", "#eeeeee" );
  }
  else
  {
    $t->set_var( "bg_color", "#dddddd" );
  }
  
  $t->set_var( "company_name", $company_array[$i][ "Name" ] );
  $t->parse( "company_list", "company_item", true );  
}



$t->pparse( "output", "contact_page");
?>
