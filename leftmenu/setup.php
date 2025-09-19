<?php
/**
 * Setup page for FancyLeftMenu
 */
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Security check
if (!$user->admin) accessforbidden();

$langs->loadLangs(array("admin", "leftmenu@leftmenu"));

$action = GETPOST('action', 'alpha');

if ($action == 'save') {
    $theme = GETPOST('theme', 'alpha');
    $collapsed = GETPOST('collapsed', 'int') ? '1' : '0';
    
    dolibarr_set_const($db, 'FANCY_LEFTMENU_THEME', $theme, 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'FANCY_LEFTMENU_COLLAPSED', $collapsed, 'chaine', 0, '', $conf->entity);
    
    setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
}

$title = $langs->trans("FancyLeftMenuSetup");
llxHeader('', $title);

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($title, $linkback, 'title_setup');

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print '</tr>';

// Theme selection
print '<tr class="oddeven">';
print '<td>'.$langs->trans("Theme").'</td>';
print '<td>';
print '<select name="theme" class="flat">';
print '<option value="dark"'.($conf->global->FANCY_LEFTMENU_THEME == 'dark' ? ' selected' : '').'>'.$langs->trans("Dark").'</option>';
print '<option value="light"'.($conf->global->FANCY_LEFTMENU_THEME == 'light' ? ' selected' : '').'>'.$langs->trans("Light").'</option>';
print '</select>';
print '</td>';
print '</tr>';

// Default collapsed state
print '<tr class="oddeven">';
print '<td>'.$langs->trans("DefaultCollapsed").'</td>';
print '<td>';
print '<input type="checkbox" name="collapsed" value="1"'.($conf->global->FANCY_LEFTMENU_COLLAPSED ? ' checked' : '').'>';
print '</td>';
print '</tr>';

print '</table>';

print '<div class="center" style="margin-top: 20px;">';
print '<input type="submit" class="button button-save" value="'.$langs->trans("Save").'">';
print '</div>';

print '</form>';

// Preview section
print '<br><hr><br>';
print '<h3>'.$langs->trans("Preview").'</h3>';
print '<div class="info">';
print $langs->trans("PreviewInfo");
print '</div>';

llxFooter();
$db->close();