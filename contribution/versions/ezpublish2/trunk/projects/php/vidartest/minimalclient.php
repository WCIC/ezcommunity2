<?
include_once( "ezxmlrpc/classes/ezxmlrpcclient.php" );
include_once( "ezxmlrpc/classes/ezxmlrpccall.php" );

include_once( "ezxmlrpc/classes/ezxmlrpcstring.php" );

// create a new client
$client = new eZXMLRPCClient( "php.ez.no", "/ezxmlrpc/server.php" );

$call = new eZXMLRPCCall( );
$call->setMethodName( "myFunc" );

// send the request
$response = $client->send( $call );

// print out the results
$result = $response->result();
print( "The server returned: " . $result->value() . "<br>" );

?>


