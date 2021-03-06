<?

//!! eZContact
//!
/*!
  Denne klassen h�ndterer link mellon companyer og telefonnummer.
  Dette slik at en company kan ha flere telefonnummer uten at dette
  har konflikt med firma som er registrert.
*/

class eZCompanyPhoneDict
{
    /*
      Constructor.
     */
    function eZCompanyPhoneDict( )
    {

    }
    
    /*
      Lagrer en company->telefonnummer link i databasen.      
    */
    function store()
    {
        $this->dbInit();
        
        query( "INSERT INTO eZContact_CompanyPhoneDict set CompanyID='$this->CompanyID',	PhoneID='$this->PhoneID' " );
        return mysql_insert_id();
    }

    /*
      Henter ut med ID == $id
    */  
    function getByPhone( $id )
    {
        $this->dbInit();    
        if ( $id != "" )
        {
            array_query( $dict_array, "SELECT * FROM eZContact_CompanyPhoneDict WHERE PhoneID='$id'" );
            if ( count( $dict_array ) > 1 )
            {
                die( "Feil: Flere dicter med samme ID funnet i database, dette skal ikke v�re mulig. " );
            }
            else if ( count( $dict_array ) == 1 )
            {
                $this->ID = $dict_array[ 0 ][ "ID" ];
                $this->CompanyID = $dict_array[ 0 ][ "CompanyID" ];
                $this->PhoneID = $dict_array[ 0 ][ "PhoneID" ];
            }
        }
    }

    /*
      Sletter dicten med ID == $id;
     */
    function delete()
    {
        $this->dbInit();
        
        query( "DELETE FROM eZContact_CompanyPhoneDict WHERE ID='$this->ID'" );
    }

    /*
      Henter ut alle telefonnummer lagret i databasen hvor CompanyID == $id.
    */
    function getByCompany( $id )
    {
        $this->dbInit();    
        $phone_array = 0;
    
        array_query( $phone_array, "SELECT * FROM eZContact_CompanyPhoneDict WHERE CompanyID='$id'" );
    
        return $phone_array;
    }
    

    /*
      Setter companyID variablen.
    */
    function setCompanyID( $value )
    {
        $this->CompanyID = $value;
    }

    /*
      Setter phoneID variablen.
    */
    function setPhoneID( $value )
    {
        $this->PhoneID = $value;
    }
    
    /*
      Returnerer companyID'en.
    */
    function companyID()
    {
        return $this->CompanyID;
    }

    /*
      Returnerer phoneID'en.
    */
    function phoneID()
    {
        return $this->PhoneID;
    }
    
    /*
      Privat: Initiering av database. 
    */
    function dbInit()
    {
        include_once( "classes/INIFile.php" );

        $ini = new INIFile( "site.ini" );
        
        $SERVER = $ini->read_var( "site", "Server" );
        $DATABASE = $ini->read_var( "site", "Database" );
        $USER = $ini->read_var( "site", "User" );
        $PWD = $ini->read_var( "site", "Password" );
        
        mysql_pconnect( $SERVER, $USER, $PWD ) or die( "Kunne ikke kople til database" );
        mysql_select_db( $DATABASE ) or die( "Kunne ikke velge database" );
    }

    var $ID;
    var $CompanyID;
    var $PhoneID;
}

?>
