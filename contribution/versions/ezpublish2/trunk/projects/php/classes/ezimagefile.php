<?
// 
// $Id: ezimagefile.php,v 1.11 2001/03/08 18:47:00 bf Exp $
//
// Definition of eZCompany class
//
// B�rd Farstad <bf@ez.no>
// Created on: <21-Sep-2000 11:22:21 bf>
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZCommon
//! The eZImageFile class handles fileuploads, and image specific functions like scale.
/*!
  <b>NOTE:</b> this class requires the ImageMagic convert program. You can
  get it from: http://www.wizards.dupont.com/cristy/ImageMagick.html
  
  Example:
  \code
  You need a HTML file like:
    <form method="post" action="script_url" enctype="multipart/form-data">
       <input type="hidden" name="max_file_size" value="3000000">

    Image:<br>
       <input name="userfile" type="file" /><br>
       <input type="submit" value="OK" />
    </form>

    And in the script_url you put code like:
  
    $file = new eZImageFile();

    // note: userfile is not a variable it's a "text" string. The value
    // must be the same as the one used in the input.
    if ( $file->getFile( "userfile" ) )
    {
        print( $file->name() . " uploaded successfully" );

        // copy and scale the uploaded file
        $file->scaleCopy( "tmp/" . $file->name(), 120, 200 );
    }
    else
    {
        print( "Error uploading file." );
    }
  
  \endcode
*/

include_once( "classes/ezfile.php" );
    

class eZImageFile extends eZFile
{
    /*!
      Constructs a new eZImageFile object.
    */
    function eZImageFile()
    {

    }

    /*!
      Returns true if the uploaded file is an image (As perceived by Apache).
      Warning!! Do not use this to figure out if a file is an true image,
      only to see if Apache can serve it directly.
    */
    function isImage()
    {
        if ( ereg( "image", $this->FileType ) )
        {
            $ret = true;
        }
        else
        {
            $ret = false;
        }

        return $ret;
    }

    /*!
      \static
      Returns a information structure of the image file.
      The structure is an associative array with the following contents:
      "suffix" - The suffix of the image file, for instance: png, jpg or gif
      "dot-suffix" - The suffix of the image file with a dot, is empty if not supported.
      "image-type" - The image type, for instance: image/jpeg, image/png or image/gif
      "support" - True if the image type is supported.
    */
    function &information( $file, $use_default = false )
    {
        $ret = array();
        $suffix = "";
        if ( ereg( "\\.([a-zA-Z]+)$", $file, $regs ) )
        {
            // We got a suffix, make it lowercase and store it
            $suffix = strtolower( $regs[1] );
        }

        // List of supported suffixes
        $suffix_list = array( "jpg" => array( ".jpg", "image/jpeg" ),
                              "jpeg" => array( ".jpg", "image/jpeg" ),
                              "gif" => array( ".gif", "image/gif" ),
                              "png" => array( ".png", "image/png" ) );
        $postfix = $suffix_list[$suffix];
        $ret["suffix"] = $suffix;
        $ret["dot-suffix"] = $postfix[0];
        $ret["image-type"] = $postfix[1];
        $ret["supported"] = is_array( $postfix );
        if ( !$ret["supported"] and $use_default )
            return eZImageFile::defaultInformation();
        return $ret;
    }

    /*!
      \static
      Returns the default image info structure, which uses the jpeg format.
      This is used for returning a standard info structure if the file format is unsupported.
    */
    function &defaultInformation()
    {
        $ret = array( "suffix" => "jpg",
                      "dot-suffix" => ".jpg",
                      "image-type" => "image/jpeg",
                      "supported" => false );
        return $ret;
    }
    /*!
      Makes a copy of the image if and scales the copy according
      to the parameters $width and $height. If $aspectScale is true
      the scaling is done with ascpect ratio if not it uses "free form"
      scaling.

      Aspect scaling is the default.
    */
    function scaleCopy( $dest, $width, $height, $aspectScale=true )
    {
        $ret = false;
        $lock_file = $dest . ".lock";
        if ( file_exists( $lock_file ) )
        {
            // If image file is locked we need to wait until it's finished
            $i = 0;
            while( file_exists( $lock_file ) and $i < 5*5 ) // Wait max 5 seconds
            {
                usleep( 200000 ); // Sleep 1/5 of a second
                clearstatcache();
                $i++;
            }
            return "locked";
        }
        touch( $lock_file );
        $ini =& INIFile::globalINI();
        $image_prog = "convert";
        if ( $ini->has_var( "classes", "ImageConversionProgram" ) )
            $image_prog = $ini->read_var( "classes", "ImageConversionProgram" );
        $execstr = "$image_prog -geometry \"$width" . "x" . "$height" . ">\" "  . $this->TmpFileName . " " . $dest;
        // print( "<br><b>$execstr</b><br>" );

        $err = system( $execstr, $ret_code );

        if ( $ret_code == 0 )
        {
            @chmod( $dest, 0644 );
            $ret = true;
        }
        else
        {
            $ret = false;
        }

        // Check for animated gif/png
        if ( file_exists( "$dest" . ".0" ) )
        {
            copy( $this->TmpFileName, $dest );
            @chmod( $dest, 0644 );
            $i = 0;
            while( file_exists( "$dest.$i" ) )
            {
                unlink( "$dest.$i" );
                $i++;
            }
            $ret = true;
        }

        unlink( $lock_file );

        return $ret;
    }

    /*!
      Makes a copy of the image if and converts it to the correct image type.
    */
    function convertCopy( $dest )
    {
        $ret = false;
        $ini =& INIFile::globalINI();
        $image_prog = "convert";
        if ( $ini->has_var( "classes", "ImageConversionProgram" ) )
            $image_prog = $ini->read_var( "classes", "ImageConversionProgram" );
        $execstr = "$image_prog -quality 95 " . $this->TmpFileName . " " . $dest;
        // print( "<br><b>$execstr</b><br>" );

        $err = system( $execstr, $ret_code );
        $ret = true;
        if ( $ret_code != 0 )
        {
            $ret = false;
        }
        else
            @chmod( $dest, 0644 );
        
        return $ret;
    }
    
}

?>
