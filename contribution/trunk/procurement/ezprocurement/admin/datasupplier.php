<?php
//
// $Id: datasupplier.php,v 1.56.2.4 2002/05/22 13:35:33 bf Exp $
//
// Created on: <23-Oct-2000 17:53:46 bf>
//
// This source file is part of eZ publish, publishing software.
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
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
include_once( "ezrfp/classes/ezrfp.php" );
include_once( "ezrfp/classes/ezrfpcategory.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );
include_once( "ezuser/classes/ezpermission.php" );
include_once( "classes/ezdatetime.php" );

/*
echo  $url_array[1] . " " . $url_array[2] . " " . $url_array[3] . " " . $url_array[4] . " " . $url_array[5];
exit();
*/

$user =& eZUser::currentUser();
if ( eZPermission::checkPermission( $user, "eZRfp", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

switch ( $url_array[2] )
{
    # IMPORTANT: REMOVE THIS BLOCK!!!
    case "cronreport":
    {
     include( "ezrfp/cron/report_cron.php" );
     break;
    }
    # END REMOVE THIS

    case "report" :
    {
        /*$Year = $url_array[3];
        $Month = $url_array[4];*/

        $Action = $url_array[3];
        $Param = $url_array[4];
	$SubParam = $url_array[5];

        include( "ezrfp/admin/rfpreport.php" );
    }
    break;

    case "insertstats" :
    {
     $Num = $url_array[3];

     include( "ezrfp/admin/statinsert.php" );
    }
    break;

    case "cache":
    {
        include( "ezsitemanager/admin/cacheadmin.php" );
    }
    break;

    case "export":
    {
        include( "ezrfp/admin/export.php" );
    }
    break;

    case "topiclist":
    {
        include( "ezrfp/admin/topiclist.php" );
    }
    break;
    
    case "archive":
    {
        if ( !is_numeric( eZHTTPTool::getVar( "CategoryID", true ) ) )
        {
            $CategoryID = $url_array[3];
            if  ( !isset( $CategoryID ) || ( $CategoryID == "" ) )
                $CategoryID = 0;
        }
        
        if ( $url_array[4] == "parent" )
            $Offset = $url_array[5];

        if ( $CategoryID == 0 || eZObjectPermission::hasPermission( $CategoryID, "rfp_category", 'r' )  ||
             eZRfpCategory::isOwner( $user, $CategoryID ) )
            include( "ezrfp/admin/rfplist.php" );
    }
    break;

    case "unpublished":
    {
        $CategoryID = $url_array[3];
        if  ( !isset( $CategoryID ) || ( $CategoryID == "" ) )
            $CategoryID = 0;

        if ( $url_array[4] == "parent" )
            $Offset = $url_array[5];

        if ( $CategoryID == 0 || eZObjectPermission::hasPermission( $CategoryID, "rfp_category", 'r' ) ||
             eZRfpCategory::isOwner( $user, $CategoryID) )
            include( "ezrfp/admin/unpublishedlist.php" );
    }
    break;

    case "pendinglist":
    {
        $CategoryID = $url_array[3];
        if ( !isset( $CategoryID ) || ( $CategoryID == "" ) )
            $CategoryID = 0;

        if ( $url_array[4] == "parent" )
            $Offset = $url_array[5];

        if ( $CategoryID == 0 || eZObjectPermission::hasPermission( $CategoryID, "rfp_category", 'r' ) ||
             eZRfpCategory::isOwner( $user, $CategoryID) )
            include( "ezrfp/admin/pendinglist.php" );
    }
    break;

    case "search" :
    {
        if ( $url_array[3] == "advanced" )
        {
            include( "ezrfp/admin/searchform.php" );
        }
        else
        {
            $Offset = 0;
            if ( $url_array[3] == "parent" )
            {
                $SearchText = urldecode( $url_array[4] );
                if ( $url_array[5] != urlencode( "+" ) )
                    $StartStamp = urldecode( $url_array[5] );
                if ( $url_array[6] != urlencode( "+" ) )
                    $StopStamp = urldecode( $url_array[6] );
                if ( $url_array[7] != urlencode( "+" ) )
                    $CategoryArray = explode( "-", urldecode( $url_array[7] ) );
                if ( $url_array[8] != urlencode( "+" ) )
                    $ContentsWriterID = urldecode( $url_array[8] );
                if ( $url_array[9] != urlencode( "+" ) )
                    $PhotographerID = urldecode( $url_array[9] );
                
                $Offset = $url_array[10];
            }
            include( "ezrfp/admin/search.php" );
        }
    }
    break;

    case "view":    
    case "procurementview":
    case "preview":
    case "procurementpreview":
    {
        $RfpID = $url_array[3];
        $PageNumber= $url_array[4];
        
        if ( !isset( $PageNumber ) || ( $PageNumber == "" ) )
            $PageNumber= 1;

        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'r' ) ||
             eZRfp::isAuthor( $user, $RfpID ) )
            include( "ezrfp/admin/rfppreview.php" );
    }
    break;

    case "rfplog" :
    case "procurementlog" :
    {
        $RfpID = $url_array[3];
        if ( eZObjectPermission::hasPermission(  $RfpID, "rfp_rfp", 'w' ) ||
             eZRfp::isAuthor( $user, $RfpID ) )
            include( "ezrfp/admin/rfplog.php" );
    }
    break;
    
  // FIXME: test for writeable categories!!!    

    case "edit" :
    case "rfpedit" :
    case "procurementedit":
    {
        if ( eZObjectPermission::getObjects( "rfp_category", 'w', true ) < 1 )
        {
            $text = "You do not have write permission to any categories";
            $info = urlencode( $text );
            eZHTTPTool::header( "Location: /error/403?Info=$info" );
            exit();
        }
            
        switch ( $url_array[3] )
        {
           
            case "insert" :
            {
                $Action = "Insert";
                include( "ezrfp/admin/rfpedit.php" );
            }
            break;
		
            case "new" :
            {
                $Action = "New";
                include( "ezrfp/admin/rfpedit.php" );
            }
            break;

            case "update" :
            {
                $Action = "Update";
                $RfpID = $url_array[4];

                if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/rfpedit.php" );
            }
            break;

            case "cancel" :
            {
                $Action = "Cancel";
                $RfpID = $url_array[4];

                if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/rfpedit.php" );
            }
            break;
                        
            case "edit" :
            {
                $Action = "Edit";
                $RfpID = $url_array[4];

                if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/rfpedit.php" );
                else
                    print("Not allowed");
            }
            break;

            case "delete" :
            {
                $Action = "Delete";
                $RfpID = $url_array[4];
exit();
                if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user , $RfpID ) )
                    include( "ezrfp/admin/rfpedit.php" );
            }
            break;

            case "imagelist" :
            {
                $RfpID = $url_array[4];
                if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/imagelist.php" );
            }
            break;

            case "medialist" :
            {
                $RfpID = $url_array[4];
                if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/medialist.php" );
            }
            break;

            case "filelist" :
            {
                $RfpID = $url_array[4];
                if ( eZObjectPermission::hasPermission(  $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/filelist.php" );
            }
            break;

            case "imagemap" :
            {
                switch ( $url_array[4] )
                {
                    case "edit" :
                    {
                        $RfpID = $url_array[6];
                        $ImageID = $url_array[5];
                        $Action = "Edit";
                        if ( eZObjectPermission::hasPermission(  $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/imagemap.php" );
                    }
                    break;
                    
                    case "store" :
                    {
                        $RfpID = $url_array[6];
                        $ImageID = $url_array[5];
                        $Action = "Store";
                        if ( eZObjectPermission::hasPermission(  $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/imagemap.php" );
                    }
                    break;
                }
            }
            break;
            
            case "attributelist" :
            {
                $RfpID = $url_array[4];
                if ( eZObjectPermission::hasPermission(  $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/attributelist.php" );
            }
            break;

            case "attributeedit" :
            {
                $Action = $url_array[4];
                if ( !isset( $TypeID ) ) 
                    $TypeID = $url_array[5];
                if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/attributeedit.php" );
            }
            break;

            
            case "formlist" :
            {
                $RfpID = $url_array[4];
                if( eZObjectPermission::hasPermission(  $RfpID, "rfp_rfp", 'w' ) ||
                    eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/formlist.php" );
            }
            break;

            
            case "imageedit" :
            {
                if ( isSet( $Browse ) )
                {
                    include ( "ezimagecatalogue/admin/browse.php" );
                    break;
                }
                switch ( $url_array[4] )
                {
                    case "new" :
                    {
                        $Action = "New";
                        $RfpID = $url_array[5];
                        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/imageedit.php" );
                    }
                    break;

                    case "edit" :
                    {
                        $Action = "Edit";
                        $RfpID = $url_array[6];
                        $ImageID = $url_array[5];
                        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/imageedit.php" );
                    }
                    break;

                    case "storedef" :
                    {
                        $Action = "StoreDef";
                        if ( isset( $DeleteSelected ) )
                            $Action = "Delete";
                        $RfpID = $url_array[5];
                        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/imageedit.php" );
                    }
                    break;

                    default :
                    {
                        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/imageedit.php" );
                    }
                }
            }
            break;

            case "mediaedit" :
            {
                if ( isSet ( $Browse ) )
                {
                    include ( "ezmediacatalogue/admin/browse.php" );
                    break;
                }
                $RfpID = $url_array[4];
                $MediaID = $url_array[5];
                if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                     eZRfp::isAuthor( $user, $RfpID ) )
                    include( "ezrfp/admin/mediaedit.php" );
            }
            break;

            case "fileedit" :
            {
                if ( isSet( $Browse ) )
                {
                    include( "ezfilemanager/admin/browse.php" );
                    break;
                }
                switch ( $url_array[4] )
                {
                    case "new" :
                    {
                        $Action = "New";
                        $RfpID = $url_array[5];
                        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/fileedit.php" );
                    }
                    break;

                    case "edit" :
                    {
                        $Action = "Edit";
                        $RfpID = $url_array[6];
                        $FileID = $url_array[5];
                        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/fileedit.php" );
                    }
                    break;

                    case "delete" :
                    {
                        $Action = "Delete";
                        $RfpID = $url_array[6];
                        $FileID = $url_array[5];
                        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/fileedit.php" );
                    }
                    break;
                    
                    default :
                    {
                        if ( eZObjectPermission::hasPermission( $RfpID, "rfp_rfp", 'w' ) ||
                             eZRfp::isAuthor( $user, $RfpID ) )
                            include( "ezrfp/admin/fileedit.php" );
                    }
                }
            }
            break;
        }
    }
    break;


    case "categoryedit":
    {
        // make switch
        if ( $url_array[3] == "cancel" )        
        {
            $Action = "Cancel";
            $CategoryID = $url_array[4];
            eZHTTPTool::header( "Location: /rfp/archive/$CategoryID/" );
            exit();
        }        

        if ( $url_array[3] == "insert" )
        {
            $Action = "insert";
            include( "ezrfp/admin/categoryedit.php" );
        }
        if ( $url_array[3] == "new" )
        {
            $Action = "new";
            include( "ezrfp/admin/categoryedit.php" );
        }
        if ( $url_array[3] == "update" )
        {
            $CategoryID = $url_array[4];
            $Action = "update";
            if ( eZObjectPermission::hasPermission( $CategoryID, "rfp_category", 'w' ) ||
                 eZRfpCategory::isOwner( $user, $CategoryID) )
                include( "ezrfp/admin/categoryedit.php" );
        }
        if ( $url_array[3] == "delete" )
        {
            $CategoryID = $url_array[4];
            $Action = "delete";
            if ( eZObjectPermission::hasPermission( $CategoryID, "rfp_category", 'w' )  ||
                 eZRfpCategory::isOwner( $user, $CategoryID) )
                include( "ezrfp/admin/categoryedit.php" );
        }
        if ( $url_array[3] == "edit" )
        {
            $CategoryID = $url_array[4];
            $Action = "edit";
            include( "ezrfp/admin/categoryedit.php" );
        }

    }
    break;

    case "sitemap":
    {
        include( "ezrfp/admin/sitemap.php" );
    }
    break;    

    case "type":
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                    include( "ezrfp/admin/typelist.php" );
            }
            break;
            
            case "new":
            case "edit":
            case "insert":
            case "update":
            case "delete":
            case "up":
            case "down":
            {
                if ( !isset( $Action ) )
                    $Action = $url_array[3];
                if ( is_numeric( $TypeID ) )
                {
                    $ActionValue = "update";
                }
                else
                {
                    $TypeID = $url_array[4];
                }
                
                if ( !is_array( $AttributeID ) )
                {
                    $AttributeID = $url_array[5];
                }
                include( "ezrfp/admin/typeedit.php" );
            }
            break;
        }
    }
    break;

    default :
    {
        eZHTTPTool::header( "Location: /error/404" );
        exit();
    }
    break;
}

// display a page with error msg

?>
