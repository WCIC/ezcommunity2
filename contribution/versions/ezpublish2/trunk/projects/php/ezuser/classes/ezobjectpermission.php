<?php
// 
// $Id: ezobjectpermission.php,v 1.10 2001/02/28 11:24:16 fh Exp $
//
// Definition of eZCompany class
//
// Frederik Holljen <fh@ez.no>
// Created on: <27-Feb-2001 08:05:56 fh>
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

//!! eZUser
//! eZObjectPermission haldes user group permissions for objects like articles and bugs.
/*!

  Example code:
  \code

  \endcode
  \sa eZUser eZUserGroup eZModule eZForgot
*/

include_once( "ezuser/classes/ezmodule.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );
include_once( "classes/ezdb.php" );

class eZObjectPermission
{
    /*!
      Constructs a new eZPermission object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZObjectPermission( )
    {
    }

    /*
      \static
      Returns true if the user has the desired permission to the desired object.
      $objectID is the ID of the object you are interested in. This could be a bug, an article etc..
      $modulTable is the nickname of the table where the permission is found. The nicknames can be found in site.ini
      $permission either 'r' for readpermission or 'w' for writepermission.
      $user (of type eZUser )is the user you want to check permissions for. Default is currentUser.

      NOTE: If you object has an owner, and this user allways should have rights, you must check this yourself.
     */
    function hasPermission( $objectID, $modulTable, $permission, $user=false )
    {
        if( $user == false )
        {
            $user = eZUser::currentUser();
        }

        $SQLGroups = "GroupID = '-1'";
        if( get_class( $user ) == "ezuser" )
        {
            $groups =& $user->groups( true );
            $first = true;
            if( count( $groups ) > 0 )
            {
                foreach( $groups as $groupItem )
                {
                    if( $first == true )
                    {
                        $SQLGroups = "GroupID='$groupItem' ";
                    }
                    else
                    {
                        $SQLGroups .= "OR GroupID='$groupItem' ";
                    }
                    $first = false;
                }
                $SQLGroups .= "OR GroupID = '-1' ";
            }
        }

        $tableName = getTableName( $modulTable );
        if( $tableName == "" )
        {
            return false;
        }

        $SQLRead = "";
        $SQLWrite = "";
        if( $permission == 'r' )
        {
            $SQLRead = "AND ReadPermission='1'";
        }
        else if( $permission == 'w' )
        {
            $SQLWrite = "AND WritePermission='1'";
        }

        $query = "SELECT count( ID ) as ID FROM $tableName WHERE ObjectID='$objectID' AND ( $SQLGroups ) $SQLRead $SQLWrite";
        $database =& eZDB::globalDatabase();

        $database->query_single( $res, $query );

        if( $res[ "ID" ] != 0 )
            return true;

        return false;
    }

    /*!
      \static
      Sets a permissions for on an object for a eZUserGroup. To set a permission for all use -1 as group.
      $group is of type eZUserGroup or the group ID and is the group that gets the permission
      $objectID is the ID of the object you are interested in. This could be a bug, an article etc..
      $modulTable is the nickname of the table where the permission is found. The nicknames can be found in site.ini
      $permission either 'r' for readpermission or 'w' for writepermission.
    */
    function setPermission( $group, $objectID, $modulTable, $permission  )
    {
        if( get_class( $group ) == "ezusergroup" )
        {
            $groupID = $group->id();
        }
        else if( $group == -1 )
        {
            $groupID = -1;
        }
        else
        {
            $groupID = $group;
        }

        $SQLPermission = "";
        if( $permission == 'r' )
        {
            $SQLPermission = "SET ReadPermission='1'";
        }
        else if( $permission == 'w' )
        {
            $SQLPermission = "SET WritePermission='1'";
        }
        else // bogus $permission input.
        {
            return false;
        }

        $tableName = getTableName( $modulTable );
        if( $tableName == "" )
        {
            return false;
        }

        $database =& eZDB::globalDatabase();
        $queryexists = "SELECT count( ID ) as ID FROM $tableName WHERE ObjectID='$objectID' AND GroupID='$groupID'";
        $database->query_single( $res, $queryexists );

        if( $res[ "ID" ] == 0 )
        {
            $query = "INSERT INTO $tableName $SQLPermission, ObjectID='$objectID', GroupID='$groupID'";
            $database->query( $query );
        }
        else if( $res[ "ID" ] == 1 )
        {
            $query = "UPDATE $tableName $SQLPermission WHERE ObjectID='$objectID' AND GroupID='$groupID'";
            $database->query( $query );
        }
        else
        {
            print("Duplicate objects in database. Please contact your administrator");
            exit();
        }
    }

    /*!
      \static
      Removes all permissions of a given type on an object.
     */
    function removePermissions( $objectID, $modulTable, $permission )
    {
        $tableName = getTableName( $modulTable );
        if( $tableName == "" )
        {
            return false;
        }

        $SQLPermission = "";
        if( $permission == 'r' )
        {
            $SQLPermission = "SET ReadPermission='0'";
        }
        else if( $permission == 'w' )
        {
            $SQLPermission = "SET WritePermission='0'";
        }
        else // bogus $permission input.
        {
            return false;
        }
        
        $query = "UPDATE $tableName $SQLPermission WHERE ObjectID='$objectID'";
        $database =& eZDB::globalDatabase();
        $database->query( $query );
    }

    /*!
      Returns all the groups that have permissions to a given object, if none are selected a empty array is returned.
      If one object with -1 is returned, everyone has access to the object.
      $group is of type eZUserGroup or a groupID, use -1 for objects everyone is allowed to see.
      $modulTable is the nickname of the table where the permission is found. The nicknames can be found in site.ini
      $permission either 'r' for readpermission or 'w' for writepermission.
     */
    function getGroups( $objectID, $modulTable, $permission, $GroupReturn=true )
    {
        $ret = array();
        $tableName = getTableName( $modulTable );
        if( $tableName == "" )
        {
            return $ret;
        }

        $SQLPermission = "";
        if( $permission == 'r' )
        {
            $SQLPermission = "ReadPermission='1'";
        }
        else if( $permission == 'w' )
        {
            $SQLPermission = "WritePermission='1'";
        }
        else // bogus $permission input.
        {
            return $ret;
        }
        
        $query = "SELECT GroupID FROM $tableName WHERE ObjectID='$objectID' AND $SQLPermission";
        $database =& eZDB::globalDatabase();
        $database->array_query( $res, $query );
        
        if( count( $res ) > 0 )
        {
            $i = 0;
            foreach( $res as $groupID )
            {
                if( $groupID["GroupID"]  == -1 )
                {
                    $res = array();
                    $res[0] = -1;
                    return $res;
                }
                $GroupReturn ? $res[$i] = new eZUserGroup( $groupID["GroupID"] ) : $res[$i] = $groupID["GroupID"];
                $i++;
            }
        }
        return $res;
    }
}

    
/*
  Returns table names.
 */
function getTableName( $name )
{
    $ret = "";
    switch( $name )
    {
        case "article_article" :
            $ret = "eZArticle_ArticlePermission";
        break;

        case "article_category" :
            $ret = "eZArticle_CategoryPermission";
        break;

        case "imagecatalogue_image" :
            $ret = "eZImageCatalogue_ImagePermission";
        break;

        case "imagecatalogue_category" :
            $ret = "eZImageCatalogue_CategoryPermission";
        break;

        default :
            $ret = "";
        break;
    }
    return $ret;
}

?>
