<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Core</div>
            <a class="nav-link" href="{{ url('admin/dashboard') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Dashboard
            </a>
            <a class="nav-link" href="{{ url('admin/kelolaakun') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Kelola Akun
            </a>
            <a class="nav-link" href="{{ url('admin/jadwal-pelayanan') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                Jadwal Pelayanan
            </a>
            <div class="sb-sidenav-menu-heading">Landing Page</div>
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLanding"
                aria-expanded="false" aria-controls="collapseLanding">
                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                Konten
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseLanding" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="{{ url('admin/warta') }}">Warta Jemaat</a>
                    <a class="nav-link" href="{{ url('admin/renungan/list') }}">Renungan</a>
                    <a class="nav-link" href="{{ url('admin/berita') }}">Berita</a>
                    <a class="nav-link" href="{{ url('admin/galeri') }}">Galeri</a>
                </nav>
            </div>
            <div class="sb-sidenav-menu-heading">Addons</div>
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseRuangan"
                aria-expanded="false" aria-controls="collapseRuangan">
                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                Ruangan
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseRuangan" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="{{ url('admin/ruangan/jadwal') }}">Jadwal Ruangan</a>
                    <a class="nav-link" href="{{ url('admin/pinjam-ruangan') }}">Validasi Pinjam Ruangan</a>
                </nav>
            </div>
        </div>
    </div>
</nav>
