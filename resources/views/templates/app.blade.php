<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TIXID</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.min.css" rel="stylesheet" />
    {{-- CDN jquery releases.jquery.com (minifed) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <!-- Container wrapper -->
        <div class="container">
            <!-- Navbar brand -->
            <a class="navbar-brand me-4" href="{{ route('home') }}">
                <img src="https://asset.tix.id/wp-content/uploads/2021/10/TIXID_logo_blue-300x82.png" height="16"
                    alt="TIXID Logo" loading="lazy" style="margin-top: -1px;" />
            </a>

            <!-- Toggle button -->
            <button data-mdb-collapse-init class="navbar-toggler" type="button" data-mdb-target="#navbarButtonsExample"
                aria-controls="navbarButtonsExample" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarButtonsExample">
                <!-- Left links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if (Auth::check() && Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#"
                                id="navbarDropdownMenuLink" role="button" aria-expanded="false">
                                Data Master
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.cinemas.index') }}">Data Bioskop</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.movies.index') }}">Data Film</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.staffs.index') }}">Data Petugas</a>
                                </li>
                            </ul>
                        </li>
                    @elseif(Auth::check() && Auth::user()->role == 'staff')
                        <li class="nav-item">
                            <a href="{{ route('staff.dashboard') }}" class="nav-link">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('staff.schedules.index') }}">Jadwal Tiket</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('staff.promos.index') }}">Promo</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cinemas.list') }}">Bioskop</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tickets.index') }}">Tiket</a>
                        </li>
                    @endif
                </ul>
                <!-- Left links -->

                <div class="d-flex align-items-center">
                    @if (Auth::check())
                        <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                    @else
                        <a href="{{ route('login') }}" data-mdb-ripple-init type="button"
                            class="btn btn-link px-3 me-2">
                            Login
                        </a>
                        <a href="{{ route('sign_up') }}" data-mdb-ripple-init type="button"
                            class="btn btn-primary me-3">
                            Sign up
                        </a>
                    @endif
                </div>
            </div>
            <!-- Collapsible wrapper -->
        </div>
        <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->
    @if (Session::get('accessDenied'))
        <div class="alert alert-danger alert-dismissible fade show alert-top-right" role="alert">
            {{ Session::get('accessDenied') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- menyimpan konten dinamis bagian HTML --}}
    @yield('content')

    {{-- datatable CDN (cdn.datatables.net) --}}
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    {{-- bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.umd.min.js"></script>
    {{-- CDN ChartJS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- menyimpan konten dinamis bagian JS --}}
    @stack('script')
</body>

</html>
