<?php
// 
// $Id: index.php,v 1.87 2001/07/20 12:04:13 jakobn Exp $
//
// Created on: <09-Nov-2000 14:52:40 ce>
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

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");

include_once( "classes/ezbenchmark.php" );

// Run benchmark test.
$bench = new eZBenchmark();
$bench->start();
  
$GLOBALS["DEBUG"] = true;
$UsePHPSessions = false;

ob_start();
// Turn on output buffering with gz compression
//ob_start("ob_gzhandler");

if ( $UsePHPSessions == true )
{
    // start session handling
    session_start();
}

// settings for sessions
// max timeout is set to 48 hours
ini_alter("session.gc_maxlifetime", "172800");
ini_alter("session.entropy_file","/dev/urandom"); 
ini_alter("session.entropy_length", "512");

include_once( "classes/INIFile.php" );
include_once( "classes/ezdb.php" );
include_once( "classes/ezhttptool.php" );
$ini =& INIFile::globalINI();
$GlobalSiteIni =& $ini;


// set character set
include_once( "classes/ezlocale.php" );
$Language = $ini->read_var( "eZCalendarMain", "Language" );
$Locale = new eZLocale( $Language );
$iso =& $Locale->languageISO();
if ( $iso != false )
    header( "Content-type: text/html;charset=$iso" );

// Design
include_once( "ezsession/classes/ezsession.php" );
include_once( "ezuser/classes/ezuser.php" );


$session =& eZSession::globalSession();

//
unset( $siteDesign );
unset( $GlobalSiteDesign );
if ( $session->fetch() == false )
{
    $siteDesign =& $ini->read_var( "site", "SiteDesign" );
}
else
{
    $siteDesign =& $session->variable( "SiteDesign" );

    if ( $siteDesign == "" )
    {
        $siteDesign =& $ini->read_var( "site", "SiteDesign" );
    }
}

// Store the site design in a global variable
$GlobalSiteDesign = $siteDesign;


$StoreStats = $ini->read_var( "eZStatsMain", "StoreStats" );

if ( $StoreStats == "enabled" )
{
    // do the statistics
    include_once( "ezstats/classes/ezpageview.php" );

    // create a global page view object for statistics
    // and store the stats
//    $GlobalPageView = new eZPageView();
//    $GlobalPageView->store();
}

// parse the URI
$meta_page = "";
$content_page = "";

// Check if userlogin is required
$user = eZUser::currentUser();

$requireUserLogin =& $ini->read_var( "eZUserMain", "RequireUserLogin" );

// Cookie auto login.
if ( $HTTP_COOKIE_VARS["eZUser_AutoCookieLogin"] != false )
{
    if ( ( !$user ) && ( $ini->read_var( "eZUserMain", "AutoCookieLogin" ) == "enabled" ) )
    {
        eZUser::autoCookieLogin( $HTTP_COOKIE_VARS["eZUser_AutoCookieLogin"] );
    }
}

// Remove url parameters
ereg( "([^?]+)", $REQUEST_URI, $regs );
$REQUEST_URI = $regs[1];

$url_array = explode( "/", $REQUEST_URI );

if ( ( $requireUserLogin == "disabled" ) ||
    ( ( $requireUserLogin == "enabled" )   & ( get_class( $user ) == "ezuser" ) && ( $user->id() != 0 ) ) ) 
{

    // do url translation if needed
    $URLTranslationKeyword = $ini->read_var( "site", "URLTranslationKeyword" );

    if ( $URLTranslationKeyword == $url_array[1] )
    {
        include_once( "ezurltranslator/classes/ezurltranslator.php" );
        $REQUEST_URI = eZURLTranslator::translate( $REQUEST_URI );
        $url_array = explode( "/", $REQUEST_URI );
    }

    // if uri == / show default page or article list
    if ( $REQUEST_URI == "/" )
    {
        if ( $ini->read_var( "site", "DefaultPage" ) == "disabled" )
        {
            $REQUEST_URI = "/article/archive/0/";
            $url_array = explode( "/", $REQUEST_URI );
        }
    }
    
    // Load the main contents and store in a variable
    $content_page = "ez" . $url_array[1] . "/user/datasupplier.php";

    // site cache check
    $SiteCacheFile = "classes/cache/" . md5( $REQUEST_URI ) . ".php";
    $SiteCache = $ini->read_var( "site", "SiteCache" );

    if ( $REQUEST_METHOD == "POST" ||
    $url_array[1] == "forum" ||
    $url_array[1] == "user" ||
    $url_array[1] == "error" ||
    $url_array[1] == "poll" 
         )
    {
        $SiteCache = "disabled";
    }

         
    // check to use site cache
    if ( ( $SiteCache == "enabled" ) and !file_exists( $SiteCacheFile ) )
    {
        $StoreSiteCache = true;
    }
    else
    {
        $StoreSiteCache = false;

        if ( $SiteCache == "enabled" and file_exists( $SiteCacheFile ) )
        {
            $timeout = $ini->read_var( "site", "SiteCacheTimeout" );
            $SiteCacheTime = filemtime( $SiteCacheFile );
            if ( ( time() - $SiteCacheTime ) < ( $timeout*60 ) )
            {
             // print( "valid cache" );
            }
            else
            {
                $StoreSiteCache = true;

                // delete cache file
                unlink( $SiteCacheFile );
                //  print( "time out-clearing cache" );
            }
        }        
    }
        
    if ( $StoreSiteCache || $SiteCache == "disabled" )
    {
        $buffer =& ob_get_contents();
        ob_end_clean();
        ob_start();
        
        // fetch the module printout
        if ( file_exists( $content_page ) )
        {
            // the page with the real contents
            include( $content_page );

            // The following variables can be set from the contents page:
            // $PrintableVersion = "enabled | disabled";
            // $GlobalSectionID = integer value, reference to the selected section.
            // $SiteTitleAppend = string which will be appended to the site title
            // $SiteDescriptionOverride = string which will override the meta content information
        }
        else
        {
            // the default page to load
            if ( $ini->read_var( "site", "DefaultPage" ) != "disabled" )
            {
                include( $ini->read_var( "site", "DefaultPage" ) );
            }
        }
    
        $MainContents =& ob_get_contents();
        ob_end_clean();
    
        // fill the buffer with the old values
        ob_start();
        print( $buffer );

        // set the sitedesign from the section
        if ( $ini->read_var( "site", "Sections" ) == "enabled" )
        {
            if ( !is_numeric( $GlobalSectionID ) )
            {
                $GlobalSectionID = $ini->read_var( "site", "DefaultSection" );
            }
                 
            include_once( "ezsitemanager/classes/ezsection.php" );

            if ( $DEBUG == true )
            {
                print( eZSection::siteDesign( $GlobalSectionID ) );
            }
        
            $siteDesign = eZSection::siteDesign( $GlobalSectionID );
            $GlobalSiteDesign = $siteDesign;
        }
        
        // include some html
        $Title = $ini->read_var( "site", "SiteTitle" );
        include( "sitedesign/$siteDesign/preamble.php" );
        

        // include more html
        if ( $PrintableVersion == "enabled" )
        {
            include( "sitedesign/$siteDesign/simpleheader.php" );
        }
        else
        {
            include( "sitedesign/$siteDesign/header.php" );    
        }

        // Main contents
        print( $MainContents );
    
        // and the html finish
        // include more html
        if ( $PrintableVersion == "enabled" )
        {
            include( "sitedesign/$siteDesign/simplefooter.php" );
        }
        else
        {
            include( "sitedesign/$siteDesign/footer.php" );
        }

        // store site cache
        if ( $StoreSiteCache == true )
        {
            $fp = fopen ( $SiteCacheFile, "w+");

            $SiteContents =& ob_get_contents();
            fwrite ( $fp, $SiteContents );
            fclose( $fp );
        }
        
    }
    else
    {
        // load site cache
        include( $SiteCacheFile );
    }
}
else
{
    include( "sitedesign/$siteDesign/preamble.php" );
    include( "sitedesign/$siteDesign/loginheader.php" );

    // parse the URI
    $page = "";


    // send the URI to the right decoder
    $page = "ezuser/user/datasupplier.php";
    if ( file_exists( $page ) )
    {
        include( $page );
    }
    include( "sitedesign/$siteDesign/loginfooter.php" );
}

// close the database connection.
$db =& eZDB::globalDatabase();
$db->close();
print( $db->isA() );

// Stop benchmark test and print the result.
$bench->stop();
$bench->printResults();

ob_end_flush();

?>
