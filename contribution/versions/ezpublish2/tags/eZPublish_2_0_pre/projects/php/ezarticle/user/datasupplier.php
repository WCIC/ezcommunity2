<?

include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezarticlecategory.php" );

include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );

include_once( "ezuser/classes/ezuser.php" );

$PageCaching = $ini->read_var( "eZArticleMain", "PageCaching" );
$UserComments = $ini->read_var( "eZArticleMain", "UserComments" );

switch ( $url_array[2] )
{
    case "author":
    {
        $Action = $url_array[3];
        switch( $Action )
        {
            case "list":
            {
                $SortOrder = $url_array[4];
                include( "ezarticle/user/authorlist.php" );
                break;
            }
            case "view":
            {
                $AuthorID = $url_array[4];
                $SortOrder = $url_array[5];
                $Offset = $url_array[6];
                include( "ezarticle/user/authorview.php" );
                break;
            }
        }
        break;
    }

    case "archive":
    {
        $CategoryID = $url_array[3];
        if  ( !isset( $CategoryID ) || ( $CategoryID == "" ) )
            $CategoryID = 0;

        $Offset = $url_array[4];
        if  ( !is_numeric( $Offset ) )
            $Offset = 0;

        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if( get_class( $user ) == "ezuser" )
        {
            $groupIDArray = $user->groups( true );
            sort( $groupIDArray );
            $first = true;
            foreach( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = 0;
//        print( "Checking category: $CategoryID <br>" );
        if ( $PageCaching == "enabled" )
        {
            //$CategoryID = $url_array[3];

            include_once( "classes/ezcachefile.php" );
            $file = new eZCacheFile( "ezarticle/cache/", array( "articlelist", $CategoryID, $Offset, $groupstr ),
                                     "cache", "," );
            $cachedFile = $file->filename( true );
//            print( "Cache file name: $cachedFile" );
            if ( $file->exists() )
            {
                include( $cachedFile );
            }
            else if( $CategoryID == 0 || eZObjectPermission::hasPermission( $CategoryID, "article_category", 'r' ) ||
                     eZArticleCategory::isOwner( $user, $CategoryID) )
                             // check if user really has permissions to browse this category
            {
                $GenerateStaticPage = "true";
                
                include( "ezarticle/user/articlelist.php" );
            }            
        }
        else if( $CategoryID == 0 || eZObjectPermission::hasPermission( $CategoryID, "article_category", 'r' ) ||
                 eZArticleCategory::isOwner( $user, $CategoryID) )
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
        
        if ( $PageNumber != -1 )
            if ( !isset( $PageNumber ) || ( $PageNumber == "" ) ||  ( $PageNumber < 1 ))
                $PageNumber= 1;
        
        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if( get_class( $user ) == "ezuser" )
        {
            $groupIDArray = $user->groups( true );
            sort( $groupIDArray );
            $first = true;
            foreach( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = 0;
        
        
        if ( $PageCaching == "enabled" )
        {
            $CategoryID = $url_array[3];

            $cachedFile = "ezarticle/cache/articleview," . $ArticleID . ",". $PageNumber . "," .$groupstr  .".cache";
            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else if( eZObjectPermission::hasPermission( $ArticleID, "article_article", 'r' )
                     || eZArticle::isAuthor( $user, $ArticleID ) )
            {
                $GenerateStaticPage = "true";
                
                include( "ezarticle/user/articleview.php" );
            }
        }
        else if( eZObjectPermission::hasPermission( $ArticleID, "article_article", 'r' )
        || eZArticle::isAuthor( $user, $ArticleID ) )
        {
            include( "ezarticle/user/articleview.php" );
        }

        /* Should there be permissions here? */
        if  ( ( $PrintableVersion != "enabled" ) && ( $UserComments == "enabled" ) )
        {
            $RedirectURL = "/article/view/$ArticleID/$PageNumber/";
            $article = new eZArticle( $ArticleID );
            if( $article->id() >= 1 )
            {
                $forum = $article->forum();
                $ForumID = $forum->id();
                include( "ezforum/user/messagesimplelist.php" );
            }
        }        
    }
    break;

    case "articleuncached":
    {
        $ViewMode = "static";

        $StaticRendering = true;
        $ArticleID = $url_array[3];
        $PageNumber= $url_array[4];
        
        if ( !isset( $PageNumber ) || ( $PageNumber == "" ) ||  ( $PageNumber < 1 ) )
            $PageNumber= 1;
        
        include( "ezarticle/user/articleview.php" );
    }
    break;

    case "print":
    case "articleprint":
    {
        $StaticRendering = false;
        $ArticleID = $url_array[3];
        $PageNumber= $url_array[4];

        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if( get_class( $user ) == "ezuser" )
        {
            $groupIDArray = $user->groups( true );
            sort( $groupIDArray );
            $first = true;
            foreach( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = 0;

        
        if ( $PageNumber != -1 )
        {
            if ( !isset( $PageNumber ) || ( $PageNumber == "" ) )
                $PageNumber = -1;
            else if ( $PageNumber < 1 )
                $PageNumber = 1;
        }

        if ( $PageCaching == "enabled" )
        {
            $CategoryID = $url_array[3];

            $cachedFile = "ezarticle/cache/articleprint," . $ArticleID . ",". $PageNumber . "," . $groupstr . ".cache";
            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else if( eZObjectPermission::hasPermission( $ArticleID, "article_article", 'r' )
                     || eZArticle::isAuthor( $user, $ArticleID ) )
            {
                $GenerateStaticPage = "true";
                
                include( "ezarticle/user/articleview.php" );
            }
        }
        else if( eZObjectPermission::hasPermission( $ArticleID, "article_article", 'r' )
                 || eZArticle::isAuthor( $user, $ArticleID ) )
        {
            include( "ezarticle/user/articleview.php" );
        }
    }
    break;

    case "static":
    case "articlestatic":
    {
        $ViewMode = "static";

        $StaticRendering = true;
        $ArticleID = $url_array[3];
        $PageNumber= $url_array[4];

        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if( get_class( $user ) == "ezuser" )
        {
            $groupIDArray = $user->groups( true );
            sort( $groupIDArray );
            $first = true;
            foreach( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = 0;
        
        if ( !isset( $PageNumber ) || ( $PageNumber == "" ) ||  ( $PageNumber < 1 ) )
            $PageNumber= 1;
        
        if ( $PageCaching == "enabled" )
        {
            $CategoryID = $url_array[3];

            $cachedFile = "ezarticle/cache/articleview," . $ArticleID . ",". $PageNumber . "," . $groupstr .".cache";
            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else if( eZObjectPermission::hasPermission( $ArticleID, "article_article", 'r' )
                     || eZArticle::isAuthor( $user, $ArticleID ) )
            {
                $GenerateStaticPage = "true";
                
                include( "ezarticle/user/articleview.php" );
            }            
        }
        else if( eZObjectPermission::hasPermission( $ArticleID, "article_article", 'r' )
                 || eZArticle::isAuthor( $user, $ArticleID ) )
        {
            include( "ezarticle/user/articleview.php" );
        }
    }
    break;

    case "rssheadlines":
    {
        include( "ezarticle/user/articlelistrss.php" );
    }
    break;

    case "articleedit":
    {
        if ( eZUser::currentUser() != false &&
             $ini->read_var( "eZArticleMain", "UserSubmitArticles" ) == "enabled" )
        {
            switch ( $url_array[3] )
            {
                case "new":
                {
                    $Action = "New";
                    include( "ezarticle/user/articleedit.php" );
                    break;
                }
                case "insert":
                {
                    $Action = "Insert";
                    $ArticleID = $url_array[4];
                    include( "ezarticle/user/articleedit.php" );
                    break;
                }
                case "cancel":
                {
                    $Action = "Cancel";
                    $ArticleID = $url_array[4];
                    include( "ezarticle/user/articleedit.php" );
                    break;
                }
            }
        }
        else
        {
            include_once( "classes/ezhttptool.php" );
            eZHTTPTool::header( "Location: /article/archive/" );
            exit();
        }
    }
    break;

}

?>
