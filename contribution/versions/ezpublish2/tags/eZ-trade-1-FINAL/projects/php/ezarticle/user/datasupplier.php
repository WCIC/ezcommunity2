<?

$PageCaching = $ini->read_var( "eZArticleMain", "PageCaching" );

switch ( $url_array[2] )
{
    case "archive":
    {
        $CategoryID = $url_array[3];
        if  ( !isset( $CategoryID ) || ( $CategoryID == "" ) )
            $CategoryID = 0;

        if ( $PageCaching == "enabled" )
        {
            $CategoryID = $url_array[3];

            $cachedFile = "ezarticle/cache/articlelist," . $CategoryID . ".cache";

            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else
            {
                $GenerateStaticPage = "true";
                
                include( "ezarticle/user/articlelist.php" );
            }            
        }
        else
        {
            include( "ezarticle/user/articlelist.php" );
        }
        
    }
    break;


    case "search":
    {
        include( "ezarticle/user/search.php" );
    }
    break;

    case "articleheaderlist":
    {
        $CategoryID = $url_array[3];
        if  ( !isset( $CategoryID ) || ( $CategoryID == "" ) )
            $CategoryID = 0;

        include( "ezarticle/user/articleheaderlist.php" );
       
    }
    break;
    
    case "view":
    case "articleview":
    {
        $StaticRendering = false;        
        $ArticleID = $url_array[3];
        $PageNumber= $url_array[4];
        
        if ( !isset( $PageNumber ) || ( $PageNumber == "" ) ||  ( $PageNumber < 1 ))
            $PageNumber= 1;
        
        if ( $PageCaching == "enabled" )
        {
            $CategoryID = $url_array[3];

            $cachedFile = "ezarticle/cache/articleview," . $ArticleID . ",". $PageNumber .".cache";
            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else
            {
                $GenerateStaticPage = "true";
                
                include( "ezarticle/user/articleview.php" );
            }
        }
        else
        {
            include( "ezarticle/user/articleview.php" );
        }
    }
    break;
    

    case "static":
    case "articlestatic":
    {
        $StaticRendering = true;
        $ArticleID = $url_array[3];
        $PageNumber= $url_array[4];
        
        if ( !isset( $PageNumber ) || ( $PageNumber == "" ) ||  ( $PageNumber < 1 ) )
            $PageNumber= 1;
        
        if ( $PageCaching == "enabled" )
        {
            $CategoryID = $url_array[3];

            $cachedFile = "ezarticle/cache/articleview," . $ArticleID . ",". $PageNumber .".cache";
            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else
            {
                $GenerateStaticPage = "true";
                
                include( "ezarticle/user/articleview.php" );
            }            
        }
        else
        {
            include( "ezarticle/user/articleview.php" );
        }
    }
    break;
    
}

?>
