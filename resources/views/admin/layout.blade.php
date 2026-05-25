<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Priyam Finserv</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #0a0a0a;
            --sidebar-color: #a0a0a0;
            --sidebar-hover: #ffffff;
            --content-bg: #f8f9fa;
            --primary: #0a0a0a;
            --border: #e0e0e0;
            --transition: all 0.3s ease;
            --sidebar-width: 260px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { display: flex; height: 100vh; background-color: var(--content-bg); color: #333; overflow: hidden; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
            z-index: 200;
        }
        .sidebar-header {
            padding: 1.6rem 1.5rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }

        .nav-links { list-style: none; flex-grow: 1; padding: 1.2rem 0.8rem; overflow-y: auto; }
        .nav-links li { margin-bottom: 0.3rem; }
        .nav-links li a {
            display: flex; align-items: center; gap: 0.9rem;
            padding: 0.75rem 1rem;
            color: var(--sidebar-color);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.92rem;
            transition: var(--transition);
        }
        .nav-links li a i { width: 18px; font-size: 1rem; text-align: center; }
        .nav-links li a:hover, .nav-links li a.active { color: var(--sidebar-hover); background: rgba(255,255,255,0.1); }

        /* ── Sidebar Overlay (mobile) ── */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(2px);
            z-index: 199;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-backdrop.visible { opacity: 1; }

        /* ── Content area ── */
        .content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }
        .topbar {
            background: #fff;
            padding: 0.9rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
            gap: 1rem;
        }
        .topbar-left { display: flex; align-items: center; gap: 1rem; }
        .topbar-date { font-weight: 500; color: #666; font-size: 0.88rem; white-space: nowrap; }
        .topbar-right { display: flex; align-items: center; gap: 1.2rem; flex-shrink: 0; }

        /* ── Admin hamburger ── */
        .admin-hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            width: 36px; height: 36px;
            background: none; border: none;
            cursor: pointer; padding: 4px;
        }
        .admin-hamburger span {
            display: block; width: 20px; height: 2px;
            background: #333; border-radius: 2px;
            transition: transform 0.3s ease, opacity 0.2s ease, width 0.3s ease;
            transform-origin: center;
        }
        .admin-hamburger.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .admin-hamburger.active span:nth-child(2) { opacity: 0; width: 0; }
        .admin-hamburger.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .content { flex-grow: 1; overflow-y: auto; padding: 2rem; }

        /* ── Cards & common ── */
        .card { background: #fff; padding: 1.8rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 1.5rem; border: 1px solid var(--border); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem; }

        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border: none; background: var(--primary); color: #fff; cursor: pointer; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.9rem; transition: var(--transition); }
        .btn:hover { opacity: 0.8; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .btn-outline { background: transparent; color: var(--primary); border: 1px solid var(--primary); }
        .btn-outline:hover { background: var(--primary); color: #fff; }

        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 560px; }
        th, td { text-align: left; padding: 0.9rem 1rem; border-bottom: 1px solid var(--border); }
        th { color: #888; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; background: #fafafa; white-space: nowrap; }
        tr:hover td { background: #fdfdfd; }

        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 500; color: #555; }
        .form-control { width: 100%; padding: 0.8rem 1rem; border: 1px solid var(--border); border-radius: 6px; font-family: inherit; font-size: 0.95rem; transition: var(--transition); background: #fafafa; }
        .form-control:focus { outline: none; border-color: var(--primary); background: #fff; }

        .alert { padding: 1rem 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; display: flex; align-items: center; gap: 0.8rem; font-weight: 500; }
        .alert-success { background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; }

        .badge { padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .badge-pending { background: #fff4e5; color: #b26a00; border: 1px solid #ffe0b2; }
        .badge-resolved { background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; }

        /* ── Mobile breakpoint ── */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100%;
                transform: translateX(-100%);
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-backdrop { display: block; }

            .content-wrapper { width: 100%; }
            .admin-hamburger { display: flex; }
            .topbar { padding: 0.8rem 1rem; }
            .topbar-date { display: none; }
            .content { padding: 1.2rem 1rem; }
            .card { padding: 1.2rem; }
        }
        @media (max-width: 480px) {
            .btn { padding: 0.5rem 0.9rem; font-size: 0.82rem; }
        }
    </style>
</head>
<body>
    <!-- Sidebar backdrop (mobile) -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-gem"></i> Admin
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Overview</a></li>
            <li><a href="{{ route('admin.queries') }}" class="{{ request()->routeIs('admin.queries') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-list"></i> Appointments</a></li>
            <li><a href="{{ route('admin.files') }}" class="{{ request()->routeIs('admin.files') ? 'active' : '' }}"><i class="fa-solid fa-file-shield"></i> File Sharing</a></li>
            <li><a href="{{ route('admin.blogs') }}" class="{{ request()->routeIs('admin.blogs') ? 'active' : '' }}"><i class="fa-solid fa-newspaper"></i> Blog Panel</a></li>
            <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Client Directory</a></li>
        </ul>
    </div>

    <div class="content-wrapper">
        <div class="topbar">
            <div class="topbar-left">
                <button class="admin-hamburger" id="adminHamburger" aria-label="Toggle sidebar">
                    <span></span><span></span><span></span>
                </button>
                <span class="topbar-date">{{ date('l, F j, Y') }}</span>
            </div>
            <div class="topbar-right">
                <a href="{{ url('/') }}" target="_blank" style="color: #666; text-decoration: none; font-size: 0.88rem; display:flex; align-items:center; gap:0.4rem;"><i class="fa-solid fa-arrow-up-right-from-square"></i> View Site</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #333; cursor: pointer; font-size: 0.9rem; font-weight: 500; font-family: inherit; display:flex; align-items:center; gap:0.4rem;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        (function () {
            var hamburger = document.getElementById('adminHamburger');
            var sidebar   = document.getElementById('adminSidebar');
            var backdrop  = document.getElementById('sidebarBackdrop');

            function openSidebar() {
                sidebar.classList.add('open');
                backdrop.classList.add('visible');
                hamburger.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                sidebar.classList.remove('open');
                backdrop.classList.remove('visible');
                hamburger.classList.remove('active');
                document.body.style.overflow = '';
            }

            hamburger.addEventListener('click', function () {
                sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
            });
            backdrop.addEventListener('click', closeSidebar);

            // Close on nav link click (mobile)
            sidebar.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () {
                    if (window.innerWidth <= 768) closeSidebar();
                });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });
        })();
    </script>
</body>
</html>

