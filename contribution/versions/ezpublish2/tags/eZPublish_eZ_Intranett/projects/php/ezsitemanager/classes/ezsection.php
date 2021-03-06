<?php
// 
// $Id: ezsection.php,v 1.9 2001/08/20 09:40:34 bf Exp $
//
// ezsection class
//
// Created on: <10-May-2001 15:13:08 ce>
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

//!! eZSection
//! eZSections handles sections..
/*!

  Example code:
  \code
  \endcode

*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezdatetime.php" );
	      
class eZSection
{

    /*!
      Constructs a new eZSection object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZSection( $id=-1 )
    {
        if ( $id != -1 )
        {
            $this->ID = $id;
            $this->get( $this->ID );
        }
    }
    
     /*!
      Stores a eZSection object to the database.
    */
    function store()
    {
        $db =& eZDB::globalDatabase();

        $db->begin( );
        
        $name = $db->escapeString( $this->Name );
        $description = $db->escapeString( $this->Description );
        $sitedesign = $db->escapeString( $this->SiteDesign );
        $templateStyle = $db->escapeString( $this->TemplateStyle );
             
        if ( !isset( $this->ID ) )
        {
            $db->lock( "eZSiteManager_Section" );

            $nextID = $db->nextID( "eZSiteManager_Section", "ID" );
            
            $timeStamp = eZDateTime::timeStamp( true );

            $res = $db->query( "INSERT INTO eZSiteManager_Section
                                     ( ID,  Name, Created, Description, TemplateStyle, SiteDesign )
                                     VALUES
                                     ( '$nextID',
                                       '$name',
                                       '$timeStamp',
                                       '$description',
                                       '$templateStyle',
                                       '$sitedesign' )" );

			$this->ID = $nextID;
        }
        else
        {
        print( $templateStyle );
            $res = $db->query( "UPDATE eZSiteManager_Section SET
		                             Name='$name',
		                             SiteDesign='$sitedesign',
		                             TemplateStyle='$templateStyle',
                                     Description='$description'
                                     WHERE ID='$this->ID'" );
        }

        $db->unlock();
    
        if ( $res == false )
            $db->rollback( );
        else
            $db->commit();
        
        return true;
    }

    /*!
      Deletes a eZSection object from the database.
    */
    function delete( $catID=-1 )
    {
        if ( $catID == -1 )
            $catID = $this->ID;

        $db =& eZDB::globalDatabase();

        $db->query( "DELETE FROM eZSiteManager_Section WHERE ID='$this->ID'" );
    }

    /*!
      Fetches the object information from the database.
    */
    function get( $id=-1 )
    {
        $db =& eZDB::globalDatabase();
        $ret = false;
        
        if ( $id != "" )
        {
            $db->array_query( $section_array, "SELECT * FROM eZSiteManager_Section WHERE ID='$id'" );
            if ( count( $section_array ) > 1 )
            {
                die( "Error: Section's with the same ID was found in the database. This shouldent happen." );
            }
            else if( count( $section_array ) == 1 )
            {
                $this->ID = $section_array[0][$db->fieldName("ID")];
                $this->Name = $section_array[0][$db->fieldName("Name")];
                $this->SiteDesign = $section_array[0][$db->fieldName("SiteDesign")];
                $this->TemplateStyle = $section_array[0][$db->fieldName("TemplateStyle")];
                $this->Description = $section_array[0][$db->fieldName("Description")];
                $this->Created = $section_array[0][$db->fieldName("Created")];
                $ret = true;
            }
        }

        return $ret;
    }

    /*!
      Returns all the categories found in the database.

      The categories are returned as an array of eZSection objects.
    */
    function getAll( $offset=0, $limit=40)
    {
        $db =& eZDB::globalDatabase();
        
        $return_array = array();
        $section_array = array();
        
        $db->array_query( $section_array, "SELECT ID, Created
                                           FROM eZSiteManager_Section
                                           ORDER BY Created ASC",
                          array( "Limit" => $limit, "Offset" => $offset ) );
        
        for ( $i=0; $i < count($section_array); $i++ )
        {
            $return_array[$i] = new eZSection( $section_array[$i][$db->fieldName("ID")]  );
        }
        
        return $return_array;
    }

    /*!
      Returns the total count.
     */
    function count()
    {
        $db =& eZDB::globalDatabase();
        $ret = false;

        $db->query_single( $result, "SELECT COUNT(ID) as Count
                                     FROM eZSiteManager_Section" );
        $ret = $result[$db->fieldName("Count")];
        return $ret;
    }


    /*!
      Returns the object ID to the section. This is the unique ID stored in the database.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the name of the section.
    */
    function name()
    {
        return htmlspecialchars( $this->Name );
    }
    
    /*!
      \static
      Returns the SiteDesign of the section.

      If $sectionID is a number, the function will return the sitedesign for that section ID.
    */
    function siteDesign( $sectionID=false )
    {
       if ( is_numeric ( $sectionID ) )
       {
           $db =& eZDB::globalDatabase();
           $db->query_single( $siteDesign, "SELECT SiteDesign FROM eZSiteManager_Section WHERE ID='$sectionID'" );
           return $siteDesign[$db->fieldName("SiteDesign")];
       }
       else
           return htmlspecialchars( $this->SiteDesign );
    }


    /*!
      Returns the template style for this section.
    */
    function templateStyle( $sectionID=false )
    {
       if ( is_numeric ( $sectionID ) )
       {
           $db =& eZDB::globalDatabase();
           $db->query_single( $templateStyle, "SELECT TemplateStyle FROM eZSiteManager_Section WHERE ID='$sectionID'" );
           return $templateStyle[$db->fieldName("TemplateStyle")];
       }
       else
           return htmlspecialchars( $this->TemplateStyle );
    }
    
    /*!
      Returns the section description.
    */
    function description()
    {
        return htmlspecialchars( $this->Description );
    }
    
    /*!
      Sets the name of the section.
    */
    function setName( $value )
    {
        $this->Name = $value;
    }
    
    /*!
      Sets the SiteDesign of the section.
    */
    function setSiteDesign( $value )
    {
        $this->SiteDesign = $value;
    }

    /*!
      Sets the TemplateStyle of the section.
    */
    function setTemplateStyle( $value )
    {
        $this->TemplateStyle = $value;
    }
    
    /*!
      Sets the description of the section.
    */
    function setDescription( $value )
    {
        $this->Description = $value;
    }


    var $ID;
    var $Name;
    var $SiteDesign;
    var $TemplateStyle;
    var $Description;
    var $Created;

}

?>
