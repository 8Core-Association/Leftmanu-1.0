<?php
require_once '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

if (!$user->admin) {
    accessforbidden();
}

$action = GETPOST('action', 'alpha');

if ($action == 'save') {
    $theme = GETPOST('theme', 'alpha');
    dolibarr_set_const($db, 'FANCY_LEFTMENU_THEME', $theme, 'chaine', 0, '', $conf->entity);
    setEventMessages("Settings saved", null, 'mesgs');
}

$title = "Fancy Left Menu Setup";
llxHeader('', $title);

print '<h1>Fancy Left Menu Setup</h1>';

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>Parameter</td>';
print '<td>Value</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>Theme</td>';
print '<td>';
print '<select name="theme" class="flat">';
$current_theme = !empty($conf->global->FANCY_LEFTMENU_THEME) ? $conf->global->FANCY_LEFTMENU_THEME : 'dark';
print '<option value="dark"'.($current_theme == 'dark' ? ' selected' : '').'>Dark</option>';
print '<option value="light"'.($current_theme == 'light' ? ' selected' : '').'>Light</option>';
print '</select>';
print '</td>';
print '</tr>';

print '</table>';

print '<div class="center" style="margin-top: 20px;">';
print '<input type="submit" class="button button-save" value="Save">';
print '</div>';

print '</form>';

llxFooter();
$db->close();