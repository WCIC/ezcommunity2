<?php
// 
// $Id: ezlinkitem.php,v 1.1 2001/03/21 13:38:56 jb Exp $
//
// Definition of eZLinkItem class
//
// Jan Borsodi <jb@ez.no>
// Created on: <19-Mar-2001 13:15:16 amos>
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

//!! 
//! The class eZLinkItem does
/*!

*/

class eZLinkItem
{
    function eZLinkItem( $id, $module )
    {
        $this->Module = $module;
        if ( is_array( $id ) )
        {
            $this->fill( $id );
        }
        else if ( is_numeric( $id ) )
        {
            $this->get( $id );
        }
    }

    function get( $id )
    {
        $db =& eZDB::globalDatabase();
        $table_name = $this->Module . "_Link";
        $db->query_single( $row, "SELECT ID, Name, URL, Placement FROM $table_name WHERE ID='$id'" );
        $this->fill( $row );
    }

    function store()
    {
        $table_name = $this->Module . "_Link";
        $db =& eZDB::globalDatabase();
        if ( is_numeric( $this->ID ) and $this->ID > 0 )
        {
            $qry_text = "UPDATE $table_name";
            $qry_where = "WHERE ID='$this->ID'";
        }
        else
        {
            $qry_text = "INSERT INTO $table_name";
            $db->array_query( $qry_array, "SELECT Placement FROM $table_name
                                           WHERE SectionID='$this->SectionID'
                                           ORDER BY Placement DESC LIMIT 1", 0, 1 );
            $this->Placement = count( $qry_array ) == 1 ? $qry_array[0]["Placement"] + 1 : 1;
        }
        $db->query( "$qry_text
                     SET SectionID='$this->Section',
                         Name='$this->Name',
                         URL='$this->URL',
                         Placement='$this->Placement' $qry_where" );
        $this->ID = $db->insertID();
    }

    function delete( $id = false, $module = false )
    {
        if ( !$id )
            $id = $this->ID;
        if ( !$module )
            $module = $this->Module;
        $table_name = $module . "_Link";
        $db =& eZDB::globalDatabase();
        $db->query( "DELETE FROM $table_name WHERE ID='$id'" );
    }

    function fill( $row )
    {
        $this->ID = $row["ID"];
        $this->Name = $row["Name"];
        $this->URL = $row["URL"];
        $this->Placement = $row["Placement"];
    }

    function setSection( $section )
    {
        $this->Section = $section;
    }

    function setName( $name )
    {
        $this->Name = $name;
    }

    function setURL( $url )
    {
        $this->URL = $url;
    }

    function id()
    {
        return $this->ID;
    }

    function section()
    {
        return $this->Section;
    }

    function name()
    {
        return $this->Name;
    }

    function url()
    {
        return $this->URL;
    }

    var $ID;
    var $Section;
    var $Name;
    var $URL;
    var $Placement;
    var $Module;
}

?>
