<?
/*
    View a consultation
 */
 
include_once( "classes/INIFile.php" );
include_once( "classes/ezhttptool.php" );
include_once( "classes/eztexttool.php" );

$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZContactMain", "Language" );

include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezuser/classes/ezpermission.php" );

$user = eZUser::currentUser();
if ( get_class( $user ) != "ezuser" )
{
    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/nopermission/login" );
    exit();
}

if ( !eZPermission::checkPermission( $user, "eZContact", "Consultation" ) )
{
    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/nopermission/consultation" );
    exit();
}

include_once( "classes/ezmail.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezdatetime.php" );
include_once( "ezcontact/classes/ezconsultation.php" );
include_once( "ezcontact/classes/ezconsultationtype.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );

$t = new eZTemplate( "ezcontact/admin/" . $ini->read_var( "eZContactMain", "AdminTemplateDir" ),
                     "ezcontact/admin/intl", $Language, "consultationedit.php" );
$t->setAllStrings();

$t->set_file( array(
    "consultation_view" => "consultationview.tpl"
    ) );
$t->set_block( "consultation_view", "consultation_item_tpl", "consultation_item" );

$t->set_block( "consultation_item_tpl", "consultation_date_item_tpl", "consultation_date_item" );
$t->set_block( "consultation_item_tpl", "group_notice_select_tpl", "group_notice_select" );
$t->set_block( "consultation_item_tpl", "no_group_notice_tpl", "no_group_notice" );

$t->set_block( "consultation_view", "company_contact_item_tpl", "company_contact_item" );
$t->set_block( "consultation_view", "person_contact_item_tpl", "person_contact_item" );

$t->set_var( "consultation_date", "" );
$t->set_var( "consultation_date_item", "" );

$t->set_var( "short_description", "" );
$t->set_var( "description", "" );
$t->set_var( "email_notification", "" );

$t->set_var( "group_notice_name", "" );

$t->set_var( "state_id", "" );

$user = eZUser::currentUser();

if ( !$user )
{
    eZHTTPTool::header( "Location: /user/login" );
    exit();
}

$status_id = 0;

/*
    The user wants to edit an existing person.

    We present a form with the info.
*/

if ( !eZConsultation::belongsTo( $ConsultationID, $user->id() ) )
{
    print( "<h1>Sorry, This page isn't for you. </h1>" );
}
else
{

    $consultation = new eZConsultation( $ConsultationID );

    $t->set_var( "short_description", eZTextTool::htmlspecialchars( $consultation->shortDescription() ) );
    $t->set_var( "description", eZTextTool::htmlspecialchars( $consultation->description() ) );
    $t->set_var( "email_notification", eZTextTool::htmlspecialchars( $consultation->emails() ) );
    $status_id = $consultation->state();

    $companyid = $consultation->company( $user->id() );
    $personid = $consultation->person( $user->id() );
    if ( $companyid )
    {
        $t->parse( "company_contact_item", "company_contact_item_tpl" );
        $t->set_var( "person_contact_item", "" );
        $company = new eZCompany( $companyid );
        $t->set_var( "company_name", eZTextTool::htmlspecialchars( $company->name() ) );
    }
    else if ( $personid )
    {
        $t->set_var( "company_contact_item", "" );
        $t->parse( "person_contact_item", "person_contact_item_tpl" );
        $person = new eZPerson( $personid );
        $t->set_var( "person_lastname", eZTextTool::htmlspecialchars( $person->lastName() ) );
        $t->set_var( "person_firstname", eZTextTool::htmlspecialchars( $person->firstName() ) );
    }

    $t->set_var( "consultation_id", $ConsultationID );

    $t->parse( "consultation_item", "consultation_item_tpl" );

// Create consultation types

    $type = new eZConsultationType( $status_id );
    $t->set_var( "status_name", eZTextTool::htmlspecialchars( $type->name() ) );

// Group list
    $groups = $consultation->groupList();
    foreach( $groups as $group )
        {
            $t->set_var( "group_notice_name", eZTextTool::htmlspecialchars( $group->name() ) );

            $t->parse( "group_notice_select", "group_notice_select_tpl", true );
        }

    if ( count( $groups ) == 0 )
    {
        $t->set_var( "group_notice_select", "" );
        $t->parse( "no_group_notice", "no_group_notice_tpl" );
    }
    else
    {
        $t->set_var( "no_group_notice", "" );
    }


// Template variabler.

    $t->set_var( "action_value", $Action_value );

    $t->pparse( "output", "consultation_view"  );
}

?>
