<?
// 
// $Id: ezimage.php,v 1.2 2000/09/21 15:47:57 bf-cvs Exp $
//
// Definition of eZCompany class
//
// B�rd Farstad <bf@ez.no>
// Created on: <21-Sep-2000 11:22:21 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//

//!! eZImageCatalogue
//! The eZImage class hadles images in the image catalogue.
/*!


  
*/

include_once( "classes/ezdb.php" );

class eZImage
{
    /*!
      Constructs a new eZImage object.
    */
    function eZImage( $id="", $fetch=true )
    {
        $this->IsConnected = false;
        $this->IsConnected = false;

        if ( $id != "" )
        {

            $this->ID = $id;
            if ( $fetch == true )
            {
                
                $this->get( $this->ID );
            }
            else
            {
                $this->State_ = "Dirty";
                
            }
        }
        else
        {
            $this->State_ = "New";
        }
    }

    /*!
      Stores a eZImage object to the database.
    */
    function store()
    {
        $this->dbInit();

        $this->Database->query( "INSERT INTO eZImageCatalogue_Image SET
                                 Name='$this->Name',
                                 Caption='$this->Caption',
                                 Description='$this->Description',
                                 FileName='$this->FileName',
                                 OriginalFileName='$this->OriginalFileName'
                                 " );
        $this->ID = mysql_insert_id();

        $this->State_ = "Coherent";
    }

    /*!
      Fetches the object information from the database.
    */
    function get( $id="" )
    {
        $this->dbInit();
        
        if ( $id != "" )
        {
            $this->Database->array_query( $image_array, "SELECT * FROM eZImageCatalogue_Image WHERE ID='$id'" );
            if ( count( $image_array ) > 1 )
            {
                die( "Error: Image's with the same ID was found in the database. This shouldent happen." );
            }
            else if( count( $image_array ) == 1 )
            {
                $this->ID = $image_array[0][ "ID" ];
                $this->Name = $image_array[0][ "Name" ];
                $this->Caption = $image_array[0][ "Caption" ];
                $this->Description = $image_array[0][ "Description" ];
                $this->FileName = $image_array[0][ "FileName" ];
                $this->OriginalFileName = $image_array[0][ "OriginalFileName" ];

                $this->State_ = "Coherent";
            }
        }
        else
        {
            $this->State_ = "Dirty";
        }
    }
    
    /*!
      Returns the id of the image.
    */
    function id()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        return $this->ID;
    }

    
    
    /*!
      Returns the name of the image.
    */
    function name()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        return $this->Name;
    }

    /*!
      Returns the caption of the image.
    */
    function caption()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        return $this->Caption;
    }

    /*!
      Returns the description of the image.
    */
    function description()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        return $this->Description;
    }    

    /*!
      Returns the filename of the image.
    */
    function fileName()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        return $this->FileName;
    }

    /*!
      Returns the path and filename to the original image.
    */
    function filePath()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        return "/ezimagecatalogue/catalogue/" .$this->FileName;
    }

    /*!
      Returns the path to a scaled version of the image. If the scaled version
      does not exist it is created.

      The path to the file is returned.
    */
    function requestImageVariation( $width, $height )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       
        return "/ezimagecatalogue/catalogue/variations/" .$this->FileName;
    }    
    
    /*!
      Sets the image name.
    */
    function setName( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->Name = $value;
    }

    /*!
      Sets the image caption.
    */
    function setCaption( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->Caption = $value;
    }

    /*!
      Sets the image description.
    */
    function setDescription( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->Description = $value;
    }

    /*!
      Sets the original imagename.
    */
    function setOriginalFileName( $value )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );
        
        $this->OriginalFileName = $value;
    }
    
    /*!
      Makes a copy of the image and stores the image in the catalogue.
      
      If the image is not of the type .jpg the image is converted.
    */
    function setImage( $file )
    {
       if ( $this->State_ == "Dirty" )
           $this->get( $this->ID );
        
       if ( get_class( $file ) == "ezimagefile" )
       {
           print( "storing image" );

           $this->OriginalFileName = $file->name();
           
           // the path to the catalogue
           $file->convertCopy( "ezimagecatalogue/catalogue/" . basename( $file->tmpName() ) . ".jpg" );

           $this->FileName = basename( $file->tmpName() ) . ".jpg";

           $name = $file->name();

           ereg( "([^.]+)\(.*)", $name, $regs );
           
           $name = $regs[0] . "jpg";
           
           $this->OriginalFileName = $name;
           
       }
    }
    
    /*!
      Private function.
      Open the database for read and write. Gets all the database information from site.ini.
    */
    function dbInit()
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = new eZDB( "site.ini", "eZImageCatalogueMain" );
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $Name;
    var $Caption;
    var $Description;
    var $FileName;
    var $OriginalFileName;
}

?>
