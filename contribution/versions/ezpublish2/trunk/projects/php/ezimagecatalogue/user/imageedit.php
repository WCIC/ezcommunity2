<?php
// 
// $Id: imageedit.php,v 1.37 2001/07/15 16:55:09 bf Exp $
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <09-Jan-2001 10:45:44 ce>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2001 eZ systems as
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//

include_once( "classes/ezhttptool.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezpermission.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );
include_once( "ezuser/classes/ezauthor.php" );


$user =& eZUser::currentUser();

$CurrentCategoryID = eZHTTPTool::getVar( "CategoryID" );
$CategoryID = eZHTTPTool::getVar( "CategoryID" );

if ( !$user )
{
    eZHTTPTool::header( "Location: /error/403/" );
    exit();
}

if ( isSet( $NewCategory ) )
{
    eZHTTPTool::header( "Location: /imagecatalogue/category/new/$CurrentCategoryID/" );
    exit();
}

if ( isSet( $Cancel ) )
{
    eZHTTPTool::header( "Location: /imagecatalogue/image/list/" . $CurrentCategoryID . "/" );
    exit();
}

if ( isSet( $DeleteImages ) )
{
    $Action = "DeleteImages";
}

if ( isSet( $DeleteCategories ) )
{
    $Action = "DeleteCategories";
}
    
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );

// include_once( "classes/ezfile.php" );
include_once( "classes/ezimagefile.php" );

include_once( "ezimagecatalogue/classes/ezimage.php" );
include_once( "ezimagecatalogue/classes/ezimagecategory.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZImageCatalogueMain", "Language" );

$t = new eZTemplate( "ezimagecatalogue/user/" . $ini->read_var( "eZImageCatalogueMain", "TemplateDir" ),
                     "ezimagecatalogue/user/intl/", $Language, "imageedit.php" );

$t->setAllStrings();

$t->set_file( "image_edit_page", "imageedit.tpl" );

$t->set_block( "image_edit_page", "value_tpl", "value" );
$t->set_block( "image_edit_page", "multiple_value_tpl", "multiple_value" );
$t->set_block( "image_edit_page", "image_tpl", "image" );
$t->set_block( "image_edit_page", "errors_tpl", "errors" );

$t->set_block( "image_edit_page", "write_group_item_tpl", "write_group_item" );
$t->set_block( "image_edit_page", "read_group_item_tpl", "read_group_item" );

$t->set_block( "image_edit_page", "photographer_item_tpl", "photographer_item" );

$t->set_var( "errors", "&nbsp;" );

$t->set_var( "name_value", "$Name" );
$t->set_var( "image_description", "$Description" );
$t->set_var( "caption_value", "$Caption" );

$error = false;
$nameCheck = true;
$captionCheck = false;
$descriptionCheck = false;
$fileCheck = true;
$permissionCheck = false;

$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
$t->set_var( "error_name", "&nbsp;" );

$t->set_block( "errors_tpl", "error_caption_tpl", "error_caption" );
$t->set_var( "error_caption", "&nbsp;" );

$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
$t->set_var( "error_file_upload", "&nbsp" );

$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
$t->set_var( "error_description", "&nbsp;" );

$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
$t->set_var( "error_read_everybody_permission", "&nbsp;" );

$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
$t->set_var( "error_write_everybody_permission", "&nbsp;" );

// Check for errors when inserting or updating.
if ( $Action == "Insert" || $Action == "Update" )
{
    if ( $nameCheck )
    {
        if ( empty ( $Name ) )
        {
            $t->parse( "error_name", "error_name_tpl" );
            $error = true;
        }
    }

    if ( $captionCheck )
    {
        if ( empty ( $Caption ) )
        {
            $t->parse( "error_caption", "error_caption_tpl" );
            $error = true;
        }
    }

    if ( $descriptionCheck )
    {
        if ( empty ( $Description ) )
        {
            $t->parse( "error_description", "error_description_tpl" );
            $error = true;
        }
    }

    if ( $permissionCheck )
    {
        if ( empty( $ReadGroupArrayID )  )
        {
            $t->parse( "error_read_everybody_permission", "error_read_everybody_permission_tpl" );
            $error = true;
        }
        if ( empty( $WriteGroupArrayID )  )
        {
            $t->parse( "error_write_everybody_permission", "error_write_everybody_permission_tpl" );
            $error = true;
        }
        
    }

    
    if ( $fileCheck )
    {
        $file = new eZImageFile();
        if ( $file->getUploadedFile( "userfile" ) )
        {
            $imageTest = new eZImage();
            if ( $imageTest->checkImage( $file ) and $imageTest->setImage( $file ) )
            {
                $fileOK = true;
            }
            else
            {
                $error = true;
                $t->parse( "error_file_upload", "error_file_upload_tpl" );
            }
        }
        else
        {
            if ( $Action == "Insert" )
            {
                $error = true;
                $t->parse( "error_file_upload", "error_file_upload_tpl" );
            }
        }
    }

    if ( $error )
    {
        $t->parse( "errors", "errors_tpl" );
        foreach( $WriteGroupArrayID as $unf )
        {
            if( $unf == 0 )
                $writeGroupArrayID[] = -1;
            else
                $writeGroupArrayID[] = $unf;
        }
        foreach( $ReadGroupArrayID as $unf )
        {
            if( $unf == 0 )
                $readGroupArrayID[] = -1;
            else
                $readGroupArrayID[] = $unf;
        }
    }
}

// Insert if error == false
if ( $Action == "Insert" && $error == false )
{
    $image = new eZImage();
    $image->setName( $Name );
    $image->setPhotographer( $PhotoID );
    $image->setCaption( $Caption );
    $image->setDescription( $Description );
    $image->setUser( $user );

    $image->setImage( $file );

    $image->store();

    if ( count ( $ReadGroupArrayID ) > 0 )
    {
        foreach ( $ReadGroupArrayID as $Read )
        {
            if ( $Read == 0 )
                $group = -1;
            else
                $group = new eZUserGroup( $Read );

            eZObjectPermission::setPermission( $group, $image->id(), "imagecatalogue_image", "r" );
        }
    }

    if ( count ( $WriteGroupArrayID ) > 0 )
    {
        foreach ( $WriteGroupArrayID as $Write )
        {
            if ( $Write == 0 )
                $group = -1;
            else
                $group = new eZUserGroup( $Write );
            
            eZObjectPermission::setPermission( $group, $image->id(), "imagecatalogue_image", "w" );
        }
    }

    $category = new eZImageCategory( $CategoryID );
    
    $image->setCategoryDefinition( $category );

    $categories = array_unique( array_merge( $CategoryArray, $CategoryID ) );
    
    foreach ( $categories as $categoryItem )
    {
        eZImageCategory::addImage( $image, $categoryItem );
    }
    eZLog::writeNotice( "Picture added to catalogue: $image->name() from IP: $REMOTE_ADDR" );

    
    eZHTTPTool::header( "Location: /imagecatalogue/image/list/" . $CategoryID . "/" );
    exit();
}

// Update if error == false
if ( $Action == "Update" && $error == false )
{
    $image = new eZImage( $ImageID );
    $image->setName( $Name );
    $image->setPhotographer( $PhotoID );
    $image->setCaption( $Caption );
    
    $image->setDescription( $Description );
    
    eZObjectPermission::removePermissions( $ImageID, "imagecatalogue_image", 'r' );
    if ( count ( $ReadGroupArrayID ) > 0 )
    {
        foreach ( $ReadGroupArrayID as $Read )
        {
            if ( $Read == 0 )
                $group = -1;
            else
                $group = new eZUserGroup( $Read );
            
            eZObjectPermission::setPermission( $group, $image->id(), "imagecatalogue_image", "r" );
        }
    }
    
    eZObjectPermission::removePermissions( $ImageID, "imagecatalogue_image", 'w' );
    if ( count ( $WriteGroupArrayID ) > 0 )
    {
        foreach ( $WriteGroupArrayID as $Write )
        {
            if ( $Write == 0 )
                $group = -1;
            else
                $group = new eZUserGroup( $Write );
            
            eZObjectPermission::setPermission( $group, $image->id(), "imagecatalogue_image", "w" );
        }
    }

    $categoryArray = $image->categories();
    // Calculate new and unused categories
    $old_maincategory = $image->categoryDefinition();
    
    if ( $old_maincategory > -1 )
        $old_categories =& array_unique( array_merge( $old_maincategory->id(),
                                                      $image->categories( false ) ) );

    $new_categories = array_unique( array_merge( $CategoryID, $CategoryArray ) );

    $category = new eZImageCategory( $CategoryID );
    $image->setCategoryDefinition( $category );

    $remove_categories = array_diff( $old_categories, $new_categories );
    $add_categories = array_diff( $new_categories, $old_categories );

    foreach ( $remove_categories as $categoryItem )
    {
        eZImageCategory::removeImage( $image, $categoryItem );
    }
    foreach ( $add_categories as $categoryItem )
    {
        eZImageCategory::addImage( $image, $categoryItem );
    }
    if ( $fileOK )
    {
        $image->setImage( $file );
    }
    
    $image->store();

    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /imagecatalogue/image/list/" . $CurrentCategoryID . "/" );
    exit();
}

// Delete an image
if ( $Action == "DeleteImages" )
{
    if ( count ( $ImageArrayID ) != 0 )
    {
        foreach ( $ImageArrayID as $ImageID )
        {
            $image = new eZImage( $ImageID );
            $image->delete();

            print( $ImageID );
        }
    }

    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /imagecatalogue/image/list/" . $CurrentCategoryID . "/" );
    exit();
}

// Delete a category
if( $Action == "DeleteCategories" )
{
    if( count( $CategoryArrayID ) > 0 )
    {
        foreach( $CategoryArrayID as $categoryID )
        {
            $category = new eZImageCategory( $categoryID );
            $category->delete();
        }
    }

    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /imagecatalogue/image/list/" . $CurrentCategoryID . "/" );
    exit();    
}

// Set the default values to null
if ( $Action == "New" || $error )
{
    $t->set_var( "action_value", "Insert" );
    $t->set_var( "image", "" );
    $t->set_var( "image_id", "" );

// author select
    
    $author = new eZAuthor();
    $authorArray = $author->getAll();
    foreach ( $authorArray as $author )
    {
        $t->set_var( "photo_id", $author->id() );
        $t->set_var( "photo_name", $author->name() );
        $t->parse( "photographer_item", "photographer_item_tpl", true );
    }
    
}

// Sets the values to the current image
if ( $Action == "Edit" )
{
    $image = new eZImage( $ImageID );
    
    $t->set_var( "image_id", $image->id() );
    $t->set_var( "name_value", $image->name() );
    $t->set_var( "caption_value", $image->caption() );
    $t->set_var( "image_description", $image->description() );
    $t->set_var( "action_value", "update" );
    
    $t->set_var( "image_alt", $image->caption() );
    
    $photographer = $image->photographer();
    $PhotographerID = $photographer->id();
    
// author select
    
    $author = new eZAuthor();
    $authorArray = $author->getAll();
    foreach ( $authorArray as $author )
    {
        if ( $PhotographerID == $author->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
        $t->set_var( "photo_id", $author->id() );
        $t->set_var( "photo_name", $author->name() );
        $t->parse( "photographer_item", "photographer_item_tpl", true );
    }
    
    $variation = $image->requestImageVariation( 150, 150 );
    
    $t->set_var( "image_src", "/" .$variation->imagePath() );
    $t->set_var( "image_width", $variation->width() );
    $t->set_var( "image_height", $variation->height() );
    $t->set_var( "image_file_name", $image->originalFileName() );
    $t->parse( "image", "image_tpl" );
    
    $objectPermission = new eZObjectPermission();
    
    $readGroupArrayID =& $objectPermission->getGroups( $image->id(), "imagecatalogue_image", "r", false );
    $writeGroupArrayID =& $objectPermission->getGroups( $image->id(), "imagecatalogue_image", "w", false );
}



$category = new eZImageCategory() ;
$categoryList =& $category->getTree( );

$tree = new eZImageCategory();
$treeArray =& $tree->getTree();
$user =& eZUser::currentUser();

$catCount = count( $treeArray );
$t->set_var( "num_select_categories", min( $catCount, 10 ) );

foreach ( $treeArray as $catItem )
{
    if ( eZObjectPermission::hasPermission( $catItem[0]->id(), "image_category", 'w', $user ) == true  ||
         eZImageCategory::isOwner( eZUser::currentUser(), $catItem[0]->id() ) )
    {
        if ( $Action == "Edit" )
        {
            $defCat = $image->categoryDefinition();
        
            if ( get_class( $defCat ) == "ezimagecategory" )
            {
                if ( $image->existsInCategory( $catItem[0] ) &&
                ( $defCat->id() != $catItem[0]->id() ) )
                {
                    $t->set_var( "multiple_selected", "selected" );
                }
                else
                {
                    $t->set_var( "multiple_selected", "" );
                }
            }
            else
            {
                $t->set_var( "multiple_selected", "" );
            }
            
            if ( get_class( $defCat ) == "ezimagecategory" )
            {
                if ( $defCat->id() == $catItem[0]->id() )
                {
                    $t->set_var( "selected", "selected" );
                }
                else
                {
                    $t->set_var( "selected", "" );
                }
            }
            else
            {
                $t->set_var( "selected", "" );
            }
        }
        else
        {
            if ( $CategoryID == $catItem[0]->id() )
                $t->set_var( "selected", "selected" );
            else
                $t->set_var( "selected", "" );
            
            $t->set_var( "multiple_selected", "" );
        }
        
    
        $t->set_var( "option_value", $catItem[0]->id() );
        $t->set_var( "option_name", $catItem[0]->name() );
        
        if ( $catItem[1] > 0 )
            $t->set_var( "option_level", str_repeat( "&nbsp;", $catItem[1] ) );
        else
            $t->set_var( "option_level", "" );
        
        
        $t->parse( "value", "value_tpl", true );
        $t->parse( "multiple_value", "multiple_value_tpl", true );
    }
}



// Print out all the groups.
$groups =& eZUserGroup::getAll();
foreach ( $groups as $group )
{
    $t->set_var( "group_id", $group->id() );
    $t->set_var( "group_name", $group->name() );

    $t->set_var( "is_write_selected1", "" );
    $t->set_var( "is_read_selected1", "" );

    if ( $readGroupArrayID )
    {
        foreach ( $readGroupArrayID as $readGroup )
        {
            if ( $readGroup == $group->id() )
            {
                $t->set_var( "is_read_selected1", "selected" );
            }
            elseif ( $readGroup == -1 )
            {
                $t->set_var( "read_everybody", "selected" );
            }
            else
            {
                $t->set_var( "is_read_selected", "" );
            }
        }
    }

    if ( $Action = "new" )
        $t->set_var( "read_everybody", "selected" );
    $t->parse( "read_group_item", "read_group_item_tpl", true );

    if ( $writeGroupArrayID )
    {
        foreach ( $writeGroupArrayID as $writeGroup )
        {
            if ( $writeGroup == $group->id() )
            {
                $t->set_var( "is_write_selected1", "selected" );
            }
            elseif ( $writeGroup == -1 )
            {
                $t->set_var( "write_everybody", "selected" );
            }
            else
            {
                $t->set_var( "is_write_selected", "" );
            }
        }
    }
    
    $t->parse( "write_group_item", "write_group_item_tpl", true );
}

$t->pparse( "output", "image_edit_page" );

?>
