<?php
// 
// $Id: ezarticlegenerator.php,v 1.8 2001/01/22 14:42:59 jb Exp $
//
// Definition of eZArticleGenerator class
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Oct-2000 15:47:43 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2001 eZ systems as
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

//!! eZArticle
//! eZArticleGenerator handles article XML generation.
/*!
  This class handles generation of XML contents for
  the articles. It will read from the .ini file and find
  the default generator to use.
*/

include_once( "classes/INIFile.php" );

class eZArticleGenerator
{
    function eZArticleGenerator( $generatorType="" )
    {
        if ( $generatorType == "" )
        {
            $ini = new INIFile( "site.ini" );

            $Generator = $ini->read_var( "eZArticleMain", "Generator" );
            $generatorType = $Generator;
        }


        switch ( $generatorType )
        {            
            case "tech" :
            {
                $this->GeneratorFile = "eztechgenerator.php";
                $this->GeneratorClass = "eZTechGenerator";
            }
            break;

            case "ez" :
            {
                $this->GeneratorFile = "ezezgenerator.php";
                $this->GeneratorClass = "eZEzGenerator";
            }
            break;
            
            case "flower" :
            {
                $this->GeneratorFile = "ezflowergenerator.php";
                $this->GeneratorClass = "eZFlowerGenerator";
            }
            break;
            
            case "simple":
            default :
            {
                $this->GeneratorFile = "ezsimplegenerator.php";
                $this->GeneratorClass = "eZSimpleGenerator";
            }
        }
    }

    /*!
      This function will parse the contents array and return valid
      XML data for insertion in the database.
    */
    function &generateXML( &$contents )
    {
        include_once( "ezarticle/classes/" . $this->GeneratorFile );

        $generator = new $this->GeneratorClass( $contents );

        $ret =& $generator->generateXML();

        print( $this->GeneratorClass );
        $this->PageCount = $generator->pageCount();
             
        return $ret;
    }

    /*!
      This function will return the number of pages of the last parsed article.
    */
    function pageCount( )
    {
        return $this->PageCount;
    }
    
    
    /*!
      This function will return an array containing the original state
      of the article so it can be used in a edit form.
    */
    function &decodeXML( &$contents )
    {
        $xml =& xmltree( $contents );
        
        if ( $xml->root->children[0]->name == "generator" )
        {
            $generator =& $xml->root->children[0]->children[0]->content;

            switch ( $generator )
            {
                case "tech" :
                {
                    $this->GeneratorFile = "eztechgenerator.php";
                    $this->GeneratorClass = "eZTechGenerator";
                }
                break;

                case "ez" :
                {
                    $this->GeneratorFile = "ezezgenerator.php";
                    $this->GeneratorClass = "eZEzGenerator";
                }
                break;
                
                case "flower" :
                {
                    $this->GeneratorFile = "ezflowergenerator.php";
                    $this->GeneratorClass = "eZFlowerGenerator";
                }
                break;

                case "simple" :
                {
                    $this->GeneratorFile = "ezsimplegenerator.php";
                    $this->GeneratorClass = "eZSimpleGenerator";
                }
                break;
            }
        }
        else
        {
            print( "<b>Error: eZArticleGenerator::decodeXML()  could not find generator in XML chunk.</b>" );
        }

        include_once( "ezarticle/classes/" . $this->GeneratorFile );

        $generator = new $this->GeneratorClass( $contents );
              
        return $generator->decodeXML();        
        
    }
        
    var $GeneratorClass;
    var $GeneratorFile;

    var $PageCount;
}

?>
