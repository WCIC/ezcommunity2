<?

switch ( $url_array[2] )
{
    case "archive" :        
    {
        $ModuleID = $url_array[3];
        $Action = "";
        include( "ezbug/admin/buglist.php" );
    }
    break;

    case "search" :        
    {
        include( "ezbug/admin/search.php" );
    }
    break;
    
    case "bugpreview" :
    case "view" :        
    {
        $BugID = $url_array[3];
        
        include( "ezbug/user/bugview.php" );
    }
    break;
    
    case "unhandled" :
    {
        include( "ezbug/admin/unhandledbugs.php" );
    }
    break;

    case "priority" :
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                include( "ezbug/admin/prioritylist.php" );
            }
            break;
        }
    }
    break;

    case "category" :
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                include( "ezbug/admin/categorylist.php" );
            }
            break;
        }
    }
    break;

    case "module" :
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                if( isset( $AddModule ) )  // new
                {
                    $Action = "new";
                    $ParentID = $url_array[4];
                    include( "ezbug/admin/moduleedit.php" );
                }
                else 
                {
                    $ParentID = $url_array[4];
                    include( "ezbug/admin/modulelist.php" );
                }
            }
            break;

            case "insert":
            {
                $Action = "insert";
                include( "ezbug/admin/moduleedit.php" );
            }
            break;

            case "edit":
            {
                $Action = "edit";
                $ModuleID = $url_array[4];
                include( "ezbug/admin/moduleedit.php" );
            }
            break;

            case "update":
            {
                $Action = "update";
                $ModuleID = $url_array[4];
                include( "ezbug/admin/moduleedit.php" );
            }
            break;

            case "delete":
            {
                $Action = "delete";
                $ModuleID = $url_array[4];
                include( "ezbug/admin/moduleedit.php" );
            }
            break;

        }
    }
    break;

    case "status" :
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                $ParentID = $url_array[4];
                include( "ezbug/admin/statuslist.php" );
            }
            break;
        }
    }
    break;

    
    case "edit" :
    {
        if ( $url_array[3] == "new" )
        {
            $Action = "New";
        }
        else if ( $url_array[3] == "edit" )
        {
            $Action = "Edit";
            $BugID = $url_array[4];
        }
        else if( $url_array[3] == "fileedit" )
        {
            switch( $url_array[4] )
            {
                case  "new" :
                {
                    $Action = "New";
                    $BugID = $url_array[5];
                    include( "ezbug/admin/fileedit.php" );
                }
                break;
                case  "edit" :
                {
                    $Action = "Edit";
                    $BugID = $url_array[6];
                    $FileID = $url_array[5];
                    include( "ezbug/admin/fileedit.php" );
                }
                break;
                case "delete" :
                {
                    $Action = "Delete";
                    $BugID = $url_array[6];
                    $FileID = $url_array[5];
                    include( "ezbug/admin/fileedit.php" );
                }
                break;
                default :
                {
                    include( "ezbug/admin/fileedit.php" );
                }
                break;
            }
        }
        else if( $url_array[3] == "imageedit" )
        {
            switch( $url_array[4] )
            {
                case "new":
                {
                    $Action = "New";
                    $BugID = $url_array[5];
                    include( "ezbug/admin/imageedit.php" );
                }
                break;
                case "edit" :
                {
                    $Action = "Edit";
                    $BugID = $url_array[6];
                    $ImageID = $url_array[5];
                    include( "ezbug/admin/imageedit.php" );
                }
                break;
                case "delete" :
                {
                    $Action = "Delete";
                    $BugID = $url_array[6];
                    $ImageID = $url_array[5];
                    include( "ezbug/admin/imageedit.php" );
                }
                break;
                default :
                {
                    include( "ezbug/admin/imageedit.php" );
                }
                break;
            }
        }
        include( "ezbug/admin/bugedit.php" );
    }
    break;


    case "report" :
    {
        switch( $url_array[3] )
        {
            case "fileedit" :
            {
                if( $url_array[4] == "new")
                {
                    $Action = "New";
                    $BugID = $url_array[5];
                    include( "ezbug/user/fileedit.php" );
                }
                else if( $url_array[4] == "edit" )
                {
                    $Action = "Edit";
                    $BugID = $url_array[6];
                    $FileID = $url_array[5];
                    include( "ezbug/user/fileedit.php" );
                }
                else if( $url_array[4] == "delete" )
                {
                    $Action = "Delete";
                    $BugID = $url_array[6];
                    $FileID = $url_array[5];
                    include( "ezbug/user/fileedit.php" );
                }
                else
                {
                    include( "ezbug/user/fileedit.php" );
                }
            }
            break;
            case "imageedit" :
            {
                if( $url_array[4] == "new")
                {
                    $Action = "New";
                    $BugID = $url_array[5];
                    include( "ezbug/user/imageedit.php" );
                }
                else if( $url_array[4] == "edit" )
                {
                    $Action = "Edit";
                    $BugID = $url_array[6];
                    $ImageID = $url_array[5];
                    include( "ezbug/user/imageedit.php" );
                }
                else if( $url_array[4] == "delete" )
                {
                    $Action = "Delete";
                    $BugID = $url_array[6];
                    $ImageID = $url_array[5];
                    include( "ezbug/user/imageedit.php" );
                }
                else
                {
                    include( "ezbug/user/imageedit.php" );
                }
            }
            break;

            case "edit" :
            {
                $BugID = $url_array[4];
                $Action = "Edit";
                include( "ezbug/admin/bugedit.php" );
            }
            break;

            
            default :
            {
                print( "Error: Bug file not found" );
            }
            break;
        }
    }
    break;
}
?>
