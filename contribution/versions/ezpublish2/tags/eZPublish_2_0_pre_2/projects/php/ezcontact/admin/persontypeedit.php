<?
/*
  Redigerer person typer.
*/

include_once( "classes/INIFile.php" );
include_once( "classes/ezhttptool.php" );

$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZContactMain", "Language" );
$DOC_ROOT = $ini->read_var( "eZContactMain", "DocumentRoot" );

include_once( "classes/eztemplate.php" );
include_once( "common/ezphputils.php" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezuser/classes/ezmodule.php" );
include_once( "ezuser/classes/ezpermission.php" );

include_once( "ezcontact/classes/ezperson.php" );
include_once( "ezcontact/classes/ezpersontype.php" );

require( "ezuser/admin/admincheck.php" );

// Legge til
if ( $Action == "insert" )
{
    if ( eZPermission::checkPermission( $user, "eZContact", "AdminAdd" ) )
    {
        $type = new eZPersonType();
        $type->setName( $PersonTypeName );
        $type->setDescription( $PersonTypeDescription );
        $type->store();

        eZHTTPTool::header( "Location: /contact/persontypelist/" );
    }
    else
    {
        print( "Du har ikke rettigheter.");
    }
}

// Oppdatere
if ( $Action == "update" )
{
    if ( eZPermission::checkPermission( $user, "eZContact", "AdminModify" ) )
    {
        $type = new eZPersonType();
        $type->get( $PID );
        print ( "$PID ..." );

        $type->setName( $PersonTypeName );
        $type->setDescription( $PersonTypeDescription );
        $type->update();

        eZHTTPTool::header( "Location: /contact/persontypelist/" );
    }
    else
    {
        print( "Du har ikke rettigheter.");
    }
}

// Slette
if ( $Action == "delete" )
{
    if ( eZPermission::checkPermission( $user, "eZContact", "AdminDelete" ) )
    {
        $type = new eZPersonType();
        $type->get( $PID );
        $type->delete( );
        eZHTTPTool::header( "Location: /contact/persontypelist/" );
    }
    else
    {
        print( "Du har ikke rettigheter.");
    }
}

$t = new eZTemplate( $DOC_ROOT . "/" . $ini->read_var( "eZContactMain", "TemplateDir" ), $DOC_ROOT . "/intl", $Language, "persontypeedit.php" );
$t->setAllStrings();

$t->set_file( array(
    "persontype_edit_page" => "persontypeedit.tpl"
    ) );    

$t->set_var( "submit_text", "Legg til" );
$t->set_var( "action_value", "insert" );
$t->set_var( "persontype_id", "" );
$t->set_var( "head_line", "Legg til ny persontype" );

// Editere
if ( $Action == "edit" )
{
    $type = new eZPersonType();
    $type->get( $PID );
  
    $PersonTypeName = $type->name();
    $PersonTypeDescription = $type->description();

    $t->set_var( "submit_text", "Lagre endringer" );
    $t->set_var( "action_value", "update" );
    $t->set_var( "persontype_id", $PID );
    $t->set_var( "head_line", "Rediger persontype" );

}

// Sette tempalte variabler
$t->set_var( "document_root", $DOC_ROOT );
$t->set_var( "persontype_name", $PersonTypeName );
$t->set_var( "description", $PersonTypeDescription );

$t->pparse( "output", "persontype_edit_page" );
?>
