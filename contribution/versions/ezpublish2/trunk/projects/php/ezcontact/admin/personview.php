<?
/*
    View a person
 */

include_once( "classes/INIFile.php" );

$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZContactMain", "Language" );

include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezdate.php" );
include_once( "classes/eztexttool.php" );

include_once( "ezcontact/classes/ezperson.php" );
include_once( "ezaddress/classes/ezaddress.php" );
include_once( "ezaddress/classes/ezaddresstype.php" );
include_once( "ezaddress/classes/ezphone.php" );
include_once( "ezaddress/classes/ezphonetype.php" );
include_once( "ezaddress/classes/ezonline.php" );
include_once( "ezaddress/classes/ezonlinetype.php" );
include_once( "ezcontact/classes/ezprojecttype.php" );
include_once( "ezcontact/classes/ezconsultation.php" );

include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezuser/classes/ezpermission.php" );

$user = eZUser::currentUser();
if ( get_class( $user ) != "ezuser" )
{
    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/nopermission/login" );
    exit();
}

if ( !eZPermission::checkPermission( $user, "eZContact", "PersonView" ) )
{
    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/nopermission/person/view" );
    exit();
}

$error = false;

$t = new eZTemplate( "ezcontact/admin/" . $ini->read_var( "eZContactMain", "AdminTemplateDir" ),
                     "ezcontact/admin/intl", $Language, "personview.php" );
$intl = new INIFile( "ezcontact/admin/intl/$Language/personview.php.ini", false );
$t->setAllStrings();

$t->set_file( array(                    
    "person_view" => "personview.tpl"
    ) );
$t->set_block( "person_view", "birth_item_tpl", "birth_item" );
$t->set_block( "person_view", "no_birth_item_tpl", "no_birth_item" );

$t->set_block( "person_view", "company_item_tpl", "company_item" );

$t->set_block( "person_view", "address_item_tpl", "address_item" );
$t->set_block( "address_item_tpl", "address_line_tpl", "address_line" );
$t->set_block( "person_view", "no_address_item_tpl", "no_address_item" );

$t->set_block( "person_view", "phone_item_tpl", "phone_item" );
$t->set_block( "phone_item_tpl", "phone_line_tpl", "phone_line" );
$t->set_block( "person_view", "no_phone_item_tpl", "no_phone_item" );

$t->set_block( "person_view", "online_item_tpl", "online_item" );
$t->set_block( "online_item_tpl", "online_line_tpl", "online_line" );
$t->set_block( "person_view", "no_online_item_tpl", "no_online_item" );

//  $t->set_block( "person_view", "contact_person_tpl", "contact_person" );
//  $t->set_block( "person_view", "no_contact_person_tpl", "no_contact_person" );
$t->set_block( "person_view", "project_status_tpl", "project_status" );
$t->set_block( "person_view", "no_project_status_tpl", "no_project_status" );

$t->set_block( "person_view", "consultation_table_item_tpl", "consultation_table_item" );
$t->set_block( "consultation_table_item_tpl", "consultation_item_tpl", "consultation_item" );

$t->set_block( "person_view", "consultation_buttons_tpl", "consultation_buttons" );

$t->set_var( "consultation_item", "" );
$t->set_var( "consultation_table_item", "" );

$t->set_var( "firstname", "" );
$t->set_var( "lastname", "" );
$t->set_var( "birthday", "" );
$t->set_var( "birthmonth", "" );
$t->set_var( "birthyear", "" );
$t->set_var( "description", "" );

$t->set_var( "user_name", "" );
$t->set_var( "old_password", "" );

$t->set_var( "street1", "" );
$t->set_var( "street2", "" );
$t->set_var( "zip", "" );
$t->set_var( "place", "" );

$t->set_var( "home_phone", "" );
$t->set_var( "work_phone", "" );

$t->set_var( "web", "" );
$t->set_var( "email", "" );

/*
    The user wants to view an existing person.
    We present a page with the info.
*/

if ( $Action == "view" )
{
    $Action_value = "view";
    $person = new eZPerson( $PersonID, true );

    $t->set_var( "firstname", eZTextTool::htmlspecialchars( $person->firstName() ) );
    $t->set_var( "lastname", eZTextTool::htmlspecialchars( $person->lastName() ) );

    $t->set_var( "birth_item", "" );
    $t->set_var( "no_birth_item", "" );
    if ( $person->hasBirthDate() )
    {
        $Birth = new eZDate();
        $Birth->setMySQLDate( $person->birthDate() );

        $locale = new eZLocale( $Language );
        $t->set_var( "birthdate", $locale->format( $Birth ) );
        $t->parse( "birth_item", "birth_item_tpl" );
    }
    else
    {
        $t->parse( "no_birth_item", "no_birth_item_tpl" );
    }
    $t->set_var( "description", eZTextTool::htmlspecialchars( $person->comment() ) );

    // Telephone list
    $phoneList = $person->phones( $person->id() );

    $count = count( $phoneList );
    if( $count != 0 )
    {
        for( $i=0; $i < $count; $i++ )
        {
            $t->set_var( "phone_id", $phoneList[$i]->id() );
            $t->set_var( "phone", eZTextTool::htmlspecialchars( $phoneList[$i]->number() ) );

            $phoneType = $phoneList[$i]->phoneType();

            $t->set_var( "phone_type_id", $phoneType->id() );
//              $t->set_var( "phone_type_name", $intl->read_var( "strings", "phone_" . $phoneType->name() ) );
            $t->set_var( "phone_type_name", eZTextTool::htmlspecialchars( $phoneType->name() ) );

            $t->parse( "phone_line", "phone_line_tpl", true );
        }
        $t->parse( "phone_item", "phone_item_tpl" );
        $t->set_var( "no_phone_item", "" );
    }
    else
    {
        $t->set_var( "phone_item", "" );
        $t->parse( "no_phone_item", "no_phone_item_tpl" );
    }

    // Address list
    $addressList = $person->addresses( $person->id() );
    $count = count( $addressList );
    
    if( $count != 0 )
    {
        foreach( $addressList as $addressItem )
        {
            $t->set_var( "address_id", $addressItem->id() );
            $t->set_var( "street1", eZTextTool::htmlspecialchars( $addressItem->street1() ) );
            $t->set_var( "street2", eZTextTool::htmlspecialchars( $addressItem->street2() ) );
            $t->set_var( "zip", eZTextTool::htmlspecialchars( $addressItem->zip() ) );
            $t->set_var( "place", eZTextTool::htmlspecialchars( $addressItem->place() ) );
            $country = $addressItem->country();
            if ( get_class( $country ) == "ezcountry" )
                $t->set_var( "country", eZTextTool::htmlspecialchars( $country->name() ) );
            else
                $t->set_var( "country", "" );

            $addressType = $addressItem->addressType();

            $t->set_var( "address_type_id", $addressType->id() );
            $t->set_var( "address_type_name", eZTextTool::htmlspecialchars( $addressType->name() ) );
            
            $t->set_var( "script_name", "personedit.php" );
            $t->parse( "address_line", "address_line_tpl", true );

        }
        $t->parse( "address_item", "address_item_tpl" );
        $t->set_var( "no_address_item", "" );
    }
    else
    {
        $t->set_var( "address_item", "" );
        $t->parse( "no_address_item", "no_address_item_tpl" );
    }
    
    // Online list
    $OnlineList = $person->onlines( $person->id() );
    $count = count( $OnlineList );
    if ( $count != 0)
    {
        for( $i=0; $i < count ( $OnlineList ); $i++ )
        {
            $onlineType = $OnlineList[$i]->onlineType();

            $t->set_var( "online_id", $OnlineList[$i]->id() );
            $prefix = $onlineType->URLPrefix();
            $vis_prefix = $prefix;
            $url = $OnlineList[$i]->URL();
            if ( $onlineType->prefixLink() )
            {
                if ( strncasecmp( $url, $prefix, strlen( $prefix ) ) == 0 )
                {
                    $prefix = "";
                }
            }
            else
            {
                $prefix = "";
            }
            if ( $onlineType->prefixVisual() )
            {
                if ( strncasecmp( $url, $vis_prefix, strlen( $vis_prefix ) ) == 0 )
                {
                    $vis_prefix = "";
                }
            }
            else
            {
                $vis_prefix = "";
            }
            $t->set_var( "online_prefix", $prefix );
            $t->set_var( "online_visual_prefix", $vis_prefix );
            $t->set_var( "online", $url );

            $t->set_var( "online_type_name", eZTextTool::htmlspecialchars( $onlineType->name() ) );

            $t->parse( "online_line", "online_line_tpl", true );
        }
        $t->parse( "online_item", "online_item_tpl" );
        $t->set_var( "no_online_item", "" );
    }
    else
    {
        $t->set_var( "online_item", "" );
        $t->parse( "no_online_item", "no_online_item_tpl" );
    }

    // List companies this person is related to
    $t->set_var( "company_item", "" );
    $companies = $person->companies();
    foreach( $companies as $company )
    {
        $t->set_var( "company_id", $company->id() );
        $t->set_var( "company_name", eZTextTool::htmlspecialchars( $company->name() ) );
        $t->parse( "company_item", "company_item_tpl", true );
    }

    $t->set_var( "person_id", $PersonID );

    // Project info
//      $t->set_var( "contact_person", "" );
//      $t->set_var( "no_contact_person", "" );

//      $contact = $person->contact();
//      if ( $contact )
//      {
//          $user = new eZUser( $contact );
//          $t->set_var( "contact_firstname", $user->firstName() );
//          $t->set_var( "contact_lastname", $user->lastName() );
//          $t->parse( "contact_person", "contact_person_tpl" );
//      }
//      else
//      {
//          $t->parse( "no_contact_person", "no_contact_person_tpl" );
//      }

    $t->set_var( "project_status", "" );
    $t->set_var( "no_project_status", "" );

    $statusid = $person->projectState();
    if ( $statusid )
    {
        $status = new eZProjectType( $statusid );
        $t->set_var( "project_status", eZTextTool::htmlspecialchars( $status->name() ) );
        $t->parse( "project_status", "project_status_tpl" );
    }
    else
    {
        $t->parse( "no_project_status", "no_project_status_tpl" );
    }

    // Consultation list
    $user = eZUser::currentUser();
    if ( eZPermission::checkPermission( $user, "eZContact", "consultation" ) )
    {
        $max = $ini->read_var( "eZContactMain", "MaxPersonConsultationList" );
        $consultations = eZConsultation::findConsultationsByContact( $PersonID, $user->id(), true, 0, $max );
        $t->set_var( "consultation_type", "person" );
        $t->set_var( "person_id", $PersonID  );

        $locale = new eZLocale( $Language );
        $i = 0;

        foreach ( $consultations as $consultation )
        {
            if( ( $i % 2 ) == 0 )
            {
                $t->set_var( "bg_color", "bglight" );
            }
            else
            {
                $t->set_var( "bg_color", "bgdark" );
            }

            $t->set_var( "consultation_id", $consultation->id() );
            $t->set_var( "consultation_date", $locale->format( $consultation->date() ) );
            $t->set_var( "consultation_short_description", eZTextTool::htmlspecialchars( $consultation->shortDescription() ) );
            $t->set_var( "consultation_status_id", $consultation->state() );
            $t->set_var( "consultation_status", eZTextTool::htmlspecialchars( eZConsultation::stateName( $consultation->state() ) ) );
            $t->parse( "consultation_item", "consultation_item_tpl", true );
            $i++;
        }
    }

    if ( eZPermission::checkPermission( $user, "eZContact", "consultation" )
         and count( $consultations ) > 0 )
    {
        $t->parse( "consultation_table_item", "consultation_table_item_tpl", true );
    }
    else
    {
        $t->set_var( "consultation_table_item", "" );
    }

    if ( eZPermission::checkPermission( $user, "eZContact", "consultation" ) )
    {
        $t->parse( "consultation_buttons", "consultation_buttons_tpl" );
    }
    else
    {
        $t->set_var( "consultation_buttons", "" );
    }
}

$t->set_var( "action_value", $Action_value );

$t->pparse( "output", "person_view"  );

?>
