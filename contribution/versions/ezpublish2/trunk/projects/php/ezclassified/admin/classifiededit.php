<?
/*
  Editere firma.
*/

include_once( "classes/INIFile.php" );

$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZClassifiedMain", "Language" );

include_once( "ezclassified/classes/ezposition.php" );
include_once( "ezclassified/classes/ezcategory.php" );
include_once( "ezcontact/classes/ezcompany.php" );
include_once( "ezcontact/classes/ezonline.php" );
include_once( "ezcontact/classes/ezaddress.php" );


if ( isSet ( $Back ) )
{
    header( "Location: /contact/companytype/list/" );
    exit();
}

if ( isSet ( $Delete ) )
{
    $Action = "delete";
}

if ( isSet ( $Preview ) )
{
    header( "Location: /contact/company/view/$CompanyID" );
    exit();
}

if ( $Action == "insert" )
{
    $position = new eZPosition();
    $position->setName( $Name );
    $position->setUser( $user );
    $position->setDescription( $Description );
    $position->setPrice( $Price );
    
    $position->setPay( $Pay );
    $position->setWorkTime( $WorkTime );
    $position->setDuration( $Duration );
    $position->setContactPerson( $ContactPerson );
    $position->setWorkPlace( $WorkPlace );
    $position->setValidUntil( $Year, $Month, $Day );
    $position->store();

    $company = new eZCompany( $CompanyID );
    $position->addCompany( $company );

    // Add classifed to categories
    if ( ( $CategoryArray ) != "" )
    {
        $category = new eZCategory();

        for( $i=0; $i<count( $CategoryArray ); $i++ )
        {
            $category->get( $CategoryArray[$i] );
            $category->addClassified( $position );
        }
    }

    Header( "Location: /classified/classifiedlist/list" );
    exit();
}

if ( $Action == "update" )
{
    $position = new eZPosition( $ClassifiedID );
    $position->setName( $Name );
    $position->setUser( $user );
    $position->setDescription( $Description );
    $position->setPrice( $Price );

    $position->setPay( $Pay );
    $position->setWorkTime( $WorkTime );
    $position->setDuration( $Duration );
    $position->setContactPerson( $ContactPerson );
    $position->setWorkPlace( $WorkPlace );
    $position->setValidUntil( $Year, $Month, $Day );

    $position->store();

    $position->removeCategoryies();
    
    // Add classifed to categories
    if ( ( $CategoryArray ) != "" )
    {
        $category = new eZCategory();

        for( $i=0; $i<count( $CategoryArray ); $i++ )
        {
            $category->get( $CategoryArray[$i] );
            $category->addClassified( $position );
        }
    }


    Header( "Location: /classified/classifiedlist/list" );
    exit();
}

if( $Action == "delete" )
{
    $position = new eZPosition( $ClassifiedID );
    $position->delete();

    Header( "Location: /classified/classifiedlist/list" );
    exit();
}

$t = new eZTemplate( "ezclassified/admin/" . $ini->read_var( "eZClassifiedMain", "AdminTemplateDir" ),
                     "ezclassified/admin/intl", $Language, "classifiededit.php" );
$t->setAllStrings();

$t->set_file( array(                    
    "classified_edit" => "classifiededit.tpl"
    ) );

$t->set_block( "classified_edit", "category_item_tpl", "category_item" );

$t->set_block( "classified_edit", "company_view_tpl", "company_view" );
$t->set_block( "classified_edit", "delete_button_tpl", "delete_button" );
$t->set_var( "delete_button", "" );
$t->set_block( "company_view_tpl", "address_item_tpl", "address_item" );
$t->set_block( "company_view_tpl", "phone_item_tpl", "phone_item" );
$t->set_block( "company_view_tpl", "fax_item_tpl", "fax_item" );
$t->set_block( "company_view_tpl", "web_item_tpl", "web_item" );
$t->set_block( "company_view_tpl", "email_item_tpl", "email_item" );
$t->set_block( "company_view_tpl", "image_view_tpl", "image_view" );
$t->set_block( "company_view_tpl", "logo_view_tpl", "logo_view" );
$t->set_block( "company_view_tpl", "no_image_tpl", "no_image" );
$t->set_block( "company_view_tpl", "no_logo_tpl", "no_logo" );

$t->set_block( "classified_edit", "company_select_tpl", "company_select" );
$t->set_block( "company_select_tpl", "company_item_tpl", "company_item" );


if ( $Action == "new" )
{
    $t->set_var( "classified_name", "" );
    $t->set_var( "classified_description", "" );
    $t->set_var( "classified_pay", "" );
    $t->set_var( "classified_worktime", "" );
    $t->set_var( "classified_duration", "" );
    $t->set_var( "classified_workplace", "" );
    $t->set_var( "classified_contact_person", "" );
    $t->set_var( "classified_year", "" );
    $t->set_var( "classified_month", "" );
    $t->set_var( "classified_day", "" );
    $Action_value = "insert";
}

// Redigering av firma.
if ( $Action == "edit" )
{
    $position = new eZPosition( $ClassifiedID );

    $t->set_var( "classified_name", $position->name() );
    $t->set_var( "classified_id", $position->id() );
    $t->set_var( "classified_description", $position->description() );
    $t->set_var( "classified_contact_person", $position->contactPerson() );
    $t->set_var( "classified_pay", $position->pay() );
    $t->set_var( "classified_worktime", $position->WorkTime() );
    $t->set_var( "classified_duration", $position->Duration() );
    $t->set_var( "classified_workplace", $position->WorkPlace() );

    $date = $position->validUntil();

    $t->set_var( "classified_year", $date->year() );
    $t->set_var( "classified_month", $date->month() );
    $t->set_var( "classified_day", $date->day() );

    $t->parse( "delete_button", "delete_button_tpl" );
    
    // Template variabler.
    $Action_value = "update";
    
}

// Category selector
$category = new eZCategory();
$categoryTypeList = $category->getTree();
for( $i=0; $i < count( $categoryTypeList ); $i++ )
{
    $t->set_var( "category_name", $categoryTypeList[$i][0]->name() );
    $t->set_var( "category_id", $categoryTypeList[$i][0]->id() );

    if ( $categoryTypeList[$i][1] > 0 )
        $t->set_var( "category_level", str_repeat( "&nbsp;", $categoryTypeList[$i][1] ) );
    else
        $t->set_var( "category_level", "" );

    if ( $position )
    {
        $categoryList = $position->categories( $ClassifiedID );
        $found = false;
        foreach ( $categoryList as $category )
            {
                if ( $category->id() == $categoryTypeList[$i][0]->id() )
                {
                    $found = true;
                }
            }
        if ( $found  == true )
        {
            $t->set_var( "is_selected", "selected" );
        }
        else
        {
            $t->set_var( "is_selected", "" );
        }
    }
    else
    {
        $t->set_var( "is_selected", "" );
    }
    $t->parse( "category_item", "category_item_tpl", true );
}

if ( $position )
{
    $company = $position->company();

    $t->set_var( "company_name", $company->name() );
    $t->set_var( "company_id", $company->id() );
    $t->set_var( "company_description", $company->comment() );
    $t->set_var( "company_companyno", $company->companyNo() );

    // View logo.
    $logoImage = $company->logoImage();

    if ( ( get_class ( $logoImage ) == "ezimage" ) && ( $logoImage->id() != 0 ) )
    {
        $variation = $logoImage->requestImageVariation( 150, 150 );

        $t->set_var( "logo_image_src", "/" . $variation->imagePath() );
        $t->set_var( "logo_name", $logoImage->name() );
        $t->set_var( "logo_id", $logoImage->id() );
        
        $t->set_var( "no_logo", "" );
        $t->parse( "logo_view", "logo_view_tpl" );
    }
    else
    {
        $t->set_var( "logo_view", "" );
        $t->parse( "no_logo", "no_logo_tpl" );
    }
    // Telephone list
    $phoneList = $company->phones( $company->id() );

    if ( count( $phoneList ) <= 2 )
    {
        for( $i=0; $i<count ( $phoneList ); $i++ )
        {
            if ( $phoneList[$i]->phoneTypeID() == 1 )
            {
                $t->set_var( "tele_phone_id", $phoneList[$i]->id() );
                $t->set_var( "telephone", $phoneList[$i]->number() );
            }
            if ( $phoneList[$i]->phoneTypeID() == 2 )
            {
                $t->set_var( "fax_phone_id", $phoneList[$i]->id() );
                $t->set_var( "fax", $phoneList[$i]->number() );
            }

            $t->parse( "phone_item", "phone_item_tpl" );
            $t->parse( "fax_item", "fax_item_tpl" );
        }
    }

// Address list
    $addressList = $company->addresses( $company->id() );
    if ( count ( $addressList ) == 1 )
    {
        foreach( $addressList as $addressItem )
            {
                $t->set_var( "address_id", $addressItem->id() );
                $t->set_var( "street1", $addressItem->street1() );
                $t->set_var( "street2", $addressItem->street2() );
                $t->set_var( "zip", $addressItem->zip() );
                $t->set_var( "place", $addressItem->place() );
            
                $t->set_var( "company_id", $CompanyID );
            
                $t->set_var( "script_name", "companyedit.php" );

                $t->parse( "address_item", "address_item_tpl", true );
            
            }
    }

// Online list
    $onlineList = $company->onlines( $company->id() );

    if ( count ( $onlineList ) <= 2 )
    {
        for( $i=0; $i<count ( $onlineList ); $i++ )
        {
            if ( $onlineList[$i]->onlineTypeID() == 1 )
            {
                $t->set_var( "web_online_id", $onlineList[$i]->id() );
                $t->set_var( "web", $onlineList[$i]->URL() );
            }
            if ( $onlineList[$i]->onlineTypeID() == 2 )
            {
                $t->set_var( "email_online_id", $onlineList[$i]->id() );
                $t->set_var( "email", $onlineList[$i]->URL() );
            }
            
        }
        $t->parse( "web_item", "web_item_tpl" );
        $t->parse( "email_item", "email_item_tpl" );

    }
	$t->set_var( "company_select", "" ); // Dette er min kode! (HellstrÝm)
    $t->set_var( "company_id", $company->id() );
    $t->parse( "company_view", "company_view_tpl" );
}
else
{
    $company = new eZCompany();
    $companyList = $company->getAll();

    foreach( $companyList as $companyItem )
    {
        $t->set_var( "company_name", $companyItem->name() );
        $t->set_var( "company_id", $companyItem->id() );

        $t->parse( "company_item", "company_item_tpl", true );
    }

    $t->set_var( "company_view", "" );
    $t->parse( "company_select", "company_select_tpl" );
}

    
// Templateoun variabler.

$t->set_var( "error", $error );
$t->set_var( "errors_item", $error );

$t->set_var( "action_value", $Action_value );

$t->pparse( "output", "classified_edit"  );

?>
