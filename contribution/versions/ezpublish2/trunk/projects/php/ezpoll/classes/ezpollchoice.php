<?// 
// $Id: ezpollchoice.php,v 1.1 2000/09/20 11:07:15 ce-cvs Exp $
//
// Definition of eZPollChoice class
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <19-Sep-2000 17:37:53 ce>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZPollChoice
//! The eZPollChoice class handles the options for polls.
/*!
  The eZPollChoice class handles poll options. The class has function for storing and fetching from
  the database.

  Example code:
  \code
  
  
  \endcode
  \sa eZPoll eZVote
*/

include_once( "classes/ezdb.php" );

class eZPollChoice
{
        /*!
      Constructor a new eZPoll object. Retrieves the data from the database
      if a valid id is given as an argument.
    */
    function eZPollChoice( $id=-1, $fetch=true )
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
      Stores a eZPoll object to the database.
    */
    function store()
    {
        $this->dbInit();

        $this->Database->query( "INSERT INTO eZPoll_PollChoice SET
                                 Name='$this->Name',
                                 Description='$this->Description',
                                 IsEnabled='$this->IsEnabled',
                                 IsClosed='$this->IsClosed' ");

        $this->ID = mysql_insert_id();

        return true;
    }

    /*!
      Fetches the poll object from the database.
    */
    function get( $id=-1 )
    {
        $this->dbInit();

        if ( $id != -1 )
        {
            $this->Database->array_query( $poll_array, "SELECT * FROM eZPoll_Poll" );

            if ( count( $poll_array ) > 1 )
            {
                die( "Error: Poll's wit the same ID was found in the database." );
            }
            else if( count( $poll_array ) == 1 )
            {
                $this->ID = $poll_array[0][ "ID" ];
                $this->
                     }

        }
    }

    /*!
      Fetches the poll id from the database. And returns a array of eZPoll objects.
    */
    function getAll()
    {
        $this->dbInit();

        $return_array = array();
        $poll_array = array();

        $this->Database->array_query( $poll_array, "SELECT ID FROM ezPoll" );

        for ( $i=0; $i<count( $poll_array ); $i++ )
        {
            $return_array[$i] = new eZPoll( $poll_array[$i][ "ID" ], 0 );
        }

        return $return_array;
    }

    /*!
      Returns the name of the pollchoice.
    */
    function name()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Name;
    }

    /*!
      Returns the PollID of the Pollchoice.
    */
    function pollID()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->PollID;
    }

    /*!
      Sets the name of the poll.
    */
    function setName( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->Name = $value;
    }

    /*!
      Sets the name of the poll.
    */
    function setPollID( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->PollID = $value;
    }

        /*!
      Private function.
      Open the database for read and write. Gets all the database information from site.ini.
    */
    function dbInit()
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = new eZDB( "site.ini", "eZPollMain" );
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $Name;
    var $PollID;
}
