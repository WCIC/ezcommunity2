<?php
// 
// $Id: eztexttool.php,v 1.13 2001/02/23 09:55:55 jb Exp $
//
// Definition of eZTextTool class
//
// B�rd Farstad <bf@ez.no>
// Created on: <16-Oct-2000 11:06:56 bf>
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZCommon
//! The eZTextTool class provies text utility functions
/*!
  This class consists of static functions for formatting of text.
  Theese functions is made as an extention to PHP ie functions you would
  use all the time, but isn't a part of php.
  
  Example of usage:
  \code
  // create a string with newlines
  $text = "This is
  a
  text
  to break
  up";

  // convert the newlines to xhtml breaks
  $text = eZTextTool::nl2br( $text );

  //print out the result
  print( $text );
  \endcode
  
*/

class eZTextTool
{
    /*!
      \static
      This function converts all newlines \n into breaks.

      The breaks are inserted before every newline.

      The default is to use xhtml breaks, html breaks is used if the
      $xhtml variable is set to false.
    */
    function &nl2br( $string, $xhtml=true )
    {
        if ( $xhtml == true )            
            return ereg_replace( "\n", "<br />\n", $string );
        else
            return ereg_replace( "\n", "<br>\n", $string );
    }    

    /*!
      \static
      This function will add a > at the beginning of each line.
    */
    function &addPre( $string, $char=">" )
    {
        $string =& wordwrap( $string, 60, "\n" );
        return preg_replace( "#^#m", "$char ", $string );
    }

    /*!
      \static
      This function will convert text into capitilzed text.

      Numbers will not be capitalized. E.g.

      "a text string" will be converted to "A  T E X T  S T R I N G"
      Where the first letter in each word will get the css style;
      span with class="$bigClass" given as argument.
      
    */
    function &capitalize( $string, $bigClass="h1bigger" )
    {
        $string = strtoupper( $string );
        
        for ( $i=0; $i<strlen( $string ); $i++)
        {
            $string2 .= $string[$i] . " ";
        }
        
        $string = trim( $string2 );
        
        $string = str_replace ("�", "�", $string );        
        $string = str_replace ("�", "�", $string );
        $string = str_replace ("�", "�", $string );

        $string = preg_replace( "#(  |^)([a-zA-Z������] )#", "\\1<span class=\"$bigClass\">\\2</span>", $string );
        
//        $string = preg_replace( "#(  |^)([^ ])#", "\\1<span class=\"$bigClass\">\\2</span>", $string );
        
        $string = str_replace ( "  ", "&nbsp;&nbsp;", $string );
        
        return  $string;
    }

    /*!
      Performs a normal htmlspecialchars with a striplashes afterwards,
      this is needed to avoid " and \ being slashed on web pages.
    */
    function &htmlspecialchars( $string )
    {
        return stripslashes( htmlspecialchars( $string ) );
    }
}

?>

