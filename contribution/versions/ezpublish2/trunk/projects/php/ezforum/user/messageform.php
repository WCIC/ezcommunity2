<?
// 
// $Id: messageform.php,v 1.2 2001/02/26 09:41:34 pkej Exp $
//
// Paul K Egell-Johnsen <pkej@ez.no>
// Created on: <21-Feb-2001 18:00:00 pkej>
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

if( $ShowMessageForm )
{
    if( $ShowVisibleMessageForm == true )
    {
        $t->set_file( "form", "messageform.tpl"  );
        $t->set_block( "form", "message_body_info_tpl", "message_body_info_item" );
        $t->set_block( "form", "message_reply_info_tpl", "message_reply_info_item" );
        $t->set_var( "message_body_info_item", "" );
        $t->set_var( "message_reply_info_item", "" );
        
        $t->set_var( "headline", $t->Ini->read_var( "strings", $Action . "_headline" ) );
    }
    
    if( $ShowHiddenMessageForm == true )
    {
        $t->set_file( "hidden_form", "messagehiddenform.tpl"  );
    }

    if( $BodyInfo == true )
    {
        $t->parse( "message_body_info_item", "message_body_info_tpl" );
    }

    if( $ReplyInfo == true )
    {
        $t->parse( "message_reply_info_item", "message_reply_info_tpl" );
    }
    
    if( $ShowEmptyMessageForm == false )
    {
        if( !is_object( $msg ) )
        {
            $msg = new eZForumMessage( $MessageID );
        }
        
        if( isset( $NewMessageTopic ) )
        {
            $MessageTopic = $NewMessageTopic;
        }
        else
        {
            $MessageTopic = $msg->topic();
        }
        
        if( isset( $NewMessageBody ) )
        {
            $MessageBody = $NewMessageBody;
        }
        else
        {
            $MessageBody = $msg->body( false );
        }
        
        $MessageNotice = $msg->emailNotice();
        $ForumID = $msg->forumId();
        
        if( isset( $NewMessageAuthor ) )
        {
            $MessageAuthor = $NewMessageAuthor;
        }
        else
        {
            if( !is_object( $author ) )
            {
               $author = new eZUser ( $msg->userId() );
            }
        }
        
        
        if( isset( $NewMessagePostedAt ) )
        {
            $MessagePostedAt = $NewMessagePostedAt;
        }
        else
        {
            $MessagePostedAt = $locale->format( $msg->postingTime() );
        }
        
        if( isset( $NewMessageNotice ) )
        {
            $MessageNotice = $NewMessageNotice;
        }
        
    }
    else
    {
        if( isset( $NewMessageAuthor ) )
        {
            $MessageAuthor = $NewMessageAuthor;
        }
        else
        {
            if( !is_object( $author ) )
            {
                $author = eZUser::currentUser();
            }
        }

        if( isset( $NewMessagePostedAt ) )
        {
            $MessagePostedAt = $NewMessagePostedAt;
        }
        else
        {
            $MessagePostedAt = $locale->format( $msg->postingTime() );
        }
    }
    
    if( is_object( $author ) )
    {
        $MessageAuthor = $author->firstName() . " " . $author->lastName();
    }
    else
    {
        $MessageAuthor = $ini->read_var( "eZForumMain", "AnonymousPoster" );
    }
    
        switch( $MessageNotice )
        {
            case "on":
            case "y":
            case "checked":
            case 1:
            case true:
            {
                $MessageNoticeText = $t->Ini->read_var( "strings", "notice_yes" );
                $MessageNotice = "checked";
                $NewMessageNotice = "checked";
            }
            break;

            case "off":
            case "n":
            case "unchecked":
            case 0:
            case false:
            {
                $MessageNoticeText = $t->Ini->read_var( "strings", "notice_no" );
                $MessageNotice = "";
                $NewMessageNotice = "";
            }
            break;
        }
    $t->set_var( "message_topic", htmlspecialchars( $MessageTopic ) );
    $t->set_var( "new_message_topic", $MessageTopic );
    $t->set_var( "message_body", htmlspecialchars( $MessageBody ) );
    $t->set_var( "new_message_body", $MessageBody );
    $t->set_var( "message_posted_at", $MessagePostedAt );
    $t->set_var( "message_author", $MessageAuthor );
    $t->set_var( "message_id", $MessageID );
    $t->set_var( "message_notice_text", $MessageNoticeText );
    $t->set_var( "message_notice", $MessageNotice );
    $t->set_var( "new_message_notice", $NewMessageNotice );

    $t->set_var( "reply_to_id", $ReplyToID );
    $t->set_var( "preview_id", $PreviewID );
    $t->set_var( "original_id", $OriginalID );

    $t->set_var( "forum_id", $ForumID );

    $t->set_var( "redirect_url", $RedirectURL );      
    $t->set_var( "end_action", $EndAction );      
    $t->set_var( "start_action", $StartAction );      
    $t->set_var( "action_value", $ActionValue );
    $AllowedTags = $ini->read_var( "eZForumMain", "AllowedTags" );
    $t->set_var( "allowed_tags", htmlspecialchars( $AllowedTags ) );      

    if( $ShowHiddenMessageForm == true )
    {
        if( $doPrint == true )
        {
            $t->pparse( "message_hidden_form_file", "hidden_form" );
        }
        else
        {
            $t->parse( "message_hidden_form_file", "hidden_form" );
        }
    }
    
    if( $ShowVisibleMessageForm == true )
    {
        if( $doPrint == true )
        {
            $t->pparse( "message_form_file", "form" );
        }
        else
        {
            $t->parse( "message_form_file", "form" );
        }
    }
    
}
else
{
    $t->parse( "message_form_file", "" );
    $t->parse( "message_hidden_form_file", "" );
}

?>
