<?
// 
// $Id: ezforumcategory.php,v 1.14 2000/09/15 13:47:28 bf-cvs Exp $
//
// Definition of eZCompany class
//
// Lars Wilhelmsen <lw@ez.no>
// Created on: <11-Sep-2000 22:10:06 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//include_once( "$DOCROOT/classes/ezdb.php" );

//!! eZForum
//! The eZForumCategory class handles forum categories.
/*!
  
  \sa eZForumForum
*/

class eZForumCategory
{
    /*!
      
    */
    function newCategory()
    {
        unset( $this->Id );
    }
        
    /*!
      
    */
    function get( $Id )
    {
        $this->openDB();
            
        $query_id = mysql_query("SELECT Name, Description, Private FROM ezforum_CategoryTable WHERE Id='$Id'")
             or die("eZforumCategory::get($id) failed, dying...");

        if ( mysql_num_rows( $query_id ) == 1 )
        {
            $this->Id = $Id;
            $this->Name = mysql_result($query_id, 0, "Name" );
            $this->Description = mysql_result($query_id, 0, "Description" );
            $this->Private = mysql_result($query_id, 0, "Private" );
        }
    }
        
    /*!
      
    */
    function getAllCategories()
    {
        $this->openDB();

        $query_id = mysql_query( "SELECT * FROM ezforum_CategoryTable" )
             or die("eZforumCategory::getAllCategories() failed, dying...");
            
        for ($i = 0;$i < mysql_num_rows( $query_id ); $i++ )
        {
            $returnArray[$i] = mysql_fetch_array($query_id);   
        }
        return $returnArray;
    }
        
    /*!
      
    */
    function store()
    {
        $this->openDB();
            
        $this->Name = addslashes($this->Name);
        $this->Description = addslashes($this->Description);
        $this->Private = addslashes($this->Private);            

        if ( $this->Id )
        {
            $query_id = mysql_query("UPDATE ezforum_CategoryTable SET Name='$this->Name',
                                                             Description='$this->Description',
                                                             Private='$this->Private'
                                         WHERE Id='$this->Id'")
                 or die("store() near UPDATE...");
        }
        else
        {
            $query_id = mysql_query("INSERT INTO ezforum_CategoryTable(Name, Description, Private)
                                                     VALUES('$this->Name', '$this->Description', '$this->Private')")
                 or die("store() near INSERT...");
            return mysql_insert_id();
        }
    }
        
    /*!
      
    */
    function delete($Id)
    {
        $this->openDB();
        
        mysql_query("DELETE FROM ezforum_CategoryTable WHERE Id='$Id'")
            or die("delete($Id)");
    }
        
    /*!
      
    */
    function id()
    {
        return $this->Id;
    }
        
    /*!
      
    */
    function setName($newName)
    {
        $this->Name = $newName;
    }

    /*!
      
    */
    function name()
    {
        return $this->Name;
    }
        
    /*!
      
    */
    function setDescription($newDescription)
    {
        $this->Description = $newDescription;
    }
        
    /*!
      
    */
    function description()
    {
        return $this->Description;
    }
        
    /*!
      
    */
    function setPrivate($newPrivate)
    {
        $this->Private = $newPrivate;
    }
        
    /*!
      
    */
    function private()
    {
        return $this->Private;
    }

    /*!
      
    */
    function categoryForumInfo($Id)
    {
        $this->openDB();
    
        $query_id = mysql_query("SELECT ezforum_ForumTable.Name AS ForumName,
                                ezforum_CategoryTable.Name AS CategoryName
                                FROM ezforum_ForumTable, ezforum_CategoryTable
                                WHERE ezforum_CategoryTable.Id = ezforum_ForumTable.CategoryId
                                AND ezforum_ForumTable.Id = '$Id'")
             or die("categoryForumInfo()");
        
        return mysql_fetch_array($query_id);
    }

    /*!
      Privat funksjon, skal kun brukes ac ezuser klassen.
      Funksjon for � �pne databasen.
    */
    function openDB( )
    {
        include_once( "classes/INIFile.php" );

        $ini = new INIFile( "site.ini" );
        
        $SERVER = $ini->read_var( "site", "Server" );
        $DATABASE = $ini->read_var( "site", "Database" );
        $USER = $ini->read_var( "site", "User" );
        $PWD = $ini->read_var( "site", "Password" );
        
        mysql_pconnect( $SERVER, $USER, $PWD ) or die( "Kunne ikke kople til database" );
        mysql_select_db( $DATABASE ) or die( "Kunne ikke velge database" );
    }
    
    var $Id;
    var $Name;
    var $Description;
    var $Private;
}
?>
