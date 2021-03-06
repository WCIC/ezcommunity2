<?
// 
// $Id: ezforum.php,v 1.5 2000/10/26 13:23:25 ce-cvs Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <11-Sep-2000 22:10:06 bf>
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

//!! eZForum
//! The eZForum class handles forum's in the database.
/*!
  
  \sa eZForumMessage \eZForumCategory
*/

/*!TODO
  Moderated='$this->Moderated',  Private='$this->Private' to use enum( 'true', 'false' )
  and use bool in the class. Rename the functions an variables to IsModerated and IsPrivate.  
*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezquery.php" );
include_once( "ezforum/classes/ezforummessage.php" );


class eZForum
{
    /*!
      Constructs a new eZForum object.
    */
    function eZForum( $id="", $fetch=true )
    {
        $this->IsConnected = false;

        if ( $id != "" )
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
      Stores a eZForum object to the database.
    */
    function store()
    {
        $this->dbInit();

        if ( !isset( $this->ID ) )
        {
            $this->Database->query( "INSERT INTO eZForum_Forum SET
		                         CategoryID='$this->CategoryID',
		                         Name='$this->Name',
		                         Description='$this->Description',
		                         Moderated='$this->Moderated',
		                         Private='$this->Private'
                                 " );

            $this->ID = mysql_insert_id();

            $this->State_ = "Coherent";
        }
        else
        {
            $this->Database->query( "UPDATE eZForum_Forum SET
		                         CategoryID='$this->CategoryID',
		                         Name='$this->Name',
		                         Description='$this->Description',
		                         Moderated='$this->Moderated',
		                         Private='$this->Private'
                                 WHERE ID='$this->ID'
                                 " );

            $this->State_ = "Coherent";
        }
        
        return true;
    }

    /*!
      Deletes a eZForumCategory object from the database.
    */
    function delete()
    {
        $this->dbInit();

        $message = new eZForumMessage();
        $message->get( $this->ID );
        $message->delete();
        $this->Database->query( "DELETE FROM eZForum_Forum WHERE ID='$this->ID'" );
        
        return true;
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
            $this->Database->array_query( $forum_array, "SELECT * FROM eZForum_Forum WHERE ID='$id'" );
            if ( count( $forum_array ) > 1 )
            {
                die( "Error: Forum's with the same ID was found in the database. This shouldent happen." );
            }
            else if( count( $forum_array ) == 1 )
            {
                $this->ID = $forum_array[0][ "ID" ];
                $this->CategoryID = $forum_array[0][ "CategoryID" ];
                $this->Name = $forum_array[0][ "Name" ];
                $this->Description = $forum_array[0][ "Description" ];
                $this->Moderated = $forum_array[0][ "Moderated" ];
                $this->Private = $forum_array[0][ "Private" ];

                $this->State_ = "Coherent";
                $ret = true;
            }
        }
        else
        {
            $this->State_ = "Dirty";
        }
        return $ret;
    }
    

    /*!
      Returns every forum.
    */
    function getAll( )
    {

    }

        /*!
      Returns every forum.
    */
    function getAllByCategory( $CategoryID )
    {
        $this->dbInit();

        $ret = array();

        $this->dbInit();

        $this->Database->array_query( $forum_array, "SELECT ID FROM
                                                       eZForum_Forum WHERE CategoryID='$CategoryID'" );
                                                     
        $ret = array();

        foreach ( $forum_array as $forum )
        {
            $ret[] = new eZForum( $forum["ID"] );
        }

        return $ret;

    }


    /*!
      Returns the messages in a forum.
    */
    function messages( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
       $this->dbInit();

       $this->Database->array_query( $message_array, "SELECT ID FROM
                                                       eZForum_Message
                                                       WHERE ForumID='$this->ID' ORDER BY PostingTime DESC" );

       $ret = array();

       foreach ( $message_array as $message )
       {
           $ret[] = new eZForumMessage( $message["ID"] );
       }
       
       return $ret;
    }

    /*!
      Returns the messages in every forum matching the query string.
    */
    function search( $query, $offset, $limit )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
       $this->dbInit();

       $query = new eZQuery( array( "Topic", "Body" ), $query );
               
       $query_str = "SELECT ID FROM eZForum_Message WHERE (" .
             $query->buildQuery()  .
             ") ORDER BY PostingTime LIMIT $offset, $limit";       

       $this->Database->array_query( $message_array, $query_str );
       $ret = array();

       foreach ( $message_array as $message )
       {
           $ret[] = new eZForumMessage( $message["ID"] );
       }
       
       return $ret;
    }


    /*!
      Returns the total count of a query.
    */
    function getQueryCount( $query  )
    {
        $this->dbInit();
        $message_array = 0;

        $query = new eZQuery( array( "Topic", "Body" ), $query );

        $query_str = "SELECT count(ID) AS Count FROM eZForum_Message WHERE (" . $query->buildQuery() . ") ORDER BY PostingTime";
        
        $this->Database->array_query( $message_array, $query_str );

        $ret = 0;
        if ( count( $message_array ) == 1 )
            $ret = $message_array[0]["Count"];

        return $ret;
    }


    /*!
      Returns all the messages and submessages as a tree.

      Default limit is set to 30.
    */
    function &messageTree( $offset=0, $limit=30 )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
       $this->dbInit();

       $this->Database->array_query( $message_array, "SELECT ID FROM
                                                       eZForum_Message
                                                       WHERE ForumID='$this->ID' ORDER BY TreeID DESC LIMIT $offset,$limit" );

       $ret = array();

       foreach ( $message_array as $message )
       {
           $ret[] = new eZForumMessage( $message["ID"] );
       }
       
       return $ret;
    }

    /*!
      Returns all the messages and submessages of a thread as a tree.

      Default limit is set to 100
    */
    function &messageThreadTree( $threadID, $offset=0, $limit=100 )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
       $this->dbInit();

       $this->Database->array_query( $message_array, "SELECT ID FROM
                                                       eZForum_Message
                                                       WHERE ForumID='$this->ID' AND ThreadID='$threadID' ORDER BY TreeID DESC LIMIT $offset,$limit" );

       $ret = array();

       foreach ( $message_array as $message )
       {
           $ret[] = new eZForumMessage( $message["ID"] );
       }
       
       return $ret;
    }
    
        
    /*!
      Returns the object id.
    */
    function id()
    {
        return $this->ID;
    }
        
    /*!
      
    */
    function categoryID()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
        return $this->CategoryID;
    }
        
    /*!
      
    */
    function setCategoryID( $newCategoryID )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
       $this->CategoryID = $newCategoryID;
    }
        
    /*!
      
    */
    function name()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
        return $this->Name;
    }
        
    /*!
      
    */
    function setName($newName)
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
        $this->Name = $newName;
    }
        
    /*!
      
    */
    function description()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
        return $this->Description;
    }
        
    /*!
      
    */
    function setDescription($newDescription)
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
        $this->Description = $newDescription;
    }
        
    /*!
      
    */
    function moderated()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
        return $this->Moderated;
    }
        
    /*!
      
    */
    function setModerated($newModerated)
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
        $this->Moderated = $newModerated;
    }
        
    /*!
      
    */
    function private()
    {
        return $this->Private;
    }
        
    /*!
      
    */
    function setPrivate($newPrivate)
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        
        $this->Private = $newPrivate;
    }

    /*!
      Returns the number of threads in the forum.
    */
    function threadCount()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $this->dbInit();

       $this->Database->array_query( $message_array, "SELECT ID FROM
                                                       eZForum_Message
                                                       WHERE ForumID='$this->ID' GROUP BY ThreadID" );

       $ret = count( $message_array );

       return $ret;
    }

    /*!
      Returns the number of messages in the forum.
    */
    function messageCount()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $this->dbInit();

       $this->Database->array_query( $message_array, "SELECT ID FROM
                                                       eZForum_Message
                                                       WHERE ForumID='$this->ID'" );

       $ret = count( $message_array );

       return $ret;
    }
    
    /*!
      \private
      Opens the database for read and write.
    */
    function dbInit( )
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = new eZDB( "site.ini", "site" );
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $CategoryID;
    var $Name;
    var $Description;
    var $Moderated;
    var $Private;

    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}

?>
