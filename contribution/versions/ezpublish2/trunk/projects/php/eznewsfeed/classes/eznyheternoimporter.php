<?php
// 
// $Id: eznyheternoimporter.php,v 1.2 2000/11/19 12:32:58 bf-cvs Exp $
//
// Definition of eZNyhterNOImporter class
//
// B�rd Farstad <bf@ez.no>
// Created on: <13-Nov-2000 17:07:56 bf>
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
//! eZNyhterNOImporter handles importing of news bullets from nyheter.no.
/*!
  Example code:
  \sa eZNews eZNewsCategory eZNewsImporter
*/

/*!TODO

*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezdatetime.php" );
include_once( "eznewsfeed/classes/eznews.php" );

class eZNyheterNOImporter
{
    /*!
      Constructor.
    */
    function eZNyheterNOImporter( $site, $login="", $password="" )
    {
        $this->Site = $site;
        $this->Login = $login;
        $this->Password = $password;
    }

    /*!
      Imports news from the given site.
    */
    function &news( )
    {
        $return_array = array();
        $fp = fopen( "/home/bf/nyheter.xml", "r" );
        $output = fread ( $fp, 100000000 );
        fclose( $fp );

        $doc = qdom_tree( $output );

        $articleCount = 0;

        foreach ( $doc->children as $child )
        {
            if ( $child->name == "NEWSFEED" )
            {
                foreach ( $child->children as $query )
                {
                    if ( $query->name == "QUERYID" )
                    {
                        foreach ( $query->children as $article )
                        {
                            
                            if ( $article->name == "ARTICLE" )
                            {
                                $name = "";
                                $description = "";
                                $urlSource = "";
                                $publishingDate = "";
                                $url = "";
                                $origin = "";
                                
                                foreach ( $article->children as $articleElement )
                                {
                                    $content = "";                                    
                                    foreach ( $articleElement->children as $value )
                                    {
                                        if ( $value->name == "#cdata-section" )
                                        {
                                            $content = $value->content;
                                        }

                                        if ( $value->name == "#text" )
                                        {
                                            $content = $value->content;
                                        }                                        
                                    }

                                    switch ( $articleElement->name )
                                    {
                                        case "URLSOURCE" :
                                        {
                                            $origin =& trim( $content );
                                        }
                                        break;

                                        case "URL" :
                                        {
                                            $url =& trim( $content );
                                        }
                                        break;
                                        
                                        case "URLTIME" :
                                        {
                                            $publishingDate =& trim( $content );
                                        }
                                        break;

                                        case "LINKTEXT" :
                                        {
                                            $name =& trim( $content );
                                        }
                                        break;

                                        case "CAPTION" :
                                        {
                                            $description =& trim( $content );
                                        }
                                        break;
                                        
                                    }

                                }
                                
                                $news = new eZNews( );

                                $news->setName( addslashes( $name ) );
                                $news->setIntro( addslashes( $description ) );
                                $news->setIsPublished( false );

                                $news->setOrigin( $origin );
                                $news->setURL( $url );

//                                  print( "-$publishingDate-"  );

                                if ( ereg( "([0-9]{4})([0-9]{2})([0-9]{2})-([0-9]{2})([0-9]{2})([0-9]{2})", $publishingDate, $valueArray ) )
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
                                else
                                {
                                    print( "<b>Error:</b> eZDateTime::setMySQLDate() received wrong MySQL date format." );
                                }
                                
                                
                                $return_array[] = $news;
                            

                                $articleCount++;                                
                            }
                        }
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
