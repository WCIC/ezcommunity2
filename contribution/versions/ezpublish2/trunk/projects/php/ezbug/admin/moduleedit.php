<?
/*
  Edit a module type.
*/

include_once( "classes/INIFile.php" );
include_once( "ezuser/classes/ezpermission.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );

$ini = new INIFIle( "site.ini" );
$Language = $ini->read_var( "eZBugMain", "Language" );

include_once( "classes/eztemplate.php" );
include_once( "ezuser/classes/ezusergroup.php" );
include_once( "ezbug/classes/ezbugmodule.php" );

if ( $Action == "insert" )
{
    $module = new eZBugModule();
    $module->setName( $Name );
    $parent = new eZBugModule( $ParentID );
    $module->setParent( $parent );
    $module->store();

    Header( "Location: /bug/module/list/" );
    exit();
}

// Updates a module.
if ( $Action == "update" )
{
    $module = new eZBugModule( $ModuleID );
    $module->setName( $Name );
    $parent = new eZBugModule( $ParentID );
    $module->setParent( $parent );
//    $ownerGroup = new eZUserGroup( $OwnerID );

    if( isset( $Recursive ) )
    {
        $recursiveList = $module->getByParent( $module, "name", array() );
        
        foreach( $recursiveList as $itemID )
        {
            eZObjectPermission::removePermissions( $itemID, "bug_module", "w" );
            if ( count ( $WriteGroupArrayID ) > 0 )
            {
                foreach ( $WriteGroupArrayID as $Write )
                {
                    if ( $Write == -1 )
                        $group = -1;
                    else
                        $group = new eZUserGroup( $Write );

                    eZObjectPermission::setPermission( $group, $itemID, "bug_module", "w" );
                }
            }
        }
    }
    else
    {
        eZObjectPermission::removePermissions( $ModuleID, "bug_module", "w" );
        eZObjectPermission::setPermission( $WriteGroupArrayID[0], $ModuleID, "bug_module", 'w' );
    }

    $module->store();

    Header( "Location: /bug/module/list/" );
    exit();
}

// Delete a module.
if ( $Action == "delete" )
{
    $module = new eZBugModule( $ModuleID );
    $module->delete();

    Header( "Location: /bug/module/list/" );
    exit();
}

$t = new eZTemplate( "ezbug/admin/" . $ini->read_var( "eZBugMain", "AdminTemplateDir" ),
                     "ezbug/admin/intl", $Language, "moduleedit.php" );
$t->setAllStrings();

$t->set_file( array(
    "moduleedit" => "moduleedit.tpl"
    ) );

$t->set_block( "moduleedit", "module_item_tpl", "module_item" );
$t->set_block( "moduleedit", "module_owner_tpl", "module_owner" );

$t->set_block( "moduleedit", "write_group_item_tpl", "write_group_item" );

if ( $Action == "new" )
{
    $parent = new eZBugModule( $ParentID );
    $t->set_var( "module_name", "" );
    $t->set_var( "action_value", "insert" );
}

// Edit a module.
if ( $Action == "edit" )
{
    $module = new eZBugModule( $ModuleID );

    $parent = $module->parent();
    $t->set_var( "module_name", $module->name() );
    $t->set_var( "module_id", $module->id() );

    $writeGroupArrayID =& eZObjectPermission::getGroups( $module->id(), "bug_module", "w", false );

    $t->set_var( "action_value", "update" );
}

// Category selector

$module = new eZBugModule();

$moduleList = $module->getAll();

foreach( $moduleList as $moduleItem )
{
    $t->set_var( "module_parent_name", $moduleItem->name() );
    $t->set_var( "module_parent_id", $moduleItem->id() );


    if ( get_class( $parent ) == "ezbugmodule" )
    {
        if ( $parent->id() == $moduleItem->id() )
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
        

    $t->parse( "module_item", "module_item_tpl", true );
}

// group selector
$group = new eZUserGroup();
$groupList =& $user->groups();

foreach( $groupList as $groupItem )
{
    $t->set_var( "group_id", $groupItem->id() );
    $t->set_var( "group_name", $groupItem->name() );

    $t->set_var( "is_write_selected1", "" );
    
    if ( $writeGroupArrayID )
    {
        foreach ( $writeGroupArrayID as $writeGroup )
        {
            
            if ( $writeGroup == $groupItem->id() )
            {
                $t->set_var( "is_write_selected1", "selected" );
            }
            elseif ( $writeGroup == -1 )
            {
                $t->set_var( "write_everybody", "selected" );
            }
            else
            {
                $t->set_var( "is_write_selected", "" );
            }
        }
    }
        
    $t->parse( "write_group_item", "write_group_item_tpl", true );
}

$t->pparse( "output", "moduleedit" );
?>
