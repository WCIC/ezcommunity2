<?

include_once( "ezaddress/classes/ezcountry.php" );

$language_file = "country.php";
$item_type = new eZCountry( $CountryID );
$page_path = "/address/country";
$typeedit = "typeedit.tpl";
$template_array = array( "country_tpl" => "countryedit.tpl" );
$block_array = array( "extra_type_input" => "country_tpl" );

$func_call = array( "item_id" => "id",
                    "item_name" => "name",
                    "item_iso" => "iso" );

$func_call_set = array( "setName" => "ItemName",
                        "setISO" => "ItemISO" );

include( "ezaddress/admin/typeedit.php" );

?>
