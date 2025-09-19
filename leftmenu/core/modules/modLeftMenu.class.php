<?php
/* FancyLeftMenu module descriptor */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

class modLeftMenu extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs, $conf;
        $this->db = $db;

        $this->numero       = 129901;
        $this->rights_class = 'leftmenu';
        $this->family       = "interface";
        $this->name         = "FancyLeftMenu";
        $this->description  = "Modern, fancy replacement for Dolibarr left menu";
        $this->version      = '1.0.0';
        $this->const_name   = 'MAIN_MODULE_LEFTMENU';
        $this->editor_name  = '8Core Association';
        $this->editor_url   = 'https://8core.hr';
        $this->picto        = 'generic';

        // Module parts - enable hooks
        $this->module_parts = array(
            'hooks' => array('commonobject', 'main')
        );

        // Dirs
        $this->dirs = array();
        
        // Config page - direct URL
        $this->config_page_url = array("setup.php@leftmenu");

        // Rights
        $this->rights = array();
        $r=0;
        $this->rights[$r][0] = 12990101;
        $this->rights[$r][1] = 'Use FancyLeftMenu';
        $this->rights[$r][2] = 'r';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'read';

        // Configuration constants
        $this->const = array();
        $this->const[0] = array(
            'FANCY_LEFTMENU_THEME',
            'chaine',
            'dark',
            'Default theme for fancy left menu',
            0
        );
    }

    public function init($options = '')
    {
        $sql = array();
        return $this->_init($sql, $options);
    }

    public function remove($options = '')
    {
        $sql = array();
        return $this->_remove($sql, $options);
    }
}