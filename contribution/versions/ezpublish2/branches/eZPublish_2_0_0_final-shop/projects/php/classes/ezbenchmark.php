<?php
// 
// $Id: ezbenchmark.php,v 1.1 2001/02/07 18:45:24 bf Exp $
//
// Definition of eZTextTool class
//
// B�rd Farstad <bf@ez.no>
// Created on: <23-Jan-2001 12:34:54 bf>
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
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

//!! eZCommon
//! Provied utility functions for http.
/*!
*/

class eZBenchmark
{
    /*!
      Creates a new eZBenchmark object.
    */
    function eZBenchmark( )
    {

    }

    /*!
      Starts a new benchmark.
    */
    function start()
    {
        $this->StartTime = microtime();
    }

    /*!
      Stops the benchmark interval.
    */
    function stop()
    {
        $this->StopTime = microtime();
    }

    /*!
      Prints the benchmark results.
    */
    function printResults()
    {
        $time_1 = explode( " ", $this->StartTime );
        $time_2 = explode( " ", $this->StopTime );

        ereg( "0\.([0-9]+)", "" . $time_1[0], $t1 );
        ereg( "0\.([0-9]+)", "" . $time_2[0], $t2 );
        
        $Start = $time_1[1] . "." . $t1[1];
        $Stop = $time_2[1] . "." . $t2[1];    
        
        $elapsed = $Stop - $Start;
        
        print( "Time elapsed: " . number_format( ( $elapsed ), 2 ) . " seconds.<br>" );
        
    }

    
    var $StartTime;
    var $StopTime;
}
