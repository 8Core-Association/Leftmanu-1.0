<?php
/**
 * AJAX endpoint to save menu state
 */
require_once '../../../main.inc.php';

// Security check
if (!$user->rights->leftmenu->read) {
    http_response_code(403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collapsed = GETPOST('collapsed', 'alpha');
    
    if ($collapsed === '1' || $collapsed === '0') {
        dolibarr_set_const($db, 'FANCY_LEFTMENU_COLLAPSED', $collapsed, 'chaine', 0, '', $conf->entity);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid parameter']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}