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

        // Get real Dolibarr menu items
        $menuItems = $this->getDolibarrMenuItems();
        
        $theme = !empty($conf->global->FANCY_LEFTMENU_THEME) ? $conf->global->FANCY_LEFTMENU_THEME : 'dark';
        
        echo $this->renderMenu($menuItems, $theme, $user);
        
        // Hide original menu with JavaScript
        echo '<style>
            /* Hide only original left menu, NOT top menu */
            div#id-left { 
                display: none !important; 
            }
            
            /* Adjust main content container */
            #id-container { 
                margin-left: 280px !important; 
            }
        </style>';
        
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Hide only left menu, preserve top menu
            var leftMenu = document.querySelector("#id-left");
            if (leftMenu) leftMenu.style.display = "none";
            
            // Adjust container
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
        
        // Try to get menu from global menumanager
        global $menumanager;
        if (!empty($menumanager) && is_object($menumanager) && !empty($menumanager->menu)) {
            foreach ($menumanager->menu as $menuentry) {
                // Get main menu items (level 0)
                if (!empty($menuentry['mainmenu']) && empty($menuentry['leftmenu']) && $menuentry['level'] == 0) {
                    $menuItems[] = array(
                        'title' => $langs->trans($menuentry['titre']) ?: $menuentry['titre'],
                        'url' => $menuentry['url'],
                        'icon' => $this->getMenuIcon($menuentry['mainmenu']),
                        'mainmenu' => $menuentry['mainmenu']
                    );
                }
            }
        }
        
        // If no menu found, try alternative approach
        if (empty($menuItems)) {
            // Include menu manager
            require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';
            
            // Get enabled modules and create menu based on them
            if (!empty($conf->societe->enabled)) {
                $menuItems[] = array('title' => $langs->trans('ThirdParties'), 'url' => DOL_URL_ROOT.'/societe/index.php', 'icon' => '🏢', 'mainmenu' => 'companies');
            }
            if (!empty($conf->product->enabled) || !empty($conf->service->enabled)) {
                $menuItems[] = array('title' => $langs->trans('Products'), 'url' => DOL_URL_ROOT.'/product/index.php', 'icon' => '📦', 'mainmenu' => 'products');
            }
            if (!empty($conf->facture->enabled)) {
                $menuItems[] = array('title' => $langs->trans('Invoices'), 'url' => DOL_URL_ROOT.'/compta/facture/list.php', 'icon' => '💰', 'mainmenu' => 'billing');
            }
            if (!empty($conf->commande->enabled)) {
                $menuItems[] = array('title' => $langs->trans('Orders'), 'url' => DOL_URL_ROOT.'/commande/list.php', 'icon' => '📋', 'mainmenu' => 'orders');
            }
            if (!empty($conf->projet->enabled)) {
                $menuItems[] = array('title' => $langs->trans('Projects'), 'url' => DOL_URL_ROOT.'/projet/index.php', 'icon' => '📊', 'mainmenu' => 'project');
            }
            if ($user->admin) {
                $menuItems[] = array('title' => $langs->trans('Tools'), 'url' => DOL_URL_ROOT.'/admin/index.php', 'icon' => '🔧', 'mainmenu' => 'tools');
            }
        }
        
        // Always add Home at the beginning
        array_unshift($menuItems, array('title' => $langs->trans('Home'), 'url' => DOL_URL_ROOT.'/index.php', 'icon' => '🏠', 'mainmenu' => 'home'));
        
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