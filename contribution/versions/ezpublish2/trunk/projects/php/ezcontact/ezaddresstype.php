<?

class eZAddressType
{
  /*!
    Constructor.
  */
  function eZAddressType()
  {

  }

  /*!
    Lagrer en addressetyperow til databasen.

  */
  function store()
  {
    $this->dbInit();
    query( "INSERT INTO AddressType set Name='$this->Name'" );
  }
  
  /*
    Henter ut en adressetype med ID == $id
  */  
  function get( $id )
  {
    $this->dbInit();    
    if ( $id != "" )
    {
      array_query( $address_type_array, "SELECT * FROM AddressType WHERE ID='$id'" );
      if ( count( $address_type_array ) > 1 )
      {
          die( "Feil: Flere addresstype med samme ID funnet i database, dette skal ikke v�re mulig. " );
      }
      else if ( count( $address_type_array ) == 1 )
      {
        $this->ID = $address_type_array[ 0 ][ "ID" ];
        $this->Name = $address_type_array[ 0 ][ "Name" ];
      }
    }
  }

  /*
    Henter ut alle adresstypene lagret i databasen.
  */
  function getAll( )
  {
    $this->dbInit();    
    $address_type_array = 0;
    
    array_query( $address_type_array, "SELECT * FROM AddressType" );
    
    return $address_type_array;
  }
  

  /*!
    Setter navnet.
  */
  function setName( $value )
  {
    $this->Name = $value;
  }

  /*!
    Returnerer navnet.
  */
  function name(  )
  {
    return $this->Name;
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
  var $Name;
}

?>
