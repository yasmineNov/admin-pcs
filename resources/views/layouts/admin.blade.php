<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin PCS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger btn-sm">Logout</button>
                </form>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Admin PCS</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Master Data -->
                <li class="nav-item has-treeview
                    {{ request()->is('users*') || request()->is('barang*') ? 'menu-open' : '' }}">
                    
                    <a href="#" class="nav-link
                        {{ request()->is('users*') || request()->is('barang*') ? 'active' : '' }}">
                        {{-- <i class="nav-icon fas fa-database"></i> --}}
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('barang.index') }}"
                               class="nav-link {{ request()->is('barang*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-box"></i>
                                <p>Barang</p>
                            </a>
                        </li>

                        {{-- @if(auth()->user()->isAdmin()) --}}
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}"
                               class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>User</p>
                            </a>
                        </li>
                        {{-- @endif --}}

                        <li class="nav-item">
                            <a href="{{ route('suppliers.index') }}"
                                class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-truck"></i>
                                <p>Supplier</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('customers.index') }}"
                                class="nav-link {{ request()->is('customers*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Customer</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('banks.index') }}" 
                                class="nav-link {{ request()->is('banks*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-university"></i>
                            <p>Master Bank</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Inventory --}}
                <li class="nav-item has-treeview
                    {{ request()->is('users*') || request()->is('barang*') ? 'menu-open' : '' }}">
                    
                    <a href="#" class="nav-link
                        {{ request()->is('users*') || request()->is('barang*') ? 'active' : '' }}">
                        <p>
                            Inventory
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('incoming-barangs.index') }}" class="nav-link {{ request()->is('incoming-barangs*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-download"></i>
                                <p>Incoming Barang</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('mutasi-barangs.index') }}" class="nav-link {{ request()->is('mutasi-barangs*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Mutasi Barang</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Penjualan -->
                {{-- <li class="nav-item {{ request()->is('surat-jalans*') || request()->is('fakturs*') || request()->is('penjualans*') || request()->is('pembayarans*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('surat-jalans*') || request()->is('fakturs*') || request()->is('penjualans*') || request()->is('pembayarans*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>
                            Penjualan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('surat-jalans.index') }}" class="nav-link {{ request()->is('surat-jalans*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Surat Jalan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('fakturs.index') }}" class="nav-link {{ request()->is('fakturs*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Faktur</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('penjualans.index') }}" class="nav-link {{ request()->is('penjualans*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Penjualan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('pembayarans.index') }}" class="nav-link {{ request()->is('pembayarans*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pelunasan Faktur</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Pembelian -->
                <li class="nav-item {{ request()->is('po-suppliers*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('po-suppliers*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>
                            Pembelian
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('po-suppliers.index') }}" class="nav-link {{ request()->is('po-suppliers*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>PO Supplier</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}

                <!-- Kas -->
                <li class="nav-item {{ request()->is('kas*') ? 'menu-open' : '' }}">
                    <a href="{{ route('kas.index') }}" class="nav-link {{ request()->is('kas*') ? 'active' : '' }}">
                    {{-- <a href="#" class="nav-link {{ request()->is('kas*') ? 'active' : '' }}"> --}}
                        {{-- <i class="nav-icon fas fa-money-bill-wave"></i> --}}
                        <p>Kas</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>


    <div class="content-wrapper p-3">
        @yield('content')
    </div>

</div>

<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
