<?php
/**
 * Hook class for FancyLeftMenu
 */
class ActionsLeftmenu
{
    public $db;
    public $conf;
    public $langs;
    public $user;

    public function __construct($db)
    {
        $this->db = $db;
        global $conf, $langs, $user;
        $this->conf = $conf;
        $this->langs = $langs;
        $this->user = $user;
    }

    /**
     * Hook to replace left menu
     */
    public function printLeftBlock($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $langs, $user, $menumanager;
        
        if (!$conf->leftmenu->enabled) return 0;

        // Get current menu items
        $menuItems = $this->getMenuItems();
        
        // Output our fancy menu
        echo $this->renderFancyMenu($menuItems);
        
        // Hide original menu with CSS
        echo '<style>#id-left { display: none !important; }</style>';
        
        return 1; // Replace original content
    }

    /**
     * Get menu items from Dolibarr
     */
    private function getMenuItems()
    {
        global $menumanager, $user, $conf, $menu_array_before, $menu_array_after;
        
        $menuItems = array();
        
        // Try different ways to get menu items
        $menus_to_check = array();
        
        // Method 1: From menumanager
        if (isset($menumanager->menu) && is_array($menumanager->menu)) {
            $menus_to_check = array_merge($menus_to_check, $menumanager->menu);
        }
        
        // Method 2: From global menu arrays
        if (isset($menu_array_before) && is_array($menu_array_before)) {
            $menus_to_check = array_merge($menus_to_check, $menu_array_before);
        }
        if (isset($menu_array_after) && is_array($menu_array_after)) {
            $menus_to_check = array_merge($menus_to_check, $menu_array_after);
        }
        
        // Method 3: Manually create basic menu items if nothing found
        if (empty($menus_to_check)) {
            $menus_to_check = $this->createBasicMenuItems();
        }
        
        // Process found menus
        foreach ($menus_to_check as $menu) {
            if (is_array($menu) && 
                (!isset($menu['type']) || $menu['type'] == 'left') && 
                (!isset($menu['enabled']) || $menu['enabled'])) {
                
                $menuItems[] = array(
                    'title' => $menu['titre'] ?? $menu['title'] ?? 'Menu Item',
                    'url' => $menu['url'] ?? '#',
                    'mainmenu' => $menu['mainmenu'] ?? 'home',
                    'leftmenu' => $menu['leftmenu'] ?? '',
                    'level' => $menu['level'] ?? 0,
                    'icon' => $this->getIconForMenu($menu['mainmenu'] ?? 'home')
                );
            }
        }
        
        return $this->organizeMenuItems($menuItems);
    }
    
    /**
     * Create basic menu items if none found
     */
    private function createBasicMenuItems()
    {
        global $user, $conf;
        
        $basicMenus = array();
        
        // Home
        $basicMenus[] = array(
            'titre' => 'Home',
            'url' => '/index.php',
            'mainmenu' => 'home',
            'leftmenu' => 'home',
            'level' => 0,
            'enabled' => 1
        );
        
        // Companies (if enabled)
        if (!empty($conf->societe->enabled)) {
            $basicMenus[] = array(
                'titre' => 'Third parties',
                'url' => '/societe/index.php',
                'mainmenu' => 'companies',
                'leftmenu' => 'companies',
                'level' => 0,
                'enabled' => 1
            );
        }
        
        // Products (if enabled)
        if (!empty($conf->product->enabled)) {
            $basicMenus[] = array(
                'titre' => 'Products/Services',
                'url' => '/product/index.php',
                'mainmenu' => 'products',
                'leftmenu' => 'products',
                'level' => 0,
                'enabled' => 1
            );
        }
        
        // Commercial (if enabled)
        if (!empty($conf->propal->enabled) || !empty($conf->commande->enabled)) {
            $basicMenus[] = array(
                'titre' => 'Commercial',
                'url' => '/comm/index.php',
                'mainmenu' => 'commercial',
                'leftmenu' => 'commercial',
                'level' => 0,
                'enabled' => 1
            );
        }
        
        // Billing (if enabled)
        if (!empty($conf->facture->enabled)) {
            $basicMenus[] = array(
                'titre' => 'Billing',
                'url' => '/compta/facture/index.php',
                'mainmenu' => 'billing',
                'leftmenu' => 'billing',
                'level' => 0,
                'enabled' => 1
            );
        }
        
        // Tools (always available for admin)
        if ($user->admin) {
            $basicMenus[] = array(
                'titre' => 'Tools',
                'url' => '/admin/index.php',
                'mainmenu' => 'tools',
                'leftmenu' => 'tools',
                'level' => 0,
                'enabled' => 1
            );
        }
        
        return $basicMenus;
    }
    
    /**
     * Hook to replace left menu - try multiple hook points
     */
    public function printCommonFooter($parameters, &$object, &$action, $hookmanager)
    {
        return $this->printLeftBlock($parameters, $object, $action, $hookmanager);
    }
    
    public function printMainArea($parameters, &$object, &$action, $hookmanager)
    {
        return $this->printLeftBlock($parameters, $object, $action, $hookmanager);
    }
    
    public function doActions($parameters, &$object, &$action, $hookmanager)
    {
        return $this->printLeftBlock($parameters, $object, $action, $hookmanager);
    }
    
    public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        return $this->printLeftBlock($parameters, $object, $action, $hookmanager);
                }
        
        return 0;
    }

    /**
     * Get appropriate icon for menu item
     */
    private function getIconForMenu($mainmenu)
    {
        $icons = array(
            'home' => 'fas fa-home',
            'companies' => 'fas fa-building',
            'commercial' => 'fas fa-handshake',
            'billing' => 'fas fa-file-invoice',
            'products' => 'fas fa-box',
            'projects' => 'fas fa-project-diagram',
            'hrm' => 'fas fa-users',
            'agenda' => 'fas fa-calendar',
            'ftp' => 'fas fa-folder',
            'tools' => 'fas fa-tools',
            'members' => 'fas fa-user-friends',
            'accountancy' => 'fas fa-calculator',
            'bank' => 'fas fa-university',
            'ecm' => 'fas fa-archive',
            'website' => 'fas fa-globe',
            'ticket' => 'fas fa-ticket-alt'
        );
        
        return $icons[$mainmenu] ?? 'fas fa-circle';
    }

    /**
     * Organize menu items into hierarchical structure
     */
    private function organizeMenuItems($items)
    {
        $organized = array();
        $mainMenus = array();
        
        // Group by main menu
        foreach ($items as $item) {
            $mainMenus[$item['mainmenu']][] = $item;
        }
        
        return $mainMenus;
    }

    /**
     * Render the fancy menu HTML
     */
    private function renderFancyMenu($menuItems)
    {
        global $conf, $user;
        
        $theme = $conf->global->FANCY_LEFTMENU_THEME ?? 'dark';
        $collapsed = $conf->global->FANCY_LEFTMENU_COLLAPSED ?? '0';
        
        ob_start();
        ?>
        
        <!-- Fancy Left Menu CSS -->
        <style>
        :root {
            --flm-primary: #2563eb;
            --flm-primary-hover: #1d4ed8;
            --flm-bg: #1f2937;
            --flm-bg-light: #374151;
            --flm-text: #f9fafb;
            --flm-text-muted: #9ca3af;
            --flm-border: #4b5563;
            --flm-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --flm-width: 280px;
            --flm-width-collapsed: 60px;
        }

        .fancy-left-menu {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--flm-width);
            background: var(--flm-bg);
            border-right: 1px solid var(--flm-border);
            box-shadow: var(--flm-shadow);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .fancy-left-menu.collapsed {
            width: var(--flm-width-collapsed);
        }

        .flm-header {
            padding: 1rem;
            border-bottom: 1px solid var(--flm-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .flm-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--flm-text);
            font-weight: 600;
            font-size: 1.125rem;
        }

        .flm-logo-icon {
            width: 32px;
            height: 32px;
            background: var(--flm-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .flm-toggle {
            background: none;
            border: none;
            color: var(--flm-text-muted);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .flm-toggle:hover {
            background: var(--flm-bg-light);
            color: var(--flm-text);
        }

        .flm-user {
            padding: 1rem;
            border-bottom: 1px solid var(--flm-border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .flm-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--flm-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .flm-user-info {
            flex: 1;
            min-width: 0;
        }

        .flm-user-name {
            color: var(--flm-text);
            font-weight: 500;
            font-size: 0.875rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .flm-user-role {
            color: var(--flm-text-muted);
            font-size: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .flm-nav {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        .flm-nav::-webkit-scrollbar {
            width: 4px;
        }

        .flm-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .flm-nav::-webkit-scrollbar-thumb {
            background: var(--flm-border);
            border-radius: 2px;
        }

        .flm-menu-group {
            margin-bottom: 0.5rem;
        }

        .flm-menu-title {
            padding: 0.5rem 1rem;
            color: var(--flm-text-muted);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .flm-menu-item {
            position: relative;
        }

        .flm-menu-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--flm-text-muted);
            text-decoration: none;
            transition: all 0.2s;
            border-radius: 0;
        }

        .flm-menu-link:hover {
            background: var(--flm-bg-light);
            color: var(--flm-text);
            text-decoration: none;
        }

        .flm-menu-link.active {
            background: var(--flm-primary);
            color: white;
        }

        .flm-menu-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: white;
        }

        .flm-menu-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .flm-menu-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .flm-menu-arrow {
            transition: transform 0.2s;
        }

        .flm-menu-item.expanded .flm-menu-arrow {
            transform: rotate(90deg);
        }

        .flm-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .flm-menu-item.expanded .flm-submenu {
            max-height: 500px;
        }

        .flm-submenu-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem 0.5rem 3rem;
            color: var(--flm-text-muted);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .flm-submenu-link:hover {
            background: var(--flm-bg-light);
            color: var(--flm-text);
            text-decoration: none;
        }

        .flm-submenu-link.active {
            color: var(--flm-primary);
        }

        /* Collapsed state */
        .fancy-left-menu.collapsed .flm-logo-text,
        .fancy-left-menu.collapsed .flm-user-info,
        .fancy-left-menu.collapsed .flm-menu-text,
        .fancy-left-menu.collapsed .flm-menu-arrow,
        .fancy-left-menu.collapsed .flm-menu-title {
            opacity: 0;
            visibility: hidden;
        }

        .fancy-left-menu.collapsed .flm-submenu {
            display: none;
        }

        /* Light theme */
        .fancy-left-menu.light {
            --flm-bg: #ffffff;
            --flm-bg-light: #f8fafc;
            --flm-text: #1f2937;
            --flm-text-muted: #6b7280;
            --flm-border: #e5e7eb;
        }

        /* Adjust main content when menu is visible */
        body.flm-active #id-container {
            margin-left: var(--flm-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.flm-active.flm-collapsed #id-container {
            margin-left: var(--flm-width-collapsed);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .fancy-left-menu {
                transform: translateX(-100%);
            }
            
            .fancy-left-menu.mobile-open {
                transform: translateX(0);
            }
            
            body.flm-active #id-container {
                margin-left: 0;
            }
        }
        </style>

        <!-- Fancy Left Menu HTML -->
        <div class="fancy-left-menu <?php echo $theme; ?> <?php echo $collapsed ? 'collapsed' : ''; ?>" id="fancyLeftMenu">
            <!-- Header -->
            <div class="flm-header">
                <div class="flm-logo">
                    <div class="flm-logo-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <span class="flm-logo-text">Dolibarr</span>
                </div>
                <button class="flm-toggle" onclick="toggleFancyMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- User Info -->
            <div class="flm-user">
                <div class="flm-user-avatar">
                    <?php echo strtoupper(substr($user->firstname ?? 'U', 0, 1)); ?>
                </div>
                <div class="flm-user-info">
                    <div class="flm-user-name"><?php echo $user->firstname.' '.$user->lastname; ?></div>
                    <div class="flm-user-role"><?php echo $user->admin ? 'Administrator' : 'User'; ?></div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flm-nav">
                <?php foreach ($menuItems as $mainMenu => $items): ?>
                    <div class="flm-menu-group">
                        <div class="flm-menu-title"><?php echo ucfirst($mainMenu); ?></div>
                        <?php foreach ($items as $item): ?>
                            <?php if ($item['level'] == 0): ?>
                                <div class="flm-menu-item" data-menu="<?php echo $item['leftmenu']; ?>">
                                    <a href="<?php echo DOL_URL_ROOT.$item['url']; ?>" class="flm-menu-link">
                                        <span class="flm-menu-icon">
                                            <i class="<?php echo $item['icon']; ?>"></i>
                                        </span>
                                        <span class="flm-menu-text"><?php echo $item['title']; ?></span>
                                        <?php if ($this->hasSubmenu($items, $item['leftmenu'])): ?>
                                            <i class="fas fa-chevron-right flm-menu-arrow"></i>
                                        <?php endif; ?>
                                    </a>
                                    <?php if ($this->hasSubmenu($items, $item['leftmenu'])): ?>
                                        <div class="flm-submenu">
                                            <?php foreach ($items as $subItem): ?>
                                                <?php if ($subItem['level'] > 0 && strpos($subItem['leftmenu'], $item['leftmenu']) === 0): ?>
                                                    <a href="<?php echo DOL_URL_ROOT.$subItem['url']; ?>" class="flm-submenu-link">
                                                        <?php echo $subItem['title']; ?>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add body class
            document.body.classList.add('flm-active');
            if (document.getElementById('fancyLeftMenu').classList.contains('collapsed')) {
                document.body.classList.add('flm-collapsed');
            }

            // Handle menu item clicks
            document.querySelectorAll('.flm-menu-item').forEach(item => {
                const link = item.querySelector('.flm-menu-link');
                const arrow = item.querySelector('.flm-menu-arrow');
                
                if (arrow) {
                    link.addEventListener('click', function(e) {
                        if (item.querySelector('.flm-submenu')) {
                            e.preventDefault();
                            item.classList.toggle('expanded');
                        }
                    });
                }
            });

            // Set active menu item
            const currentPath = window.location.pathname;
            document.querySelectorAll('.flm-menu-link, .flm-submenu-link').forEach(link => {
                if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
                    // Expand parent if submenu
                    const parentItem = link.closest('.flm-menu-item');
                    if (parentItem) {
                        parentItem.classList.add('expanded');
                    }
                }
            });
        });

        function toggleFancyMenu() {
            const menu = document.getElementById('fancyLeftMenu');
            const body = document.body;
            
            menu.classList.toggle('collapsed');
            body.classList.toggle('flm-collapsed');
            
            // Save state
            const collapsed = menu.classList.contains('collapsed') ? '1' : '0';
            fetch('<?php echo DOL_URL_ROOT; ?>/custom/leftmenu/ajax/save_state.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'collapsed=' + collapsed
            });
        }
        </script>

        <?php
        return ob_get_clean();
    }

    /**
     * Check if menu item has submenu
     */
    private function hasSubmenu($items, $leftmenu)
    {
        foreach ($items as $item) {
            if ($item['level'] > 0 && strpos($item['leftmenu'], $leftmenu) === 0) {
                return true;
            }
        }
        return false;
    }
}