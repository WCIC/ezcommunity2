<?
/*
  Denne klassen h�ndterer personer i eZ contact. Disse lagres og hentes ut fra databasen.
*/

//require "ezphputils.php";

class eZPerson
{

  /*
    Constructor.
  */
  function eZPerson( )
  {
    $this->ID = 0;
    $this->Company = 0;
  }
  
  /*
    Lagrer en ny personrad i databasen. 
  */  
  function store()
  {
    $this->dbInit();
    query( "INSERT INTO Person set FirstName='$this->FirstName',
	LastName='$this->LastName',
	Owner='$this->Owner',
	Comment='$this->Comment',
	PersonNr='$this->PersonNr',
	Company='$this->Company',
	ContactType='$this->ContactType' " );
    return mysql_insert_id();
  }

  /*
    Oppdaterer informasjonen som ligger i databasen.
  */  
  function update()
  {
    $this->dbInit();    
  }

  /*
    Henter ut person med ID == $id
  */  
  function get( $id )
  {
    $this->dbInit();    
    if ( $id != "" )
    {
      array_query( $person_array, "SELECT * FROM Person WHERE ID='$id'" );
      if ( count( $person_array ) > 1 )
      {
          die( "Feil: Flere personer med samme ID funnet i database, dette skal ikke v�re mulig. " );
      }
      else if ( count( $person_array ) == 1 )
      {
        $this->ID = $person_array[ 0 ][ "ID" ];
        $this->FirstName = $person_array[ 0 ][ "FirstName" ];
        $this->LastName = $person_array[ 0 ][ "LastName" ];
        $this->Owner = $person_array[ 0 ][ "Owner" ];
        $this->ContactType = $person_array[ 0 ][ "ContactType" ];
        $this->Company = $person_array[ 0 ][ "Company" ];
        $this->Comment = $person_array[ 0 ][ "Comment" ];
      }
    }
  }

  /*
    Henter ut alle personene lagret i databasen.
  */
  function getAll( )
  {
    $this->dbInit();    
    $person_array = 0;
    
    array_query( $person_array, "SELECT * FROM Person ORDER BY LastName" );
    
    return $person_array;
  }

  /*
  */
  function setFirstName( $value )
  {
    $this->FirstName = $value;
  }

  /*
  */
  function setLastName( $value )
  {
    $this->LastName = $value;
  }

  /*!
  */
  function setCompany( $value )
  {
    $this->Company = $value;
  }

  /*!
  */
  function setComment( $value )
  {
    $this->Comment = $value;
  }


  /*!
  */
  function setContactType( $value )
  {
    $this->ContactType = $value;
  }

  /*!
  */
  function setOwner( $value )
  {
    $this->Owner = $value;
  }
  
  
  /*!
  */
  function setPersonNr( $value )
  {
    $this->PersonNr = $value;
  }
  
  /*
    Returnerer ID'en til personen.
  */
  function id()
  {
    return $this->ID;
  }
  
  /*
    Returnerer fornavnet.
  */
  function firstName()
  {
    return $this->FirstName;
  }

  /*
    Returnerer etternavnet.
  */
  function lastName()
  {    
    return $this->LastName;
  }

  /*!
    Returnerer person nummer
  */
  function personNr()
  {
    return $this->PersonNr;
  }

  /*!
    Returnerer firma
  */
  function company()
  {
    return $this->Company;
  }

  /*!
  */
  function comment( )
  {
    return $this->Comment;
  }

  /*!
  */
  function contactType( )
  {
    return $this->ContactType;
  }

  /*!
  */
  function owner( )
  {
    return $this->Owner;
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
  var $FirstName;
  var $LastName;
  var $Company;  
  var $Owner;
  var $PersonNr;
  var $ContactType;
  var $Comment;
};

?>
