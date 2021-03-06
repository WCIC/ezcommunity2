<?
// 
// $Id: ezfile.php,v 1.10 2001/03/08 11:18:48 jb Exp $
//
// Definition of eZCompany class
//
// B�rd Farstad <bf@ez.no>
// Created on: <21-Sep-2000 11:22:21 bf>
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

//!! eZCommon
//! The eZFile class handles fileuploads, and other file functions.
/*!
  Example:
  \code
    $file = new eZFile();

    if ( $file->getFile( "userfile" ) )
    {
        print( $file->name() . " uploaded successfully" );
    }
    else
    {
        print( $file->name() . " not uploaded successfully" );
    }
  \endcode
  
*/

class eZFile
{
    /*!
      Constructs a new eZFile object
    */
    function eZFile( )
    {


    }

    /*!
      Fetches the uploaded file information.

      The $name_var variable is refering to the html <input .. variable>

      See the example for more details.
    */
    function getUploadedFile( $name_var )
    {
        global $HTTP_POST_FILES;

        $name_var = $HTTP_POST_FILES[ $name_var ];
        $ret = true;

        $this->FileName = $name_var['name'];
        $this->FileType = $name_var['type'];
        $this->FileSize = $name_var['size'];
        $this->TmpFileName = $name_var['tmp_name'];

        if ( ( $this->FileSize == "0" ) || ( $this->FileSize == "" ) )
        {
            $ret = false;
        }
                
        return $ret;
    }

    /*!
      
    */
    function getFile( $fileName )
    {
        $this->FileName = $fileName;
        $this->FileType = $name_var['type'];
        $this->FileSize = filesize( $fileName );
        $this->TmpFileName = $fileName;

        $ret = true;
        
        if ( ( $this->FileSize == "0" ) || ( $this->FileSize == "" ) )
        {
            $ret = false;
        }
        return $ret;
    }

    /*!
      Moves the uploaded file to the desired directory.

      Returns true if successful.
    */
    function move( $dest )
    {
        return move_uploaded_file ( $this->TmpFileName, $dest );
    }

   /*!
      Copies the uploaded file to the desired directory. 

      Returns true if successful.
    */
    function copy( $dest )
    {
        $ret = true;
        
        if ( !copy( $this->TmpFileName, $dest ) )
        {
            $ret = false;            
        }
        else
            chmod( $dest, 0644 );
        
        return $ret;
    }
    
    /*!
      Returns the original file name.
    */
    function name()
    {
        return $this->FileName;
    }
    
    /*!
      Returns the file type.
    */
    function type()
    {
        return $this->FileType;
    }

    /*!
      Returns the file size.
    */
    function size()
    {
        return $this->FileSize;
    }
    
    /*!
      \static
      Returns the size of the file in a shortened form useful for printing to the user,
      the returned value is an array with the filesize, the size as a shortened string
      and the unit. The keys used for fetching the various items in the array are:
      "size" - The full file size
      "size-string" - The shortened file size as a string
      "unit" - The unit for the shortened size, either B, KB, MB or GB
    */

    function &siFileSize( $size )
    {
        $units = array( "GB" => 10737741824,
                        "MB" => 1048576,
                        "KB" => 1024,
                        "B" => 0 );
        $decimals = 0;
        $shortsize = $size;
        while( list($unit_key,$val) = each( $units ) )
        {
            if ( $size >= $val )
            {
                $unit = $unit_key;
                if ( $val > 0 )
                {
                    $decimals = 2;
                    $shortsize = $size / $val;
                }
                break;
            }
        }
        $shortsize = number_format( ( $shortsize ), $decimals);
        $size = array( "size" => $size,
                       "size-string" => $shortsize,
                       "unit" => $unit );
        return $size;
    }

    /*!
      Returns the temporary file name.
    */
    function tmpName()
    {
        return $this->TmpFileName;
    }

    /*!
      Sets the mime type of the file.
    */
    function setType( $type )
    {
        $this->FileType = $type;
    }
    

    var $FileName;
    var $TmpFileName;
    var $FileType;
    var $FileSize;
}


?>
