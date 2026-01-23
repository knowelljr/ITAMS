<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>ITAMS</title>
    <style>
        :root {
            /* Professional Navy & Teal Theme */
            --primary: #0c4a6e; /* Deep navy blue */
            --primary-light: #0369a1;
            --primary-dark: #082f49;
            --accent: #0891b2; /* Teal/Cyan */
            --accent-light: #06b6d4;
            --accent-dark: #0e7490;
            
            /* Backgrounds */
            --bg: #f8fafc; /* Cool gray light */
            --bg-secondary: #f1f5f9;
            --card-bg: #ffffff;
            --nav-bg: #ffffff;
            --footer-bg: #ffffff;
            --sidebar-bg: linear-gradient(180deg, rgba(12, 74, 110, 0.95) 0%, rgba(8, 47, 73, 0.95) 100%);
            
            /* Text */
            --text: #0f172a; /* Slate 900 */
            --text-secondary: #475569; /* Slate 600 */
            --text-muted: #64748b; /* Slate 500 */
            --nav-text: #334155;
            
            /* Borders & Dividers */
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --footer-border: #e2e8f0;
            
            /* Status Colors */
            --success: #059669; /* Emerald */
            --success-light: #10b981;
            --warning: #d97706; /* Amber */
            --warning-light: #f59e0b;
            --error: #dc2626; /* Red */
            --error-light: #ef4444;
            --info: #0284c7; /* Sky blue */
            --info-light: #0ea5e9;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --alert-shadow: rgba(12, 74, 110, 0.15);
        }
        
        html.dark {
            /* Dark Mode Palette */
            --primary: #0ea5e9; /* Lighter cyan for visibility */
            --primary-light: #38bdf8;
            --primary-dark: #0284c7;
            --accent: #06b6d4;
            --accent-light: #22d3ee;
            --accent-dark: #0891b2;
            
            /* Dark Backgrounds */
            --bg: #0f172a; /* Slate 900 */
            --bg-secondary: #1e293b; /* Slate 800 */
            --card-bg: #1e293b;
            --nav-bg: #0f172a;
            --footer-bg: #0f172a;
            --sidebar-bg: linear-gradient(180deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%);
            
            /* Dark Text */
            --text: #f1f5f9; /* Slate 100 */
            --text-secondary: #cbd5e1; /* Slate 300 */
            --text-muted: #94a3b8; /* Slate 400 */
            --nav-text: #e2e8f0;
            
            /* Dark Borders */
            --border: #334155; /* Slate 700 */
            --border-light: #475569;
            --footer-border: #1e293b;
            
            /* Dark Status Colors (slightly lighter) */
            --success: #10b981;
            --success-light: #34d399;
            --warning: #f59e0b;
            --warning-light: #fbbf24;
            --error: #ef4444;
            --error-light: #f87171;
            --info: #0ea5e9;
            --info-light: #38bdf8;
            
            /* Dark Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.6);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.6);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.7);
            --alert-shadow: rgba(0, 0, 0, 0.8);
        }
        
        /* Global Font Family */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        }
        
        html, body { height: 100%; margin: 0; padding: 0; }
        body { display: flex; flex-direction: column; background-color: var(--bg); color: var(--text); }
        nav { position: fixed; top: 0; left: 0; right: 0; z-index: 50; background-color: var(--nav-bg); color: var(--nav-text); box-shadow: var(--shadow); }
        #sidebar {
            position: fixed;
            left: 0;
            top: 64px;
            height: calc(100vh - 64px);
            width: 16rem;
            background: var(--sidebar-bg);
            overflow-y: auto;
            z-index: 50;
            transition: width 0.3s ease;
            padding: 1rem;
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-lg);
        }
        #sidebar.collapsed { width: 4rem; }
        #sidebar.collapsed .menu-text { display: none; }
        #sidebar.collapsed h2 { font-size: 0; padding: 0.5rem 0; }
        #sidebar.collapsed hr { margin: 0.5rem 0; }
        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 3px; }
        #sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.5); }
        main {
            margin-top: 64px;
            margin-left: 16rem;
            margin-bottom: 64px;
            flex: 1;
            overflow-y: auto;
        }
        body.sidebar-collapsed main { margin-left: 4rem; }
        main::-webkit-scrollbar { width: 8px; }
        main::-webkit-scrollbar-track { background: #f1f1f1; }
        main::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        main::-webkit-scrollbar-thumb:hover { background: #555; }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: var(--footer-bg);
            text-align: center;
            padding: 1rem;
            border-top: 1px solid var(--border);
            z-index: 40;
            box-shadow: 0 -1px 3px rgba(0,0,0,0.05);
        }
        @media (max-width: 768px) {
            #sidebar { width: 16rem; }
            main { margin-left: 0; }
            nav { padding: 0 1rem; }
        }
        /* Alert Balloon Styles */
        #alertBalloon {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            max-width: 400px;
            padding: 16px;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            z-index: 999;
            display: none;
            animation: slideInDown 0.3s ease-out;
            font-size: 14px;
            line-height: 1.5;
        }
        #alertBalloon.show { display: block; }
        #alertBalloon.success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid var(--success);
        }
        #alertBalloon.error {
            background-color: #fee2e2;
            color: #7f1d1d;
            border-left: 4px solid var(--error);
        }
        #alertBalloon.warning {
            background-color: #fef3c7;
            color: #78350f;
            border-left: 4px solid var(--warning);
        }
        #alertBalloon.info {
            background-color: #e0f2fe;
            color: #075985;
            border-left: 4px solid var(--info);
        }
        #alertBalloon .alert-close {
            float: right;
            cursor: pointer;
            box-shadow: 0 4px 12px var(--alert-shadow);
            font-weight: bold;
            margin-left: 10px;
        }
        #alertBalloon .alert-close:hover {
            opacity: 0.7;
        }
        /* Dropdown menu adopts theme */
        #userMenu { background-color: var(--card-bg); color: var(--text); border: 1px solid var(--border); box-shadow: var(--shadow-md); border-radius: 0.5rem; }
        .hover-surface:hover { background-color: rgba(0,0,0,0.05); }
        html.dark .hover-surface:hover { background-color: rgba(255,255,255,0.06); }
        .footer-text { color: var(--nav-text); opacity: 0.75; }
        
        /* Dark mode overrides for Tailwind utility classes */
        html.dark .bg-white { background-color: var(--card-bg) !important; }
        html.dark .bg-gray-50 { background-color: var(--bg-secondary) !important; }
        html.dark .bg-gray-100 { background-color: rgba(51, 65, 85, 0.4) !important; }
        html.dark .text-gray-500 { color: var(--text-muted) !important; }
        html.dark .text-gray-600 { color: var(--text-secondary) !important; }
        html.dark .text-gray-700 { color: var(--text) !important; }
        html.dark .text-gray-800 { color: var(--text) !important; }
        html.dark .text-gray-900 { color: var(--text) !important; }
        html.dark .border-gray-200 { border-color: var(--border) !important; }
        html.dark .border-gray-300 { border-color: var(--border-light) !important; }
        html.dark .divide-gray-200 { border-color: var(--border) !important; }
        html.dark input, html.dark select, html.dark textarea { 
            background-color: var(--bg-secondary) !important; 
            color: var(--text) !important;
            border-color: var(--border) !important;
        }
        html.dark input::placeholder { color: var(--text-muted) !important; }
        
        /* Professional button styles */
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: var(--primary-dark); }
        .btn-accent { background-color: var(--accent); color: white; }
        .btn-accent:hover { background-color: var(--accent-dark); }
        html.dark .bg-blue-600 { background-color: var(--primary) !important; }
        html.dark .hover\:bg-blue-700:hover { background-color: var(--primary-dark) !important; }
        html.dark .text-blue-600 { color: var(--primary) !important; }
        @keyframes slideInDown {
            from {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
        @keyframes slideOutUp {
            from {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
            to {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
        }
    .sidebar-bullet {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #fff;
        border-radius: 2px;
        margin-right: 12px;
        vertical-align: middle;
        transition: all 0.2s;
    }
    #sidebar.collapsed .sidebar-bullet {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        margin-right: 0;
        background: #fff;
        box-shadow: 0 0 0 2px #0c4a6e; /* Add a subtle outline for visibility */
    }
    </style>

</head>
<body>
    <?php $userRole = $_SESSION['user_role'] ?? 'REQUESTER'; ?>
    
    <!-- Alert Balloon Container -->
    <div id="alertBalloon">
        <span class="alert-close" onclick="closeAlert()">&times;</span>
        <div id="alertMessage"></div>
    </div>

    <!-- Auto-show session alerts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
                showError('<?php echo addslashes($_SESSION['error']); ?>');
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
                showSuccess('<?php echo addslashes($_SESSION['success']); ?>');
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['warning']) && !empty($_SESSION['warning'])): ?>
                showWarning('<?php echo addslashes($_SESSION['warning']); ?>');
                <?php unset($_SESSION['warning']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['info']) && !empty($_SESSION['info'])): ?>
                showInfo('<?php echo addslashes($_SESSION['info']); ?>');
                <?php unset($_SESSION['info']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['restriction']) && !empty($_SESSION['restriction'])): ?>
                showError('<?php echo addslashes($_SESSION['restriction']); ?>');
                <?php unset($_SESSION['restriction']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['system_error']) && !empty($_SESSION['system_error'])): ?>
                showError('<?php echo addslashes($_SESSION['system_error']); ?>');
                <?php unset($_SESSION['system_error']); ?>
            <?php endif; ?>
        });
    </script>
    
    <nav class="shadow">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <button id="toggleSidebar" class="p-2 hover-surface rounded" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <img src="/images/logo-mini-header.png" alt="Company Logo" style="height:28px;width:auto;object-fit:contain;" class="ml-3 mr-2 align-middle drop-shadow" loading="lazy">
                <div class="text-lg font-bold" style="color: var(--primary);">Arabian Fal</div>
            </div>
            <div class="flex items-center gap-4">
                <span>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong></span>
                <!-- Notification Bell -->
                <button id="notifBtn" class="relative p-2 hover-surface rounded" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span id="notifDot" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <!-- User menu with Dark Mode toggle -->
                <div class="relative">
                    <button id="userMenuBtn" class="p-2 hover-surface rounded" title="Menu">
                        <i class="fas fa-ellipsis-vertical"></i>
                    </button>
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-52 shadow rounded py-2">
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-sm">Dark Mode</span>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="darkModeToggle" class="form-checkbox">
                            </label>
                        </div>
                        <a href="/logout" class="block px-4 py-2 text-red-600 hover-surface">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <aside id="sidebar">
        <?php
        // Sidebar menu structure: group => [title, icon, roles, [ [label, url, icon, roles] ] ]
        $sidebarMenu = [
            [
                'id' => 'home',
                'title' => 'Home',
                'icon' => 'fa-home',
                'roles' => ['ADMIN', 'REQUESTER', 'DEPARTMENT_MANAGER', 'IT_MANAGER', 'IT_STAFF'],
                'items' => [
                    ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'fa-home', 'roles' => ['ADMIN', 'REQUESTER', 'DEPARTMENT_MANAGER', 'IT_MANAGER', 'IT_STAFF']],
                    ['label' => 'My Profile', 'url' => '/profile', 'icon' => 'fa-user', 'roles' => ['ADMIN', 'REQUESTER', 'DEPARTMENT_MANAGER', 'IT_MANAGER', 'IT_STAFF']],
                    ['label' => 'Settings', 'url' => '/settings', 'icon' => 'fa-cog', 'roles' => ['ADMIN', 'REQUESTER', 'DEPARTMENT_MANAGER', 'IT_MANAGER', 'IT_STAFF']],
                ]
            ],
            [
                'id' => 'admin',
                'title' => 'Admin',
                'icon' => 'fa-users-cog',
                'roles' => ['ADMIN'],
                'items' => [
                    ['label' => 'Users', 'url' => '/users', 'icon' => 'fa-users', 'roles' => ['ADMIN']],
                    ['label' => 'Department', 'url' => '/departments', 'icon' => 'fa-building', 'roles' => ['ADMIN']],
                    ['label' => 'Store', 'url' => '/stores', 'icon' => 'fa-map-marker-alt', 'roles' => ['ADMIN']],
                ]
            ],
            [
                'id' => 'issue',
                'title' => 'Issue / Receipt',
                'icon' => 'fa-exchange-alt',
                'roles' => ['ADMIN', 'IT_MANAGER', 'DEPARTMENT_MANAGER', 'REQUESTER'],
                'items' => [
                    ['label' => 'Issuance', 'url' => '/assets/issue', 'icon' => 'fa-arrow-right', 'roles' => ['ADMIN', 'IT_MANAGER']],
                    ['label' => 'Receive', 'url' => '/assets/receive', 'icon' => 'fa-arrow-left', 'roles' => ['ADMIN', 'IT_MANAGER', 'DEPARTMENT_MANAGER', 'REQUESTER']],
                    ['label' => 'Manage Asset Request', 'url' => '/asset-requests/manage', 'icon' => 'fa-tasks', 'roles' => ['ADMIN', 'IT_MANAGER']],
                ]
            ],
            [
                'id' => 'request',
                'title' => 'Request',
                'icon' => 'fa-plus-circle',
                'roles' => ['ADMIN', 'REQUESTER', 'IT_MANAGER'],
                'items' => [
                    ['label' => 'Asset Request', 'url' => '/asset-requests/create', 'icon' => 'fa-plus-circle', 'roles' => ['ADMIN', 'REQUESTER', 'IT_MANAGER']],
                    ['label' => 'My Request', 'url' => '/asset-requests/my-requests', 'icon' => 'fa-list', 'roles' => ['ADMIN', 'REQUESTER', 'IT_MANAGER']],
                ]
            ],
            [
                'id' => 'approval',
                'title' => 'Approval',
                'icon' => 'fa-check-circle',
                'roles' => ['ADMIN', 'DEPARTMENT_MANAGER', 'IT_MANAGER'],
                'items' => [
                    ['label' => 'Approve Request', 'url' => '/asset-requests/manager-approvals', 'icon' => 'fa-check-circle', 'roles' => ['ADMIN', 'DEPARTMENT_MANAGER', 'IT_MANAGER']],
                ]
            ],
            [
                'id' => 'reports',
                'title' => 'Reports',
                'icon' => 'fa-chart-bar',
                'roles' => ['ADMIN', 'REQUESTER', 'DEPARTMENT_MANAGER', 'IT_MANAGER'],
                'items' => [
                    ['label' => 'Stock On-hand', 'url' => '/reports/stock-on-hand', 'icon' => 'fa-clipboard-list', 'roles' => ['ADMIN', 'IT_MANAGER']],
                    ['label' => 'Movement Report', 'url' => '/assets/movement', 'icon' => 'fa-route', 'roles' => ['ADMIN', 'IT_MANAGER']],
                    ['label' => 'Open Order', 'url' => '/reports/open-orders', 'icon' => 'fa-box-open', 'roles' => ['ADMIN', 'REQUESTER', 'DEPARTMENT_MANAGER', 'IT_MANAGER', 'IT_STAFF']],
                ]
            ],
        ];

        // Helper: check if user role is allowed for group/item
        function sidebar_role_allowed($roles, $userRole) {
            return in_array('*', $roles) || in_array($userRole, $roles);
        }
        ?>
        <?php foreach ($sidebarMenu as $group): ?>
            <?php if (sidebar_role_allowed($group['roles'], $userRole)): ?>
                <div class="sidebar-group" data-group="<?= $group['id'] ?>">
                    <button class="sidebar-group-toggle flex items-center w-full mb-2 text-white font-bold border-b border-blue-400 pb-2 focus:outline-none" style="background: none;" type="button">
                        <i class="fas <?= $group['icon'] ?> w-5"></i>
                        <span class="ml-3 menu-text"><?= $group['title'] ?></span>
                        <i class="fas fa-chevron-down ml-auto group-chevron"></i>
                    </button>
                    <ul class="sidebar-group-list">
                        <?php foreach ($group['items'] as $item): ?>
                            <?php if (sidebar_role_allowed($item['roles'], $userRole)): ?>
                                <li>
                                    <a href="<?= $item['url'] ?>" class="flex items-center p-2 rounded hover:bg-blue-900 text-white" title="<?= htmlspecialchars($item['label']) ?>">
                                        <span class="sidebar-bullet"></span>
                                        <span class="menu-text"><?= $item['label'] ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <hr class="border-blue-700 my-3">
        <ul>
            <li><a href="/logout" class="flex items-center p-2 rounded hover:bg-red-900 text-white" title="Logout"><i class="fas fa-sign-out-alt w-5"></i><span class="ml-3 menu-text">Logout</span></a></li>
        </ul>
    </aside>
    <main class="p-6">
        <?php echo $content; ?>
    </main>
    <footer class="bg-white text-center py-2">
        <p class="text-sm footer-text">&copy; 2026 ITAMS. All Rights Reserved.</p>
    </footer>
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        toggleBtn.addEventListener('click', function() {
            const isCollapsed = sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed', isCollapsed);
            if (isCollapsed) {
                this.querySelector('i').classList.replace('fa-bars', 'fa-angles-right');
            } else {
                this.querySelector('i').classList.replace('fa-angles-right', 'fa-bars');
            }
        });

        // Collapsible sidebar groups
        document.querySelectorAll('.sidebar-group-toggle').forEach(btn => {
            const group = btn.closest('.sidebar-group');
            const groupId = group.getAttribute('data-group');
            const list = group.querySelector('.sidebar-group-list');
            const chevron = btn.querySelector('.group-chevron');
            // Restore state
            let open = localStorage.getItem('sidebar-group-' + groupId);
            if (open === null) open = 'true';
            if (open === 'false') {
                list.style.display = 'none';
                chevron.style.transform = 'rotate(-90deg)';
            }
            btn.addEventListener('click', () => {
                const isOpen = list.style.display !== 'none';
                list.style.display = isOpen ? 'none' : '';
                chevron.style.transform = isOpen ? 'rotate(-90deg)' : '';
                localStorage.setItem('sidebar-group-' + groupId, (!isOpen).toString());
            });
        });

        // Notification bell: hide dot on click
        const notifBtn = document.getElementById('notifBtn');
        const notifDot = document.getElementById('notifDot');
        if (notifBtn && notifDot) {
            notifBtn.addEventListener('click', () => {
                notifDot.style.display = 'none';
            });
        }
        // User menu toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');
        if (userMenuBtn && userMenu) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }
        // Dark mode init and toggle
        const theme = localStorage.getItem('theme');
        const htmlEl = document.documentElement;
        const darkToggle = document.getElementById('darkModeToggle');
        if (theme === 'dark') {
            htmlEl.classList.add('dark');
            if (darkToggle) darkToggle.checked = true;
        }
        if (darkToggle) {
            darkToggle.addEventListener('change', (e) => {
                if (e.target.checked) {
                    htmlEl.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    htmlEl.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            });
        }
    </script>

    <script>
        // ============================================
        // GENERAL TABLE EXPORT/PRINT FUNCTIONALITY
        // ============================================
        
        document.addEventListener('DOMContentLoaded', function() {
            // Find all export buttons
            document.querySelectorAll('.dt-export').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Find the nearest table (could be sibling or in parent)
                    let table = this.closest('.bg-white')?.querySelector('table') || 
                               this.parentElement?.closest('.bg-white')?.querySelector('table') ||
                               document.querySelector('table');
                    if (table) {
                        let exportName = this.closest('[data-export-name]')?.dataset?.exportName || 'export';
                        exportTableToCSV(table, exportName);
                    }
                });
            });
            
            // Find all print buttons
            document.querySelectorAll('.dt-print').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Find the nearest table
                    let table = this.closest('.bg-white')?.querySelector('table') || 
                               this.parentElement?.closest('.bg-white')?.querySelector('table') ||
                               document.querySelector('table');
                    if (table) {
                        printTable(table);
                    }
                });
            });
        });
        
        function exportTableToCSV(tableElement, exportName) {
            const rows = [];
            const headers = [];
            
            // Extract headers
            tableElement.querySelectorAll('thead th').forEach((th) => {
                const headerText = th.textContent.trim().toLowerCase();
                if (headerText !== 'actions') {
                    headers.push(th.textContent.trim());
                }
            });
            
            if (headers.length === 0) {
                alert('No headers found in table');
                return;
            }
            
            rows.push(headers.map(h => `"${h}"`).join(','));
            
            // Extract rows
            tableElement.querySelectorAll('tbody tr').forEach((row) => {
                const cols = [];
                const tds = row.querySelectorAll('td');
                const ths = tableElement.querySelectorAll('thead th');
                
                tds.forEach((td, index) => {
                    const headerText = ths[index]?.textContent.trim().toLowerCase();
                    if (headerText !== 'actions') {
                        let text = td.textContent.trim()
                            .replace(/[\n\r]+/g, ' ')
                            .replace(/"/g, '""')
                            .replace(/,/g, ';');
                        cols.push(`"${text}"`);
                    }
                });
                
                if (cols.length > 0) {
                    rows.push(cols.join(','));
                }
            });
            
            if (rows.length <= 1) {
                alert('No data to export');
                return;
            }
            
            const csv = rows.join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.href = url;
            link.download = `${exportName}_${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
        
        function printTable(tableElement) {
            const printWindow = window.open('', '_blank', 'height=600,width=900');
            const title = document.querySelector('h1')?.textContent || 'Report';
            const timestamp = new Date().toLocaleString();
            
            // Clone and clean table
            const clonedTable = tableElement.cloneNode(true);
            
            // Remove last column if it's Actions
            const headerCells = clonedTable.querySelectorAll('thead th');
            if (headerCells.length > 0) {
                const lastHeader = headerCells[headerCells.length - 1];
                if (lastHeader.textContent.trim().toLowerCase() === 'actions') {
                    lastHeader.remove();
                    clonedTable.querySelectorAll('tbody tr').forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length > 0) {
                            cells[cells.length - 1].remove();
                        }
                    });
                }
            }
            
            // Build print HTML
            const html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${title}</title>
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif; }
                        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
                        h1 { margin-bottom: 10px; color: #0c4a6e; }
                        .print-header { margin-bottom: 20px; border-bottom: 2px solid #0c4a6e; padding-bottom: 10px; }
                        .print-meta { font-size: 12px; color: #666; }
                        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                        th { background-color: #f0f9ff; border: 1px solid #0c4a6e; padding: 10px; text-align: left; font-weight: 600; color: #0c4a6e; }
                        td { border: 1px solid #ddd; padding: 8px; }
                        tr:nth-child(even) { background-color: #f8fafc; }
                        @media print { 
                            body { margin: 0; } 
                            .print-header { page-break-after: avoid; }
                        }
                    </style>
                    <style>
                    /* Sidebar sub-menu bullet styles */
                    .sidebar-bullet {
                        display: inline-block;
                        width: 8px;
                        height: 8px;
                        background: #fff;
                        border-radius: 2px;
                        margin-right: 12px;
                        vertical-align: middle;
                        transition: all 0.2s;
                    }
                    #sidebar.collapsed .sidebar-bullet {
                        width: 5px;
                        height: 5px;
                        border-radius: 50%;
                        margin-right: 0;
                        background: #fff;
                    }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h1>${title}</h1>
                        <p class="print-meta">Printed on: ${timestamp}</p>
                    </div>
                    ${clonedTable.outerHTML}
                </body>
                </html>
            `;
            
            printWindow.document.write(html);
            printWindow.document.close();
            printWindow.focus();
            
            setTimeout(() => {
                printWindow.print();
            }, 500);
        }
    </script>

    <!-- Alert Functions -->
    <script>
        // Show alert balloon with message and type
        function showAlert(message, type = 'info', duration = 3000) {
            const alertBalloon = document.getElementById('alertBalloon');
            const alertMessage = document.getElementById('alertMessage');
            
            // Set message and type
            alertMessage.textContent = message;
            alertBalloon.className = 'show ' + type;
            
            // Auto-hide after duration
            if (duration > 0) {
                setTimeout(() => {
                    closeAlert();
                }, duration);
            }
        }

        // Close alert
        function closeAlert() {
            const alertBalloon = document.getElementById('alertBalloon');
            alertBalloon.style.animation = 'slideOutUp 0.3s ease-out';
            setTimeout(() => {
                alertBalloon.classList.remove('show');
                alertBalloon.style.animation = '';
            }, 300);
        }

        // Convenience functions
        function showSuccess(message, duration = 3000) {
            showAlert(message, 'success', duration);
        }

        function showError(message, duration = 3000) {
            showAlert(message, 'error', duration);
        }

        function showWarning(message, duration = 3000) {
            showAlert(message, 'warning', duration);
        }

        function showInfo(message, duration = 3000) {
            showAlert(message, 'info', duration);
        }
    </script>
</body>
</html>

