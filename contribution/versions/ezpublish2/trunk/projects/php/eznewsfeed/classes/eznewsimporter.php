<?php
// 
// $Id: eznewsimporter.php,v 1.2 2000/11/19 11:10:02 bf-cvs Exp $
//
// Definition of eZNewsImporter class
//
// B�rd Farstad <bf@ez.no>
// Created on: <13-Nov-2000 16:56:48 bf>
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

//!! eZNewsFeed
//! eZNewsImporter handles importing of news bullets from other sites.
/*!
  Example code:
  \sa eZNewsCategory

*/

/*!TODO

*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezdatetime.php" );
include_once( "ezuser/classes/ezuser.php" );

class eZNewsImporter
{
    /*!
      Create a new importer with the given decoder and site. Login and
      password are default not used.
    */
    function eZNewsImporter( $decoder, $site, $category,  $login="", $password="" )
    {
        $this->Site = $site;
        $this->Decoder = $decoder;
        $this->Login = $login;
        $this->Password = $password;
        if ( get_class( $category ) == "eznewscategory" )
        {
            $this->CategoryID = $category->id();
        }
    }

    /*!
      Imports news from the given site.
    */
    function importNews( )
    {
        $category = new eZNewsCategory( $this->CategoryID );
        
        switch ( $this->Decoder )
        {
            case "nyheter.no" :
            {
                include_once( "eznewsfeed/classes/eznyheternoimporter.php" );
                
                $importer = new eZNyheterNOImporter();
                $importer->news();
            }

            case "rdf" :
            {
                include_once( "eznewsfeed/classes/ezrdfimporter.php" );
                
                $importer = new eZRDFImporter( $this->Site, $this->Login, $this->Password );
                $newsList =& $importer->news();

                foreach ( $newsList as $newsItem )
                {
                    if ( $newsItem->store() == true )
                    {
                        $category->addNews( $newsItem );
                        print( "storing: -" .$newsItem->name() . "<br>");
                    }
                    else
                    {
                        print( "already stored: -" .$newsItem->name() . "<br>");
                    }
                }
            }
        }
    }

    var $Decoder;
    var $Site;
    var $Login;
    var $Password;
    var $CategoryID;
}
