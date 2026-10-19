<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Panel') ?> | BeniTours Admin</title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo2.png?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/img/logo2.png') ?>">

    <!-- Bootstrap 5.3.3 CSS (Local) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap/css/bootstrap.min.css?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons (Local) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap-icons/bootstrap-icons.min.css?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/css/admin.css?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/css/admin.css') ?>">
</head>
<body class="admin-theme">

    <!-- Overlay móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Barra Lateral (Sidebar) -->
    <aside id="adminSidebar" class="d-lg-none" aria-label="Menú de administración">
        <div class="sidebar-header">
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard" class="d-flex align-items-center gap-2 text-decoration-none">
                
                <div class="lh-sm">
                    <img src="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo2.png" 
                    alt="BeniTours" 
                    class="navbar-logo"
                    height="42">
                </div>
            </a>
        </div>

        <div class="sidebar-menu-wrapper">
            <div class="sidebar-label">Principal</div>
            <ul class="nav-admin mb-3">
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard" class="nav-admin-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard')) ? 'active' : '' ?>">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-label">Directorio Turístico</div>
            <ul class="nav-admin mb-3">
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares" class="nav-admin-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'lugares') && !str_contains($_SERVER['REQUEST_URI'] ?? '', 'crear')) ? 'active' : '' ?>">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Lugares y Comercios</span>
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares/crear" class="nav-admin-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'crear')) ? 'active' : '' ?>">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Nueva Ficha</span>
                    </a>
                </li>
            </ul>

                        <div class="sidebar-label">Comercial & Finanzas</div>
            <ul class="nav-admin mb-3">
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/solicitudes" class="nav-admin-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'solicitudes')) ? 'active' : '' ?>">
                        <i class="bi bi-inbox-fill"></i>
                        <span>Solicitudes</span>
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos" class="nav-admin-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'pagos')) ? 'active' : '' ?>">
                        <i class="bi bi-cash-stack"></i>
                        <span>Pagos & Vigencias</span>
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes" class="nav-admin-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'reportes')) ? 'active' : '' ?>">
                        <i class="bi bi-bar-chart-line-fill"></i>
                        <span>Reportes Analíticos</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-label">Ajustes & Catálogos</div>
            <ul class="nav-admin mb-3">
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/catalogos" class="nav-admin-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'catalogos')) ? 'active' : '' ?>">
                        <i class="bi bi-sliders2"></i>
                        <span>Parámetros & Planes</span>
                    </a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/eventos" class="nav-admin-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'eventos')) ? 'active' : '' ?>">
                        <i class="bi bi-calendar-event"></i>
                        <span>Agenda & Eventos</span>
                    </a>
                </li>
            </ul>

        </div>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.85rem;">
                        <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="lh-1 text-truncate">
                        <small class="text-white fw-semibold d-block text-truncate"><?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin') ?></small>
                        <span class="text-white-50" style="font-size: 0.68rem;">Conectado</span>
                    </div>
                </div>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/logout" class="btn btn-outline-danger btn-sm p-1 rounded-circle" title="Cerrar Sesión" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-power"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Barra Superior Moderna de Administración -->
    <header id="adminTopbar">
        <div class="topbar-inner-container">
            <!-- Bloque 1: Brand / Título / Botón móvil -->
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <button class="btn btn-light border d-lg-none p-1 px-2 rounded-3 shadow-none flex-shrink-0" id="btnToggleSidebar" aria-label="Abrir Menú" aria-controls="adminSidebar" aria-expanded="false">
                    <i class="bi bi-list fs-5 text-dark"></i>
                </button>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo.png" 
                         alt="BeniTours" 
                         class="admin-topbar-logo"
                         height="38">
                    <span class="badge bg-warning text-dark rounded-pill fw-bold small d-none d-sm-inline-block px-2 py-1 shadow-sm">
                        Admin
                    </span>
                </a>
            </div>

            <!-- Bloque 2: Navegación Central (Desktop) -->
            <nav class="admin-desktop-nav d-none d-lg-flex" aria-label="Navegación de administración">
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard" class="admin-top-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares" class="admin-top-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'lugares') && !str_contains($_SERVER['REQUEST_URI'] ?? '', 'crear') ? 'active' : '' ?>">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Lugares</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares/crear" class="admin-top-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'crear') ? 'active' : '' ?>">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Nueva Ficha</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/solicitudes" class="admin-top-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'solicitudes')) ? 'active' : '' ?>">
                    <i class="bi bi-inbox-fill"></i>
                    <span>Solicitudes</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos" class="admin-top-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'pagos')) ? 'active' : '' ?>">
                    <i class="bi bi-cash-stack"></i>
                    <span>Pagos</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes" class="admin-top-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'reportes')) ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Reportes</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/catalogos" class="admin-top-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'catalogos')) ? 'active' : '' ?>">
                    <i class="bi bi-sliders2"></i>
                    <span>Parámetros</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/eventos" class="admin-top-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'eventos')) ? 'active' : '' ?>">
                    <i class="bi bi-calendar-event"></i>
                    <span>Eventos</span>
                </a>
            </nav>

            <!-- Bloque 3: Acciones a la Derecha (Catálogo Público + Menú de Usuario) -->
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <a href="<?= htmlspecialchars($baseUrl) ?>/" target="_blank" 
                   class="btn btn-topbar-catalog btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center gap-1 shadow-sm" 
                   title="Ver Catálogo Público">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span class="d-none d-md-inline">Ver Portal</span>
                </a>

                <!-- Dropdown del usuario con avatar elegante -->
                <div class="dropdown">
                    <button class="btn btn-topbar-user rounded-pill border d-flex align-items-center gap-2 px-2 px-sm-3 py-1 shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="topbar-avatar">
                            <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
                        </div>
                        <span class="small fw-bold d-none d-sm-inline"><?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?></span>
                        <i class="bi bi-chevron-down text-muted small d-none d-sm-inline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 p-2" style="min-width: 220px;">
                        <li class="px-3 py-2 bg-light rounded-3 mb-2">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Administrador Activo</small>
                            <strong class="text-dark small d-block text-truncate"><?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin') ?></strong>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded-3 text-secondary d-flex align-items-center gap-2" href="<?= htmlspecialchars($baseUrl) ?>/" target="_blank">
                                <i class="bi bi-globe text-success"></i> Visitar Catálogo
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 rounded-3 text-danger d-flex align-items-center gap-2" href="<?= htmlspecialchars($baseUrl) ?>/admin/logout">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenedor Principal Dinámico -->
    <main id="adminContent">
        <?= $content ?>
    </main>

    <!-- Bootstrap Bundle JS (Local) -->
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- Chart.js 4.4.4 (Local) -->
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/chartjs/chart.umd.min.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/chartjs/chart.umd.min.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('adminSidebar');
            const toggleBtn = document.getElementById('btnToggleSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function setMenu(open) {
                sidebar.classList.toggle('show', open);
                overlay.classList.toggle('show', open);
                toggleBtn.setAttribute('aria-expanded', String(open));
                document.body.classList.toggle('sidebar-open', open);
            }

            function toggleMenu() {
                setMenu(!sidebar.classList.contains('show'));
            }

            if (toggleBtn) toggleBtn.addEventListener('click', toggleMenu);
            if (overlay) overlay.addEventListener('click', () => setMenu(false));
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && sidebar.classList.contains('show')) {
                    setMenu(false);
                    toggleBtn.focus();
                }
            });
            window.matchMedia('(min-width: 992px)').addEventListener('change', event => {
                if (event.matches) setMenu(false);
            });
        });
    </script>
</body>
</html>
