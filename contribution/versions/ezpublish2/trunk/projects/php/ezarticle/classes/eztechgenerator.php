<?php
// 
// $Id: eztechgenerator.php,v 1.20 2000/10/31 22:14:36 bf-cvs Exp $
//
// Definition of eZTechGenerator class
//
// B�rd Farstad <bf@ez.no>
// Created on: <18-Oct-2000 15:55:16 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZArticle
//! eZTechGenerator generates  XML contents for articles.
/*!
  This class will generate a tech XML article. This class is ment
  as an example of how to write your own special generator.

*/

/*!TODO
  
*/

class eZTechGenerator
{
    /*!
      Creates a new eZTechGenerator object.
    */
    function eZTechGenerator( &$contents )
    {
        $this->PageCount = 0;
        $this->Contents = $contents;
    }

    /*!
      Generates valid XML data to use for storage.
    */
    function &generateXML()
    {
        // add the XML header.
        $newContents = "<?xml version=\"1.0\"?>";
        
        //add the generator, this is used for rendering.
        $newContents .= "<article><generator>tech</generator>\n";

        //add the contents
        // What does strip_tags do? needed anymore?
//          $newContents .= "<intro>" . strip_tags( $this->Contents[0], "<bold>,<italic>,<strike>,<underline>" ) . "</intro>";
        $newContents .= "<intro>" . $this->generatePage( $this->Contents[0] ) . "</intro>";

        // get every page in an array
        $pages = split( "<page>" , $this->Contents[1] );

        $body = "";
        foreach ( $pages as $page )
        {
            $tmpPage = $page;

            $tmpPage = $this->generatePage( $tmpPage );

            $body .= "<page>" . $tmpPage  . "</page>";        
        }

        $this->PageCount = count( $pages );

        $newContents .= "<body>" . $body . "</body></article>";

        return $newContents;
    }

    function &generatePage( $tmpPage )
    {
        $tmpPage = $this->generateImage( $tmpPage );

        $tmpPage = $this->generateLink( $tmpPage );

        // replace & with &amp; to prevent killing the xml parser..
        // is that a bug in the xmltree(); function ? answer to bf@ez.no
        $tmpPage = ereg_replace ( "&", "&amp;", $tmpPage );

        $tmpPage = $this->generateHTML( $tmpPage );

        $tmpPage = $this->generateUnknowns( $tmpPage );

        return $tmpPage;
    }

    function &generateUnknowns( $tmpPage )
    {
        // make unknown tags readable.. look-ahead assertion is used ( ?! ) 
        $tmpPage = preg_replace( "/<(?!(page|php|\/|image|cpp|shell|sql|hea|lin|per|bol|ita|und|str|pre|ver|lis|ezhtml|java))/", "&lt;", $tmpPage );

        // look-behind assertion is used here (?<!) 
        // the expression must be fixed with eg just use the 3 last letters of the tag

        $tmpPage = preg_replace( "#(?<!(age|php|age|cpp|ell|sql|der|erl|old|lic|ine|ike|pre|tim|isp|tml|ava))>#", "&gt;", $tmpPage );
        // make better..
        $tmpPage = preg_replace( "#/&gt;#", "/>", $tmpPage );

        return $tmpPage;
    }

    function &generateHTML( $tmpPage )
    {
        // Begin html tag replacer
        // replace all < and >  between <ezhtml> and </ezhtml>
        // and to the same for <php> </php>
        // ok this is a bit slow code, but it works
        $startHTMLTag = "<ezhtml>";
        $endHTMLTag = "</ezhtml>";

        $startPHPTag = "<php>";
        $endPHPTag = "</php>";
            
        $numberBeginHTML = substr_count( $tmpPage, $startHTMLTag );
        $numEndHTML = substr_count( $tmpPage, $endHTMLTag );

        if ( $numberBegin != $numEnd )
        {
            print( "Unmatched ezhtml tags, check that you have end tags for all begin tags" );
        }
            
        $numberBeginPHP = substr_count( $tmpPage, $startPHPTag );
        $numEndPHP = substr_count( $tmpPage, $endPHPTag );
            
        if ( $numberBegin != $numEnd )
        {
            print( "Unmatched PHP tags, check that you have end tags for all begin tags" );
        }

        if ( ( $numberBeginPHP > 0 ) || ( $numberBeginHTML > 0 ) )
        {
            $resultPage = "";
            $isInsideHTML = false;
            $isInsidePHP = false;
            for ( $i=0; $i<strlen( $tmpPage ); $i++ )
            {    
                if ( substr( $tmpPage, $i - strlen( $startHTMLTag ), strlen( $startHTMLTag ) ) == $startHTMLTag )
                {
                    $isInsideHTMLTag = true;
                }

                if ( substr( $tmpPage, $i, strlen( $endHTMLTag ) ) == $endHTMLTag )
                {
                    $isInsideHTMLTag = false;
                }

                if ( substr( $tmpPage, $i - strlen( $startPHPTag ), strlen( $startPHPTag ) ) == $startPHPTag )
                {
                    $isInsidePHPTag = true;
                }

                if ( substr( $tmpPage, $i, strlen( $endPHPTag ) ) == $endPHPTag )
                {
                    $isInsidePHPTag = false;
                }
                
                if ( ( $isInsideHTMLTag == true ) ||  ( $isInsidePHPTag == true ) )
                {
                    switch ( $tmpPage[$i] )
                    {
                        case "<" :
                        {
                            $resultPage .= "&lt;";
                        }
                        break;

                        case ">" :
                        {
                            $resultPage .= "&gt;";
                        }
                        break;
            
                        default:
                        {
                            $resultPage .= $tmpPage[$i];
                        }
                    }
                }
                else
                {
                    $resultPage .= $tmpPage[$i];
                }
            }

            $tmpPage = $resultPage;
        }
        return $tmpPage;
    }

    function &generateLink( $tmpPage )
    {
        // convert <link ez.no ez systems> to valid xml
        // $tmpPage = "<link ez.no ez systems> <link ez.no ez systems>";
        $tmpPage = preg_replace( "#(<link\s+?([^ ]+)\s+?([^>]+)>)#", "<link href=\"\\2\" text=\"\\3\" />", $tmpPage );
        return $tmpPage;
    }

    function &generateImage( $tmpPage )
    {
        // parse the <image id align size> tag and convert it
        // to <image id="id" align="align" size="size" />
        $tmpPage = preg_replace( "/(<image\s+?([^ ]+)\s+?([^ ]+)\s+?([^( |>)]+)([^>]*?)>)/", "<image id=\"\\2\" align=\"\\3\" size=\"\\4\" />", $tmpPage );
        return $tmpPage;
    }

    /*!
      Decodes the xml chunk and returns the original array to the article. 
    */
    function &decodeXML()
    {
        $contentsArray = array();
        
        $xml = xmltree( $this->Contents );

        if ( !$xml )
        {
            print( "<br /><b>Error: eZTechRenderer::docodeXML() could not decode XML</b><br />" );
        }
        else
        {
            $into = "";
            $body = "";
            
            foreach ( $xml->root->children as $child )
            {
                if ( $child->name == "intro" )
                {
                    if ( count( $child->children ) > 0 )
                    foreach ( $child->children as $paragraph )
                    {                        
                        // ordinary text
                        if ( $paragraph->name == "text" )
                        {
                            $intro .= $paragraph->content;
                        }
                        
                        $intro = $this->decodeStandards( $intro, $paragraph );

                        $intro = $this->decodeCode( $intro, $paragraph );

                        $intro = $this->decodeImage( $intro, $paragraph );

                        $intro = $this->decodeLink( $intro, $paragraph );

                    }
                }
                

                if ( $child->name == "body" )
                {
                    $body = $child->children;
                }
            }

            $contentsArray[] = $intro;

            $bodyContents = "";
            $i=0;
            // loop on the pages
            foreach ( $body as $page )
            {
                $pageContent = "";
                // loop on the contents of the pages
                if ( count( $page->children ) > 0 )
                foreach ( $page->children as $paragraph )
                {
                    // ordinary text
                    if ( $paragraph->name == "text" )
                    {
                        $pageContent .= $paragraph->content;
                    }

                    $pageContent = $this->decodeStandards( $pageContent, $paragraph );

                    $pageContent = $this->decodeCode( $pageContent, $paragraph );

                    $pageContent = $this->decodeImage( $pageContent, $paragraph );

                    $pageContent = $this->decodeLink( $pageContent, $paragraph );
                }

                if ( $i > 0 )
                    $bodyContents .=  "<page>" . $pageContent;
                else
                    $bodyContents .=  $pageContent;
                    
                $i++;
            }

            $contentsArray[] = $bodyContents;
        }

        return $contentsArray;
    }

    function &decodeCode( $pageContent, $paragraph )
    {
        // php code 
        if ( $paragraph->name == "php" )
        {
            $pageContent .= "<php>" . $paragraph->children[0]->content . "</php>";
        }

        // html code 
        if ( $paragraph->name == "ezhtml" )
        {
            $pageContent .= "<ezhtml>" . $paragraph->children[0]->content . "</ezhtml>";
        }

        // java code 
        if ( $paragraph->name == "java" )
        {
            $pageContent .= "<java>" . $paragraph->children[0]->content . "</java>";
        }

        // sql code
        if ( $paragraph->name == "sql" )
        {
            $pageContent .= "<sql>" . $paragraph->children[0]->content . "</sql>";
        }

        // shell code
        if ( $paragraph->name == "shell" )
        {
            $pageContent .= "<shell>" . $paragraph->children[0]->content . "</shell>";
        }

        // c++  code
        if ( $paragraph->name == "cpp" )
        {
            $pageContent .= "<cpp>" . $paragraph->children[0]->content . "</cpp>";
        }

        // perl  code
        if ( $paragraph->name == "perl" )
        {
            $pageContent .= "<perl>" . $paragraph->children[0]->content . "</perl>";
        }

        // lisp  code
        if ( $paragraph->name == "lisp" )
        {
            $pageContent .= "<lisp>" . $paragraph->children[0]->content . "</lisp>";
        }
        return $pageContent;
    }

    function &decodeImage( $pageContent, $paragraph )
    {
        // image 
        if ( $paragraph->name == "image" )
        {
            foreach ( $paragraph->attributes as $imageItem )
                {
                    switch ( $imageItem->name )
                    {

                        case "id" :
                        {
                            $imageID = $imageItem->children[0]->content;
                        }
                        break;

                        case "align" :
                        {
                            $imageAlignment = $imageItem->children[0]->content;
                        }
                        break;

                        case "size" :
                        {
                            $imageSize = $imageItem->children[0]->content;
                        }
                        break;
                                
                    }
                }
                        
            $pageContent .= "<image $imageID $imageAlignment $imageSize>";
        }
        return $pageContent;
    }

    function &decodeLink( $pageContent, $paragraph )
    {
        // link
        if ( $paragraph->name == "link" )
        {
            foreach ( $paragraph->attributes as $imageItem )
                {
                    print( $imageItem->name );
                    switch ( $imageItem->name )
                    {

                        case "href" :
                        {
                            $href = $imageItem->children[0]->content;
                        }
                        break;

                        case "text" :
                        {
                            $text = $imageItem->children[0]->content;
                        }
                        break;
                                
                    }
                }
                        
            $pageContent .= "<link $href $text>";
        }
        return $pageContent;
    }

    function &decodeStandards( $pageContent, $paragraph )
    {
        // header
        if ( $paragraph->name == "header" )
        {
            $pageContent .= "<header>" . $paragraph->children[0]->content . "</header>";
        }

        // bold text
        if ( $paragraph->name == "bold" )
        {
            $pageContent .= "<bold>" . $paragraph->children[0]->content . "</bold>";
        }

        // italic text
        if ( $paragraph->name == "italic" )
        {
            $pageContent .= "<italic>" . $paragraph->children[0]->content . "</italic>";
        }

        // underline text
        if ( $paragraph->name == "underline" )
        {
            $pageContent .= "<underline>" . $paragraph->children[0]->content . "</underline>";
        }

        // strike text
        if ( $paragraph->name == "strike" )
        {
            $pageContent .= "<strike>" . $paragraph->children[0]->content . "</strike>";
        }

        // pre defined text
        if ( $paragraph->name == "pre" )
        {
            $pageContent .= "<pre>" . $paragraph->children[0]->content . "</pre>";
        }

        // verbatim text
        if ( $paragraph->name == "verbatim" )
        {
            $pageContent .= "<verbatim>" . $paragraph->children[0]->content . "</verbatim>";
        }
        return $pageContent;
    }

    /*!
      Returns the number of pages found in the article.
    */
    function pageCount( )
    {
        return $this->PageCount;
    }    

    var $Contents;
    var $PageCount;
}

?>
