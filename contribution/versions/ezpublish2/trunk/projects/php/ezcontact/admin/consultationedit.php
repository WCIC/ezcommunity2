<?
/*
    Edit a consultation
 */
 
include_once( "classes/INIFile.php" );

$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZContactMain", "Language" );

include_once( "classes/ezmail.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezdatetime.php" );
include_once( "ezcontact/classes/ezconsultation.php" );

if( $Action == "delete" )
{
    $consultation = new eZConsultation( $ConsultationID );
    $person = $consultation->person();
    $company = $consultation->person();
    $consultation->delete();

    if ( isset( $contact_type ) && isset( $contact_id ) )
    {
        header( "Location: /contact/consultation/$contact_type/list/$contact_id" );
    }
    else
    {
        header( "Location: /contact/consultation/list" );
    }
    exit;
}

   
include_once( "ezcontact/classes/ezconsultationtype.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezusergroup.php" );

$error = false;

$t = new eZTemplate( "ezcontact/admin/" . $ini->read_var( "eZContactMain", "AdminTemplateDir" ),
                     "ezcontact/admin/intl", $Language, "consultationedit.php" );
$t->setAllStrings();

$t->set_file( array(
    "consultation_edit" => "consultationedit.tpl"
    ) );
$t->set_block( "consultation_edit", "consultation_item_tpl", "consultation_item" );

$t->set_block( "consultation_item_tpl", "consultation_date_item_tpl", "consultation_date_item" );
$t->set_block( "consultation_item_tpl", "group_notice_select_tpl", "group_notice_select" );

$t->set_block( "consultation_item_tpl", "no_status_item_tpl", "no_status_item" );
$t->set_block( "consultation_item_tpl", "status_item_tpl", "status_item" );
$t->set_block( "status_item_tpl", "status_select_tpl", "status_select" );

$t->set_block( "consultation_edit", "contact_item_tpl", "contact_item" );
$t->set_block( "contact_item_tpl", "company_contact_select_tpl", "company_contact_select" );
$t->set_block( "contact_item_tpl", "person_contact_select_tpl", "person_contact_select" );

$t->set_block( "consultation_edit", "company_contact_item_tpl", "company_contact_item" );
$t->set_block( "consultation_edit", "person_contact_item_tpl", "person_contact_item" );

$t->set_block( "consultation_edit", "hidden_company_contact_item_tpl", "hidden_company_contact_item" );
$t->set_block( "consultation_edit", "hidden_person_contact_item_tpl", "hidden_person_contact_item" );

$t->set_block( "consultation_edit", "errors_tpl", "errors_item" );

$t->set_block( "errors_tpl", "error_company_person_item_tpl", "error_company_person_item" );
$t->set_block( "errors_tpl", "error_no_company_person_item_tpl", "error_no_company_person_item" );
$t->set_block( "errors_tpl", "error_no_status_item_tpl", "error_no_status_item" );
$t->set_block( "errors_tpl", "error_short_description_item_tpl", "error_short_description_item" );
$t->set_block( "errors_tpl", "error_description_item_tpl", "error_description_item" );
$t->set_block( "errors_tpl", "error_email_notice_item_tpl", "error_email_notice_item" );

$t->set_var( "consultation_date", "" );
$t->set_var( "consultation_date_item", "" );

$t->set_var( "short_description", "" );
$t->set_var( "description", "" );
$t->set_var( "email_notification", "" );

$t->set_var( "group_notice_id", "" );
$t->set_var( "is_selected", "" );
$t->set_var( "group_notice_name", "" );

$t->set_var( "hidden_company_contact_item", "" );
$t->set_var( "hidden_person_contact_item", "" );

$t->set_var( "state_id", "" );

if( $Action == "insert" || $Action == "update" )
{
    $t->set_var( "error_company_person_item", "" );
    $t->set_var( "error_no_company_person_item", "" );
    $t->set_var( "error_no_status_item", "" );
    $t->set_var( "error_short_description_item", "" );
    $t->set_var( "error_description_item", "" );
    $t->set_var( "error_email_notice_item", "" );

    if( isset( $PersonContact ) && isset( $CompanyContact ) )
    {
        $t->parse( "error_company_person_item", "error_company_person_item_tpl" );
        $error = true;
    }

    if( !isset( $PersonContact ) && !isset( $CompanyContact ) )
    {
        $t->parse( "error_no_company_person_item", "error_no_company_person_item_tpl" );
        $error = true;
    }

    if( $StatusID == -1 )
    {
        $t->parse( "error_no_status_item", "error_no_status_item_tpl" );
        $error = true;
    }

    if( empty( $ShortDescription ) )
    {
        $t->parse( "error_short_description_item", "error_short_description_item_tpl" );
        $error = true;
    }

    if( empty( $Description ) )
    {
        $t->parse( "error_description_item", "error_description_item_tpl" );
        $error = true;
    }

//      if( empty( $EmailNotice ) )
//      {
//          $t->parse( "error_email_notification_item", "error_email_notificiation_item_tpl" );
//          $error = true;
//      }

//      if( empty( $GroupNotice[] ) )
//      {
//          $t->parse( "error_group_notification_item", "error_group_notification_item_tpl" );
//          $error = true;
//      }
        
    if( $error == true )
    {
        $t->parse( "errors_item", "errors_tpl" );
   }
}

if( $error == false )
{
    $t->set_var( "errors_item", "" );
}
else
{
    $Action = "formdata";
}

$user = eZUser::currentUser();

if( ( $Action == "insert" || $Action == "update" ) && $error == false )
{
    if ( $ConsultationID > 0 )
        $consultation = new eZConsultation( $ConsultationID );
    else
        $consultation = new eZConsultation();
    $consultation->setShortDescription( $ShortDescription );
    $consultation->setDescription( $Description );
    $consultation->setDate( new eZDateTime() );
    $consultation->setState( $StatusID );
    $consultation->setEmail( $EmailNotice );

    $consultation->store();

    if ( isset( $CompanyContact ) )
    {
        $contact_type = "company";
        $contact_id = $CompanyContact;
        $consultation->addConsultationToCompany( $CompanyContact, $user->id() );
    }
    else if ( isset( $PersonContact ) )
    {
        $contact_type = "person";
        $contact_id = $PersonContact;
        $consultation->addConsultationToPerson( $PersonContact, $user->id() );
    }

    $consultation->removeGroups();
    foreach( $GroupNotice as $group )
        {
            $consultation->addGroup( $group );
        }

    $ConsultationID = $consultation->id();

    $t->set_var( "consultation_id", $ConsultationID );

    if ( isset( $contact_type ) && isset( $contact_id ) )
    {
        header( "Location: /contact/consultation/$contact_type/list/$contact_id" );
    }
    else
    {
        header( "Location: /contact/consultation/list" );
    }
    exit();
}

/*
    The user wants to create a new consultation.

    We present an empty form.
 */
if( $Action == "new" )
{
    if( $ConsultationID != 0 ) // 1
    {
        header( "Location: /contact/consultation/edit/$ConsultationID" );
        exit();
    }

    $Action_value = "insert";
    $t->set_var( "consultation_id", "0" );
    $t->parse( "consultation_item", "consultation_item_tpl" );
}

$status_id = 0;
$groups = array();

/*
    The user wants to edit an existing person.
    
    We present a form with the info.
 */
if( $Action == "edit" )
{
    $Action_value = "update";
    $consultation = new eZConsultation( $ConsultationID );

    $t->set_var( "short_description", $consultation->shortDescription() );
    $t->set_var( "description", $consultation->description() );
    $t->set_var( "email_notification", $consultation->emails() );
    $status_id = $consultation->state();

    $companyid = $consultation->company( $user->id() );
    $personid = $consultation->person( $user->id() );
    if ( $companyid )
    {
        $CompanyID = $companyid;
        $t->set_var( "company_contact", $CompanyID );
        $t->parse( "hidden_company_contact_item", "hidden_company_contact_item_tpl" );
        $t->set_var( "hidden_person_contact_item", "" );
    }
    else if ( $personid )
    {
        $PersonID = $personid;
        $t->set_var( "person_contact", $PersonID );
        $t->parse( "hidden_person_contact_item", "hidden_person_contact_item_tpl" );
        $t->set_var( "hidden_company_contact_item", "" );
    }

    $groups = $consultation->groupIDList();

    $t->set_var( "consultation_id", $ConsultationID );

    $t->parse( "consultation_item", "consultation_item_tpl" );
}

if( $Action == "formdata" )
{
    $Action_value = "insert";
    $t->set_var( "short_description", $ShortDescription );
    $t->set_var( "description", $Description );
    $t->set_var( "email_notification", $EmailNotice );
    $status_id = $StatusID;
    $groups = $GroupNotice;
    if ( !isset( $groups ) )
        $groups = array();

    // Group list here

    $t->parse( "consultation_item", "consultation_item_tpl" );
}

if ( !( isset( $CompanyID ) || isset( $PersonID ) ) )
{
    $company = new eZCompany();
    $companies = $company->getAll();
    foreach( $companies as $company )
        {
            $t->set_var( "contact_id", $company->id() );
            $t->set_var( "contact_name", $company->name() );
            if ( $CompanyContact == $company->id() )
                $t->set_var( "selected", "selected" );
            else
                $t->set_var( "selected", "" );

            $t->parse( "company_contact_select", "company_contact_select_tpl", true );
        }

    $person = new eZPerson();
    $persons = $person->getAll();
    foreach( $persons as $person )
        {
            $t->set_var( "contact_id", $person->id() );
            $t->set_var( "contact_firstname", $person->firstName() );
            $t->set_var( "contact_lastname", $person->lastName() );
            if ( $PersonContact == $person->id() )
                $t->set_var( "selected", "selected" );
            else
                $t->set_var( "selected", "" );

            $t->parse( "person_contact_select", "person_contact_select_tpl", true );
        }

    $t->parse( "contact_item", "contact_item_tpl" );
    $t->set_var( "company_contact_item", "" );
    $t->set_var( "person_contact_item", "" );
}
else
{
    $t->set_var( "contact_item" );
    if ( isset( $CompanyID ) )
    {
        $t->parse( "company_contact_item", "company_contact_item_tpl" );
        $t->set_var( "person_contact_item", "" );
        $company = new eZCompany( $CompanyID );
        $t->set_var( "company_name", $company->name() );
    }
    else if ( isset( $PersonID ) )
    {
        $t->parse( "person_contact_item", "person_contact_item_tpl" );
        $t->set_var( "company_contact_item", "" );
        $person = new eZPerson( $PersonID );
        $t->set_var( "person_firstname", $person->firstName() );
        $t->set_var( "person_lastname", $person->lastName() );
    }
    else
    {
    }
}

// Create consultation types

$types = eZConsultationType::findTypes();
if ( count( $types ) > 0 )
{
    foreach ( $types as $type )
        {
            $t->set_var( "status_id", $type->id() );
            if ( $type->id() == $status_id )
                $t->set_var( "selected", "selected" );
            else
                $t->set_var( "selected", "" );
            $t->set_var( "status_name", $type->name() );

            $t->parse( "status_select", "status_select_tpl", true );
        }
    $t->parse( "status_item", "status_item_tpl" );
    $t->set_var( "no_status_item", "" );
}
else
{
    $t->parse( "no_status_item", "no_status_item_tpl" );
    $t->set_var( "status_item", "" );
}

// Group list
$group = new eZUserGroup();
$group_list = $group->getAll();
foreach( $group_list as $group_item )
{
    if ( in_array( $group_item->id(), $groups ) )
        $t->set_var( "is_selected", "selected" );
    else
        $t->set_var( "is_selected", "" );
    $t->set_var( "group_notice_id", $group_item->id() );
    $t->set_var( "group_notice_name", $group_item->name() );

    $t->parse( "group_notice_select", "group_notice_select_tpl", true );
}


// Template variabler.

$t->set_var( "action_value", $Action_value );

$t->pparse( "output", "consultation_edit"  );


?>
