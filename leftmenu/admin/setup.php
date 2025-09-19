<?php
/**
 * Setup page for FancyLeftMenu
 */
require_once '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Security check
if (!$user->admin) {
    accessforbidden();
}

$langs->loadLangs(array("admin"));
$langs->load("leftmenu@leftmenu");

$action = GETPOST('action', 'alpha');

if ($action == 'save') {
    $theme = GETPOST('theme', 'alpha');
    $collapsed = GETPOST('collapsed', 'int') ? '1' : '0';
    
    dolibarr_set_const($db, 'FANCY_LEFTMENU_THEME', $theme, 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'FANCY_LEFTMENU_COLLAPSED', $collapsed, 'chaine', 0, '', $conf->entity);
    
    setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
}

$title = "Fancy Left Menu Setup";
llxHeader('', $title);

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php">Back to module list</a>';
print load_fiche_titre($title, $linkback, 'title_setup');

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>Parameter</td>';
print '<td>Value</td>';
print '</tr>';

// Theme selection
print '<tr class="oddeven">';
print '<td>Theme</td>';
print '<td>';
print '<select name="theme" class="flat">';
print '<option value="dark"'.(!empty($conf->global->FANCY_LEFTMENU_THEME) && $conf->global->FANCY_LEFTMENU_THEME == 'dark' ? ' selected' : '').'>Dark</option>';
print '<option value="light"'.(!empty($conf->global->FANCY_LEFTMENU_THEME) && $conf->global->FANCY_LEFTMENU_THEME == 'light' ? ' selected' : '').'>Light</option>';
print '</select>';
print '</td>';
print '</tr>';

// Default collapsed state
print '<tr class="oddeven">';
print '<td>Default Collapsed</td>';
print '<td>';
print '<input type="checkbox" name="collapsed" value="1"'.(!empty($conf->global->FANCY_LEFTMENU_COLLAPSED) && $conf->global->FANCY_LEFTMENU_COLLAPSED ? ' checked' : '').'>';
print '</td>';
print '</tr>';

print '</table>';

print '<div class="center" style="margin-top: 20px;">';
print '<input type="submit" class="button button-save" value="Save">';
print '</div>';

print '</form>';

// Preview section
print '<br><hr><br>';
print '<h3>Preview</h3>';
print '<div class="info">';
print 'After saving settings, go to any page to see the new menu in action.';
print '</div>';

llxFooter();
$db->close();