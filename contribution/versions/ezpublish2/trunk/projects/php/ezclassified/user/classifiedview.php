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
include_once( "ezcontact/classes/ezperson.php" );
include_once( "ezcontact/classes/ezonline.php" );
include_once( "ezcontact/classes/ezaddress.php" );

if ( isSet ( $Back ) )
{
    header( "Location: /contact/companytype/list/" );
    exit();
}

$t = new eZTemplate( "ezclassified/user/" . $ini->read_var( "eZClassifiedMain", "TemplateDir" ),
                     "ezclassified/user/intl", $Language, "classifiedview.php" );
$t->setAllStrings();

$t->set_file( array(                    
    "classified_edit" => "classifiedview.tpl"
    ) );

$t->set_block( "classified_edit", "company_view_tpl", "company_view" );
$t->set_block( "company_view_tpl", "address_item_tpl", "address_item" );
$t->set_block( "company_view_tpl", "phone_item_tpl", "phone_item" );
$t->set_block( "company_view_tpl", "no_phone_item_tpl", "no_phone_item" );
$t->set_block( "company_view_tpl", "fax_item_tpl", "fax_item" );
$t->set_block( "company_view_tpl", "no_fax_item_tpl", "no_fax_item" );
$t->set_block( "company_view_tpl", "web_item_tpl", "web_item" );
$t->set_block( "company_view_tpl", "no_web_item_tpl", "no_web_item" );
$t->set_block( "company_view_tpl", "email_item_tpl", "email_item" );
$t->set_block( "company_view_tpl", "no_email_item_tpl", "no_email_item" );
$t->set_block( "company_view_tpl", "image_view_tpl", "image_view" );
$t->set_block( "company_view_tpl", "logo_view_tpl", "logo_view" );
$t->set_block( "company_view_tpl", "no_image_tpl", "no_image" );
$t->set_block( "company_view_tpl", "no_logo_tpl", "no_logo" );
$t->set_block( "classified_edit", "person_item_tpl", "person_item" );
$t->set_block( "classified_edit", "no_person_item_tpl", "no_person_item" );
$t->set_block( "person_item_tpl", "person_mail_item_tpl", "person_mail_item" );
$t->set_block( "person_item_tpl", "person_phone_item_tpl", "person_phone_item" );
$t->set_block( "person_item_tpl", "person_fax_item_tpl", "person_fax_item" );

$position = new eZPosition( $PositionID );

$t->set_var( "classified_title", $position->title() );
$t->set_var( "classified_duedate", $position->dueDate() );
$reference = $position->reference();
if ( $reference )
    $t->set_var( "classified_reference", $reference );
else
    $t->set_var( "classified_reference", $position->id() );
$t->set_var( "classified_position_type", positionTypeName( $position->positionType() ) );
$t->set_var( "classified_initiate_type", initiateTypeName( $position->initiateType() ) );
$t->set_var( "classified_id", $position->id() );
$t->set_var( "classified_description", $position->description() );
//  $t->set_var( "classified_contact_person", $position->contactPerson() );
$t->set_var( "classified_pay", $position->pay() );
$t->set_var( "classified_worktime", $position->WorkTime() );
$t->set_var( "classified_duration", $position->Duration() );
$t->set_var( "classified_workplace", $position->WorkPlace() );

// Template variabler.
$Action_value = "update";

$company = $position->company();

$t->set_var( "company_name", $company->name() );
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

if ( count( $phoneList ) >= 2 )
{
    $has_phone = true;
    $has_fax = true;
    for( $i=0; $i<count ( $phoneList ); $i++ )
    {
        if ( $phoneList[$i]->phoneTypeID() == 5 )
        {
            $t->set_var( "tele_phone_id", $phoneList[$i]->id() );
            $t->set_var( "telephone", $phoneList[$i]->number() );
            $t->set_var( "no_phone_item", "" );
        }
        else
        {
            $t->set_var( "phone_item", "" );
            $has_phone = false;
        }
        if ( $phoneList[$i]->phoneTypeID() == 8 )
        {
            $t->set_var( "fax_phone_id", $phoneList[$i]->id() );
            $t->set_var( "fax", $phoneList[$i]->number() );
            $t->set_var( "no_fax_item", "" );
        }
        else
        {
            $t->set_var( "fax_item", "" );
            $has_fax = false;
        }

        if ( $has_phone )
            $t->parse( "phone_item", "phone_item_tpl" );
        else
            $t->parse( "no_phone_item", "no_phone_item_tpl" );
        $t->parse( "fax_item", "fax_item_tpl" );
    }
}
else
{
    $t->set_var( "phone_item", "" );
    $t->set_var( "fax_item", "" );
    $t->parse( "no_phone_item", "no_phone_item_tpl" );
    $t->parse( "no_fax_item", "no_fax_item_tpl" );
}

// Address list

$addressList = $company->addresses( $company->id() );

if ( count ( $addressList ) >= 1 )
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
else
{
    $t->set_var( "street1", "Ingen adresse funnet." );
    $t->set_var( "street2", "" );
    $t->set_var( "place", "" );
    $t->set_var( "zip", "" );

    $t->parse( "address_item", "address_item_tpl", true );
}

// Contact persons

$contactList = getPositionContactPersons( $position->id() );

$t->set_var( "person_item", "" );
$t->set_var( "no_person_item", "" );

if ( count ( $contactList ) >= 1 )
{
    foreach( $contactList as $contactID )
    {
        $contactPerson = new eZPerson( $contactID );
        $t->set_var( "person_name", $contactPerson->firstName() . " " . $contactPerson->lastName() );
        $t->set_var( "person_title", $contactPerson->title( $company->id() ) );
        $t->set_var( "person_mail_item", "" );
        $mail = $contactPerson->emailAddress();
        if ( $mail )
        {
            $t->set_var( "person_mail", $mail );
            $t->parse( "person_mail_item", "person_mail_item_tpl", true );
        }
        else
            $t->set_var( "person_mail", "" );
        $t->set_var( "person_phone_item", "" );
        $work_phone = $contactPerson->workPhone();
        if ( $work_phone )
        {
            $t->set_var( "person_phone", $work_phone );
            $t->parse( "person_phone_item", "person_phone_item_tpl", true );
        }
        else
            $t->set_var( "person_phone", "" );
        $t->set_var( "person_fax_item", "" );
        $fax_phone = $contactPerson->faxPhone();
        if ( $fax_phone )
        {
            $t->set_var( "person_fax", $fax_phone );
            $t->parse( "person_fax_item", "person_fax_item_tpl", true );
        }
        else
            $t->set_var( "person_fax", "" );
        $t->parse( "person_item", "person_item_tpl", true );
    }
}
else
{
    $t->parse( "no_person_item", "no_person_item_tpl", true );
}

// Online list
$onlineList = $company->onlines( $company->id() );

if ( count ( $onlineList ) >= 1 )
{
    $has_web = true;
    $has_email = true;
    for( $i=0; $i<count ( $onlineList ); $i++ )
    {
        if ( $onlineList[$i]->onlineTypeID() == 4 )
        {
            $t->set_var( "web_online_id", $onlineList[$i]->id() );
            $t->set_var( "web", $onlineList[$i]->URL() );
            $t->set_var( "no_web_item", "" );
        }
        else
        {
            $t->set_var( "web_item", "" );
            $has_web = false;
        }
        if ( $onlineList[$i]->onlineTypeID() == 5 )
        {
            $t->set_var( "email_online_id", $onlineList[$i]->id() );
            $t->set_var( "email", $onlineList[$i]->URL() );
            $t->set_var( "no_email_item", "" );
        }
        else
        {
            $t->set_var( "email_item", "" );
            $has_email = false;
        }
    }
    if ( $has_web )
        $t->parse( "web_item", "web_item_tpl" );
    else
        $t->parse( "no_web_item", "no_web_item_tpl" );
    if ( $has_email )
        $t->parse( "email_item", "email_item_tpl" );
    else
        $t->parse( "no_email_item", "no_email_item_tpl" );
}
else
{
  $t->set_var( "web_item", "" );
  $t->set_var( "email_item", "" );
  $t->parse( "no_web_item", "no_web_item_tpl" );
  $t->parse( "no_email_item", "no_email_item_tpl" );
}
$t->set_var( "company_id", $company->id() );
$t->parse( "company_view", "company_view_tpl" );


    
// Templateoun variabler.

$t->set_var( "error", $error );
$t->set_var( "errors_item", $error );

$t->set_var( "action_value", $Action_value );

$t->pparse( "output", "classified_edit"  );

?>
