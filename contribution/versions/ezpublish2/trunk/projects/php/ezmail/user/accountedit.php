<?
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "classes/ezhttptool.php" );

include_once( "ezmail/classes/ezmailaccount.php" );

if( !eZMailAccount::isOwner( eZUser::currentUser(), $AccountID ) )
    $AccountID = 0;

if( isset( $Ok ) )
{
    if( $AccountID == 0 )
    {
        $account = new eZMailAccount();
        $account->setUser( eZUser::currentUser() );
        $account->setIsActive( true );
    }
    else
    {
        $account = new eZMailAccount( $AccountID );
    }

    $account->setName( $Name );
    $account->setPassword( $Password );
    $account->setLoginName( $Login );
    $account->setServer( $Server );
    if( isset( $DelFromServer ) )
        $account->setDeleteFromServer( true );
    else
        $account->setDeleteFromServer( false );

    $account->store();
    eZHTTPTool::header( "Location: /mail/config" );
    exit();
}

if( isset( $Cancel ) )
{
    eZHTTPTool::header( "Location: /mail/config" );
    exit();
}


$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZMailMain", "Language" ); 

$t = new eZTemplate( "ezmail/user/" . $ini->read_var( "eZMailMain", "TemplateDir" ),
                     "ezmail/user/intl/", $Language, "accountedit.php" );
$t->setAllStrings();

$t->set_file( array(
    "account_edit_page_tpl" => "accountedit.tpl"
    ) );

$t->set_var( "current_account_id", $AccountID );
$t->set_var( "name_value", "" );
$t->set_var( "login_value", "" );
$t->set_var( "password_value", "" );
$t->set_var( "port_value", "110" );
$t->set_var( "server_value" ,"" );
$t->set_var( "delete_from_server_checked", "" );

if( $AccountID != 0 ) // TODO: check that user really is the owner of the account.
{
    $account = new eZMailAccount( $AccountID );
    $t->set_var( "name_value", htmlspecialchars( $account->name() ) );
    $t->set_var( "login_value", htmlspecialchars( $account->loginName() ) );
    $t->set_var( "password_value", htmlspecialchars( $account->password() ) );
    $t->set_var( "server_value", htmlspecialchars( $account->server() ) );
    if( $account->deleteFromServer() )
        $t->set_var( "delete_from_server_checked", "checked" );
}


$t->pparse( "output", "account_edit_page_tpl" );
?>
