<?
// $url_array = explode( "/", $REQUEST_URI );

$user =& eZUser::currentUser();
include_once( "ezuser/classes/ezpermission.php" );
include_once( "classes/ezhttptool.php" );

if( eZPermission::checkPermission( $user, "eZLink", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

switch ( $url_array[2] )
{
    case "" :
    {
        include( "ezlink/admin/linkcategorylist.php" );
    }
    break;
    case "link" :
    {
        $LID = $url_array[3];
        include( "ezlink/admin/linkcategorylist.php" );
    }
    break;

    case "typelist" :
    {
        include( "ezlink/admin/typelist.php" );
    }
    break;
 
    case "typeedit" :
    {
        if ( $url_array[3] == "edit" )
        {
            $TypeID = $url_array[4];
            $Action = "Edit";
        }
        if ( $url_array[3] == "delete" )
        {
            $TypeID = $url_array[4];
            $Action = "Delete";
        }
        if ( $url_array[3] == "up" )
        {
            $TypeID = $url_array[4];
            $AttributeID = $url_array[5];
            $Action = "up";
        }
        if ( $url_array[3] == "down" )
        {
            $TypeID = $url_array[4];
            $AttributeID = $url_array[5];
            $Action = "down";
        }
 
        include( "ezlink/admin/typeedit.php" );
    }
    break;
    
    case "category" :
    {
        if ( $url_array[4] == "parent" )
            $Offset = $url_array[5];
        $LinkCategoryID = $url_array[3];
        include( "ezlink/admin/linkcategorylist.php" );
    }
    break;

    case "unacceptedlist":
    {
        if ( $url_array[3] )
            $Offset = $url_array[3];
        include( "ezlink/admin/unacceptedlist.php" );
    }
    break;
    case "unacceptededit":
    {
        include( "ezlink/admin/unacceptededit.php" );
    }
    break;
    
    case "linkedit" :
    {
        switch ( $url_array[3] )
        {
            case "new" :
            {
                $Action = "new";
                include( "ezlink/admin/linkedit.php" );
            }
            break;
            
            case "insert" :
            {
                $LinkID = $url_array[4];
                if ( isSet( $Update ) )
                {
                    $Action = "AttributeList";
                }
                else
                {
                    $Action = "insert";
                }
                include( "ezlink/admin/linkedit.php" );
            }
            break;
            
            case "edit" :
            {
                $LinkID = $url_array[4];
                $Action = "edit";
                include( "ezlink/admin/linkedit.php" );
            }
            break;
            
            case "update" :
            {
                $LinkID = $url_array[4];
                if ( isSet( $Update ) )
                {
                    $Action = "AttributeList";
                }
                else
                {
                    $Action = "insert";
                }
                include( "ezlink/admin/linkedit.php" );
            }
            break;
            
            case "delete" :
            {
                $LinkID = $url_array[4];
                $Action = "delete";
                include( "ezlink/admin/linkedit.php" );
            }
            break;
            
            case "attributeedit" :
            {
                $LinkID = $url_array[4];
                include( "ezlink/admin/attributeedit.php" );
            }
            break;
        }
    }
    break;

    case "categoryedit" :
    {
        if ( $url_array[3] == "new" )
        {
            $Action = "new";
            include( "ezlink/admin/categoryedit.php" );
        }
        else if ( $url_array[3] == "insert" )
        {
            $LinkCategoryID = $url_array[4];
            $Action = "insert";
            include( "ezlink/admin/categoryedit.php" );
        }

        else if ( $url_array[3] == "edit" )
        {
            $LinkCategoryID = $url_array[4];
            $Action = "edit";
            include( "ezlink/admin/categoryedit.php" );
        }
        else if ( $url_array[3] == "update" )
        {
            $LinkCategoryID = $url_array[4];
            $Action = "update";
            include( "ezlink/admin/categoryedit.php" );
        }
        else if ( $url_array[3] == "delete" )
        {
            $LinkCategoryID = $url_array[4];
            $Action = "delete";
            include( "ezlink/admin/categoryedit.php" );
        }
    }
    break;
    case "testbench" :
        include( "eztrade/admin/testbench.php" );
        break;
    case "search" :
    {
        if ( $url_array[3] == "parent" )
        {
            $QueryString = urldecode( $url_array[4] );
            $Offset = $url_array[5];
        }
        include( "ezlink/admin/search.php" );
    }
        break;
    case "norights" :
        include( "ezlink/admin/norights.php" );        
        break;
    case "gotolink" :
    {
        $Action = $url_array[3];
        $LinkID = $url_array[4];
        include( "ezlink/admin/gotolink.php" );
    }
    break;


    default :
        print( "<h1>Sorry, Your link page could not be found. </h1>" );
        break;
}

?>
