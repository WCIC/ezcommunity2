<?
// 
// $Id: ezlink.php,v 1.29 2000/10/19 10:49:29 ce-cvs Exp $
//
// Definition of eZCompany class
//
// B�rd Farstad <bf@ez.no>
// Created on: <15-Sep-2000 14:40:06 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZLink
//! The eZLink class handles URL links.
/*!

  Example code:
  \code
  // Create a new link and set some values.
  $link = new eZLink();
  $link->setTitle( "ZEZ website" );
  $link->Description( "zez.org is a page dedicated to all kinds of computer programming." );
  $link->KeyWords( "code programing c++ php sql python" );
  $setModified( date() );
  $setAccepted( "Y" );
  $setUrl( "zez.org" );

  // Store the link to the datavase.
  $link->store();

  // Check if the url exist in the database.
  $link->checkUrl( "zez.org" );

  // Get all the links in a group.
  $link->getByGroup( $linkGroupID );

  // Get all the not accepted links.
  $link->getNotAccepted();

  \endcode
  
  \sa eZLinkGroup eZHit eZQuery
*/

include_once( "classes/ezquery.php" );
include_once( "classes/ezdb.php" );

class eZLink
{
    /*!
      Constructor
    */
    function eZLink( $id=-0, $fetch=true  )
    {

        $this->IsConnected = false;
        if ( $id != -1 )
        {
            $this->ID = $id;
            if ( $fetch == true )
            {
                $this->get( $this->ID );
            }
            else
            {
                $this->State_ = "Dirty";
            }
        }
        else
        {
            $this->State_ = "New";
        }

    }

    /*!
      Stores a link to the database.
    */
    function store()
    {
        $this->dbInit();
       // Sets the created to the system clock
        $this->Created = date( "Y-m-d G:i:s" );        
        query( "INSERT INTO eZLink_Link SET
                ID='$this->ID',
                Title='$this->Title',
                Description='$this->Description',
                LinkGroup='$this->LinkGroupID',
                KeyWords='$this->KeyWords',
                Created='$this->Created',
                Url='$this->Url',
                Accepted='$this->Accepted'" );
    }

    /*!
      Update to the database.
    */
    function update()
    {
        $this->dbInit();
        query( "UPDATE eZLink_Link SET
                Title='$this->Title',
                LinkGroup='$this->LinkGroupID',
                KeyWords='$this->KeyWords',
                Url='$this->Url',
                Accepted='$this->Accepted'
                WHERE ID='$this->ID'" );
    }

    /*!
      Remove the link and the hits that belongs to the link.
    */
    function delete( )
    {
        $this->dbInit();
        query( "DELETE FROM eZLink_Hit WHERE Link='$this->ID'" );        
        query( "DELETE FROM eZLink_Link WHERE ID='$this->ID'" );
    }

    /*!
      Fetches out informasjon from the daatabase where ID=$id
    */
    function get ( $id )
    {
        $this->dbInit();
        if ( $id != "" )
        {
            array_query( $link_array, "SELECT * FROM eZLink_Link WHERE ID='$id'" );
            if ( count( $link_array ) > 1 )
            {
                die( "Feil: flere linker med samme ID ble funnet i databasen, dette skal ikke v�re mulig." );
            }
            else if ( count( $link_array ) == 1 )
            {
                $this->ID = $link_array[ 0 ][ "ID" ];
                $this->Title = $link_array[ 0 ][ "Title" ];
                $this->Description = $link_array[ 0 ][ "Description" ];
                $this->LinkGroupID = $link_array[ 0 ][ "LinkGroup" ];
                $this->KeyWords = $link_array[ 0 ][ "KeyWords" ];
                $this->Created = $link_array[ 0 ][ "Created" ];
                $this->Modified = $link_array[ 0 ][ "Modified" ];
                $this->Accepted = $link_array[ 0 ][ "Accepted" ];
                $this->Url = $link_array[ 0 ][ "Url" ];

            }
        }
    }

    /*!
      Fetchs out the links where the linkgroup=$id. Fetchs only accepted links.
    */
    function &getByGroup( $id )
    {
        $this->dbInit();
        $link_array = array();
        $return_array = array();
        
        $this->Database->array_query( $link_array, "SELECT ID FROM eZLink_Link WHERE LinkGroup='$id' AND Accepted='Y' ORDER BY Title" );

        for( $i=0; $i<count( $link_array ); $i++ )
        {
            $return_array[] = new eZLink( $link_array[$i][ "ID" ] );
        }


        return $return_array;
    }

    /*!
      Fetches out the links that is not accepted.
    */
    function getNotAccepted( )
    {
        $this->dbInit();
        $link_array = array();
        $return_array = array();
        
        $this->Database->array_query( $link_array, "SELECT ID FROM eZLink_Link WHERE Accepted='N' ORDER BY Title" );

        for ( $i=0; $i<count( $link_array ); $i++ )
        {
            $return_array[] = new eZLink( $link_array[$i]["ID"] );
        }

        return $return_array;
    }

    /*!
      Fetches out the last teen accpeted links.
    */
    function getLastTenDate( $limit, $offset )
    {
        $this->dbInit();
        $link_array = array();
        $return_array = array();
        
        $this->Database->array_query( $link_array, "SELECT * FROM eZLink_Link WHERE Accepted='Y' ORDER BY Created DESC LIMIT $offset, $limit" );

        for( $i=0; $i<count( $link_array ); $i++ )
        {
            $return_array[] = new eZLink( $link_array[$i][ "ID" ] );
        }
        return $return_array;
    }

    /*!
      Fetches out the last teen accpeted links.
    */
    function getLastTen( $limit, $offset )
    {
        $this->dbInit();
        $link_array = 0;
        
        array_query( $link_array, "SELECT * FROM eZLink_Link WHERE Accepted='Y' ORDER BY Title DESC LIMIT $offset, $limit" );

        return $link_array;
    }

    /*!
      Fetches the links that matches the $query.

      Default limit is set to 25.
    */
    function getQuery( $query, $limit, $offset )
    {
        $this->dbInit();
        $link_array = array();
        $return_array = array();

        $query = new eZQuery( array( "KeyWords", "Title", "Description" ), $query );
        
        $query_str =  "SELECT ID FROM eZLink_Link WHERE (" .
             $query->buildQuery()  .
             ") AND Accepted='Y' ORDER BY Title LIMIT $offset, $limit";


        $this->Database->array_query( $link_array, $query_str );
        $ret = array();

        foreach( $link_array as $linkItem )
        {
            $ret[] = new eZLink( $linkItem["ID"] );
        }
        return $ret;
    }


    /*!
      Returns the total count of a query.
    */
    function getQueryCount( $query  )
    {
        $this->dbInit();
        $link_array = 0;

        $query = new eZQuery( array( "KeyWords", "Title", "Description" ), $query );
        
        $query_str = "SELECT count(ID) AS Count FROM eZLink_Link WHERE (" .
             $query->buildQuery()  .
             ") AND Accepted='Y' ORDER BY Title";

        array_query( $link_array, $query_str );

        $ret = 0;
        if ( count( $link_array ) == 1 )
            $ret = $link_array[0]["Count"];

        return $ret;
    }
    

    /*!
      Fetches all the links.
    */
    function getAll()
    {
        $this->dbInit();
        $group_array = 0;

        array_query( $group_array, "SELECT * FROM eZLink_Link ORDER BY Title" );

        return $group_array;
    }

    /*!
      Check if the url exists.
    */
    function checkUrl( $url )
    {
        $this->dbInit();

        array_query( $url_array, "SELECT url FROM eZLink_Link WHERE url='$url'" );

        return count( $url_array );
    }

    /*!
      Returns the id of the link.
    */
    function id()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->ID;
    }


    /*!
      Sets the link title.
    */
    function setTitle( $value )
    {
        $this->Title = $value;
    }

    /*!
      Sets the link description
    */
    function setDescription( $value )
    {
        $this->Description = $value;
    }

    /*!
      Sets the linkgroupID.
    */
    function setLinkGroupID( $value )
    {
        $this->LinkGroupID = ( $value );
    }

    /*!
      Sets the link keywords.
    */    
    function setKeyWords( $value )
    {
        $this->KeyWords = ( $value );
    }

    /*!
       Sets the modified date of the link.
    */
    function setModified( $value )
    {
        $this->Modified = ( $value );
    }

    /*!
      Sets if the link is accepted.
    */
    function setAccepted( $value )
    {
        $this->Accepted = ( $value );
    }

    /*!
      Sets the link URL.
    */
    function setUrl( $value )
    {
        $this->Url = ( $value );
    }

    /*!
      Returns the link title.
    */
    function title()
    {
        return $this->Title;
    }


    /*!
      Returns the link description.
    */
    function description()
    {
        return $this->Description;
    }

    /*!
      Returns the linkgroupID.
    */
    function linkGroupID()
    {
        return $this->LinkGroupID;
    }

    /*!
      Returns the link keywords.
    */
    function keyWords()
    {
        return $this->KeyWords;
    }

    /*!
      Returns the date when the link was created.
    */
    function created()
    {
        return $this->Created;
    }

    /*!
      Returns the date when the link was modified.
    */
    function modified()
    {
        return $this->Modified;
    }

    /*!
      Returns if the link is Accepted.
    */
    function accepted()
    {
        return $this->Accepted;
    }

    /*!
      Retruns the url of the link.
    */
    function url()
    {
        return $this->Url;
    }

    /*!
      Returns the id of the link.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      \private
      \static
      
      Open the database for read and write. Gets all the database information from site.ini.
    */
    function dbInit()
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = new eZDB( "site.ini", "site" );
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $Title;
    var $Description;
    var $LinkGroupID;
    var $KeyWords;
    var $Created;
    var $Modified;
    var $Accepted;
    var $Url;
    var $url_array;

    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}
?>
