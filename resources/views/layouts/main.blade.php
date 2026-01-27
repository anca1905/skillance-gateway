<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Skillance Gateway')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
            overflow-x: hidden;
        }

        /* --- LAYOUT UTAMA --- */
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        /* --- SIDEBAR STYLE --- */
        #sidebar-wrapper {
            min-width: 250px;
            max-width: 250px;
            background: #0D47A1;
            /* Biru Dinas Skillance */
            color: white;
            min-height: 100vh;
            transition: all 0.3s;
            margin-left: -250px;
            /* Hidden di HP */
        }

        /* Responsif: Tampil di Laptop */
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }
        }

        /* Logic Toggle */
        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        .sidebar-content {
            padding: 20px;
        }

        /* Menu Links */
        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #F57C00;
            /* Orange Aksen */
            color: white;
            font-weight: bold;
        }

        /* --- KONTEN --- */
        #page-content-wrapper {
            width: 100%;
            padding: 20px;
        }

        /* --- NAVBAR --- */
        .navbar-custom {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
            padding: 15px;
        }

        /* --- UTILS --- */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <div id="sidebar-wrapper" class="sidebar">
            <div class="sidebar-content">
                <h4 class="text-center mb-4 fw-bold"><i class="fas fa-rocket me-2"></i>SKILLANCE</h4>
                <hr style="border-color: rgba(255,255,255,0.2);">

                <nav class="nav flex-column">
                    <a href="{{ route('devices.index') }}"
                        class="{{ request()->routeIs('devices.*') ? 'active' : '' }}">
                        <i class="fab fa-whatsapp me-2"></i> Kelola Device
                    </a>

                    <a href="{{ route('autoreply.index') }}"
                        class="{{ request()->routeIs('autoreply.*') ? 'active' : '' }}">
                        <i class="fas fa-robot me-2"></i> Auto Reply
                    </a>

                    <a href="{{ route('broadcast.index') }}"
                        class="{{ request()->routeIs('broadcast.*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn me-2"></i> Broadcast
                    </a>

                    <a href="{{ route('history.index') }}"
                        class="{{ request()->routeIs('history.*') ? 'active' : '' }}">
                        <i class="fas fa-history me-2"></i> Riwayat Pesan
                    </a>

                    <a href="{{ route('docs.index') }}" class="{{ request()->routeIs('docs.*') ? 'active' : '' }}">
                        <i class="fas fa-book me-2"></i> Dokumentasi API
                    </a>

                    <a href="{{ route('settings.index') }}"
                        class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog me-2"></i> Pengaturan Akun
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none text-start w-100 p-0">
                            <a href="#" class="mt-5 bg-danger text-white">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-custom mb-4">
                <div class="container-fluid">
                    <button class="btn btn-primary d-md-none me-3" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>

                    <span class="navbar-brand mb-0 h1 fw-bold">@yield('header', 'Dashboard')</span>

                    <div class="ms-auto d-flex align-items-center">
                        <div class="text-end me-2 d-none d-sm-block">
                            <small class="d-block fw-bold">{{ Auth::user()->name ?? 'User' }}</small>
                            <small class="text-muted">Member Skillance</small>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'A') }}&background=0D47A1&color=fff"
                            class="rounded-circle" height="40">
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-0">
                @yield('content')
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Logic Toggle Sidebar di HP
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function() {
            el.classList.toggle("toggled");
        };
    </script>

    @yield('scripts')

</body>

</html>
