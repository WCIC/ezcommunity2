<?
/*
  Viser liste over prioriteringer
*/
include_once( "classes/INIFile.php" );
$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZBugMain", "Language" );
$LanguageIni = new INIFIle( "ezbug/admin/intl/" . $Language . "/categorylist.php.ini", false );


include_once( "classes/eztemplate.php" );

include_once( "ezbug/classes/ezbugcategory.php" );

$t = new eZTemplate( "ezbug/admin/" . $ini->read_var( "eZBugMain", "AdminTemplateDir" ),
                     "ezbug/admin/intl", $Language, "categorylist.php" );
$t->setAllStrings();

$t->set_file( array(
    "category_page" =>  "categorylist.tpl"
    ) );

$t->set_block( "category_page", "category_item_tpl", "category_item" );

$t->set_var( "site_style", $SiteStyle );

if( isset( $Ok ) || isset( $AddCategory ) )
{
    $i = 0;
    if( count( $CategoryID ) > 0 )
    {
        foreach( $CategoryID as $itemID )
        {
            $category = new eZBugCategory( $itemID );
            $category->setName( $CategoryName[$i] );
            $category->store();
            $i++;
        }
    }
}

if( isset( $AddCategory ) )
{
    $newItem = new eZBugCategory();
    $newName = $LanguageIni->read_var( "strings", "newcategory" );
    $newItem->setName($newName);
    $newItem->store();
}

if( isset( $DeleteCategories ) )
{
    if( count( $CategoryArrayID ) > 0 )
    {
        foreach( $CategoryArrayID as $deleteItemID )
        {
            $item = new eZBugCategory( $CategoryID[ $deleteItemID ] );
            $item->delete();
        }
    }
}



$category = new eZBugCategory();
$categoryList = $category->getAll();

$i=0;
foreach( $categoryList as $categoryItem )
{
    if ( ( $i %2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }
        
    $t->set_var( "category_id", $categoryItem->id() );
    $t->set_var( "category_name", $categoryItem->name() );
    $t->set_var( "index_nr", $i );
    
    $t->parse( "category_item", "category_item_tpl", true );
    $i++;
} 

$t->pparse( "output", "category_page" );
?>
