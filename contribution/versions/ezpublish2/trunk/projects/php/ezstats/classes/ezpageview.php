<?
// 
// $Id: ezpageview.php,v 1.1 2001/01/07 12:31:48 bf Exp $
//
// Definition of eZPageView class
//
// B�rd Farstad <bf@ez.no>
// Created on: <04-Jan-2001 18:00:08 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2000 eZ systems as
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

//!! eZStats
//! The eZPageView handled user page views on the site.
/*!
  This class hadles collecting of statistical information. It does
  not contain any query functions due to speed considerations.

  The class eZPageViewQuery handles the queries on the gathered information.
  
  \sa eZPageViewQuery
*/

/*!TODO
 */

include_once( "classes/ezdb.php" );
include_once( "ezuser/classes/ezuser.php" );

class eZPageView
{
    /*!
      Constructs a new eZPageView object.
    */
    function eZPageView( $id="" )
    {
        $this->IsConnected = false;

        if ( $id != "" )
        {
            $this->ID = $id;
            $this->get( $this->ID );
            $this->State_ = "Coherent";
        }
        else
        {
            $this->State_ = "New";
        }
    }

    /*!
      Stores a eZPageView object to the database.

      This function will also automatically fetch the user information and set the values
      before storing them to the database.
    */
    function store()
    {
        $this->dbInit();
        
        if ( !isset( $this->ID ) )
        {
            // parse information which is not relevant reported by browsers like konqueror
            $userAgent = preg_replace( "#(.*)\);.*#", "\\1)", $GLOBALS["HTTP_USER_AGENT"] );

            // check if the browser type is already stored in the database, if it it just
            // create a reference to it.
            $this->Database->array_query( $browser_type_array,
            "SELECT ID FROM eZStats_BrowserType
             WHERE BrowserType='$userAgent'" );

            if ( count( $browser_type_array ) == 0 )
            {

                $this->Database->query( "INSERT INTO eZStats_BrowserType SET
                                 BrowserType='$userAgent'
                                 " );
                
                $this->BrowserTypeID = mysql_insert_id();
            }
            else
            {
                $this->BrowserTypeID = $browser_type_array[0]["ID"];
            }

            // check if the remote host is already stored in the database, if it it just
            // create a reference to it.

            $remoteIP = $GLOBALS["REMOTE_ADDR"];
            
            $this->Database->array_query( $remote_host_array,
            "SELECT ID FROM eZStats_RemoteHost
             WHERE IP='$remoteIP'" );
            
            if ( count( $remote_host_array ) == 0 )
            {
                $this->Database->query( "INSERT INTO eZStats_RemoteHost SET
                                 IP='$remoteIP'
                                 " );
                
                $this->RemoteHostID = mysql_insert_id();
            }
            else
            {
                $this->RemoteHostID = $remote_host_array[0]["ID"];
            }

            // check if the referer url is already stored in the database, if it it just
            // create a reference to it.

            $refererDomain = "";
            $refererURI = "";
            
            if ( preg_match( "#(htt.*?://)(.*?)(/.*)#", $GLOBALS["HTTP_REFERER"], $valueArray ) )
            {
                // we don't need to store the http:// or the https://
                // $valueArray[1];

                // store the referer parts
                $refererDomain =& $valueArray[2];
                $refererURI =& $valueArray[3];
            }
            
            $this->Database->array_query( $referer_url_array,
            "SELECT ID FROM eZStats_RefererURL
             WHERE Domain='$refererDomain' AND URI='$refererURI'" );
            
            if ( count( $referer_url_array ) == 0 )
            {
                $this->Database->query( "INSERT INTO eZStats_RefererURL SET
                                 Domain='$refererDomain',
                                 URI='$refererURI'
                                 " );
                
                $this->RefererURLID = mysql_insert_id();
            }
            else
            {
                $this->RefererURLID = $referer_url_array[0]["ID"];
            }

            // check if the requested page is already stored. If so store
            // the id.
            $requestURI = $GLOBALS["REQUEST_URI"];

            $this->Database->array_query( $request_page_array,
            "SELECT ID FROM eZStats_RequestPage
             WHERE URI='$requestURI'" );
            
            if ( count( $request_page_array ) == 0 )
            {
                $this->Database->query( "INSERT INTO eZStats_RequestPage SET
                                 URI='$requestURI'
                                 " );
                
                $this->RequestPageID = mysql_insert_id();
            }
            else
            {
                $this->RequestPageID = $request_page_array[0]["ID"];
            }


            $user = eZUser::currentUser();
            if ( $user )
            {                
                $this->UserID = $user->id();
            }
            else
            {
                $this->UserID = 0;
            }
            
            
            $this->Database->query( "INSERT INTO eZStats_PageView SET
                                 UserID='$this->UserID',
                                 BrowserTypeID='$this->BrowserTypeID',
                                 RemoteHostID='$this->RemoteHostID',
                                 RefererURLID='$this->RefererURLID',
                                 RequestPageID='$this->RequestPageID',
                                 Date=now()
                                 " );
        }
        else
        {
            $this->Database->query( "UPDATE eZStats_PageView SET
                                 UserID='$this->UserID',
                                 BrowserTypeID='$this->BrowserTypeID',
                                 RemoteHostID='$this->RemoteHostID',
                                 RefererURLID='$this->RefererURLID',
                                 Date='Date'
                                 WHERE ID='$this->ID'
                                 " );
        }
        
        $this->ID = mysql_insert_id();

        $this->State_ = "Coherent";
    }
    
    /*!
      Fetches the object information from the database.
    */
    function get( $id="" )
    {
        $this->dbInit();

        $ret = false;
        if ( $id != "" )
        {
            $this->Database->array_query( $pageview_array, "SELECT * FROM eZStats_PageView WHERE ID='$id'" );
            if ( count( $pageview_array ) > 1 )
            {
                die( "Error: Pageview's with the same ID was found in the database. This shouldent happen." );
            }
            else if ( count( $pageview_array ) == 1 )
            {
                $this->ID =& $pageview_array[0][ "ID" ];
                $this->UserID =& $pageview_array[0][ "UserID" ];
                $this->Date =& $pageview_array[0][ "Date" ];
                $this->BrowserTypeID =& $pageview_array[0][ "Date" ];
                $this->RemoteHostID =& $pageview_array[0][ "RemoteHostID" ];
                $this->RefererURLID =& $pageview_array[0][ "RefererURLID" ];
                $this->RequestPageID =& $pageview_array[0][ "RequestPageID" ];


                // fetch the remote IP and domain
                $this->Database->array_query( $pageview_array,
                "SELECT IP, HostName FROM eZStats_RemoteHost WHERE ID='$this->RemoteHostID'" );

                $this->RemoteIP = $pageview_array[0]["IP"];

                $this->RemoteHostName = $pageview_array[0]["HostName"];

                // check if the domain name is fetched, if not try to fetch it 
                // and store the result in the table.
                if ( $this->RemoteHostName = "NULL" )
                {
                    $this->RemoteHostName =& gethostbyaddr( $this->RemoteIP );
                    $this->Database->query( "UPDATE eZStats_RemoteHost SET HostName='$this->RemoteHostName' WHERE ID='$this->RemoteHostID'" );
                }

                // fetch the requested page
                $this->Database->array_query( $pageview_array,
                "SELECT URI FROM eZStats_RequestPage WHERE ID='$this->RequestPageID'" );

                $this->RequestPage = $pageview_array[0]["URI"];                
                

                $this->State_ = "Coherent";
                $ret = true;

            }
            else if ( count( $pageview_array ) < 1 )
            {
                $this->State_ = "Dirty";
            }
        }
        else
        {
            $this->State_ = "Dirty";
        }

        return $ret;
    }

    /*!
      Returns the id of the virtual file.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the user who got the page. False if the user was not logged in.
    */
    function user()
    {
        $ret = false;
        if ( $this->UserID != 0 )
        {
            include_once( "ezuser/classes/ezuser.php" );
            $ret = new eZUser( $this->UserID );
        }
        return $ret;
    }

    /*!
      Returns the remote ip address.
    */
    function remoteIP()
    {
        return $this->RemoteIP;
    }

    /*!
      Returns the remote host name.
    */
    function remoteHostName()
    {
        return $this->RemoteHostName;
    }
    
    /*!
      Returns the requested page
    */
    function requestPage()
    {
        return $this->RequestPage;
    }
    

    /*!
      \private
      Open the database for read and write. Gets all the database information from site.ini.
    */
    function dbInit()
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = eZDB::globalDatabase();
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $UserID;
    var $Date;
    var $BrowserTypeID;
    var $RemoteHostID;
    var $RefererID;
    var $RequestPageID;

    var $BrowserType;
    var $RemoteIP;
    var $RemoteHostName;
    var $RefererURL;
    var $RefererDomain;
    var $RequestPage;

    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}

?>
