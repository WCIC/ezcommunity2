<?php
// 
// $Id: ezbackslashimporter.php,v 1.9.2.1 2001/10/29 16:49:39 bf Exp $
//
// Definition of ezbackslashimporter class
//
// Created on: <02-Jan-2001 14:14:55 bf>
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

//!! eZNewsFeed
//! eZBackslashImporter handles importing of news bullets from rdf driver sites like freshmeat.net.
/*!
  Example code:
  \sa eZNews eZNewsCategory eZNewsImporter
*/

/*!TODO

*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezdatetime.php" );
include_once( "eznewsfeed/classes/eznews.php" );

class eZBackslashImporter
{
    /*!
      Constructor.
    */
    function eZBackslashImporter( $site, $login="", $password="" )
    {
        $this->Site = $site;
        $this->Login = $login;
        $this->Password = $password;
    }

    /*!
      Returns the news items as an array.
    */
    function &news( )
    {
        $db =& eZDB::globalDatabase();
        $return_array = array();
        $fp = eZFile::fopen( $this->Site, "r" );
        $output = fread ( $fp, 100000000 );
        fclose( $fp );

        $doc = xmltree( $output );
        if ( count( $doc->children ) > 0 )
        foreach ( $doc->children as $child )
        {
            if ( $child->name == "backslash" || $child->name == "linuxtoday" || $child->name == "RDF" )
            {
                foreach ( $child->children as $channel )
                {
                    if ( $channel->name == "story" )
                    {
                        $title = "";
                        $link = "";
                        $description = "";
                        
                        foreach ( $channel->children as $item )
                        {
                            $content = $value->content;
                            
                            switch ( $item->name )
                            {
                                case "title" :
                                {
                                    $title = $content;
                                }
                                break;
                                
                                case "url" :
                                {
                                    $link = $content;
                                }
                                break;
                                
                                case "time" :
                                {
                                    $publishingDate = $content;
                                }
                                break;                                    
                            }
                        }

                        $news = new eZNews();
                        $news->setName( $title );
                        $news->setURL( $link );

                        if ( ereg( "([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $publishingDate, $valueArray ) )
                        {
                            $year = ( $valueArray[1] );
                            $month = ( $valueArray[2] );
                            $day = ( $valueArray[3] );
                            $hour = ( $valueArray[4] );
                            $minute = ( $valueArray[5] );
                            $second = ( $valueArray[6] );
                                            
                            $dateTime = new eZDateTime( $year, $month, $day, $hour, $minute, $second );
                            $news->setOriginalPublishingDate( $dateTime );
                        }

                        $return_array[] = $news;
                    }
                }
            }
        }
        
        return $return_array;
    }

    var $Site;
    var $Login;
    var $Password;    
}

?>
