<?
include_once( "classes/ezhttptool.php" );

switch ( $url_array[2] )
{
    case "categorylist":
    {
        include_once( "ezbulkmail/admin/categorylist.php" );
    }
    break;

    case "categoryedit" :
    {
        $CategoryID = $url_array[3];
        if( !is_numeric( $CategoryID ) )
            $CategoryID = 0;
        include_once( "ezbulkmail/admin/categoryedit.php" );
    }
    break;

    case "templatelist" :
    {
        include_once( "ezbulkmail/admin/templatelist.php" );
    }
    break;

    case "templateedit" :
    {
        $TemplateID = $url_array[3];
        if( !is_numeric( $TemplateID ) )
            $TemplateID = 0;
        include_once( "ezbulkmail/admin/templateedit.php" );
    }
    break;

    case "mailedit" :
    {
        $MailID = $url_array[3];
        if( !is_numeric( $MailID ) )
            $MailID = 0;
        include_once( "ezbulkmail/admin/mailedit.php" );
    }
    break;
    
    default:
    {
        eZHTTPTool::header( "Location: /error/404" );
        exit();
    }
    break;
}

?>
