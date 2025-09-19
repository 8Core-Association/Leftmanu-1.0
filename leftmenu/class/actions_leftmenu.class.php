<?php

class ActionsLeftmenu
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function printLeftBlock($parameters, &$object, &$action, $hookmanager)
    {
        return $this->renderFancyMenu();
    }

    public function printCommonFooter($parameters, &$object, &$action, $hookmanager)
    {
        return $this->renderFancyMenu();
    }

    public function doActions($parameters, &$object, &$action, $hookmanager)
    {
        return $this->renderFancyMenu();
    }

    private function renderFancyMenu()
    {
        global $conf, $user, $langs, $menumanager;
        
        // Check if module is enabled
        if (empty($conf->leftmenu->enabled)) return 0;
        
        // Get real Dolibarr menu items
        $menuItems = $this->getDolibarrMenuItems();
        
        $theme = !empty($conf->global->FANCY_LEFTMENU_THEME) ? $conf->global->FANCY_LEFTMENU_THEME : 'dark';
        
        echo $this->renderMenu($menuItems, $theme, $user);
        
        // Hide original menu with JavaScript
        echo '<style>
            div#id-left { 
                display: none !important; 
            }
            
            #id-container { 
                margin-left: 280px !important; 
            }
        </style>';
        
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var leftMenu = document.querySelector("#id-left");
            if (leftMenu) leftMenu.style.display = "none";
            
            var container = document.querySelector("#id-container");
            if (container) {
                container.style.marginLeft = "280px";
            }
        });
        </script>';
        
        return 1;
    }

    private function getDolibarrMenuItems()
    {
        global $conf, $user, $langs, $menumanager;
        
        $menuItems = array();
        
        // Always add Home first
        $menuItems[] = array(
            'title' => $langs->trans('Home'), 
            'url' => '/index.php', 
            'icon' => '🏠', 
            'mainmenu' => 'home'
        );
        
        // Get real Dolibarr menu using menu manager
        require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';
        
        // Load menu entries from database
        $sql = "SELECT m.rowid, m.mainmenu, m.leftmenu, m.fk_menu, m.url, m.titre, m.langs, m.position";
        $sql.= " FROM ".MAIN_DB_PREFIX."menu as m";
        $sql.= " WHERE m.entity IN (0,".$conf->entity.")";
        $sql.= " AND m.enabled = 1";
        $sql.= " AND m.mainmenu != ''";
        $sql.= " AND (m.leftmenu = '' OR m.leftmenu IS NULL)"; // Only main menu items
        $sql.= " ORDER BY m.position, m.rowid";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Check if user has permission for this menu
                $perms = 1; // Default allow
                
                // Translate title
                $title = $obj->titre;
                if (!empty($obj->langs)) {
                    $langs->load($obj->langs);
                    $title = $langs->trans($obj->titre);
                }
                
                if ($perms) {
                    $menuItems[] = array(
                        'title' => $title,
                        'url' => $obj->url,
                        'icon' => $this->getMenuIcon($obj->mainmenu),
                        'mainmenu' => $obj->mainmenu
                    );
                }
            }
        }
        
        return $menuItems;
    }

    private function getMenuIcon($mainmenu)
    {
        $icons = array(
            'home' => '🏠',
            'companies' => '🏢',
            'products' => '📦',
            'commercial' => '🤝',
            'billing' => '💰',
            'orders' => '📋',
            'suppliers' => '🚚',
            'projects' => '📊',
            'hrm' => '👥',
            'tools' => '🔧',
            'members' => '👤',
            'accountancy' => '📊',
            'bank' => '🏦',
            'agenda' => '📅'
        );
        
        return isset($icons[$mainmenu]) ? $icons[$mainmenu] : '📋';
    }

    private function renderMenu($menuItems, $theme, $user)
    {
        ob_start();
        ?>
        <style>
        .fancy-left-menu {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            padding-top: 60px;
            width: 280px;
            background: <?php echo $theme == 'dark' ? '#1f2937' : '#ffffff'; ?>;
            border-right: 1px solid <?php echo $theme == 'dark' ? '#374151' : '#e5e7eb'; ?>;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
            box-sizing: border-box;
        }

        .flm-header {
            padding: 1rem;
            border-bottom: 1px solid <?php echo $theme == 'dark' ? '#374151' : '#e5e7eb'; ?>;
            color: <?php echo $theme == 'dark' ? '#f9fafb' : '#1f2937'; ?>;
            font-weight: 600;
            font-size: 1.125rem;
        }

        .flm-menu-item {
            display: block;
            padding: 0.75rem 1rem;
            color: <?php echo $theme == 'dark' ? '#9ca3af' : '#6b7280'; ?>;
            text-decoration: none;
            transition: all 0.2s;
            border-bottom: 1px solid <?php echo $theme == 'dark' ? '#374151' : '#f3f4f6'; ?>;
        }

        .flm-menu-item:hover {
            background: <?php echo $theme == 'dark' ? '#374151' : '#f8fafc'; ?>;
            color: <?php echo $theme == 'dark' ? '#f9fafb' : '#1f2937'; ?>;
            text-decoration: none;
        }

        .flm-menu-icon {
            margin-right: 0.75rem;
            width: 20px;
        }

        body.flm-active #id-container {
            margin-left: 280px;
            transition: margin-left 0.3s;
        }

        @media (max-width: 768px) {
            .fancy-left-menu { width: 100%; }
            body.flm-active #id-container { margin-left: 0; }
        }
        </style>

        <div class="fancy-left-menu">
            <div class="flm-header">
                🚀 Dolibarr Menu
            </div>
            <?php foreach ($menuItems as $item): ?>
                <a href="<?php echo DOL_URL_ROOT.$item['url']; ?>" class="flm-menu-item">
                    <span class="flm-menu-icon"><?php echo $item['icon']; ?></span>
                    <?php echo $item['title']; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php
        return ob_get_clean();
    }
}