<?php
// 
// $Id: authorlist.php,v 1.7.2.2 2002/05/23 09:12:28 jhe Exp $
//
// Created on: <16-Feb-2001 14:54:04 amos>
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

include_once( "ezrfp/classes/ezrfp.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZRfpMain", "Language" );
$Limit = $ini->read_var( "eZRfpMain", "AuthorLimit" );

$t = new eZTemplate( "ezrfp/user/" . $ini->read_var( "eZRfpMain", "TemplateDir" ),
                     "ezrfp/user/intl/", $Language, "authorlist.php" );

$t->setAllStrings();

$t->set_file( "author_list_tpl", "authorlist.tpl" );
$t->set_block( "author_list_tpl", "author_item_tpl", "author_item" );
$t->set_block( "author_item_tpl", "company_item_tpl", "company_item" );


if ( !isset( $Offset ) )
    $Offset = 0;
if ( !isset( $Limit ) or !is_numeric( $Limit ) )
    $Limit = 5;
if ( !isset( $SortOrder ) )
    $SortOrder = "name";

// $SortOrder = "name";

$authors =& eZRfp::authorList( $Offset, $Limit, $SortOrder );

$db =& eZDB::globalDatabase();

$t->set_var( "author_item", "" );
$i = 0;

/*
 $testArr = array ( 0 => '1', 1 => '3', 2 => '18');
 $rfpCountArr = eZRfp::getFullAuthorCount( $testArr);
*/

foreach ( $authors as $author )
{
  $rfpCount = eZRfp::authorRfpCount( $author->ID );

 if ( $rfpCount > 0 )
    {
        $author_name = $author->firstName() .' '. $author->lastName();
	$t->set_var( "author_id", $author->id() );
	$t->set_var( "author_name", $author_name );
	// $planholder = new eZPerson( $author->id() );
	$Writer_Companies = $author->companies();

	if(count($Writer_Companies) ) {
	  $Writer_Companies = $Writer_Companies[0];
	  $Writer_CompanyName = $Writer_Companies->name();
	  $Writer_CompanyID = $Writer_Companies->id();

	  $t->set_var( "planholder_company_name", $Writer_CompanyName );
	  $t->set_var( "planholder_company_id", $Writer_CompanyID );
	  $t->parse( "company_item", "company_item_tpl");
	} else {
	  $Writer_CompanyName = 'No Affiliation';
	  $Writer_CompanyID = '0';
	  $t->set_var( "planholder_company_name", $Writer_CompanyName );
	  $t->set_var( "planholder_company_id", $Writer_CompanyID );
	  // $t->parse( "company_item", "company_item_tpl");
	  $t->set_var( "company_item", "");
	}
	/*
	$t->set_var( "planholder_company_id", $planholder_company_id );
	$t->set_var( "planholder_company_name", $planholder_company_name ); 
	*/
	
        $t->set_var( "rfp_count", $rfpCount );
        $t->set_var( "td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );
        $t->parse( "author_item", "author_item_tpl", true );
        $i++;
    }
}

$t->pparse( "output", "author_list_tpl" );

?>
