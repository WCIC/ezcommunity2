<?
/*
  Denne filen sjekker om brukeren er logget p�..

*/

  $menuTemplate = new Template( "." );
  $menuTemplate->set_file( "top_menu", "templates/topmenu.tpl" );               
  
  $session = new eZSession();
  if ( !$session->get( $AuthenticatedSession ) )
  {
    die( "Du m� logge deg p�." );    
  }

  $usr = new eZUser();
  $usr->get( $session->userID() );
  $menuTemplate->set_var( "current_user", $usr->login() );
  $menuTemplate->pparse( "output", "top_menu" );

?>
