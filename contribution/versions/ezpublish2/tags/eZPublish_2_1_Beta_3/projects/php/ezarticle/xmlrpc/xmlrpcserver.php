<?
ob_end_clean();
ob_start();

// site information
include_once( "classes/INIFile.php" );
$ini = new INIFile( "site.ini" );
$GlobalSiteIni =& $ini;


include_once( "classes/ezlocale.php" );


// eZ user
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezpermission.php" );

// eZ article classes
include_once( "ezarticle/classes/ezarticlecategory.php" );
include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezarticlegenerator.php" );
include_once( "ezarticle/classes/ezarticlerenderer.php" );


// include the server
include_once( "ezxmlrpc/classes/ezxmlrpcserver.php" );

// include the datatype(s) we need
include_once( "ezxmlrpc/classes/ezxmlrpcstring.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcint.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcdouble.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcarray.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcresponse.php" );

// for payment information
include_once( "eztrade/classes/ezcheckout.php" );

$server = new eZXMLRPCServer( );

$server->registerFunction( "newArticle",
                           array( new eZXMLRPCString(),
                                  new eZXMLRPCString(),
                                  new eZXMLRPCString() ) );

$server->registerFunction( "login",
                           array( new eZXMLRPCString(),
                                  new eZXMLRPCString() ) );

$server->registerFunction( "articleCategoryTree",
                           array( new eZXMLRPCString(),
                                  new eZXMLRPCString() ) );



// process the server requests
$server->processRequest();

//
// Will add a new article to the archive.
// The ID of the article will be returned.
//
function &newArticle( $args )
{
//      $user = new eZUser();
//      $user = $user->validateUser( $args[0]->value(), $args[1]->value() );

    $title = $args[0]->value();
    $contents = $args[1]->value();
    $readMore = $args[2]->value();

    print( $title );
    print( $body );
    print( $readMore );


    $generator = new eZArticleGenerator();

    $user = new eZUser( 2 );
    $article = new eZArticle( );
    $article->setAuthor( $user );

    $article->setName( $title );    
    $article->setContents( $contents );

    // only one page
    $article->setPageCount( 1 );
    
    $article->setAuthorText( "XML-RPC imported article" );    
    $article->setLinkText( $readMore );

    $article->store(); // to get the ID

    $WriteGroupArray[0] = 0;
    if( isset( $WriteGroupArray ) )
    {
        if( $WriteGroupArray[0] == 0 )
        {
            eZObjectPermission::setPermission( -1, $article->id(), "article_article", 'w' );
        }
        else
        {
            eZObjectPermission::removePermissions( $article->id(), "article_article", 'w' );
            foreach ( $WriteGroupArray as $groupID )
            {
                eZObjectPermission::setPermission( $groupID, $article->id(), "article_article", 'w' );
            }
        }
    }
    else
    {
        eZObjectPermission::removePermissions( $article->id(), "article_article", 'w' );
    }
    
    $GroupArray[0] = 0;
    /* read access thingy */
    if ( isset( $GroupArray ) )
    {
        if( $GroupArray[0] == 0 )
        {
            eZObjectPermission::setPermission( -1, $article->id(), "article_article", 'r' );
        }
        else // some groups are selected.
        {
            eZObjectPermission::removePermissions( $article->id(), "article_article", 'r' );
            foreach ( $GroupArray as $groupID )
            {
                eZObjectPermission::setPermission( $groupID, $article->id(), "article_article", 'r' );
            }
        }
    }
    else
    {
        eZObjectPermission::removePermissions( $article->id(), "article_article", 'r' );
    }
    
    
    $article->setIsPublished( true );

    // no keywords..
    $article->setKeywords( "" );        
    $article->store();

    // add to categories
    $category = new eZArticleCategory( 1 );
    $category->addArticle( $article );
    
    $article->setCategoryDefinition( $category );
    
    $articleID = $article->id();

    $tmp = new eZXMLRPCInt( $articleID );
    
    return $tmp;
}

function &login( $args )
{
    $user = new eZUser();
    $user = $user->validateUser( $args[0]->value(), $args[1]->value() );
    
    if ( ( get_class( $user ) == "ezuser" ) and eZPermission::checkPermission( $user, "eZUser", "AdminLogin" ) )
    {

        $ret = new eZXMLRPCString( "Login success" );
    }
    else
    {
        $ret = new eZXMLRPCResponse( );
        $ret->setError( 100, "Authorization failed." );
    }

    return $ret;
}

function &articleCategoryTree( $args )
{
    $user = new eZUser();
    $user = $user->validateUser( $args[0]->value(), $args[1]->value() );
    
    if ( ( get_class( $user ) == "ezuser" ) and eZPermission::checkPermission( $user, "eZUser", "AdminLogin" ) )
    {        
        $category = new eZArticleCategory();
        $categoryTree =& $category->getTree();

        $cat = array();
        foreach ( $categoryTree as $catItem )
        {
            $cat[] = new eZXMLRPCStruct( array( "ID" => new eZXMLRPCInt( $catItem[0]->id() ),
                                                "ParentID" => new eZXMLRPCInt( $catItem[0]->parent( false ) ),
                                                "Name" => new eZXMLRPCString( $catItem[0]->name() ),
                                                "Level" => new eZXMLRPCInt( $catItem[1] ) ) );

        }        
        
        $ret = new eZXMLRPCArray( $cat );
        
    }
    else
    {
        $ret = new eZXMLRPCResponse( );
        $ret->setError( 100, "Authorization failed." );
    }

    return $ret;
}



ob_end_flush();
exit();
?>
