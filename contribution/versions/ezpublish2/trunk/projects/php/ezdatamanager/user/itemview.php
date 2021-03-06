<?php
//
// $Id: itemview.php,v 1.4 2002/02/21 14:50:52 jhe Exp $
//
// Created on: <20-Nov-2001 17:23:58 bf>
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

include_once( "classes/ezlocale.php" );
include_once( "classes/ezhttptool.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/INIFile.php" );
include_once( "classes/ezfile.php" );

include_once( "ezdatamanager/classes/ezdatatype.php" );
include_once( "ezdatamanager/classes/ezdatatypeitem.php" );
include_once( "ezdatamanager/classes/ezdataitem.php" );

include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezqdomrenderer.php" );

include_once( "ezimagecatalogue/classes/ezimage.php" );
include_once( "ezimagecatalogue/classes/ezimagevariation.php" );

$Language = $ini->read_var( "eZDataManagerMain", "Language" );
$TemplateDir = $ini->read_var( "eZDataManagerMain", "TemplateDir" );
$t = new eZTemplate( "ezdatamanager/user/" . $ini->read_var( "eZDataManagerMain", "TemplateDir" ),
                     "ezdatamanager/user/intl", $Language, "itemview.php" );

$locale = new eZLocale( $Language );

if ( $ItemID > 0 )
{
    $item = new eZDataItem( $ItemID );
    $type =& $item->dataType();

    // check for override template
    $override = "_override_" . $type->id();

    if ( eZFile::file_exists( "ezdatamanager/user/$TemplateDir/itemview" . $override  . ".tpl" ) )
        $t->set_file( "item_edit_tpl", "itemview" . $override . ".tpl" );
    else
        $t->set_file( "item_edit_tpl", "itemview.tpl" );

    $t->set_block( "item_edit_tpl", "item_value_list_tpl", "item_value_list" );
    $t->set_block( "item_value_list_tpl", "item_value_tpl", "item_value" );
    $t->set_block( "item_edit_tpl", "image_view_tpl", "image_view" );

    $t->setAllStrings();

    $t->set_var( "item_id", $ItemID );
    $t->set_var( "image_view", "" );

    $t->set_var( "item_name", $ItemName );
    $t->set_var( "item_value_list", "" );
    
    $t->set_var( "data_type_value", "" );

    $t->set_var( "item_name", $item->name() );

    $image = $item->image();
    if ( $image )
    {
        $variation = $image->requestImageVariation( 150, 150 );
        $t->set_var( "image_src", "/" . $variation->imagePath() );
        $t->set_var( "image_width", $variation->width() );
        $t->set_var( "image_height", $variation->height() );
        $t->set_var( "image_alt", eZTextTool::htmlspecialchars( $image->caption() ) );
        $t->parse( "image_view", "image_view_tpl" );
    }

    $dataType =& $item->dataType();
    $dataTypeItems =& $dataType->typeItems();

    $article = new eZArticle();
    $renderer = new eZQDomRenderer( &$article );
    $i = 0;
    foreach ( $dataTypeItems as $dataTypeItem )
    {
        switch ( $dataTypeItem->itemType() )
        {
            case "1" :
            {        
                $article->setContents( $item->itemValue( $dataTypeItem ) );
                $value =& $renderer->renderIntro();
                $t->set_var( "data_type_value", $value );
                $t->set_var( "data_type_value_$i", $value );
                
            }
            break;

            case "2" :
            {
                $tempItem = new eZDataItem( $item->itemValue( $dataTypeItem ) );
                
                $value = ( "<a href=\"/datamanager/itemview/" . $item->itemValue( $dataTypeItem ) . "/\">". $tempItem->name() . "</a>" );
                $t->set_var( "data_type_value", $value );
                $t->set_var( "data_type_value_$i", $value );
            }
            break;
        }
        
        $t->set_var( "data_type_name", $dataTypeItem->name() );
        $t->set_var( "data_type_id", $dataTypeItem->id() );
        
        $t->set_var( "data_type_name_$i", $dataTypeItem->name() );
        $t->set_var( "data_type_id_$i", $dataTypeItem->id() );        
        
        $t->parse( "item_value", "item_value_tpl", true );
        $i++;
    }
    $t->parse( "item_value_list", "item_value_list_tpl" );
}
else
{
    $t->set_file( "item_edit_tpl", "itemview.tpl" );
    $t->set_block( "item_edit_tpl", "item_value_list_tpl", "item_value_list" );
    $t->set_block( "item_value_list_tpl", "item_value_tpl", "item_value" );

    $t->setAllStrings();

    $t->set_var( "item_id", $ItemID );

    $t->set_var( "item_name", $ItemName );
    $t->set_var( "item_value_list", "" );
    
    $t->set_var( "data_type_value", "" );
}

$t->pparse( "output", "item_edit_tpl" );

?>
