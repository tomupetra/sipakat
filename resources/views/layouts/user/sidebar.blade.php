<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Core</div>
            <a class="nav-link" href="{{ url('admin/dashboard') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Dashboard
            </a>
            <a class="nav-link" href="{{ route('user.jadwal-pelayanan') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Jadwal Pelayanan
            </a>
            <a class="nav-link" href="{{ url('user/jadwal-ruangan') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                Jadwal Ruangan
            </a>
        </div>
    </div>
</nav>
