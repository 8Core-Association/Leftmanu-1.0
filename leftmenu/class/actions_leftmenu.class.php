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
        // This hook might not work, trying different approach
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
        global $conf, $user, $langs;
        
        // Check if module is enabled
        if (empty($conf->leftmenu->enabled)) return 0;
        
        // Debug output
        error_log("FancyLeftMenu: printLeftBlock called");

        // Get Dolibarr menu items or create basic ones
        $menuItems = array(
            array('title' => 'Home', 'url' => DOL_URL_ROOT.'/index.php', 'icon' => '🏠'),
            array('title' => 'Third Parties', 'url' => DOL_URL_ROOT.'/societe/index.php', 'icon' => '🏢'),
            array('title' => 'Products/Services', 'url' => DOL_URL_ROOT.'/product/index.php', 'icon' => '📦'),
            array('title' => 'Commercial', 'url' => DOL_URL_ROOT.'/comm/index.php', 'icon' => '🤝'),
            array('title' => 'Invoices', 'url' => DOL_URL_ROOT.'/compta/facture/list.php', 'icon' => '💰'),
            array('title' => 'Tools', 'url' => DOL_URL_ROOT.'/admin/index.php', 'icon' => '🔧')
        );

        // Try to get real Dolibarr menu
        global $menumanager;
        if (!empty($menumanager) && !empty($menumanager->menu)) {
            $realMenuItems = array();
            foreach ($menumanager->menu as $menu) {
                if (!empty($menu['mainmenu']) && empty($menu['leftmenu'])) {
                    $realMenuItems[] = array(
                        'title' => $menu['titre'] ?? $menu['mainmenu'],
                        'url' => $menu['url'] ?? '#',
                        'icon' => '📋'
                    );
                }
            }
            if (!empty($realMenuItems)) {
                $menuItems = $realMenuItems;
            }
        }

        $theme = !empty($conf->global->FANCY_LEFTMENU_THEME) ? $conf->global->FANCY_LEFTMENU_THEME : 'dark';
        
        echo $this->renderMenu($menuItems, $theme, $user);
        
        // Hide original menu with JavaScript
        echo '<style>
            /* Hide original left menu */
            div#id-left, 
            .side-nav-vert,
            #leftmenu { 
                display: none !important; 
            }
            
            /* Adjust main container */
            #id-container,
            .fiche { 
                margin-left: 280px !important; 
            }
        </style>';
        
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Additional JavaScript hiding
            var leftMenus = document.querySelectorAll("#id-left, .side-nav-vert, #leftmenu");
            leftMenus.forEach(function(menu) {
                if (menu) menu.style.display = "none";
            });
            
            // Adjust container
            var container = document.querySelector("#id-container");
            if (container) {
                container.style.marginLeft = "280px";
            }
        });
        </script>';
        
        return 1;
    }

    private function renderMenu($menuItems, $theme, $user)
    {
        ob_start();
        ?>
        <style>
        .fancy-left-menu {
            position: fixed;
            top: 60px;
            left: 0;
            height: calc(100vh - 60px);
            width: 280px;
            background: <?php echo $theme == 'dark' ? '#1f2937' : '#ffffff'; ?>;
            border-right: 1px solid <?php echo $theme == 'dark' ? '#374151' : '#e5e7eb'; ?>;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
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