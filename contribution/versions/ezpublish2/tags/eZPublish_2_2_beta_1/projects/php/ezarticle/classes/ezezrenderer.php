<?php
// 
// $Id: ezezrenderer.php,v 1.20 2001/07/29 23:30:58 kaid Exp $
//
// Definition of eZEzRenderer class
//
// Created on: <26-Oct-2000 13:46:30 bf>
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
  <mail bf@ez.no subject text here, link text here>

  <ezanchor anchorname>

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



//  $tmpPage = preg_replace( "#<mail\s+?([^ ]*?)\s+?(.*?),\s+?([^>]*?)>#", "<mail to=\"\\1\" subject=\"\\2\" text=\"\\3\" />", $tmpPage );

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
            $intro = "";
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
            $intro = "";
            $body = "";

            
            foreach ( $xml->root->children as $child )
            {
                if ( $child->name == "intro" )
                {
                    $intro = trim( $child->children[0]->content );
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
                        $pageContent .= eZTextTool::nl2br($paragraph->content );
                    }
                    
                    // header
                    if ( $paragraph->name == "header" )
                    {
                        $tmpText = "
                        <br clear=\"all\" />   
                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                             <tr>
                             <td bgcolor=\"#c0c0c0\" width=\"100%\">
                             
                             <div class=\"listheadline\"><img src=\"$wwwDir$index/images/1x1.gif\" width=\"4\" height=\"1\" border=\"0\">"
                             .
                             $paragraph->children[0]->content
                             .
                             "
                             </div>
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

                        if ( ( $href[0] == "/" ) || ( $href[0] == "#" ) )
                        {                        
                            $pageContent .= "<a href=\"$wwwDir$index$href\">" . $text . "</a>";
                        }
                        else
                        {
                            $pageContent .= "<a href=\"http://$href\">" . $text . "</a>";
                        }
                    }

                    // mail
                    if ( $paragraph->name == "mail" )
                    {
                        foreach ( $paragraph->attributes as $mailItem )
                        {
                            switch ( $mailItem->name )
                            {
                                case "to" :
                                {
                                    $to = $mailItem->children[0]->content;
                                }
                                break;

                                case "subject" :
                                {
                                    $subject = $mailItem->children[0]->content;
                                }
                                break;

                                case "text" :
                                {
                                    $text = $mailItem->children[0]->content;
                                }
                                break;
                            }
                        }
                        
                        $pageContent .= "<a href=\"mailto:$to?subject=$subject\">$text</a>";
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

                        if ( ( $href[0] == "/" ) || ( $href[0] == "#" ) )
                        {
                            $pageContent .= "
                                       <img align=\"baseline\" src=\"$wwwDir$index/images/pil-space.gif\" width=\"50\" height=\"10\" border=\"0\" hspace=\"0\">&nbsp;<a class=\"path\" href=\"$wwwDir$index$href\">"
                                 . $text . "</a>";
                        }
                        else
                        {
                            $pageContent .= "
                                       <img align=\"baseline\" src=\"$wwwDir$index/images/pil-space.gif\" width=\"50\" height=\"10\" border=\"0\" hspace=\"0\">&nbsp;<a class=\"path\" href=\"http://$href\">"
                                 . $text . "</a>";

                        }
                    }


                    // ez anchor
                    if ( $paragraph->name == "ezanchor" )
                    {
                        foreach ( $paragraph->attributes as $anchorItem )
                        {
                            switch ( $anchorItem->name )
                            {
                                case "href" :
                                {
                                    $href = $anchorItem->children[0]->content;
                                }
                                break;
                            }
                        }
                        
                        $pageContent .= "<a name=\"$href\"></a>";
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
                            $ini =& INIFile::globalINI();

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
                                                        <img src=\"$wwwDir$index$imageURL\" border=\"0\" width=\"$imageWidth\" height=\"$imageHeight\" />
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
                if ( $intro != "" )
                {                    
                    $newArticle = "<p>" . eZTextTool::nl2br( $intro ) . "</p>". $pageArray[$pageNumber];
                }
                else
                {
                    $newArticle = $pageArray[$pageNumber];
                }
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
            $intro = "";
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
