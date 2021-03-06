<?php
// 
// $Id: formedit.php,v 1.3 2001/07/19 13:03:49 jakobn Exp $
//
// Created on: <12-Jun-2001 13:07:24 pkej>
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

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "ezform/classes/ezform.php" );
include_once( "ezform/classes/ezformelement.php" );
include_once( "ezform/classes/ezformelementtype.php" );
include_once( "ezmail/classes/ezmail.php" );

$ini =& INIFile::globalINI();

if( isset( $Cancel ) )
{
    eZHTTPTool::header( "Location: /form/form/list/" );
    exit();
}

$ActionValue="edit";

$form = new eZForm( $FormID );

if( $Action == "up" )
{
    $element = new eZFormElement( $ElementID );
    $form->moveUp( $element );
    eZHTTPTool::header( "Location: /form/form/edit/$FormID/" );
    exit();
}

if( $Action == "down" )
{
    $element = new eZFormElement( $ElementID );
    $form->moveDown( $element );
    eZHTTPTool::header( "Location: /form/form/edit/$FormID/" );
    exit();
}


if( isset( $DeleteSelected ) )
{
    foreach( $elementDelete as $deleteMe )
    {
        $element = new eZFormElement( $deleteMe );
        $element->delete();
    }
    eZHTTPTool::header( "Location: /form/form/edit/$FormID/" );
}


$errorMessages = array();

if( isset( $OK ) || isset( $Update ) || isset( $Preview ) || isset( $NewElement ) )
{
    if( empty( $formSender ) )
    {
        if( isset( $formSendAsUser ) == false )
        {
            $errorMessages[] = "form_must_have_sender";
        }
    }
    else
    {
        if( isset( $formSendAsUser ) )
        {
            $errorMessages[] = "form_cant_have_both";
        }
        else
        {
            if( eZMail::validate( $formReceiver ) == false )
            {
                $errorMessages[] = "form_sender_not_valid";
            }
        }
    }

    if( empty( $formName ) )
    {
        $errorMessages[] = "form_name_not_set";
    }

    if( empty( $formReceiver ) )
    {
        $errorMessages[] = "form_receiver_not_set";
    }
    else
    {
        if( eZMail::validate( $formReceiver ) == false )
        {
            $errorMessages[] = "form_receiver_not_valid";
        }
    }

    if( empty( $formCompletedPage ) )
    {
        $errorMessages[] = "form_completed_page_not_set";
    }

    if( empty( $formCC ) == false )
    {
        if( eZMail::validate( $formCC ) == false )
        {
            $errorMessages[] = "form_cc_not_valid";
        }
    }

    if( count( $errorMessages ) == 0 )
    {
        $form->setName( $formName );
        $form->setReceiver( $formReceiver );
        $form->setCC( $formCC );
        $form->setCompletedPage( $formCompletedPage );
        $form->setInstructionPage( $formInstructionPage );
        $form->setSender( $formSender );
        
        if( isset( $formSendAsUser ) )
        {
            $form->setSendAsUser( true );
        }
        else
        {
            $form->setSendAsUser( false );
        }
        
        $form->store();
        $FormID = $form->id();
        
        $existingElementCount = $form->numberOfElements();
        $existingElementCount++;
        
        if( isset( $NewElement ) )
        {
            $newElementName =& $ini->read_var( "eZFormMain", "DefaultElementName" );
            $newElementName = $newElementName . " " . $existingElementCount;
            $element = new eZFormElement();
            $element->setName( $newElementName );
            $element->store();
        }
        
        if( isset( $element ) )
        {
            $form->addElement( $element );
        }

        $elementCount = count( $elementID );
        $elementTypeError = false;
        
        for( $i = 0; $i < $elementCount; $i++ )
        {
            $element = new eZFormElement( $elementID[$i] );
            $elementType = new eZFormElementType( $elementTypeID[$i] );
            $element->setElementType( $elementType );

            if( $elementType->id() == 0 && $elementTypeError == false )
            {
                $errorMessages[] = "all_elements_must_have_type";
                $elementTypeError = true;
            }

            $element->setName( $elementName[$i] );

            $required = false;

            if( count( $elementRequired ) > 0 )
            {
                foreach( $elementRequired as $requiredID )
                {
                    if( $elementID[$i] == $requiredID )
                    {
                        $element->setRequired( true );
                        $required = true;
                    }
                }
            }
            $element->setRequired( $required );

            $element->store();
        }

        if( isset( $OK ) && count( $errorMessages ) == 0 )
        {
            eZHTTPTool::header( "Location: /form/form/list/" );
            exit();
        }

        if( isset( $Preview ) && count( $errorMessages ) == 0 )
        {
            eZHTTPTool::header( "Location: /form/form/preview/$FormID/" );
            exit();
        }
    }
}

$Language = $ini->read_var( "eZFormMain", "Language" );

$t = new eZTemplate( "ezform/admin/" . $ini->read_var( "eZFormMain", "AdminTemplateDir" ),
                     "ezform/admin/intl/", $Language, "form.php" );

$t->set_file( array(
    "form_edit_page_tpl" => "formedit.tpl"
    ) );

$t->set_block( "form_edit_page_tpl", "form_item_tpl", "form_item" );
$t->set_block( "form_edit_page_tpl", "error_list_tpl", "error_list" );
$t->set_block( "error_list_tpl", "error_item_tpl", "error_item" );
$t->set_block( "form_edit_page_tpl", "no_types_item_tpl", "no_types_item" );
$t->set_block( "form_edit_page_tpl", "no_elements_item_tpl", "no_elements_item" );
$t->set_block( "form_edit_page_tpl", "element_list_tpl", "element_list" );
$t->set_block( "element_list_tpl", "element_item_tpl", "element_item" );
$t->set_block( "element_item_tpl", "typelist_item_tpl", "typelist_item" );

$move_item = true;
$t->set_block( "element_item_tpl", "item_move_up_tpl", "item_move_up" );
$t->set_block( "element_item_tpl", "item_separator_tpl", "item_separator" );
$t->set_block( "element_item_tpl", "item_move_down_tpl", "item_move_down" );
$t->set_block( "element_item_tpl", "no_item_move_up_tpl", "no_item_move_up" );
$t->set_block( "element_item_tpl", "no_item_separator_tpl", "no_item_separator" );
$t->set_block( "element_item_tpl", "no_item_move_down_tpl", "no_item_move_down" );

$t->set_var( "no_types_item", "" );
$t->set_var( "no_elements_item", "" );
$t->set_var( "element_list", "" );
$t->set_var( "element_item", "" );
$t->set_var( "typelist_item", "" );
$t->set_var( "error_list", "" );
$t->set_var( "checked", "" );

if( $form->completedPage() )
{
    $t->set_var( "form_completed_page", $form->completedPage() );
}
else
{
    if( $ini->read_var( "eZFormMain", "UseDefaultRedirectPage" ) == "enabled" )
    {
        $t->set_var( "form_completed_page", $ini->read_var( "eZFormMain", "DefaultRedirectPage" ) );
    }
}

if( $form->instructionPage() )
{
    $t->set_var( "form_instruction_page", $form->instructionPage() );
}
else
{
    if( $ini->read_var( "eZFormMain", "UseDefaultInstructionPage" ) == "enabled" )
    {
        $t->set_var( "form_instruction_page", $ini->read_var( "eZFormMain", "DefaultInstructionPage" ) );
    }
}

if( $form->numberOfTypes() == 0 )
{
     $t->parse( "no_types_item", "no_types_item_tpl" );
}

if( $form->numberOfElements() == 0 )
{
    if( $ini->read_var( "eZFormMain", "CreateEmailDefaults" ) == "enabled" )
    {
        $form->store();
        $FormID = $form->id();
        $elementTypeA = new eZFormElementType( 1 );
        $elementTypeB = new eZFormElementType( 2 );
        $elementA = new eZFormElement();
        $elementB = new eZFormElement();
        $name = $t->Ini->read_var( "strings", "subject_label" );
        $elementA->setName( $name );
        $name = $t->Ini->read_var( "strings", "content_label" );
        $elementB->setName( $name );
        $elementA->setElementType( $elementTypeA );
        $elementB->setElementType( $elementTypeB );
        $elementA->setRequired( true );
        $elementB->setRequired( true );
        $elementA->store();
        $elementB->store();
        $form->addElement( $elementA );
        $form->addElement( $elementB );
    }
    else
    {
        $t->parse( "no_elements_item", "no_elements_item_tpl" );
    }
}

$t->set_var( "form_id", $FormID );
$t->set_var( "form_name", $form->name() );
$t->set_var( "form_receiver", $form->receiver() );
$t->set_var( "form_cc", $form->cc() );
$t->set_var( "form_sender", $form->sender() );

if( $form->isSendAsUser() )
{
    $t->set_var( "form_send_as_user", "1" );
    $t->set_var( "checked", "checked" );
}
else
{
    $t->set_var( "form_send_as_user", "0" );
}

$elements = $form->formElements();

$count = $form->numberOfElements();

if( $count > 0 )
{
    $i = 0;
    foreach( $elements as $element )
    {
        if ( ( $i % 2 ) == 0 )
        {
            $t->set_var( "td_class", "bglight" );
        }
        else
        {
            $t->set_var( "td_class", "bgdark" );
        }
        $t->set_var( "element_name", $element->name() );
        $t->set_var( "element_id", $element->id() );

        if( $element->isRequired() )
        {
            $t->set_var( "element_required", "checked" );
        }
        else
        {
            $t->set_var( "element_required", "" );
        }

        $currentType = $element->elementType();
        $types = $currentType->getAll();
        
        $t->set_var( "typelist_item", "" );
        foreach( $types as $type )
        {
            $t->set_var( "selected", "" );

            if( $type->id() == $currentType->id() )
            {
                $t->set_var( "selected", "selected" );
            }
            
            $t->set_var( "element_type_id", $type->id() );
            $t->set_var( "element_type_name", $type->name() );
            $t->parse( "typelist_item", "typelist_item_tpl", true );
        }
        
        $t->set_var( "item_move_up", "" );
        $t->set_var( "no_item_move_up", "" );
        $t->set_var( "item_move_down", "" );
        $t->set_var( "no_item_move_down", "" );
        $t->set_var( "item_separator", "" );
        $t->set_var( "no_item_separator", "" );

        if ( isset( $move_item ) )
        {
            $t->parse( "item_move_up", "item_move_up_tpl" );
        }
        
        if ( isset( $move_item ) )
        {
            $t->parse( "item_separator", "item_separator_tpl" );
        }
        
        if ( isset( $move_item ) )
        {
            $t->parse( "item_move_down", "item_move_down_tpl" );
        }

        $t->parse( "element_item", "element_item_tpl", true );
        $i++;
    }
    
    $t->parse( "element_list", "element_list_tpl" );
}

if( count( $errorMessages ) > 0 )
{
    foreach( $errorMessages as $errorMessage )
    {
        $errorMessage =& $t->Ini->read_var( "strings", $errorMessage );
        $t->set_var( "error_message", $errorMessage );
        $t->parse( "error_item", "error_item_tpl", true );
    }
    
    $t->set_var( "form_name", $formName );
    $t->set_var( "form_receiver", $formReceiver );
    $t->set_var( "form_cc", $formCC );
    $t->set_var( "form_completed_page", $formCompletedPage );
    $t->set_var( "form_sender", $formSender );
    
    if( isset( $formSendAsUser ) )
    {
        $t->set_var( "checked", "checked" );
    }
    $t->set_var( "form_instruction_page", $formInstructionPage );

    $t->parse( "error_list", "error_list_tpl" );
}

$t->parse( "form_item", "form_item_tpl" );

$t->set_var( "action_value", $ActionValue );
$t->set_var( "site_style", $SiteStyle );
$t->setAllStrings();
$t->pparse( "output", "form_edit_page_tpl" );

?>
