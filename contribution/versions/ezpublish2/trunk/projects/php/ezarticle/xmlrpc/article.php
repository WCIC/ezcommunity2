<?
include_once( "ezarticle/classes/ezarticlecategory.php" );
include_once( "ezarticle/classes/ezarticleattribute.php" );
include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezform/classes/ezform.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcarray.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcbool.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcint.php" );

if( $Command == "data" ) // return all the data in the category
{
    $article = new eZArticle();
    if ( !$article->get( $ID ) )
    {
        $Error = createErrorMessage( EZERROR_NONEXISTING_OBJECT );
    }
    else
    {
        $writeGroups = eZObjectPermission::getGroups( $ID, "article_article", 'w', false );
        $readGroups = eZObjectPermission::getGroups( $ID, "article_article", 'r', false );
        $contentsWriter =& $article->contentsWriter( true );

        $type_arr = array();
        $types =& $article->types();
        foreach( $types as $type )
        {
            $attributes =& $type->attributes();
            if ( count( $attributes ) > 0 )
            {
                $attr_arr = array();
                foreach( $attributes as $attrib )
                {
                    $attr_arr[] = new eZXMLRPCStruct( array( "ID" => new eZXMLRPCInt( $attrib->id() ),
                                                             "Name" => new eZXMLRPCString( $attrib->name() ),
                                                             "Content" => new eZXMLRPCString( $attrib->value( $article ) ) ) );
                }
                $type_arr[] = new eZXMLRPCStruct( array( "ID" => new eZXMLRPCInt( $type->id() ),
                                                         "Name" => new eZXMLRPCString( $type->name() ),
                                                         "Attributes" => new eZXMLRPCArray( $attr_arr ) ) );
            }
        }

        $ret = array( "Location" => createURLStruct( "ezarticle", "article", $article->id() ),
                      "AuthorID" => new eZXMLRPCInt( $article->author( false ) ),
                      "Name" => new eZXMLRPCString( $article->name( false ) ), // title
                      "Contents" => new eZXMLRPCString( $article->contents( false ) ),
                      "ContentsWriterID" => new eZXMLRPCInt( $contentsWriter->id() ),
                      "LinkText" => new eZXMLRPCString( $article->linkText( false ) ),
                      "ManualKeyWords" => new eZXMLRPCString( $article->manualKeywords() ),
                      "Discuss" => new eZXMLRPCBool( $article->discuss() ),
                      "IsPublished" => new eZXMLRPCBool( $article->isPublished() ),
                      "PageCount" => new eZXMLRPCInt( $article->pageCount() ),
                      "Thumbnail" => new eZXMLRPCInt( $article->thumbnailImage( false ) ),
                      "Images" => new eZXMLRPCArray( $article->images( false ), "integer" ),
                      "Files" => new eZXMLRPCArray( $article->files( false ), "integer" ),
                      "Forms" => new eZXMLRPCArray( $article->forms( false ), "integer" ),
                      "ReadGroups" => new eZXMLRPCArray( $readGroups, "integer" ),
                      "WriteGroups" => new eZXMLRPCArray( $writeGroups, "integer" ),
                      "Types" => new eZXMLRPCArray( $type_arr ),
                      "Topic" => new eZXMLRPCInt( $article->topic( false ) )
//                                             "PublishedDate" => new eZXMLRPCStruct(),
                      );
        $start_date = $article->startDate();
        if ( $start_date->isValid() )
            $ret["StartDate"] = createDateTimeStruct( $start_date );
        $stop_date =& $article->stopDate();
        if ( $stop_date->isValid() )
            $ret["StopDate"] = createDateTimeStruct( $stop_date );
        $published =& $article->published();
        if ( $published->isValid() )
            $ret["PublishDate"] = createDateTimeStruct( $published );
        $ReturnData = new eZXMLRPCStruct( $ret );
    }
}
else if( $Command == "storedata" )
{
    $article = new eZArticle();
    if( $ID != 0 )
        $article->get( $ID );

    $article->setAuthor( $Data["AuthorID"]->value() );
    $article->setName( $Data["Name"]->value() ); // title
    $article->setContents( $Data["Contents"]->value() );
    $article->setContentsWriter( $Data["ContentsWriterID"]->value() );
    $article->setLinkText( $Data["LinkText"]->value() );
    $article->setManualKeywords( $Data["ManualKeyWords"]->value() );
    $article->setDiscuss( $Data["Discuss"]->value() );
    $article->setIsPublished( $Data["IsPublished"]->value() );
    $thumbImage = new eZImage( $Data["Thumbnail"]->value() );
    $article->setThumbnailImage( $thumbImage );
    $article->setTopic( $Data["Topic"]->value() );

    if ( isset( $Data["StartDate"] ) )
    {
        $startDate = createDateTime( $Data["StartDate"]->value() );
        $article->setStartDate( $startDate );
    }
    if ( isset( $Data["StopDate"] ) )
    {
        $stopDate = createDateTime( $Data["StopDate"]->value() );
        $article->setStopDate( $stopDate );
    }

    $article->store();
    $ID = $article->id();

    // images
    $images = $Data["Images"]->value();
    $new_images = array();
    foreach( $images as $img )
    {
        $new_images[] = $img->value();
    }
    $images = $article->images( false );
    $old_images = array_diff( $images, $new_images );
    $added_images = array_diff( $new_images, $images );
    $changed_images = array_intersect( $new_images, $images );

    foreach( $old_images as $image )
        $article->deleteImage( $image );
    foreach( $added_images as $image )
        $article->addImage( $image );

    // files
    $files = $Data["Files"]->value();
    $new_files = array();
    foreach( $files as $fl )
    {
        $new_files[] = $fl->value();
    }
    $files =& $article->files( false );
    $old_files = array_diff( $files, $new_files );
    $added_files = array_diff( $new_files, $files );
    $changed_files = array_intersect( $new_files, $files );
    foreach( $old_files as $file )
        $article->deleteFile( $file );
    foreach( $added_files as $file )
        $article->addFile( $file );

    // permissions....
    eZObjectPermission::removePermissions( $ID, "article_article", 'r' );
    $readGroups = $Data["ReadGroups"]->value();
    foreach( $readGroups as $readGroup )
        eZObjectPermission::setPermission( $readGroup->value(), $ID, "article_article", 'r' );

    eZObjectPermission::removePermissions( $ID, "article_article", 'w' );
    $writeGroups = $Data["WriteGroups"]->value();
    foreach( $writeGroups as $writeGroup )
        eZObjectPermission::setPermission( $writeGroup->value(), $ID, "article_article", 'w' );

    // types
    $types = $Data["Types"]->value();
    $old_types = $article->types( false );
    $new_types = array();
    foreach ( $types as $type )
    {
        $type = $type->value();
        $typeID = $type["ID"]->value();
        $new_types[] = $typeID;
        $attrs = $type["Attributes"]->value();
        $attr_map = array();
        foreach( $attrs as $attr )
        {
            $attr = $attr->value();
            $id = $attr["ID"]->value();
            $name = $attr["Name"]->value();
            $content = $attr["Content"]->value();
            $attr_map[$id] = array( "ID" => $id,
                                    "Name" => $name,
                                    "Content" => $content );
        }
        $articleType = new eZArticleType( $typeID );
        $attributes = $articleType->attributes();

        foreach( $attributes as $attribute )
        {
            $id = $attribute->id();
            if ( isset( $attr_map[$id] ) )
            {
                $attribute->setName( $attr_map[$id]["Name"] );
                $attribute->store();
                $attribute->setValue( $article, $attr_map[$id]["Content"] );
            }
        }
    }
    $removed_types = array_diff( $old_types, $new_types );
    foreach( $removed_types as $typeID )
    {
        $type = new eZArticleType();
        if ( $type->get( $typeID ) )
            $type->delete();
    }

    // forms
    $article->deleteForms();
    $forms = $Data["Forms"]->value();
    foreach( $forms as $form )
    {
        $form = new eZForm( $form->value() );
        $article->addForm( $form );
    }

    // categories
    $category = new eZArticleCategory( eZArticle::categoryDefinitionStatic( $ID ) );
    $path =& $category->path();
    if ( $category->id() != 0 )
    {
        $par[] = createURLStruct( "ezarticle", "category", 0 );
    }
    else
    {
        $par[] = createURLStruct( "ezarticle", "" );
    }
    foreach( $path as $item )
    {
        if ( $item[0] != $category->id() )
            $par[] = createURLStruct( "ezarticle", "category", $item[0] );
    }

//      eZArticleTool::deleteCache( $ID, $CategoryID, $CategoryArray );

    $ReturnData = new eZXMLRPCStruct( array( "Location" => createURLStruct( "ezarticle", "article", $ID ),
                                             "Path" => new eZXMLRPCArray( $par ),
                                             "UpdateType" => new eZXMLRPCString( $Command )
                                             )
                                      );
    $Command = "update";

}
else if( $Command == "delete" )
{
    $category = eZArticle::categoryDefinitionStatic( $ID );
    $category = new eZArticleCategory( $category );
    $path =& $category->path();
    if ( $category->id() != 0 )
    {
        $par[] = createURLStruct( "ezarticle", "category", 0 );
    }
    else
    {
        $par[] = createURLStruct( "ezarticle", "" );
    }
    foreach( $path as $item )
    {
        if ( $item[0] != $category->id() )
            $par[] = createURLStruct( "ezarticle", "category", $item[0] );
    }

    $ReturnData = new eZXMLRPCStruct( array( "Location" => createURLStruct( "ezarticle", "article", $ID ),
                                             "Path" => new eZXMLRPCArray( $par ),
                                             "UpdateType" => new eZXMLRPCString( $Command )
                                             )
                                      );
    $Command = "update";

    $article = new eZArticle( $ID );
    $article->delete();
}
?>
