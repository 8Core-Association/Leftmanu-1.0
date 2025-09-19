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
        global $conf, $user, $langs;
        
        // Check if module is enabled
        if (empty($conf->leftmenu->enabled)) return 0;
        
        // Debug output
        error_log("FancyLeftMenu: printLeftBlock called");

        // Simple menu items
        $menuItems = array(
            array('title' => 'Home', 'url' => '/index.php', 'icon' => 'fas fa-home'),
            array('title' => 'Companies', 'url' => '/societe/index.php', 'icon' => '🏢'),
            array('title' => 'Products', 'url' => '/product/index.php', 'icon' => '📦'),
            array('title' => 'Commercial', 'url' => '/comm/index.php', 'icon' => '🤝'),
            array('title' => 'Billing', 'url' => '/compta/facture/index.php', 'icon' => '💰'),
            array('title' => 'Tools', 'url' => '/admin/index.php', 'icon' => '🔧')
        );

        $theme = !empty($conf->global->FANCY_LEFTMENU_THEME) ? $conf->global->FANCY_LEFTMENU_THEME : 'dark';
        
        echo $this->renderMenu($menuItems, $theme, $user);
        
        // Hide original menu
        echo '<style>
            #id-left { display: none !important; }
            .side-nav-vert { display: none !important; }
            #id-container { margin-left: 280px !important; }
        </style>';
        
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

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('flm-active');
        });
        </script>
        <?php
        return ob_get_clean();
    }
}