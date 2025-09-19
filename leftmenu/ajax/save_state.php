<?php
/**
 * Save menu state via AJAX
 */
require '../../../main.inc.php';

if (!$user->rights->leftmenu->read) {
    http_response_code(403);
    exit;
}

if ($_POST['collapsed']) {
    $collapsed = $_POST['collapsed'] == '1' ? '1' : '0';
    
    // Save to user preferences or global config
    dolibarr_set_const($db, 'FANCY_LEFTMENU_COLLAPSED', $collapsed, 'chaine', 0, '', $conf->entity);
    
    echo json_encode(['success' => true]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}