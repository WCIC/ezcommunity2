<?php
ob_start();

// brukes for sider som skal redirectes..
if ( file_exists( $prePage ) )
{
  include( $prePage );
  die();
}

?>

<?php

include_once( "../classes/ezdb.php" );
include_once( "classes/INIFile.php" );
include_once( "classes/template.inc" );
include_once( "../classes/ezuser.php" );


//include_once( "../ezpublish/settings/dbconnect.php" );
//include_once( "../dbsettings.php" );

include_once( "../classes/ezsession.php" );
include_once( "../common/ezphputils.php" );

$session = new eZSession();

$ini = new INIFile( "../site.ini" );
$Db = $ini->read_var( "eZPublishMain", "Database");
$t = new Template( "." );


// html header
include( "header.php" );

if( $session->get( $AuthenticatedSession ) == 0 )
{

    if ( $ini->read_var( "site", "eZPublish" ) == "enabled" )
        include( "ezpublishadmin.php" );

    if ( $ini->read_var( "site", "eZForum" ) == "enabled" )
        include( "ezforumadmin.php" );

    if ( $ini->read_var( "site", "eZLink" ) == "enabled" )
        include( "ezlinkadmin.php" );

    if ( $ini->read_var( "site", "eZContact" ) == "enabled" )
        include( "ezcontactadmin.php" );

    if ( $Ini->read_var( "site", "eZTodo" ) == "enabled" )
        include( "eztodoadmin.php" );

    if ( $ini->read_var( "site", "eZTrade" ) == "enabled" )
        include( "../eztrade/admin/eztradeadminmenu.php" );

    include( "useradmin.php" );

    $user = new eZUser( $session->userID() );
    print( "<p class=\"small\"><b>User:</b><br>" . $user->firstName() . " " . $user->lastName() . "</p>" );

    // break the column an draw a horizontal line
    include( "separator.php" );
    
    
    if( file_exists( $page ) )
    {
        include( $page );
    }
    else
    {
        include( "main.php" );
    }
}
else
{
    $t->set_file( "login", "./templates/login.tpl" );
    if( !isset( $message ) )
    {
        $message = "Skriv inn brukernavn og passord!";
    }
    
    $t->set_var( "text", $message );
    $t->pparse( "output", "login" );
}

// html footer
include( "footer.php" );
?>

<?php
ob_end_flush();
?>
