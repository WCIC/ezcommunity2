<?php

$PageCaching = $ini->read_var( "eZNewsFeedMain", "PageCaching" );

switch ( $url_array[2] )
{
    case "latest":
    {
        $CategoryID = $url_array[3];
        if  ( !isset( $CategoryID ) || ( $CategoryID == "" ) )
            $CategoryID = 0;

        if ( $PageCaching == "enabled" )
        {
            $CategoryID = $url_array[3];
            $cachedFile = "eznewsfeed/cache/latestnews," . $CategoryID . ".cache";

            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else
            {
                $GenerateStaticPage = "true";
                include( "eznewsfeed/user/newslist.php" );
            }            
        }
        else
        {
            include( "eznewsfeed/user/newslist.php" );
        }
    }
    break;

    case "search":
    {
        include( "eznewsfeed/user/search.php" );
    }
    break;
}

?>
