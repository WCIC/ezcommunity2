<?php
// 
// $Id: ezezrenderer.php,v 1.4 2000/10/28 12:52:24 bf-cvs Exp $
//
// Definition of eZEzRenderer class
//
// B�rd Farstad <bf@ez.no>
// Created on: <26-Oct-2000 13:46:30 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZArticle
//! eZEzRenderer renders XML contents into html articles.
/*!
  This class wil decode the ez articles generated by eZEzGenerator.
  Supported tags:
  \code
  <page> - pagebreak
  <header>
  Header text
  </header>
  <link ez.no text to the link> - anchor
  <image 42 align size> - image tag, 42 is the id, alignment (left|center|right), size (small|medium|large)

  <bold>
  bold text
  </bold>

  <italic>
  italic text
  </italic>

  <underline>
  underlined text
  </underline>

  <strike>
  strike text
  </strike>

  <pre>
  predefined text
  </pre>

  <verbatim>
  predefined text
  </verbatim>
  
  \endcode
  \sa eZEzGenerator  
*/

/*!TODO
  Add better syntax highlighting.

*/

//  $tmpPage = "<image 1 center big> <image 43 large large>";
//  $tmpPage = preg_replace( "/(<image\s+?([^ ]+)\s+?([^ ]+)\s+?([^( |>)]+)([^>]*?)>)/", "<image id=\"\\2\" align=\"\\3\" size=\"\\4\" />", $tmpPage );

//  $tmpPage = "<link ez.no ez systems> <link ez.no ez systems>";
//  $tmpPage = preg_replace( "#(<link\s+?([^ ]+)\s+?([^>]+)>)#", "<link href=\"\\2\" text=\"\\3\" />", $tmpPage );

//  $tmpPage = preg_replace( "#(?<!(age|php|age|cpp|ell|sql|der))>#", "&gt;", $tmpPage );
//  $tmpPage = preg_replace( "#/&gt;#", "/>", $tmpPage );

//  print( htmlspecialchars( $tmpPage ) );

include_once( "classes/eztexttool.php" );
include_once( "classes/ezlog.php" );

class eZEzRenderer
{
    /*!
      Creates a new eZEzGenerator object.
    */
    function eZEzRenderer( &$article )
    {
        $this->Article = $article;
    }

    /*!
      Returns the XHTML contents of the introduction of the article.
    */
    function &renderIntro()
    {
        $xml = xmltree( $this->Article->contents() );

        if ( !$xml )
        {
            print( "<br /><b>Error: eZEzRenderer::docodeXML() could not decode XML</b><br />" );
        }
        else
        {
            $into = "";
            $body = "";
            
            $i=0;
            foreach ( $xml->root->children as $child )
            {
                if ( $child->name == "intro" )
                {
                    $intro = $child->children[0]->content;
                    $intro = preg_replace( "#(http://.*?)(\s|\))#", "<a href=\"\\1\">\\1</a>", $intro );                    
                }
            }

            $newArticle = eZTextTool::nl2br( $intro );
        }
        
        return $newArticle;
    }

    /*!
      Returns the XHTML article of the article.
    */
    function &renderPage( $pageNumber=0 )
    {
        $xml = xmltree( $this->Article->contents() );

        if ( !$xml )
        {
            print( "<br /><b>Error: eZEzRenderer::docodeXML() could not decode XML</b><br />" );
        }
        else
        {
            $into = "";
            $body = "";

            
            foreach ( $xml->root->children as $child )
            {
                if ( $child->name == "intro" )
                {
                    $intro = $child->children[0]->content;
                    $intro = preg_replace( "#(http://.*?)(\s|\))#", "<a href=\"\\1\">\\1</a>", $intro );
                }
                
                if ( $child->name == "body" )
                {
                    $body = $child->children;
                }
            }

            $articleImages = $this->Article->images();
            $articleID = $this->Article->id();
            $pageArray = array();
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
                        $pageContent .= eZTextTool::nl2br( $paragraph->content );
                    }
                    
                    // header
                    if ( $paragraph->name == "header" )
                    {
                        $tmpText = "
                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                             <tr>
                             <td bgcolor=\"#c0c0c0\" width=\"100%\">
                             
                             <strong class=\"h2\"><img src=\"images/1x1.gif\" width=\"3\" height=\"1\" border=\"0\">&nbsp;"
                             .
                             $paragraph->children[0]->content
                             .
                             "
                             </strong>
                             </td>
                         </tr>
                       </table>";
                        
                        $pageContent .= $tmpText;
                    }

                    // bold text
                    if ( $paragraph->name == "bold" )
                    {
                        $pageContent .= "<b>" . $paragraph->children[0]->content . "</b>";
                    }

                    // italic text
                    if ( $paragraph->name == "italic" )
                    {
                        $pageContent .= "<i>" . $paragraph->children[0]->content . "</i>";
                    }

                    // underline text
                    if ( $paragraph->name == "underline" )
                    {
                        $pageContent .= "<u>" . $paragraph->children[0]->content . "</u>";
                    }

                    // strike text
                    if ( $paragraph->name == "strike" )
                    {
                        $pageContent .= "<s>" . $paragraph->children[0]->content . "</s>";
                    }

                    // pre text
                    if ( ( $paragraph->name == "pre" ) || ( $paragraph->name == "verbatim" ) )
                    {
                        $pageContent .= "<pre>" . $paragraph->children[0]->content . "</pre>";
                    }
                    
                    // link
                    if ( $paragraph->name == "link" )
                    {
                        foreach ( $paragraph->attributes as $imageItem )
                        {
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
                        
                        $pageContent .= "<a href=\"http://$href\">" . $text . "</a>";
                    }

                    // ezlink
                    if ( $paragraph->name == "ezlink" )
                    {
                        foreach ( $paragraph->attributes as $imageItem )
                        {
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

                        
                        $pageContent .= "
                                       <img align=\"baseline\" src=\"/images/pil-space.gif\" width=\"50\" height=\"10\" border=\"0\" hspace=\"0\"><a href=\"http://$href\">"
                             . $text . "</a>";
                    }
                    

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

                            
                        setType( $imageID, "integer" );
                        
                        $image = $articleImages[$imageID-1];
                        
                        // add image if a valid image was found, else report an error in the log.
                        if ( get_class( $image ) == "ezimage" )
                        {
                            $ini = new INIFile( "site.ini" );
                            
                            switch ( $imageSize )
                            {
                                case "small" :
                                {
                                    $variation =& $image->requestImageVariation( $ini->read_var( "eZArticleMain", "SmallImageWidth" ),
                                    $ini->read_var( "eZArticleMain", "SmallImageHeight" ) );
                                }
                                break;
                                case "medium" :
                                {
                                    $variation =& $image->requestImageVariation( $ini->read_var( "eZArticleMain", "MediumImageWidth" ),
                                    $ini->read_var( "eZArticleMain", "MediumImageHeight" ) );
                                }
                                break;
                                case "large" :
                                {
                                    $variation =& $image->requestImageVariation( $ini->read_var( "eZArticleMain", "LargeImageWidth" ),
                                    $ini->read_var( "eZArticleMain", "LargeImageHeight" ) );
                                }
                                break;
                            }
                            
                            $imageURL = "/" . $variation->imagePath();
                            $imageWidth = $variation->width();
                            $imageHeight = $variation->height();
                            $imageCaption = $image->caption();
                            
                            $imageTags = "<table width=\"$imageWidth\" align=\"$imageAlignment\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                                            <tr>
                                            <td>
                                                        <img src=\"$imageURL\" border=\"0\" width=\"$imageWidth\" height=\"$imageHeight\" />
                                                        </td>
                                                </tr>
                                                <tr>
                                                         <td class=\"pictext\">
                                                         $imageCaption
                                                         </td>
                                                </tr>
                                             </table>";
                                $pageContent .=  $imageTags;
                        }
                        else
                        {
                            eZLog::writeError( "Image nr: $imageID not found in article: $articleID from IP: $REMOTE_ADDR" );        
                        }
                    }
                }

                
                $pageArray[] = $pageContent;
                
            }
            

            if ( $pageNumber != 0 )
            {
                $newArticle = $pageArray[$pageNumber];
            }
            else
            {
                $newArticle = eZTextTool::nl2br( $intro ) . "</p><p>". $pageArray[$pageNumber];
            }
                
        }
        
        return $newArticle;
    }


    /*!
      Returns the XHTML contents of the introduction of the article.
    */
    function &renderIntro()
    {
        $xml = xmltree( $this->Article->contents() );

        if ( !$xml )
        {
            print( "<br /><b>Error: eZEzRenderer::docodeXML() could not decode XML</b><br />" );
        }
        else
        {
            $into = "";
            $body = "";
            
            $i=0;
            foreach ( $xml->root->children as $child )
            {
                if ( $child->name == "intro" )
                {
                    $intro = $child->children[0]->content;
                }
            }

            $newArticle = eZTextTool::nl2br( $intro );
        }
        
        return $newArticle;
    }
    
    var $Article;
}

?>

