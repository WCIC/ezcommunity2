<?
// 
// $Id: ezlocale.php,v 1.2 2000/09/08 12:13:53 bf-cvs Exp $
//
// Definition of eZCompany class
//
// B�rd Farstad <bf@ez.no>
// Created on: <07-Sep-2000 14:33:48 bf>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//


//!! eZCommon
//! The eZLocale class provides locale functions.
/*!
  eZLocale handles locale information and formats time, date, and currency
  information to the locale format.


  The following characters are regognized in the date/time format.
  \pre
a - "am" or "pm" 
A - "AM" or "PM" 
d - day of the month, 2 digits with leading zeros; i.e. "01" to "31" 
D - day of the week, textual, 3 letters; i.e. "Fri" 
F - month, textual, long; i.e. "January" 
g - hour, 12-hour format without leading zeros; i.e. "1" to "12" 
G - hour, 24-hour format without leading zeros; i.e. "0" to "23" 
h - hour, 12-hour format; i.e. "01" to "12" 
H - hour, 24-hour format; i.e. "00" to "23" 
i - minutes; i.e. "00" to "59" 
I (capital i) - "1" if Daylight Savings Time, "0" otherwise. 
j - day of the month without leading zeros; i.e. "1" to "31" 
l (lowercase 'L') - day of the week, textual, long; i.e. "Friday" 
L - boolean for whether it is a leap year; i.e. "0" or "1" 
m - month; i.e. "01" to "12" 
M - month, textual, 3 letters; i.e. "Jan" 
n - month without leading zeros; i.e. "1" to "12" 
s - seconds; i.e. "00" to "59" 
S - English ordinal suffix, textual, 2 characters; i.e. "th", "nd" 
t - number of days in the given month; i.e. "28" to "31" 
T - Timezone setting of this machine; i.e. "MDT" 
U - seconds since the epoch 
w - day of the week, numeric, i.e. "0" (Sunday) to "6" (Saturday) 
Y - year, 4 digits; i.e. "1999" 
y - year, 2 digits; i.e. "99" 
z - day of the year; i.e. "0" to "365" 
Z - timezone offset in seconds (i.e. "-43200" to "43200")

\sa eZDate eZDateTime eZTime eZCurrency eZNumber
*/

include_once( "classes/INIFile.php" );

class eZLocale
{
    /*!
      Constructs a new eZLocale object. If an ISO code is given as
      an argument the regional file for that language is used. Otherwise
      the default regional settings are used.
    */
    function eZLocale( $iso="" )
    {
        $ini = new INIFile( "site.ini", false );
        $SERVER_ROOT = $ini->read_var( "site", "ServerRoot" );
        
        if ( file_exists( $SERVER_ROOT . "/classes/locale/" . $iso . ".ini" ) )
        {
            $localeIni = new INIFile( $SERVER_ROOT . "/classes/locale/" . $iso . ".ini", false );
        }
        else
        {
            $localeIni = new INIFile( $SERVER_ROOT . "/classes/locale/en_GB.ini", false );
        }

        $this->CurrencySymbol = $localeIni->read_var( "RegionalSettings", "CurrencySymbol" );
        $this->DecimalSymbol = $localeIni->read_var( "RegionalSettings", "DecimalSymbol" );
        $this->ThousandsSymbol = $localeIni->read_var( "RegionalSettings", "ThousandsSymbol" );
        $this->FractDigits = $localeIni->read_var( "RegionalSettings", "FractDigits" );

        $this->TimeFormat = $localeIni->read_var( "RegionalSettings", "TimeFormat" );
        $this->DateFormat = $localeIni->read_var( "RegionalSettings", "DateFormat" );
        $this->ShortDateFormat = $localeIni->read_var( "RegionalSettings", "ShortDateFormat" );

    }

    /*!
      Returns a nicely formatted string. This function automatically finds
      the appropriate format to use based on locale information and the type
      of object passed as an argument.

      If isShort is set to false then the long version of the string is used,
      if it exists.
    */
    function format( $obj, $isShort=true )
    {
        $returnString = "<b>Locale error</b>: object or type not supported.";

        // TODO: implement more options for the date and time format.
        switch ( get_class( $obj ) )
        {
            case "ezdate" :
            {
                $date = $this->DateFormat;

                // d - day of the month, 2 digits with leading zeros; i.e. "01" to "31" 
                $date = ereg_replace( "\%d", "" . $obj->day() . "", $date );
                
                // m - month; i.e. "01" to "12" 
                $date = ereg_replace( "%m", "" . $obj->month(), $date );

                // Y - year, 4 digits; i.e. "1999"
                $date = ereg_replace( "%Y", "" . $obj->year(), $date );
                
                $returnString = $date;
                break;
            }
            case "eztime" :
            {
                $time = "" . $obj->value();
                // H - hour, 24-hour format; i.e. "00" to "23"

                $time = chunk_split( $time, 3, "a " );
                    
                
                // i - minutes; i.e. "00" to "59"
                

                $returnString = $time;
                break;
            }
            case "ezdatetime" :
            {
                
                break;
            }
            case "ezcurrency" :
            {
                $value = $obj->value();

                $valueArray = explode( ".", $value );
                $fracts = $valueArray[1] . "<br>";
                $integerValue = $valueArray[0];          

                $revInteger = strrev( $integerValue );
                
                $revInteger = ereg_replace( "([0-9]{3})", "\\1$this->ThousandsSymbol", $revInteger );

                $integerValue = strrev( $revInteger );

                $value = $integerValue . $this->DecimalSymbol . $fracts;
                $returnString = $value;
                break;
            }
        }
        return $returnString;
    }

    var $CurrencySymbol;
    var $DecimalSymbol;
    var $ThousandsSymbol;
    var $FractDigits;
    var $TimeFormat;
    var $DateFormat;
    var $ShortDateFormat;
}


?>
