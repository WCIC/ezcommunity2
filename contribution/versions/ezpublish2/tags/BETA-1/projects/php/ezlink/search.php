<?
/*!
    $Id: search.php,v 1.12 2000/08/23 08:43:02 ce-cvs Exp $

    Author: B�rd Farstad <bf@ez.no>
    
    Created on: 
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/

/*!
  listlink.php viser alle kategorier
*/

include_once( "class.INIFile.php" );
$ini = new INIFile( "site.ini" );

$DOC_ROOT = $ini->read_var( "eZLinkMain", "DocumentRoot" );

include_once( "classes/eztemplate.php" );
include_once( "ezphputils.php" );

include_once( "ezlink/classes/ezlinkgroup.php" );
include_once( "ezlink/classes/ezlink.php"  );
include_once( "ezlink/classes/ezhit.php" );

$Language= "no_NO";

require $DOC_ROOT . "classes/ezquery.php";

// setter template filer
$t = new eZTemplate( $DOC_ROOT . "/" . $Ini->read_var( "eZLinkMain", "TemplateDir" ), $DOC_ROOT . "/intl", $Language, "search.php" );
$t->setAllStrings();

$t->set_file( array(
    "search_item" => "searchitemuser.tpl",
    "search_list" => "searchlistuser.tpl"
    ) );

$limit = "10";
$offset = $Offset;

$linkGroup = new eZLinkGroup();
$linkGroup->get ( $LGID );

$t->set_var( "printpath", $linkGroup->printPath( $LGID, $DOC_ROOT . "linklist.php" ) );


$link = new eZLink();

if ( $Action == "search" )
{
    $link_array = $link->getQuery( $QueryText, $limit, $offset );
    $thit_count = count( $link->getQuery( $QueryText ) );
    $tlink_message = "S�k resultater";
}

// Lister alle linker i kategori
if ( count( $link_array ) == 0 )
{
    $t->set_var( "link_list", "Ingen linker funnet" );
}
else
{
    
    for ( $i=0; $i<count( $link_array ); $i++ )
    {

                if ( ( $i % 2 ) == 0 )
        {
            $t->set_var( "bg_color", "#f0f0f0" );
        }
        else
        {
            $t->set_var( "bg_color", "#dcdcdc" );
        }  

        $t->set_var( "link_id", $link_array[ $i ][ "ID" ] );
        $t->set_var( "link_title", $link_array[ $i ][ "Title" ] );
        $t->set_var( "link_description", $link_array[ $i ][ "Description" ] );
        $t->set_var( "link_groupid", $link_array[ $i ][ "LinkGroup" ] );
        $t->set_var( "link_keywords", $link_array[ $i ][ "KeyWords" ] );
        $t->set_var( "link_created", $link_array[ $i ][ "Created" ] );
        $t->set_var( "link_modified", $link_array[ $i ][ "Modified" ] );
        $t->set_var( "link_accepted", $link_array[ $i ][ "Accepted" ] );
        $t->set_var( "link_url", $link_array[ $i ][ "Url" ] );

        $t->set_var( "link_next_offs", $offset + $limit );
        $t->set_var( "link_prev_offs", $offset - $limit );
        $t->set_var( "query_text", $QueryText );

        $LGID =  ( $link_array[ $i ][ "LinkGroup" ] );

        $t->set_var( "printpath", $linkGroup->printPath( $LGID, $DOC_ROOT . "linklist.php" ) );                       

        $hit = new eZHit();
        $hits = $hit->getLinkHits( $link_array[ $i ][ "ID" ] );

        $t->set_var( "link_hits", $hits );

        $tlink_message = "Linker";

        $t->set_var( "document_root", $DOC_ROOT );

        $t->parse( "link_list", "search_item", true );
    }
}

$t->set_var( "hit_count", $thit_count );
$t->set_var( "link_message", $tlink_message );
$t->set_var( "linkgroup_id", $LGID );
$t->set_var( "document_root", $DOC_ROOT );

$t->set_var( "printpath", $linkGroup->printPath( 0, $DOC_ROOT . "linklist.php" ) );                       

$t->pparse( "output", "search_list" );
?>
