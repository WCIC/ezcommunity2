<?php
// 
// $Id: ezproduct.php,v 1.74 2001/08/03 14:08:19 jhe Exp $
//
// Definition of eZProduct class
//
// Created on: <11-Sep-2000 22:10:06 bf>
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
//!! eZTrade
//! eZProduct handles product information.
/*!

  Example code:
  \code
  // Create a new product and set some values.
  $product = new eZProduct();
  $product->setName( "Toothbrush" );
  $product->setBrief( "A nice and small toothbrush" );
  $product->setDescription( "Bla bla environment bla bla cheap bla bla must have." );
  $product->setProductNumber( "Jordan-A101" );
  $product->setKeywords( "teeth cheap cool brush" );
  $product->setPrice( 21.50 );

  // Store the product to the database
  $product->store();

  \endcode
  \sa eZProductCategory eZOption
*/

/*!TODO
  Add query builder to search. Use same as in eZLink.
*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezdatetime.php" );

include_once( "eztrade/classes/ezoption.php" );
include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "ezimagecatalogue/classes/ezimage.php" );
include_once( "eztrade/classes/ezproducttype.php" );
include_once( "eztrade/classes/ezvattype.php" );
include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "eztrade/classes/ezshippinggroup.php" );


class eZProduct
{
    /*!
      Constructs a new eZProduct object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZProduct( $id="" )
    {
        $this->ExpiryTime = 0;
        if ( $id != "" )
        {
            $this->ID = $id;
            $this->get( $this->ID );
        }
        else
        {
            // default verdier
            $this->ShowPrice = true;
            $this->ShowProduct = true;
            $this->Discontinued = false;
        }
    }
    
    /*!
      Stores a product to the database.
    */
    function store()
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        
        if ( $this->ShowPrice == true )
            $showPrice = 1;
        else
            $showPrice = 0;            

        if ( $this->ShowProduct == true )
            $showProduct = 1;
        else
            $showProduct = 0;            

        if ( $this->Discontinued == true )
            $discontinued = 1;
        else
            $discontinued = 0;

        if ( isSet( $this->Price ) and $this->Price != "" and is_numeric( $this->Price ) )
        {
            $price = "'$this->Price'";
        }
        else
        {
            $price = "NULL";
        }

        $name = $db->escapeString( $this->Name );
        $brief = $db->escapeString( $this->Brief );
        $description = $db->escapeString( $this->Description );
        $keywords = $db->escapeString( $this->Keywords );
        $productNumber = $db->escapeString( $this->ProductNumber );
        
        if ( !isSet( $this->ID ) )
        {
            $timeStamp = eZDateTime::timeStamp( true );
            $db->lock( "eZTrade_Product" );
            $nextID = $db->nextID( "eZTrade_Product", "ID" );            

            $res = $db->query( "INSERT INTO eZTrade_Product
                                ( ID,
                                  Name,
                                  Brief,
                                  Description,
                                  Keywords,
                                  ProductNumber,
                                  Price,
                                  ShowPrice,
                                  ShowProduct,
                                  Discontinued,
                                  ExternalLink,
                                  RemoteID,
                                  IsHotDeal,
                                  VATTypeID,
                                  ProductType,
                                  ShippingGroupID,
                                  Published,
                                  ExpiryTime )
                                  VALUES
                                  ( '$nextID',
		                            '$this->Name',
                                    '$this->Brief',
                                    '$this->Description',
                                    '$this->Keywords',
                                    '$this->ProductNumber',
                                     $price,
                                    '$showPrice',
                                    '$showProduct',
                                    '$discontinued',
                                    '$this->ExternalLink',
                                    '$this->RemoteID',
                                    '$this->IsHotDeal',
                                    '$this->VATTypeID',
                                    '$this->ProductType',
                                    '$this->ShippingGroupID',
                                    '$timeStamp',
                                    '$this->ExpiryTime' )" );
            $db->unlock();
			$this->ID = $nextID;
        }
        else
        {
            $res = $db->query( "UPDATE eZTrade_Product SET
		                         Name='$this->Name',
                                 Brief='$this->Brief',
                                 Description='$this->Description',
                                 Keywords='$this->Keywords',
                                 ProductNumber='$this->ProductNumber',
                                 Price=$price,
                                 ShowPrice='$showPrice',
                                 ShowProduct='$showProduct',
                                 Discontinued='$discontinued',
                                 ExternalLink='$this->ExternalLink',
                                 IsHotDeal='$this->IsHotDeal',
                                 VATTypeID='$this->VATTypeID',
                                 ShippingGroupID='$this->ShippingGroupID',
                                 ProductType='$this->ProductType',
                                 Published=Published,
                                 ExpiryTime='$this->ExpiryTime'
                                 WHERE ID='$this->ID'
                                 " );
        }
    
        if ( $res == false )
        {
            $db->rollback( );
        }
        else
            $db->commit();
        
        return true;
    }

    /*!
      Fetches the object information from the database.
    */
    function get( $id="" )
    {
        $db =& eZDB::globalDatabase();
        $ret = false;
        
        if ( $id != "" )
        {
            $db->array_query( $category_array, "SELECT * FROM eZTrade_Product WHERE ID='$id'" );
            if ( count( $category_array ) > 1 )
            {
                die( "Error: Category's with the same ID was found in the database. This shouldent happen." );
            }
            else if ( count( $category_array ) == 1 )
            {
                $this->ID =& $category_array[0][$db->fieldName( "ID" )];
                $this->Name =& $category_array[0][$db->fieldName( "Name" )];
                $this->Brief =& $category_array[0][$db->fieldName( "Brief" )];
                $this->Description =& $category_array[0][$db->fieldName( "Description" )];
                $this->Keywords =& $category_array[0][$db->fieldName( "Keywords" )];
                $this->ProductNumber =& $category_array[0][$db->fieldName( "ProductNumber" )];
                $this->ExternalLink =& $category_array[0][$db->fieldName( "ExternalLink" )];
                $this->Price =& $category_array[0][$db->fieldName( "Price" )];
                $this->IsHotDeal =& $category_array[0][$db->fieldName( "IsHotDeal" )];
                $this->RemoteID =& $category_array[0][$db->fieldName( "RemoteID" )];
                $this->VATTypeID =& $category_array[0][$db->fieldName( "VATTypeID" )];
                $this->ShippingGroupID =& $category_array[0][$db->fieldName( "ShippingGroupID" )];
                $this->ProductType =& $category_array[0][$db->fieldName( "ProductType" )];
                $this->ExpiryTime =& $category_array[0][$db->fieldName( "ExpiryTime" )];
                if ( $this->Price == "NULL" )
                    unset( $this->Price );

                if ( $category_array[0][ $db->fieldName( "ShowPrice" )] == 1 )
                    $this->ShowPrice = true;
                else
                    $this->ShowPrice = false;

                if ( $category_array[0][$db->fieldName( "ShowProduct" )] == 1 )
                    $this->ShowProduct = true;
                else
                    $this->ShowProduct = false;

                if ( $category_array[0][$db->fieldName( "Discontinued" )] == 1 )
                    $this->Discontinued = true;
                else
                    $this->Discontinued = false;
                $ret = true;
            }
        }
        return $ret;
    }

    /*!
      Deletes a eZProduct object from the database.

    */
    function delete()
    {
        $db =& eZDB::globalDatabase();

        if ( isSet( $this->ID ) )
        {
            $db->begin();
            $res[] = $db->query( "DELETE FROM eZTrade_ProductTypeLink WHERE ProductID='$this->ID'" );
            $res[] = $db->query( "DELETE FROM eZTrade_AttributeValue WHERE ProductID='$this->ID'" );

            $res[] = $db->query( "DELETE FROM eZTrade_ProductCategoryLink WHERE ProductID='$this->ID'" );
            $res[] = $db->query( "DELETE FROM eZTrade_ProductCategoryDefinition WHERE ProductID='$this->ID'" );
            
            $res[] = $db->query( "DELETE FROM eZTrade_ProductImageLink WHERE ProductID='$this->ID'" );
            $res[] = $db->query( "DELETE FROM eZTrade_ProductImageDefinition WHERE ProductID='$this->ID'" );

            $db->array_query( $qry_array, "SELECT QuantityID FROM eZTrade_ProductQuantityDict
                                                       WHERE ProductID='$this->ID'" );
            foreach( $qry_array as $row )
            {
                $id = $row[$db->fieldName( "QuantityID" )];
                $res[] = $db->query( "DELETE FROM eZTrade_Quantity WHERE ID='$id'" );
            }
            $res[] = $db->query( "DELETE FROM eZTrade_ProductQuantityDict WHERE ProductID='$this->ID'" );

            
            $options = $this->options();
            foreach ( $options as $option )
            {
                $option->delete();
            }            

            $res[] = $db->query( "DELETE FROM eZTrade_Product WHERE ID='$this->ID'" );

            if ( in_array( false, $res ) )
                $db->rollback( );
            else
                $db->commit();            
        }
        return true;
    }

    /*!
      Returns the object ID to the product. This is the unique ID stored in the database.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returns the name of the product.
    */
    function &name( )
    {
       return htmlspecialchars( $this->Name );
    }

    /*!
      Returns the remote ID of the product.
    */
    function &remoteID( )
    {
       return $this->RemoteID;
    }    


    /*!
      Returns the price of the product.
    */
    function &price( )
    {
       return $this->Price;
    }    

    /*!
      Returns the price of the product.
    */
    function hasPrice()
    {
       return isSet( $this->Price );
    }    

    /*!
      Returns the price of the product exclusive VAT ( prive - VAT value ).

      If a value is given as argument this value is used for VAT calculation.
      This is used in carts where you have multiple products and prices on options.
    */
    function &priceExVAT( $price="" )
    {
       if ( $price == "" )
       {
           $calcPrice = $this->Price;
       }
       else
       {
           $calcPrice = $price;
       }
       
       $vatType =& $this->vatType();

       $vat = 0;
       
       if ( $vatType )
       {
           $value =& $vatType->value();

           $vat = ( $calcPrice / ( $value + 100  ) ) * $value;
       }
      
       $priceExVat = $calcPrice - $vat;

       return $priceExVat;
    }

    /*!
      Returns the price of the product included VAT ( prive + VAT value ).

      If a value is given as argument this value is used for VAT calculation.
      This is used in carts where you have multiple products and prices on options.
    */
    function &priceIncVAT( $price="" )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       if ( $price == "" )
       {
           $calcPrice = $this->Price;
       }
       else
       {
           $calcPrice = $price;
       }
       
       $vatType =& $this->vatType();

       $vat = 0;
       
       if ( $vatType )
       {
           $value =& $vatType->value();

           $vat = ( $calcPrice / ( $value + 100  ) ) * $value;
       }
      
       $priceExVat = $calcPrice + $vat;

       return array( "Price" => $priceExVat, "VAT" => $vat );
    }


    /*!
      Returns the VAT value of the product.

      If a value is given as argument this value is used for VAT calculation.
      This is used in carts where you have multiple products and prices on options.
    */
    function &vat( $price="" )
    {
       if ( $price == "" )
       {
           $calcPrice = $this->Price;
       }
       else
       {
           $calcPrice = $price;
       }
       
       $vatType =& $this->vatType();
       $vat = 0;
       if ( $vatType )
       {
           $value =& $vatType->value();
           $vat = ( $calcPrice / ( $value + 100  ) ) * $value;        
       }
       return $vat;
    }

    /*!
      Returns the product type.

      1 = normal product
      2 = voucher
    */
    function productType( $price="" )
    {
       return $this->ProductType;
    }

    /*!
      Returns the products expiry time
    */
    function expiryTime()
    {
        return $this->ExpiryTime;
    }
    
    /*!
      Sets the product type.

      1 = normal product
      2 = voucher
    */
    function setProductType( $type=1 )
    {
        $this->ProductType = $type;
    }

    /*!
      Sets the total quantity of the product.
    */
    function setTotalQuantity( $quantity )
    {
        $id = $this->ID;
        $db =& eZDB::globalDatabase();
        $db->array_query( $qry_array,
                          "SELECT Q.ID
                           FROM eZTrade_Quantity AS Q, eZTrade_ProductQuantityDict AS PQD
                           WHERE Q.ID=PQD.QuantityID AND ProductID='$id'" );
        $db->begin();
        $res[] = $db->query( "DELETE FROM eZTrade_ProductQuantityDict WHERE ProductID='$id'" );
        
        foreach( $qry_array as $row )
        {
            $q_id = $row[$db->fieldName( "ID" )];
            $res[] = $db->query( "DELETE FROM eZTrade_Quantity WHERE ID='$q_id'" );
        }
        if ( is_bool( $quantity ) and !$quantity )
            return;

        $db->lock( "eZTrade_Quantity" );
        
        $nextQuantityID = $db->nextID( "eZTrade_Quantity", "ID" );

        $res[] = $db->query( "INSERT INTO eZTrade_Quantity ( ID, Quantity ) VALUES ('$nextQuantityID','$quantity')" );
        $q_id = $nextQuantityID;
        $res[] = $db->query( "INSERT INTO eZTrade_ProductQuantityDict ( ProductID, QuantityID ) VALUES ('$id','$q_id')" );

        $db->unlock();

        if ( in_array( false, $res ) )
            $db->rollback();
        else
            $db->commit();            
    }

    /*!
      Sets the expiry time for this product
    */
    function setExpiryTime( $time )
    {
        $this->ExpiryTime = $time;
    }

    /*!
      \static
      Returns the total quantity of this product.
    */
    function totalQuantity( $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();
        $db->array_query( $qry_array,
                          "SELECT Q.Quantity
                           FROM eZTrade_Quantity AS Q, eZTrade_ProductQuantityDict AS PQD
                           WHERE Q.ID=PQD.QuantityID AND ProductID='$id'" );

        $quantity = 0;
        if ( count( $qry_array ) > 0 )
        {
            foreach( $qry_array as $row )
            {
                if ( $row[$db->fieldName( "Quantity" )] == "NULL" )
                    return false;
                $quantity += $row[$db->fieldName( "Quantity" )];
            }
        }
        else
            return false;
        return $quantity;
    }

    /*!
      \static
      Returns true if the product has some sort of quantity which can be bought.
    */
    function hasQuantity( $require = true, $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $quantity = eZProduct::totalQuantity( $id );
        if ( is_bool($quantity) or
             !$require or ( $require and $quantity > 0 ) )
            return true;
        return false;
    }

    /*!
      Returns a textual version of the quantity, this allows a site to hide the
      exact quantity but instead give indications.
      If no named quantity can be found the quantity is returned.
    */
    function namedQuantity( $quantity )
    {
        $db =& eZDB::globalDatabase();
        if ( is_bool( $quantity ) and !$quantity )
        {
            $db->array_query( $qry_array, "SELECT Name FROM eZTrade_QuantityRange
                                       WHERE MaxRange=-1", array( "Limit" => 1 ), 1 );
            $name = $qry_array[0][$db->fieldName( "Name" )];
        }
        else
        {
            $db->array_query( $qry_array, "SELECT Name FROM eZTrade_QuantityRange
                                       WHERE MaxRange IS NOT NULL AND MaxRange>=$quantity
                                       ORDER BY MaxRange", array( "Limit" => 1 ), 1 );
            $name = "";
            if ( count( $qry_array ) == 1 )
            {
                $name = $qry_array[0][$db->fieldName( "Name" )];
            }
            else
            {
                $db->array_query( $qry_array, "SELECT Name FROM eZTrade_QuantityRange
                                           WHERE MaxRange IS NULL", array( "Limit" => 1 ), 1 );
                if ( count( $qry_array ) == 1 )
                    $name = $qry_array[0][$db->fieldName( "Name" )];
                else
                    $name = $quantity;
            }
        }
        return $name;
    }

    /*!
      Returns the keywords of the product.
    */
    function &keywords( )
    {
       return htmlspecialchars( $this->Keywords );
    }    

    /*!
      Returns the product number of the product.
    */
    function &productNumber( )
    {
       return htmlspecialchars( $this->ProductNumber );
    }    

    /*!
      Returns the introduction to the product.
    */
    function &brief( )
    {
       return htmlspecialchars( $this->Brief );
    }    

    /*!
      Returns the description of the product.
    */
    function &description( )
    {
       return htmlspecialchars( $this->Description );
    }

    /*!
      Returns the ShowPrice value. The Price should not be shown if this value
      is false.
    */
    function showPrice()
    {
       return $this->ShowPrice;
    }


    /*!
      Returns true if the product should be shown. False if not.
    */
    function showProduct()
    {
       return $this->ShowProduct;
    }

    /*!
      Returns true if the product is discontinued.
    */
    function discontinued()
    {
       return $this->Discontinued;
    }

    /*!
      Returns the external link to the product.
    */
    function externalLink()
    {
       return htmlspecialchars( $this->ExternalLink );
    }

    /*!
      Returns true if the product is a hot deal.
      False if not.
    */
    function isHotDeal()
    {
       $ret = false;
       if ( $this->IsHotDeal == 1 )
       {
           $ret = true;
       }

       return $ret;
    }
      
    
    /*!
      Sets the product name.
    */
    function setName( $value )
    {
       $this->Name =& $value;        
    }

    /*!
      Sets the remote ID.
    */
    function setRemoteID( $remoteID )
    {
       $this->RemoteID = $remoteID;        
    }


    /*!
      Sets the brief description of the product.
    */
    function setBrief( $value )
    {
       $this->Brief = $value;
    }

    /*!
      Sets the product description.
    */
    function setDescription( $value )
    {
       $this->Description = $value;       
    }

    /*!
      Sets the keywords.
    */
    function setKeywords( $value )
    {
       $this->Keywords = $value;
    }

    /*!
      Sets the product number.
    */
    function setProductNumber( $value )
    {
       $this->ProductNumber = $value;
    }

    /*!
      Sets the product number.
    */
    function setPrice( $value )
    {
       $this->Price = $value;
       setType( $this->Price, "double" );
    }

    /*!
      Sets the ShowPrice value.
    */
    function setShowPrice( $value )
    {
       $this->ShowPrice = $value;
       setType( $this->ShowPrice, "integer" );
    }
    
    /*!
      Sets the ShowProduct value.
    */
    function setShowProduct( $value )
    {
       $this->ShowProduct = $value;
       setType( $this->ShowProduct, "integer" );
    }
    
    /*!
      Sets the Discontinued value. This indicates that the product is no longer
      available. The product is still shown in the store.
    */
    function setDiscontinued( $value )
    {
       $this->Discontinued = $value;
       setType( $this->Discontinued, "integer" );
    }

    /*!
      Sets the external link.
    */
    function setExternalLink( $value )
    {
       $this->ExternalLink = $value;
    }

    /*!
      Set the product to be a hot deal or not. True makes
      the product a hot deal, false it it just as an ordinary
      product.
    */
    function setIsHotDeal( $value )
    {
       if ( $value == true )
       {
           $this->IsHotDeal = 1;
       }
       else
       {
           $this->IsHotDeal = 0;
       }
    }

    /*!
      Adds a option to the product.
    */
    function addOption( $value )
    {
        if ( get_class( $value ) == "ezoption" )
        {
            $optionID = $value->id();
            $value->store();

            $db =& eZDB::globalDatabase();
            $db->begin();
            $db->lock( "eZTrade_ProductOptionLink" );
            $nextID = $db->nextID( "eZTrade_ProductOptionLink", "ID" );            
            $res = $db->query( "INSERT INTO eZTrade_ProductOptionLink ( ID, ProductID, OptionID ) VALUES ( '$nextID', '$this->ID', '$optionID'" );
            $db->unlock();
            if ( $res == false )
                $db->rollback( );
            else
                $db->commit();
        }
    }

    /*!
      Returns every option to a product as a array of eZOption objects.
    */
    function options()
    {
       $return_array = array();
       $option_array = array();
       $db =& eZDB::globalDatabase();
       
       $db->array_query( $option_array, "SELECT OptionID FROM eZTrade_ProductOptionLink WHERE ProductID='$this->ID'" );

       for ( $i = 0; $i < count( $option_array ); $i++ )
       {
           $return_array[$i] = new eZOption( $option_array[$i][$db->fieldName( "OptionID" )], true );
       }
       
       return $return_array;
    }

    /*!
      Adds an image to the product.
    */
    function addImage( $value )
    {
        if ( get_class( $value ) == "ezimage" )
        {
            $imageID = $value->id();

            $db =& eZDB::globalDatabase();
            $db->begin();
            $db->lock( "eZTrade_ProductImageLink" );
            $nextID = $db->nextID( "eZTrade_ProductImageLink", "ID" );            
            $res = $db->query( "INSERT INTO eZTrade_ProductImageLink ( ID, ProductID, ImageID ) VALUES ( '$nextID', '$this->ID', '$imageID'" );
            $db->unlock();
            if ( $res == false )
                $db->rollback( );
            else
                $db->commit();
        }
    }

    /*!
      Deletes an image from the product.

      NOTE: the image does not get deleted from the image catalogue.
    */
    function deleteImage( $value )
    {
        if ( get_class( $value ) == "ezimage" )
        {
            $imageID = $value->id();

            $db =& eZDB::globalDatabase();
            $db->begin();
            $res[] = $db->query( "DELETE FROM eZTrade_ProductImageLink WHERE ProductID='$this->ID' AND ImageID='$imageID'" );
            $res[] = $db->query( "DELETE FROM eZTrade_ProductImageDefinition WHERE ProductID='$this->ID' AND MainImageID='$imageID'" );
            $res[] = $db->query( "DELETE FROM eZTrade_ProductImageDefinition WHERE ProductID='$this->ID' AND ThumbnailImageID='$imageID'" );
            if ( in_array( false, $res ) )
                $db->rollback();
            else
                $db->commit();            
        }
    }
    
    /*!
      Returns every image to a product as a array of eZImage objects.
    */
    function images()
    {
       $return_array = array();
       $image_array = array();

       $db =& eZDB::globalDatabase();
       $db->array_query( $image_array, "SELECT ImageID FROM eZTrade_ProductImageLink WHERE ProductID='$this->ID' ORDER BY Created" );
       
       for ( $i=0; $i < count($image_array); $i++ )
       {
           $return_array[$i] = new eZImage( $image_array[$i][$db->fieldName( "ImageID" )], false );
       }
       
       return $return_array;
    }

    /*!
      Sets the main image for the product.

      The argument must be a eZImage object, or false to unset the main image.
    */
    function setMainImage( $image )
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        if ( get_class( $image ) == "ezimage" )
        {
            $imageID = $image->id();

            $db->array_query( $res_array, "SELECT COUNT(*) AS Number FROM eZTrade_ProductImageDefinition
                                                       WHERE
                                                       ProductID='$this->ID'" );

            if ( $res_array[0][$db->fieldName( "Number" )] == "1" )
            {            
                $res[] = $db->query( "UPDATE eZTrade_ProductImageDefinition
                                         SET
                                         MainImageID='$imageID'
                                         WHERE
                                         ProductID='$this->ID'" );
            }
            else
            {
                $db->lock( "eZTrade_ProductImageDefinition" );
                $nextID = $db->nextID( "eZTrade_ProductImageDefinition", "ID" );            
                $res[] = $db->query( "INSERT INTO eZTrade_ProductImageDefinition
                                   ( ID, ProductID, MainImageID )
                                   VALUES ( '$nextID', '$this->ID', '$imageID' )
                                   " );
                $db->unlock();
            }
        }
        else if ( $image == false )
        {
            $db->array_query( $res_array, "SELECT COUNT(*) AS Number FROM eZTrade_ProductImageDefinition
                                                       WHERE
                                                       ProductID='$this->ID'" );

            if ( $res_array[0][$db->fieldName( "Number" )] == "1" )
            {
                $res[] = $db->query( "UPDATE eZTrade_ProductImageDefinition
                                         SET
                                         MainImageID='0'
                                         WHERE
                                         ProductID='$this->ID'" );
            }
        }

        if ( in_array( false, $res ) )
            $db->rollback( );
        else
            $db->commit();            
    }    
    
    /*!
      Sets the thumbnail image for the product.

      The argument must be a eZImage object, or false to unset the thumbnail image.
    */
    function setThumbnailImage( $image )
    {
        $db =& eZDB::globalDatabase();
        $db->begin();

        if ( get_class( $image ) == "ezimage" )
        {
            $imageID = $image->id();

            $db->array_query( $res_array, "SELECT COUNT(*) AS Number FROM eZTrade_ProductImageDefinition
                                                       WHERE
                                                       ProductID='$this->ID'" );

            if ( $res_array[0][$db->fieldName( "Number" )] == "1" )
            {            
                $res[] = $db->query( "UPDATE eZTrade_ProductImageDefinition
                                         SET
                                         ThumbnailImageID='$imageID'
                                         WHERE
                                         ProductID='$this->ID'" );
            }
            else
            {
                $db->lock( "eZTrade_ProductImageDefinition" );
                $nextID = $db->nextID( "eZTrade_ProductImageDefinition", "ID" );            

                $res[] = $db->query( "INSERT INTO eZTrade_ProductImageDefinition
                                   ( ID, ProductID, ThumbnailImageID )
                                   VALUES
                                   ( '$nextID',
                                     '$this->ID',
                                     '$imageID' )" ) ;
                $db->unlock();
            }
        }
        else if ( $image == false )
        {
            $db->array_query( $res_array, "SELECT COUNT(*) AS Number FROM eZTrade_ProductImageDefinition
                                                       WHERE
                                                       ProductID='$this->ID'" );

            if ( $res_array[0][$db->fieldName( "Number" )] == "1" )
            {
                $res[] = $db->query( "UPDATE eZTrade_ProductImageDefinition
                                         SET
                                         ThumbnailImageID='0'
                                         WHERE
                                         ProductID='$this->ID'" );
            }
        }
        
        if ( in_array( false, $res ) )
            $db->rollback( );
        else
            $db->commit();            
    }

    /*!
      Returns the main image of the product as a eZImage object.

      false (0) is returned if no main image is found.
    */
    function mainImage( )
    {
        $db =& eZDB::globalDatabase(); 
        $ret = false;
        $res = $db->array_query( $res_array, "SELECT * FROM eZTrade_ProductImageDefinition
                                     WHERE
                                     ProductID='$this->ID'
                                   " );
       
        if ( count( $res_array ) == 1 )
        {
            if ( $res_array[0][$db->fieldName( "MainImageID" )] != "NULL" )
            {
                $ret = new eZImage( $res_array[0][$db->fieldName( "MainImageID" )], false );
            }               
        }
        
        return $ret;
    }

    /*!
      Returns the thumbnail image of the product as a eZImage object.
    */
    function thumbnailImage( )
    {
        $db =& eZDB::globalDatabase(); 
        $ret = false;
   
        $db->array_query( $res_array, "SELECT * FROM eZTrade_ProductImageDefinition
                                     WHERE
                                     ProductID='$this->ID'
                                   " );
       
        if ( count( $res_array ) == 1 )
        {
           if ( is_numeric( $res_array[0][$db->fieldName( "ThumbnailImageID" )] ) )
           {
               $ret = new eZImage( $res_array[0][$db->fieldName( "ThumbnailImageID" )], false );
           }               
        }
        
        return $ret;
       
    }

    /*!
      Searches through every product and returns the result as an array
      of eZProduct objects.
    */
    function activeProductSearch( $query, $offset, $limit )
    {
        $db =& eZDB::globalDatabase(); 
        $ret = array();
        
        $db->array_query( $res_array, "SELECT ID FROM eZTrade_Product
                                     WHERE
                                     ( Name LIKE '%$query%' ) OR
                                     ( Description LIKE '%$query%' ) OR
                                     ( Keywords LIKE '%$query%' )", 
                                     array( "Limit" => $limit, "Offset" => $offset ) );

       foreach ( $res_array as $product )
       {
           $ret[] = new eZProduct( $product[$db->fieldName( "ID" )] );
       }
       
       return $ret;
    }

    /*!
      Searches through every product and returns the result count
    */
    function activeProductSearchCount( $query )
    {
        $db =& eZDB::globalDatabase(); 
        $ret = array();

        $db->array_query( $res_array, "SELECT count(ID) AS Count FROM eZTrade_Product
                                     WHERE
                                     ( Name LIKE '%$query%' ) OR
                                     ( Description LIKE '%$query%' ) OR
                                     ( Keywords LIKE '%$query%' )
                                   " );
       
        return $res_array[0][$db->fieldName( "Count" )];
    }

    /*!
      Search through the products.

      Returns the products as an array of eZProducts objects.
    */
    function extendedSearch( $priceLower, $priceHigher, $text, $offset=0, $limit=10, $categoryArrayID=array() )
    {
        $db =& eZDB::globalDatabase();
        
        $products = array();

        if ( is_numeric( $priceLower )  )
        {
            $price = " AND eZTrade_Product.Price > $priceLower";
        }
        if ( is_numeric( $priceHigher )  )
        {
            $price .= " AND eZTrade_Product.Price < $priceHigher";
        }

        $text = trim( $text );
        if ( $text != "" )
        {
            $query = new eZQuery( array( "eZTrade_Product.Name", "eZTrade_Product.Keywords", "eZTrade_Product.Description" ), $text );
            if ( $price || $categorySQL )
                $text = "AND (" . $query->buildQuery()  . ")";
            else
                $text = "AND (" . $query->buildQuery()  . ")";
        }

        $tables = array();

        foreach ( $categoryArrayID as $cat )
        {
            $id = $cat[$db->fieldName( "id" )];
            $table = "eZTrade_ExtendedTemp$id";
            $tables[] = $table;
            $db->query( "CREATE TEMPORARY TABLE $table
                         ( ProductID int(11) NOT NULL, PRIMARY KEY( ProductID ) )" );
            $cats =& $cat["categories"];
            $catSQL = "";
            $i = 0;
            foreach ( $cats as $cat_item )
            {
                if ( $i > 0 )
                    $catSQL .= " OR ";
                $catSQL .= "eZTrade_ProductCategoryLink.CategoryID='$cat_item'";
                ++$i;
            }
            $db->query( "INSERT INTO $table(ProductID)
                         SELECT eZTrade_Product.ID FROM eZTrade_Product, eZTrade_ProductCategoryLink
                         WHERE eZTrade_Product.ID=eZTrade_ProductCategoryLink.ProductID AND ( $catSQL )
                         GROUP BY eZTrade_Product.ID" );
        }

        reset( $tables );
        list($key,$first_table) = each($tables);
        $i = 0;
        $table_sql = "";
        $table_from = ", $first_table";
        while( list($key,$table) = each($tables) )
        {
            if ( $i > 0 )
                $table_sql = " AND ";
            $table_sql .= "$first_table.ProductID=$table.ProductID";
            $table_from .= ", $table";
            ++$i;
        }
        if ( count( $tables ) > 0 )
            $table_sql = "( $table_sql )";

        $queryString = "SELECT eZTrade_Product.ID as PID
                        FROM eZTrade_Product, eZTrade_ProductCategoryLink $table_from
                        WHERE $table_sql $price $text AND
                        eZTrade_Product.ID = $first_table.ProductID GROUP BY PID LIMIT $offset, $limit";

        $db->array_query( $res_array, $queryString );

        if ( count ( $res_array ) > 0 )
        {
            foreach( $res_array as $productItem )
            {
                $products[] = new eZProduct( $productItem["PID"] );
            }
        }

        foreach( $tables as $table )
        {
            $db->query( "DROP TABLE $table" );
        }

        return $products;
    }


    /*!
      Search through the products and returns the count.
    */
    function extendedSearchCount( $priceLower, $priceHigher, $text, $categoryArrayID=array() )
    {
        $db =& eZDB::globalDatabase();

        $db->begin();
        
        $products = array();

        if ( is_numeric( $priceLower )  )
        {
            $price = " AND eZTrade_Product.Price > $priceLower";
        }
        if ( is_numeric( $priceHigher )  )
        {
            $price .= " AND eZTrade_Product.Price < $priceHigher";
        }
        $text = trim( $text );
        if ( $text != "" )
        {
            $query = new eZQuery( array( "eZTrade_Product.Name", "eZTrade_Product.Keywords", "eZTrade_Product.Description" ), $text );
            if ( $price || $categorySQL )
                $text = "AND (" . $query->buildQuery()  . ")";
            else
                $text = "AND (" . $query->buildQuery()  . ")";
        }

        $tables = array();

        foreach( $categoryArrayID as $cat )
        {
            $id = $cat[$db->fieldName( "id" )];
            $table = "eZTrade_ExtendedTemp$id";
            $tables[] = $table;
            $db->query( "CREATE TEMPORARY TABLE $table
                         ( ProductID int(11) NOT NULL, PRIMARY KEY( ProductID ) )" );
            $cats =& $cat["categories"];
            $catSQL = "";
            $i = 0;
            foreach( $cats as $cat_item )
            {
                if ( $i > 0 )
                    $catSQL .= " OR ";
                $catSQL .= "eZTrade_ProductCategoryLink.CategoryID='$cat_item'";
                ++$i;
            }
            $db->query( "INSERT INTO $table(ProductID)
                         SELECT eZTrade_Product.ID FROM eZTrade_Product, eZTrade_ProductCategoryLink
                         WHERE eZTrade_Product.ID=eZTrade_ProductCategoryLink.ProductID AND ( $catSQL )
                         GROUP BY eZTrade_Product.ID" );
        }

        reset( $tables );
        list($key,$first_table) = each($tables);
        $i = 0;
        $table_sql = "";
        $table_from = ", $first_table";
        while( list($key,$table) = each($tables) )
        {
            if ( $i > 0 )
                $table_sql = " AND ";
            $table_sql .= "$first_table.ProductID=$table.ProductID";
            $table_from .= ", $table";
            ++$i;
        }
        if ( count( $tables ) > 0 )
            $table_sql = "( $table_sql )";

        $queryString = "SELECT count( DISTINCT eZTrade_Product.ID ) AS Count
                        FROM eZTrade_Product, eZTrade_ProductCategoryLink $table_from
                        WHERE $table_sql $price $text AND
                        eZTrade_Product.ID = $first_table.ProductID";
        $db->query_single( $res_array, $queryString );

        foreach( $tables as $table )
        {
            $db->query( "DROP TABLE $table" );
        }

        return $res_array[$db->fieldName( "Count" )];
    }
   
    
    /*!
      Returns the products set to hot deal.
    */
    function &hotDealProducts( $limit = false )
    {
       if ( is_numeric( $limit ) and $limit >= 0 )
       {
           $limit_text = "array( \"Limit\" => $limit, \"Offset\" => 0 )";
       }

       $ret = array();
       $db =& eZDB::globalDatabase();

       $db->array_query( $res_array, "SELECT ID FROM eZTrade_Product
                                     WHERE
                                     IsHotDeal='1' ORDER BY Name", $limit_text );

       foreach ( $res_array as $product )
       {
           $ret[] = new eZProduct( $product[$db->fieldName( "ID" )] );
       }
       
       return $ret;

    }

    /*!
      Returns the categrories a product is assigned to.

      The categories are returned as an array of eZProductCategory objects.
    */
    function categories( $as_object = true )
    {
       $db =& eZDB::globalDatabase();

       $ret = array();
       $db->array_query( $category_array, "SELECT * FROM eZTrade_ProductCategoryLink WHERE ProductID='$this->ID'" );

       if ( $as_object )
       {
           foreach ( $category_array as $category )
           {
               $ret[] = new eZProductCategory( $category[$db->fieldName( "CategoryID" )] );
           }
       }
       else
       {
           foreach ( $category_array as $category )
           {
               $ret[] = $category[$db->fieldName( "CategoryID" )];
           }
       }

       return $ret;
    }
    

    /*!
      Removes every category assignments from the current product.
    */
    function removeFromCategories()
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        
        $res = $db->query( "DELETE FROM eZTrade_ProductCategoryLink WHERE ProductID='$this->ID'" );
        eZDB::finish( $res, $db );
        
    }

    /*!
      Returns true if the product is assigned to the category given
      as argument. False if not.
     */
    function existsInCategory( $category )
    {
       $ret = false;
       if ( get_class( $category ) == "ezproductcategory" )
       {
           $db =& eZDB::globalDatabase();
           $catID = $category->id();
        
           $db->array_query( $ret_array, "SELECT ID FROM eZTrade_ProductCategoryLink
                                    WHERE ProductID='$this->ID' AND CategoryID='$catID'" );

           if ( count( $ret_array ) == 1 )
           {
               $ret = true;
           }           
       }
       return $ret;
    }

    /*!
      Set's the products defined category. This is the main category for the product.
      Additional categories can be added with eZProductCategory::addProduct();
    */
    function setCategoryDefinition( $value )
    {
       if ( get_class( $value ) == "ezproductcategory" )
       {
           $db =& eZDB::globalDatabase();
           $db->begin();

           $categoryID = $value->id();
           
           $db->query( "DELETE FROM eZTrade_ProductCategoryDefinition
                                     WHERE ProductID='$this->ID'" );
            
           $db->lock( "eZTrade_ProductCategoryDefinition" );
           $nextID = $db->nextID( "eZTrade_ProductCategoryDefinition", "ID" );

           $query = "INSERT INTO eZTrade_ProductCategoryDefinition
                         ( ID,
                           CategoryID,
                           ProductID )
                         VALUES
                         ( '$nextID',   
                           '$categoryID',
                           '$this->ID' )";
           $db->unlock();
           $res[] = $db->query( $query );

           eZDB::finish( $res, $db );
       }       
    }

    /*!
      Returns the product's definition category.
    */
    function categoryDefinition( $as_object = true )
    {
       $db =& eZDB::globalDatabase();
       $db->array_query( $res, "SELECT CategoryID FROM
                                            eZTrade_ProductCategoryDefinition
                                            WHERE ProductID='$this->ID'" );
       $category = false;
       if ( count( $res ) == 1 )
       {
           if ( $as_object )
               $category = new eZProductCategory( $res[0][$db->fieldName( "CategoryID" )] );
           else
               $category = $res[0][$db->fieldName( "CategoryID" )];
       }
       else
       {
           print( "<br><b>Failed to fetch product category definition for ID $this->ID</b><br>" );
       }
       
       return $category;
    }

    /*!
      Sets the products type.
    */
    function setType( $type )
    {
       if ( get_class( $type ) == "ezproducttype" )
       {
            $db =& eZDB::globalDatabase();
            $db->begin();

            $typeID = $type->id();

            
            $res[] = $db->query( "DELETE FROM eZTrade_AttributeValue
                                     WHERE ProductID='$this->ID'" );
            
            $res[] = $db->query( "DELETE FROM eZTrade_ProductTypeLink
                                     WHERE ProductID='$this->ID'" );

            
            $db->lock( "eZTrade_ProductTypeLink" );
            $nextID = $db->nextID( "eZTrade_ProductTypeLink", "ID" );
            $query = "INSERT INTO eZTrade_ProductTypeLink
                         ( ID,
                           TypeID,
                           ProductID )
                         VALUES
                         ( '$nextID',
                           '$typeID',
                           '$this->ID' )";
            $db->unlock();
            $res[] = $db->query( $query );
            eZDB::finish( $res, $db );
       }       
    }

    /*!
      Returns the product's type.
    */
    function type( )
    {
       $db =& eZDB::globalDatabase();

       $db->array_query( $res, "SELECT TypeID FROM
                                            eZTrade_ProductTypeLink
                                            WHERE ProductID='$this->ID'" );

       $type = false;
       
       if ( count( $res ) == 1 )
       {
           $type = new eZProductType( $res[0][$db->fieldName( "TypeID" )] );
       }

       return $type;
    }

    /*!
      Removes the products type definition.
    */
    function removeType()
    {
       $db =& eZDB::globalDatabase();
       $db->begin();
       
       // delete values
       $res[] = $db->query( "DELETE FROM eZTrade_AttributeValue
                                     WHERE ProductID='$this->ID'" );

       $res[] = $db->query( "DELETE FROM eZTrade_ProductTypeLink
                                     WHERE ProductID='$this->ID'" );
       eZDB::finish( $res, $db );
            
    }

    /*!
      Check if there are a product where RemoteID == $id. Return the product if true.
    */
    function getByRemoteID( $id )
    {
        $db =& eZDB::globalDatabase();
        
        $product = false;
        
        $db->array_query( $res, "SELECT ID FROM
                                            eZTrade_Product
                                            WHERE RemoteID='$id'" );
        
        if ( count( $res ) == 1 )
        {
            $product = new eZProduct( $res[0][$db->fieldName( "ID" )] );
        }
        
        return $product;
    }

    /*!
      Returns the name of the product with the given id.
    */
    function productName( $id )
    {
        $db =& eZDB::globalDatabase();
        $ret = false;
        
        if ( $id != "" )
        {
            $db->array_query( $product_array, "SELECT * FROM eZTrade_Product WHERE ID='$id'" );
            
            if( count( $product_array ) == 1 )
            {
                $ret =& $product_array[0][$db->fieldName( "Name" )];
            }
        }
        
        return $ret;
    }

    /*!
      Sets the VAT type.
    */
    function setVATType( $type )
    {
       $db =& eZDB::globalDatabase();

       if ( get_class( $type ) == "ezvattype" )
       {
           $this->VATTypeID = $type->id();
       }
    }


    /*!
      Returns the VAT type.

      False if no type is assigned.
    */
    function vatType( )
    {
       $db =& eZDB::globalDatabase();

       $ret = false;
       if ( is_numeric( $this->VATTypeID ) and ( $this->VATTypeID > 0 ) )
       {
           $ret = new eZVATType( $this->VATTypeID );
       }

       return $ret;
    }

    /*!
      Sets the shipping group
    */
    function setShippingGroup( $group )
    {
       $db =& eZDB::globalDatabase();

       if ( get_class( $group ) == "ezshippinggroup" )
       {
           $this->ShippingGroupID = $group->id();
       }
    }


    /*!
      Returns the shipping group as a eZShippingGroup object.

      False if no type is not assigned.
    */
    function shippingGroup( )
    {
       $db =& eZDB::globalDatabase();

       $ret = false;
       if ( is_numeric( $this->ShippingGroupID ) and ( $this->ShippingGroupID > 0 ) )
       {
           $ret = new eZShippingGroup( $this->ShippingGroupID );
       }

       return $ret;
    }
    
    
    var $ID;
    var $Name;
    var $Brief;
    var $Description;
    var $Keywords;
    var $ProductNumber;
    var $ShowPrice;
    var $ShowProduct;
    var $Discontinued;
    var $ExternalLink;
    var $IsHotDeal;
    var $RemoteID;
    var $VATTypeID;
    var $ShippingGroupID;
    var $ProductType;
    var $Price;
    var $ExpiryTime;
}

?>
