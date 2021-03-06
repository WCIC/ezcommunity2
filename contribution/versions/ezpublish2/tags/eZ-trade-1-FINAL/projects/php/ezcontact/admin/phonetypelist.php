<?
/*
  Viser liste over kontakt typer.
*/
include_once( "classes/INIFile.php" );

$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZContactMain", "Language" );
$DOC_ROOT = $ini->read_var( "eZContactMain", "DocumentRoot" );

include_once( "classes/eztemplate.php" );
include_once( "common/ezphputils.php" );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezuser/classes/ezmodule.php" );
include_once( "ezuser/classes/ezpermission.php" );

include_once( "../ezcontact/classes/ezphonetype.php" );

require( "ezuser/admin/admincheck.php" );

$t = new eZTemplate( $DOC_ROOT . "/" . $ini->read_var( "eZContactMain", "TemplateDir" ), $DOC_ROOT . "/intl", $Language, "phonetypelist.php" );
$t->setAllStrings();

$t->set_file( array(
    "phone_type_page" =>  "phonetypelist.tpl",
    "phone_type_item" =>  "phonetypeitem.tpl"
    ) );

$phone_type = new eZPhoneType();
$phone_type_array = $phone_type->getAll();

for ( $i=0; $i<count( $phone_type_array ); $i++ )
{
    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "bg_color", "#eeeeee" );
    }
    else
    {
        $t->set_var( "bg_color", "#dddddd" );
    }  

    $t->set_var( "document_root", $DOC_ROOT );
    $t->set_var( "phone_type_id", $phone_type_array[$i][ "ID" ] );
    $t->set_var( "phone_type_name", $phone_type_array[$i][ "Name" ] );

    $t->parse( "phone_type_list", "phone_type_item", true );
} 

$t->pparse( "output", "phone_type_page" );
?>
