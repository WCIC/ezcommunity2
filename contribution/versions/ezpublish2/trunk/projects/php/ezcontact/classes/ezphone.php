<?

class eZPhone
{
    /*

    */
    function eZPhone()
    {

    }

    /*
      Lagrer et telefonnummer link i databasen.      
    */
    function store()
    {
        $this->dbInit();
        
        query( "INSERT INTO eZContact_Phone set Number='$this->Number', Type='$this->Type' " );
        return mysql_insert_id();
    }

    /*
      Oppdaterer  
    */
    function update()
    {
        $this->dbInit();
        
        query( "UPDATE eZContact_Phone set Number='$this->Number', Type='$this->Type' WHERE ID='$this->ID' " );
    }

    /*
      Sletter.
    */
    function delete()
    {
        $this->dbInit();
        
        query( "DELETE FROM eZContact_Phone WHERE ID='$this->ID' " );
    }
    
    /*
      Henter ut telefonnummer med ID == $id
    */  
    function get( $id )
    {
        $this->dbInit();    
        if ( $id != "" )
        {
            array_query( $phone_array, "SELECT * FROM eZContact_Phone WHERE ID='$id'" );
            if ( count( $phone_array ) > 1 )
            {
                die( "Feil: Flere telefonnummer med samme ID funnet i database, dette skal ikke v�re mulig. " );
            }
            else if ( count( $phone_array ) == 1 )
            {
                $this->ID = $phone_array[ 0 ][ "ID" ];
                $this->Number = $phone_array[ 0 ][ "Number" ];
                $this->Type = $phone_array[ 0 ][ "Type" ];
            }
        }
    }    

    function setNumber( $value )
    {
        $this->Number = $value;
    }

    function setType( $value )
    {
        $this->Type = $value;
    }

    function setID( $value )
    {
        $this->ID = $value;
    }
    
    function number( )
    {
        return $this->Number;
    }

    function type( )
    {
        return $this->Type;
    }
    
    function id( )
    {
        return $this->ID;
    }
    
    /*!
      Privat funksjon, skal kun brukes av ezusergroup klassen.
      Funksjon for � �pne databasen.
    */
    function dbInit()
    {
        include_once( "class.INIFile.php" );

        $ini = new INIFile( "site.ini" );
        
        $SERVER = $ini->read_var( "site", "Server" );
        $DATABASE = $ini->read_var( "site", "Database" );
        $USER = $ini->read_var( "site", "User" );
        $PWD = $ini->read_var( "site", "Password" );
        
        mysql_pconnect( $SERVER, $USER, $PWD ) or die( "Kunne ikke kople til database" );
        mysql_select_db( $DATABASE ) or die( "Kunne ikke velge database" );
    }

    var $ID;
    var $Number;
    var $Type;
}

?>
