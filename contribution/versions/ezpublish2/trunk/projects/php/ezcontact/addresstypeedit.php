<?
include_once( "class.INIFile.php" );

$ini = new INIFIle( "site.ini" );

$Language = $ini->read_var( "eZContactMain", "Language" );

$DOC_ROOT = $ini->read_var( "eZContactMain", "DocumentRoot" );

include_once( "../classes/eztemplate.php" );
include_once( "ezphputils.php" );
//  require $DOC_ROOT . "classes/ezsession.php";
//  require $DOC_ROOT . "classes/ezuser.php";
include_once( "ezcontact/classes/ezusergroup.php" );
include_once( "ezcontact/classes/ezaddresstype.php" );

// Legge til
if ( $Action == "insert" )
{
    $type = new eZAddressType();
    $type->setName( $AddressTypeName );
    $type->store();    

    Header( "Location: index.php?page=" . $DOC_ROOT . "addresstypelist.php" );
}

// Oppdatere
if ( $Action == "update" )
{
    $type = new eZAddressType();
    $type->get( $AID );
    $type->setName( $AddressTypeName );
    $type->update();

    Header( "Location: index.php?page=" . $DOC_ROOT . "addresstypelist.php" );
}

// Slette
if ( $Action == "delete" )
{
    $type = new eZAddressType();
    $type->get( $AID );
    $type->delete( );

    Header( "Location: index.php?page=" . $DOC_ROOT . "addresstypelist.php" ); 
}

//  // sjekke session
//  {
//      include( $DOC_ROOT . "checksession.php" );
//  }


//  // hente ut rettigheter
//  {    
//      $session = new eZSession();
    
//      if ( !$session->get( $AuthenticatedSession ) )
//      {
//          die( "Du m� logge deg p�." );    
//      }        
    
//      $usr = new eZUser();
//      $usr->get( $session->userID() );

//      $usrGroup = new eZUserGroup();
//      $usrGroup->get( $usr->group() );
//  }

//  // vise feilmelding dersom brukeren ikke har rettigheter.
//  if ( $usrGroup->addressTypeAdmin() == 'N' )
//  {    
//      $t = new Template( "." );
//      $t->set_file( array(
//          "error_page" => $DOC_ROOT . "templates/errorpage.tpl"
//          ) );

//      $t->set_var( "error_message", "Du har ikke rettiheter til dette." );
//      $t->pparse( "output", "error_page" );
//  }
//  else
{
    $t = new eZTemplate( $DOC_ROOT . "/" . $ini->read_var( "eZContactMain", "TemplateDir" ), $DOC_ROOT . "/intl", $Language, "addresstypeedit.php" );
    $t->setAllStrings();

    $t->set_file( array(
        "address_type_edit_page" =>  "addresstypeedit.tpl"
        ) );    

    $t->set_var( "submit_text", "Legg til" );
    $t->set_var( "action_value", "insert" );
    $t->set_var( "address_type_id", "" );
    $t->set_var( "head_line", "Legg til ny addressetype" );

// Editere
    if ( $Action == "edit" )
    {
        $type = new eZAddressType();
        $type->get( $AID );
        $type->name( $AddressTypeName );
    
        $t->set_var( "submit_text", "Lagre endringer" );
        $t->set_var( "action_value", "update" );
        $t->set_var( "address_type_id", $AID  );  
        $t->set_var( "head_line", "Rediger addressetype");

        $AddressTypeName = $type->name();
    }

// Sette template variabler
    $t->set_var( "document_root", $DOC_ROOT );
    $t->set_var( "address_type_name", $AddressTypeName );

    $t->pparse( "output", "address_type_edit_page" );
}
?>
