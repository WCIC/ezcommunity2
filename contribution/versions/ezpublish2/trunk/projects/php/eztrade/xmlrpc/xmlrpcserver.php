<?
ob_end_clean();
ob_start();

// eZ trade classes
include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezorder.php" );


// include the server
include_once( "ezxmlrpc/classes/ezxmlrpcserver.php" );

// include the datatype(s) we need
include_once( "ezxmlrpc/classes/ezxmlrpcstring.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcint.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcdouble.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcarray.php" );
include_once( "ezxmlrpc/classes/ezxmlrpcresponse.php" );


$VersionNumber = "Pre release 1.0";

$server = new eZXMLRPCServer( );

// register functions
$server->registerFunction( "version" );
$server->registerFunction( "newOrders", array( new eZXMLRPCString(), new eZXMLRPCString() ) );

// process the server requests
$server->processRequest();

// implemented functions
function version( )
{
    $VersionNumber = $GLOBALS["VersionNumber"];
    return new eZXMLRPCString( "This is eZ trade xml rpc version: $VersionNumber" );
}

//
// Returns all the new orders and sets them to exported.
//
function &newOrders( $args )
{
    if ( $args[0]->value() == "bf" && $args[1]->value() == "mofser" )
    {
        $orders = array();

        // fetch all new orders
        $order = new eZOrder();

        // perform search
        $orderArray =& $order->getNew( );

        foreach ( $orderArray as $orderItem )
        {
            // set the order item to be exported
            $orderItem->setIsExported( true );
            $orderItem->store();
            
            $user =& $orderItem->user();
            if ( $user )
            {
                $shippingAddress =& $orderItem->shippingAddress();                
                $shippingCountry =& $shippingAddress->country();

                $billingAddress =& $orderItem->billingAddress();
                $billingCountry =& $billingAddress->country();

                $itemArray = array();

                $items = $orderItem->items( $OrderType );
                
                foreach ( $items as $item )
                {
                    $product = $item->product();
                    
                    $itemArray[] = new eZXMLRPCStruct( array( "ProductID" => new eZXMLRPCInt( $product->id() ),
                                                              "ProductNumber" => new eZXMLRPCInt( $product->productNumber() ),
                                                              "Name" => new eZXMLRPCString( $product->name() ),
                                                              "Name" => new eZXMLRPCString( $product->name() ),
                                                              "Count" => new eZXMLRPCInt( $item->count() ),
                                                              "Price" => new eZXMLRPCDouble( ( $item->count() * $product->price() ) ),
                                                              "TotalPrice" => new eZXMLRPCDouble( ($product->price() ) )
                                                              ) );
                }
                
                $orders[] = new eZXMLRPCStruct(
                    array(
                        "OrderID" => new eZXMLRPCInt( $orderItem->id() ),
                        "ShippingCharge" => new eZXMLRPCDouble( $orderItem->shippingCharge() ),
                        "FirstName" => new eZXMLRPCString( $user->firstName() ),
                        "LastName" => new eZXMLRPCString( $user->lastName()  ),
                        "ShippingStreet1" => new eZXMLRPCString( $shippingAddress->street1() ),
                        "ShippingStreet2" => new eZXMLRPCString( $shippingAddress->street2() ),
                        "ShippingZip" => new eZXMLRPCString( $shippingAddress->zip() ),
                        "ShippingPlace" => new eZXMLRPCString( $shippingAddress->place() ),
                        "ShippingCountry" => new eZXMLRPCString(  $shippingCountry->name() ),
                        "BillingStreet1" => new eZXMLRPCString( $billingAddress->street1() ),
                        "BillingStreet2" => new eZXMLRPCString( $billingAddress->street2() ),
                        "BillingZip" => new eZXMLRPCString( $billingAddress->zip() ),
                        "BillingPlace" => new eZXMLRPCString( $billingAddress->place() ),
                        "BillingCountry" => new eZXMLRPCString(  $billingCountry->name() ),
                        "OrderLines" => new eZXMLRPCArray( $itemArray )
                        ) );
            }
        }
        
        $tmp = new eZXMLRPCArray( $orders );
    }
    else
    {
        $tmp = new eZXMLRPCResponse( );
        $tmp->setError( 100, "Authorization failed." );
    }
    
    return $tmp;
}



ob_end_flush();
exit();
?>
