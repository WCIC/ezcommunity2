<?

class eZNote
{
    /*
      Constructor.
     */
    function eZNote( )
    {
    }

    /*
      Lagrer en notat i databasen.
     */
    function store( )
    {
        $this->dbInit();
        query( "INSERT INTO Note set Title='$this->Title',
		Body='$this->Body',
		UserID='$this->UserID' " );
        return mysql_insert_id();
    }

    /*
      Sletter adressetypen fra databasen.
     */
    function delete()
    {
        $this->dbInit();
        query( "DELETE FROM Note WHERE ID='$this->ID'" );
    }

    
    /*!
      Oppdaterer tabellen.
    */
    function update()
    {
        $this->dbInit();
        query( "UPDATE Note set Title='$this->Title' Body='$this->Body' WHERE ID='$this->ID'" );
    }


    
    /*
      Henter ut Notat med ID == $id
    */  
    function get( $id )
    {
        $this->dbInit();    
        if ( $id != "" )
        {
            array_query( $note_array, "SELECT * FROM Note WHERE ID='$id'" );
            if ( count( $note_array ) > 1 )
            {
                die( "Feil: Flere notater med samme ID funnet i database, dette skal ikke v�re mulig. " );
            }
            else if ( count( $note_array ) == 1 )
            {
                $this->ID = $note_array[ 0 ][ "ID" ];
                $this->Title = $note_array[ 0 ][ "Title" ];
                $this->Body = $note_array[ 0 ][ "Body" ];
                $this->UserID = $note_array[ 0 ][ "UserID" ];
            }
        }
    }


    /*
      Henter ut alle notatene lagret i databasen med UserID == $id.
    */
    function getAllByUser( $id )
    {
        $this->dbInit();    
        $note_array = 0;
    
        array_query( $note_array, "SELECT * FROM Note WHERE UserID='$id' ORDER BY Title" );
    
        return $note_array;
    }    

    function setTitle( $value )
    {
        $this->Title = $value;
    }

    function setBody( $value )
    {
        $this->Body = $value;
    }

    function setUserID( $value )
    {
        $this->UserID = $value;
    }

    function title( )
    {
        return $this->Title;
    }

    function body( )
    {
        return $this->Body;
    }

    function userID(  )
    {
        return $this->UserID;
    }
    

    /*
      Privat: Initiering av database. 
    */
    function dbInit()
    {
        require "ezcontact/dbsettings.php";
        mysql_pconnect( $SERVER, $USER, $PWD ) or die( "Kunne ikke kople til database" );
        mysql_select_db( $DATABASE ) or die( "Kunne ikke velge database" );
    }    

    var $ID;
    var $Title;
    var $Body;
    var $UserID;
}

?>
