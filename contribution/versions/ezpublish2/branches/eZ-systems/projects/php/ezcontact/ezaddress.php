<?

class eZAddress
{
  /*!

  */
  function eZAddress( )
  {

  }

  /*
    Lagrer en ny adresserad i databasen. 
  */  
  function store()
  {
    $this->dbInit();
    query( "INSERT INTO Address set Street1='$this->Street1', Street2='$this->Street2', Zip='$this->Zip, AddressType=$this->AddressType'" );
  }

  /*
    Oppdaterer informasjonen som ligger i databasen.
  */  
  function update()
  {
    $this->dbInit();
  }
  
  /*
    Henter ut en adresse med ID == $id
  */  
  function get( $id )
  {
    $this->dbInit();    
    if ( $id != "" )
    {
      array_query( $address_array, "SELECT * FROM Address WHERE ID='$id'" );
      if ( count( $address_array ) > 1 )
      {
          die( "Feil: Flere addresser med samme ID funnet i database, dette skal ikke v�re mulig. " );
      }
      else if ( count( $address_array ) == 1 )
      {
        $this->ID = $address_array[ 0 ][ "ID" ];
        $this->Street1 = $address_array[ 0 ][ "Street1" ];
        $this->Street2 = $address_array[ 0 ][ "Street2" ];
        $this->Zip = $address_array[ 0 ][ "Zip" ];
        $this->AddressType = $address_array[ 0 ][ "AddressType" ];
      }
    }
  }

  /*
    Henter ut alle adressene lagret i databasen.
  */
  function getAll( )
  {
    $this->dbInit();    
    $address_array = 0;
    
    array_query( $address_array, "SELECT * FROM Address" );
    
    return $address_array;
  }
  

  /*!
    Setter  street1.
  */
  function setStreet1( $value )
  {
    $this->Street1 = $value;
  }

  /*!
    Setter  street2.
  */
  function setStreet2( $value )
  {
    $this->Street2 = $value;
  }

  /*!
    Setter postkode.
  */
  function setZip( $value )
  {
    $this->Zip = $value;
  }

  /*!
    Setter adressetype.
  */
  function setAddressType( $value )
  {
    $this->AddressType = $value;
  }
  
  /*!
    Returnerer  street1.
  */
  function setStreet1( $value )
  {
    return $this->Street1;
  }

  /*!
    Returnerer  street2.
  */
  function setStreet2( $value )
  {
    return $this->Street2;
  }

  /*!
    Returnerer postkode.
  */
  function setZip( $value )
  {
    return $this->Zip;
  }

  /*!
    Returnerer adressetype.
  */
  function setAddressType( $value )
  {
    return $this->AddressType;
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
  var $Street1;
  var $Street2;
  var $Zip;
  var $AddressType;
}

?>
