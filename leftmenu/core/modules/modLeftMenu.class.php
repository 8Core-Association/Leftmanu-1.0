<?php
/* LeftMenu module descriptor */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

class modLeftMenu extends DolibarrModules
{
  public function __construct($db)
  {
    global $langs;
    $this->db = $db;

    $this->numero       = 129901;                       // Unique ID
    $this->rights_class = 'leftmenu';
    $this->family       = "ECM";
    $this->name         = "LeftMenu";
    $this->description  = "Lightweight left-side menu (scoped UI)";
    $this->version      = '1.0.0';
    $this->const_name   = 'MAIN_MODULE_LEFTMENU';
    $this->editor_name  = '8Core Association';
    $this->editor_url   = 'https://8core.hr';
    $this->picto        = 'generic';

    $this->module_parts = array();

    // Rights
    $this->rights = array();
    $r=0;
    $this->rights[$r][0] = 12990101;               // id
    $this->rights[$r][1] = 'Read LeftMenu';        // label
    $this->rights[$r][2] = 'r';                    // type
    $this->rights[$r][3] = 1;                      // default
    $this->rights[$r][4] = 'read';                 // perm code
    $r++;

    // Menus
    $this->menu = array();

    // Top menu
    $this->menu[] = array(
      'fk_menu'  => 0,
      'type'     => 'top',
      'titre'    => 'LeftMenu',
      'mainmenu' => 'leftmenu',
      'leftmenu' => '',
      'url'      => '/custom/leftmenu/index.php',
      'langs'    => 'leftmenu@leftmenu',
      'position' => 110,
      'enabled'  => '1',
      'perms'    => '$user->rights->leftmenu->read'
    );

    // Left menus
    $this->menu[] = array(
      'fk_menu'  => 'fk_mainmenu=leftmenu',
      'type'     => 'left',
      'titre'    => 'Pregled',
      'mainmenu' => 'leftmenu',
      'leftmenu' => 'lm_home',
      'url'      => '/custom/leftmenu/index.php',
      'position' => 10,
      'enabled'  => '1',
      'perms'    => '$user->rights->leftmenu->read'
    );
    $this->menu[] = array(
      'fk_menu'  => 'fk_mainmenu=leftmenu',
      'type'     => 'left',
      'titre'    => 'Stavke',
      'mainmenu' => 'leftmenu',
      'leftmenu' => 'lm_items',
      'url'      => '/custom/leftmenu/items.php',
      'position' => 20,
      'enabled'  => '1',
      'perms'    => '$user->rights->leftmenu->read'
    );
    $this->menu[] = array(
      'fk_menu'  => 'fk_mainmenu=leftmenu',
      'type'     => 'left',
      'titre'    => 'Postavke',
      'mainmenu' => 'leftmenu',
      'leftmenu' => 'lm_settings',
      'url'      => '/custom/leftmenu/settings.php',
      'position' => 30,
      'enabled'  => '1',
      'perms'    => '$user->rights->leftmenu->read'
    );
  }
}