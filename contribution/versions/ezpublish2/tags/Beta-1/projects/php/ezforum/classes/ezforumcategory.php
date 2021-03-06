<?
// 
// $Id: ezforumcategory.php,v 1.21 2000/10/17 14:16:49 ce-cvs Exp $
//
// Definition of eZForumCategory class
//
// Lars Wilhelmsen <lw@ez.no>
// Created on: <11-Sep-2000 22:10:06 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZForum
//! The eZForumCategory class handles forum categories.
/*!
  
  \sa eZForum
*/


/*!TODO
  

*/

include_once( "classes/ezdb.php" );
include_once( "ezforum/classes/ezforum.php" );

class eZForumCategory
{
    /*!
      Constructs a new eZForumCategory object.
    */
    function eZForumCategory( $id="", $fetch=true )
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
      Stores a eZForumCategory object to the database.
    */
    function store()
    {
        $this->dbInit();

        if ( !isset( $this->ID ) )
        {
            $this->Database->query( "INSERT INTO eZForum_Category SET
		                         Name='$this->Name',
		                         Description='$this->Description'
                                 " );

            $this->ID = mysql_insert_id();

            $this->State_ = "Coherent";
        }
        else
        {
            $this->Database->query( "UPDATE eZForum_Category SET
		                         Name='$this->Name',
		                         Description='$this->Description'
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
        print( $this->ID );
        $this->dbInit();

        $forum = new eZForum();
        $forum->get( $this->ID );
        $forum->delete();
        $this->Database->query( "DELETE FROM eZForum_Category WHERE ID='$this->ID'" );
        
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
            $this->Database->array_query( $category_array, "SELECT * FROM eZForum_Category WHERE ID='$id'" );
            if ( count( $category_array ) > 1 )
            {
                die( "Error: Category's with the same ID was found in the database. This shouldent happen." );
            }
            else if( count( $category_array ) == 1 )
            {
                $this->ID = $category_array[0][ "ID" ];
                $this->Name = $category_array[0][ "Name" ];
                $this->Description = $category_array[0][ "Description" ];

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
      Returns every category as an array of eZForumCategory objects.
    */
    function getAll( )
    {
        $this->dbInit();

        $ret = array();

        $this->dbInit();

        $this->Database->array_query( $category_array, "SELECT ID FROM
                                                       eZForum_Category" );
                                                     
        $ret = array();

        foreach ( $category_array as $category )
            {
                $ret[] = new eZForumCategory( $category["ID"] );
            }

        return $ret;
    }

    /*!
      Returns every forum under the current category.
    */
    function forums()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
       $this->dbInit();

       $this->Database->array_query( $forum_array, "SELECT ID FROM
                                                       eZForum_Forum
                                                       WHERE CategoryID='$this->ID'" );

       $ret = array();

       foreach ( $forum_array as $forum )
       {
           $ret[] = new eZForum( $forum["ID"] );
       }
       
       return $ret;
    }
    
        
    /*!
      
    */
    function getAllCategories()
    {
        $this->dbInit();

        $this->Database->array_query( $category_array, "SELECT ID FROM eZForum_Category" );

        $ret = array();
        foreach( $category_array as $category )
        {
            $ret[] = new eZForumCategory( $category["ID"] );
        }
        return $ret;
    }

    /*
      Returns the object id.
    */
    function id()
    {
        return $this->ID;
    }
        
    /*!
      
    */
    function setName( $newName )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->Name = $newName;
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
    function setDescription( $newDescription )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->Description = $newDescription;
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
    var $Name;
    var $Description;

    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;
}
?>
