<?
/*!
    $Id: category.php,v 1.18 2000/10/17 13:43:58 ce-cvs Exp $

    Author: Lars Wilhelmsen <lw@ez.no>
    
    Created on: Created on: <14-Jul-2000 13:41:35 lw>
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/
include( "ezforum/dbsettings.php" );
include_once( "ezphputils.php" );
include_once( "template.inc" );
//include_once( "../classes/ezdb.php" );
include_once( "$DOCROOT/classes/ezforumcategory.php" );
include_once( "../classes/ezusergroup.php" );
include_once( "../classes/ezsession.php" );

$session = new eZSession();


$cat = new eZforumCategory();
$t = new Template( "$DOCROOT/admin/templates" );

$t->set_file(Array( "category" => "category.tpl",
                    "category-add" => "category-add.tpl",
                    "category-modify" => "category-modify.tpl",
                    "listelements" => "category-list-elements.tpl"
                    ) );

$t->set_var( "docroot", $DOCROOT);

if ( $session->get( $AuthenticatedSession ) != 0 )
{
    // fail  - reason: user not logged in.
}

if ( $add )
{
    if ( !eZUserGroup::verifyCommand( $session->userID, "eZForum_AddCategory" ) )
    {
        die( "Insufficient user rights" );
        exit;
    }

    $cat->setName( $Name );
    $cat->setDescription( $Description );
    
    if ( $Private )
        $cat->setPrivate( "Y" );
    else
        $cat->setPrivate( "N" );
    
    $cat->store();
}
  
if ($action == "delete")
{
    if ( !eZUserGroup::verifyCommand( $session->userID, "eZForum_DeleteCategory" ) )
    {
        die( "Insufficient user rights" );
        exit;
    }
        
    $cat->delete( $category_id );
}

if ( $modifyCategory )
{
    if ( !eZUserGroup::verifyCommand( $session->userID(), "eZForum_DeleteCategory" ) )
    {
        die( "Insufficient user rights to modify category" );
        exit;
    }
    $cat->get( $category_id );

    $cat->setName( $Name );
    $cat->setDescription( $Description );
    if ( $Private )
        $cat->setPrivate( "Y" );
    else
        $cat->setPrivate( "N" );
    
    $cat->store();
}

$t->set_var("category_id", $category_id );
    
if ($action == "modify")
{
    $cat->get( $category_id );    
    $t->set_var("category-name", $cat->name() );
    $t->set_var("category-description", $cat->description() );
    if ($cat->private() == "Y")
        $t->set_var("category-private", "checked");
    else
        $t->set_var("category-private", "");
    
    $t->parse("box", "category-modify", true);
}
else
{
    $t->parse("box", "category-add", true);
}

$categories = eZforumCategory::getAllCategories();

for ($i = 0; $i < count( $categories ); $i++)
{
    arrayTemplate( $t, $categories[$i], Array( Array("Id", "list-Id" ),
                                               Array("Name", "list-Name" ),
                                               Array("Description", "list-Description" ),
                                               Array("Private", "list-Private" )
                                               )
                 );

    $t->set_var( "color", switchColor( $i, "#f0f0f0", "#dcdcdc" ) );
    $t->parse( "categories", "listelements", true );
}

$t->pparse("output", "category");
?>
