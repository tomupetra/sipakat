<header id="header" class="header sticky-top" style="padding: 0.5rem 0;">
    <div class="container position-relative d-flex align-items-center justify-content-between">

        <a href="/" class="logo d-flex align-items-center me-auto me-xl-0">
            <!-- Uncomment the line below if you also wish to use an image logo -->
            <img src="assets/img/logo.png" alt="">
            <h1 class="sitename">HKBP Kayu Tinggi</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/berita') }}">Berita</a></li>
                <li><a href="{{ url('/renungan') }}">Renungan</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
        <a class="btn btn-primary btn-getstarted" href="{{ route('login') }}" id="myBtn"
            style="background-color: #000033;">Login</a>

    </div>
</header>
