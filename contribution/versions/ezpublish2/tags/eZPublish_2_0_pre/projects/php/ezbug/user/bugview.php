<?
// 
// $Id: bugview.php,v 1.7 2001/02/23 13:42:01 fh Exp $
//
// B�rd Farstad <bf@ez.no>
// Created on: <04-Dec-2000 11:44:31 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2000 eZ systems as
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
include_once( "classes/ezlog.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/eztexttool.php" );

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZBugMain", "Language" );

include_once( "ezbug/classes/ezbug.php" );
include_once( "ezbug/classes/ezbugcategory.php" );
include_once( "ezbug/classes/ezbugmodule.php" );
include_once( "ezbug/classes/ezbugpriority.php" );
include_once( "ezbug/classes/ezbugstatus.php" );
include_once( "ezbug/classes/ezbuglog.php" );

$t = new eZTemplate( "ezbug/user/" . $ini->read_var( "eZBugMain", "TemplateDir" ),
                     "ezbug/user/intl", $Language, "bugview.php" );
$t->setAllStrings();

$t->set_file( array(
    "bug_edit_tpl" => "bugview.tpl"
    ) );

// path
$t->set_block( "bug_edit_tpl", "path_item_tpl", "path_item" );

$t->set_block( "bug_edit_tpl", "log_item_tpl", "log_item" );
$t->set_block( "bug_edit_tpl", "yes_tpl", "yes" );
$t->set_block( "bug_edit_tpl", "no_tpl", "no" );

$t->set_block( "bug_edit_tpl", "screenshots_tpl", "screenshots" );
$t->set_block( "screenshots_tpl", "screenshot_item_tpl", "screenshot_item" );
$t->set_block( "bug_edit_tpl", "patches_tpl", "patches" );
$t->set_block( "patches_tpl", "patch_item_tpl", "patch_item" );

$locale = new eZLocale( $Language );
$bug = new eZBug( $BugID );

// path
$module = $bug->module();
$pathArray = $module->path();

$t->set_var( "path_item", "" );
foreach ( $pathArray as $path )
{
    $t->set_var( "module_id", $path[0] );

    $t->set_var( "module_name", $path[1] );
    
    $t->parse( "path_item", "path_item_tpl", true );
}


$t->set_var( "bug_id", $bug->id() );

$tmp = $bug->name();
$t->set_var( "name_value", htmlspecialchars( $tmp ) );

$bug_reporter = $bug->user();
if( $bug_reporter )
{
    $tmp = $bug_reporter->name();
    $t->set_var( "reporter_name_value", htmlspecialchars( $tmp ) );
}
else
{
    $t->set_var( "reporter_name_value", "Unknown" );
}

$tmp =  eZTextTool::nl2br( $bug->description() );
$t->set_var( "description_value", htmlspecialchars( $tmp ) );
$t->set_var( "action_value", "Update" );

$date =& $bug->created();
$t->set_var( "bug_date", $locale->format( $date ) );    

$pri =& $bug->priority();
$status =& $bug->status();
$module =& $bug->module();
$category =& $bug->category();

if ( $pri )
{    
    $t->set_var( "priority_name", $pri->name() );
}
else
{
    $t->set_var( "priority_name", "" );
}

if ( $status )
{
    $tmp = $status->name();
    $t->set_var( "status_name", htmlspecialchars( $tmp ) );
}
else
{
    $t->set_var( "status_name", "" );
}

if ( $module )
{
    $tmp = $module->name();
    $t->set_var( "module_name", htmlspecialchars( $tmp ) );
}
else
{
    $t->set_var( "module_name", "" );
}

if ( $category )
{
    $tmp = $category->name();
    $t->set_var( "category_name", htmlspecialchars( $tmp ) );
}
else
{
    $t->set_var( "category_name", "" );
}

if ( $bug->isClosed() == true )
{
    $t->parse( "yes", "yes_tpl" );
    $t->set_var( "no", "" );
}
else
{
    $t->parse( "no", "no_tpl" );
    $t->set_var( "yes", "" );
}

/* screenshot */
$images = $bug->images();
if( count( $images ) > 0 )
{
    $i = 0;
    foreach( $images as $image )
    {
        if ( ( $i % 2 ) == 0 )
        {
            $t->set_var( "td_class", "bglight" );
        }
        else
        {
            $t->set_var( "td_class", "bgdark" );
        }
        $t->set_var( "image_number", $i + 1 );
        $t->set_var( "image_id", $image->id() );

        $tmp = $image->caption();
        $t->set_var( "image_name", "<a href=\"/imagecatalogue/imageview/" . $image->id()  . "\">" . htmlspecialchars( $tmp ) . "</a>" );
        $t->parse( "screenshot_item", "screenshot_item_tpl", true );
    
        $i++;
    }
    $t->parse( "screenshots", "screenshots_tpl", true );

}
else
{
    $t->set_var( "screenshots", "" );
}

/* Pathes */
    $files = $bug->files();
if( count( $files ) > 0 )
{
    $i = 0;
    foreach( $files as $file )
    {
        if ( ( $i % 2 ) == 0 )
        {
            $t->set_var( "td_class", "bglight" );
        }
        else
        {
            $t->set_var( "td_class", "bgdark" );
        }

        $t->set_var( "file_number", $i + 1 );
//        $t->set_var( "file_id", $file->id() );
        $tmp = $file->name();
        $t->set_var( "file_name", "<a href=\"/filemanager/download/" . $file->id() . "/" . $file->originalFileName() . "\">" . htmlspecialchars( $tmp ) . "</a>" );
    
        $t->parse( "patch_item", "patch_item_tpl", true );
    
        $i++;
    }
    $t->parse( "patches", "patches_tpl", true );
}
else
{
    $t->set_var( "patches", "" );
}


$bugLog = new eZBugLog();
$logList = $bugLog->getByBug( $bug );


foreach ( $logList as $log )
{
    $date =& $log->created();
    
    $t->set_var( "log_date", $locale->format( $date ) );
    
    $tmp = $log->description();
    $t->set_var( "log_description", htmlspecialchars( $tmp ) );
    
    $t->parse( "log_item", "log_item_tpl", true );
}

if ( count( $logList  ) == 0 )
{
    $t->set_var( "log_item", "" );
}

$t->pparse( "output", "bug_edit_tpl" );

?>

